<?php
// Dieses Skript hilft bei der Diagnose von Problemen beim Modulupload
// Platzieren Sie es im Hauptverzeichnis Ihrer Anwendung

// Fehlerberichterstattung aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Erforderliche Klassen einbinden
require_once 'init.php';
require_once 'classes/ModulManager.php';
require_once 'classes/Logger.php';

// HTML-Header ausgeben
echo '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul-Upload Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        pre { background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
        .step { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1>Modul-Upload Diagnose-Tool</h1>
        <p class="lead">Dieses Tool hilft bei der Diagnose von Problemen beim Hochladen von Modulen.</p>';

// Formular für den Modulupload
if (!isset($_FILES['module_file'])) {
    echo '
        <div class="card mb-4">
            <div class="card-header">Modul hochladen</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="module_file" class="form-label">Modul-ZIP-Datei auswählen</label>
                        <input type="file" class="form-control" id="module_file" name="module_file" accept=".zip" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Hochladen und analysieren</button>
                </form>
            </div>
        </div>';
} else {
    // Modul-Upload verarbeiten
    echo '<div class="alert alert-info">Modul-Datei wurde hochgeladen. Starte Analyse...</div>';
    
    try {
        echo '<div class="step">
            <h3>Schritt 1: ZIP-Datei prüfen</h3>';
        
        // ZIP-Datei-Informationen anzeigen
        $zipFile = $_FILES['module_file']['tmp_name'];
        $zipName = $_FILES['module_file']['name'];
        $zipSize = $_FILES['module_file']['size'];
        $zipType = $_FILES['module_file']['type'];
        
        echo "<p>Dateiname: <strong>$zipName</strong></p>";
        echo "<p>Dateigröße: <strong>" . number_format($zipSize / 1024, 2) . " KB</strong></p>";
        echo "<p>MIME-Typ: <strong>$zipType</strong></p>";
        
        // ZIP-Datei öffnen und Inhalt anzeigen
        $zip = new ZipArchive();
        $result = $zip->open($zipFile);
        
        if ($result !== true) {
            $errorMessages = [
                ZipArchive::ER_EXISTS => 'Datei existiert bereits',
                ZipArchive::ER_INCONS => 'Zip-Archiv inkonsistent',
                ZipArchive::ER_INVAL => 'Ungültiges Argument',
                ZipArchive::ER_MEMORY => 'Speicherzuweisungsfehler',
                ZipArchive::ER_NOENT => 'Datei nicht gefunden',
                ZipArchive::ER_NOZIP => 'Keine Zip-Datei',
                ZipArchive::ER_OPEN => 'Kann Datei nicht öffnen',
                ZipArchive::ER_READ => 'Lesefehler',
                ZipArchive::ER_SEEK => 'Positionierungsfehler'
            ];
            
            $errorMessage = isset($errorMessages[$result]) ? $errorMessages[$result] : 'Unbekannter Fehler (' . $result . ')';
            throw new Exception("Fehler beim Öffnen der ZIP-Datei: " . $errorMessage);
        }
        
        echo "<p>ZIP-Datei erfolgreich geöffnet. Enthält <strong>{$zip->numFiles}</strong> Dateien.</p>";
        
        echo "<p>Inhalt der ZIP-Datei:</p>";
        echo "<pre>";
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            echo $stat['name'] . " (" . number_format($stat['size'] / 1024, 2) . " KB)\n";
        }
        echo "</pre>";
        
        echo '</div>';
        
        // info.php prüfen
        echo '<div class="step">
            <h3>Schritt 2: info.php prüfen</h3>';
        
        $infoIndex = $zip->locateName('info.php');
        if ($infoIndex === false) {
            throw new Exception("Die Modul-ZIP-Datei enthält keine info.php");
        }
        
        echo "<p class='text-success'>✓ info.php gefunden</p>";
        
        // info.php extrahieren und validieren
        $tempDir = sys_get_temp_dir() . '/module_' . uniqid();
        if (!mkdir($tempDir, 0777, true)) {
            throw new Exception("Fehler beim Erstellen des temporären Verzeichnisses: " . $tempDir);
        }
        
        echo "<p>Temporäres Verzeichnis erstellt: <code>$tempDir</code></p>";
        
        if (!$zip->extractTo($tempDir, 'info.php')) {
            throw new Exception("Fehler beim Extrahieren der info.php aus der ZIP-Datei");
        }
        
        echo "<p class='text-success'>✓ info.php erfolgreich extrahiert</p>";
        
        if (!file_exists($tempDir . '/info.php')) {
            throw new Exception("Die extrahierte info.php wurde nicht gefunden");
        }
        
        // info.php einlesen
        echo "<p>Versuche info.php einzulesen...</p>";
        
        try {
            $moduleInfo = include($tempDir . '/info.php');
            
            if (!is_array($moduleInfo)) {
                throw new Exception("Die info.php enthält keine gültigen Modulinformationen (kein Array zurückgegeben)");
            }
            
            echo "<p class='text-success'>✓ info.php erfolgreich eingelesen</p>";
            
            // Modulinformationen anzeigen
            echo "<p>Modulinformationen:</p>";
            echo "<pre>";
            // Gekürzte Ausgabe für große Arrays
            $infoOutput = $moduleInfo;
            
            // Wenn files vorhanden ist, kürzen wir die Ausgabe
            if (isset($infoOutput['files']) && is_array($infoOutput['files'])) {
                $fileCount = count($infoOutput['files']);
                $infoOutput['files'] = "[" . $fileCount . " Dateien]";
            }
            
            print_r($infoOutput);
            echo "</pre>";
            
            // Erforderliche Felder prüfen
            $requiredFields = ['name', 'version', 'description', 'type'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($moduleInfo[$field]) || empty($moduleInfo[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                echo "<p class='text-danger'>⚠ Fehlende Pflichtfelder in info.php: " . implode(', ', $missingFields) . "</p>";
            } else {
                echo "<p class='text-success'>✓ Alle Pflichtfelder vorhanden</p>";
            }
            
            // Modultyp prüfen
            if (isset($moduleInfo['type'])) {
                $validTypes = ['system', 'dashboard'];
                if (!in_array($moduleInfo['type'], $validTypes)) {
                    echo "<p class='text-danger'>⚠ Ungültiger Modultyp: {$moduleInfo['type']}. Erlaubte Typen: " . implode(', ', $validTypes) . "</p>";
                } else {
                    echo "<p class='text-success'>✓ Gültiger Modultyp: {$moduleInfo['type']}</p>";
                }
            }
            
            // Files-Array prüfen
            if (isset($moduleInfo['files']) && is_array($moduleInfo['files'])) {
                echo "<p class='text-success'>✓ Files-Array vorhanden mit " . count($moduleInfo['files']) . " Einträgen</p>";
                
                // Stichprobenartig prüfen
                $sampleKeys = array_slice(array_keys($moduleInfo['files']), 0, 3);
                echo "<p>Stichprobe der Dateien:</p>";
                echo "<ul>";
                foreach ($sampleKeys as $key) {
                    $fileContent = substr($moduleInfo['files'][$key], 0, 100);
                    echo "<li><strong>$key</strong>: " . (strlen($fileContent) > 97 ? substr($fileContent, 0, 97) . "..." : $fileContent) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='text-danger'>⚠ Kein gültiges Files-Array gefunden</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='text-danger'>Fehler beim Einlesen der info.php: " . $e->getMessage() . "</p>";
            
            // Dateiinhalt anzeigen
            echo "<p>Inhalt der info.php:</p>";
            echo "<pre>" . htmlspecialchars(file_get_contents($tempDir . '/info.php')) . "</pre>";
            
            throw new Exception("Fehler beim Einlesen der info.php: " . $e->getMessage());
        }
        
        echo '</div>';
        
        // ModuleManager testen
        echo '<div class="step">
            <h3>Schritt 3: ModuleManager testen</h3>';
        
        echo "<p>Versuche, das Modul mit dem ModuleManager zu verarbeiten...</p>";
        
        try {
            $moduleManager = new ModuleManager();
            $result = $moduleManager->uploadModule($_FILES['module_file']);
            
            if ($result['success']) {
                echo "<p class='text-success'>✓ Modul erfolgreich hochgeladen</p>";
                echo "<pre>";
                print_r($result);
                echo "</pre>";
            } else {
                echo "<p class='text-danger'>⚠ Fehler beim Hochladen des Moduls: " . $result['message'] . "</p>";
                if (isset($result['details'])) {
                    echo "<p>Details:</p>";
                    echo "<pre>";
                    print_r($result['details']);
                    echo "</pre>";
                }
            }
        } catch (Exception $e) {
            echo "<p class='text-danger'>Ausnahme beim Verarbeiten des Moduls: " . $e->getMessage() . "</p>";
            echo "<p>Stack Trace:</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        echo '</div>';
        
        // Aufräumen
        if (isset($tempDir) && is_dir($tempDir)) {
            $objects = scandir($tempDir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_file($tempDir . "/" . $object)) {
                        unlink($tempDir . "/" . $object);
                    }
                }
            }
            rmdir($tempDir);
            echo "<p>Temporäres Verzeichnis aufgeräumt</p>";
        }
        
        if (isset($zip) && $zip instanceof ZipArchive) {
            $zip->close();
        }
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">';
        echo "<strong>Fehler:</strong> " . $e->getMessage();
        echo "<p>Stack Trace:</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        echo '</div>';
    }
}

// HTML-Footer ausgeben
echo '
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';

