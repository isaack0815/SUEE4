<?php
// Output Buffering starten
ob_start();

// Fehlerberichterstattung aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfiguration laden
require_once 'config/config.php';
require 'vendor/autoload.php';

echo "<h1>Überprüfung der Template-Verzeichnisse</h1>";

// Smarty initialisieren
use Smarty\Smarty;
$smarty = new Smarty();

// Absolute Pfade für Smarty-Verzeichnisse
$rootPath = dirname(__FILE__) . '/';
$templateDir = $rootPath . TEMPLATE_DIR;
$compileDir = $rootPath . COMPILE_DIR;
$cacheDir = $rootPath . CACHE_DIR;
$configDir = $rootPath . CONFIG_DIR;

$smarty->setTemplateDir($templateDir);
$smarty->setCompileDir($compileDir);
$smarty->setCacheDir($cacheDir);
$smarty->setConfigDir($configDir);

// Verzeichnisse überprüfen
echo "<h2>Verzeichnisse</h2>";
echo "<ul>";

$directories = [
    'Template-Verzeichnis' => $templateDir,
    'Compile-Verzeichnis' => $compileDir,
    'Cache-Verzeichnis' => $cacheDir,
    'Config-Verzeichnis' => $configDir,
    'Admin-Template-Verzeichnis' => $templateDir . 'admin/'
];

foreach ($directories as $name => $dir) {
    echo "<li>$name: ";
    if (is_dir($dir)) {
        echo "<span style='color:green'>$dir existiert ✓</span>";
        
        // Berechtigungen überprüfen
        if (is_writable($dir)) {
            echo " <span style='color:green'>(beschreibbar ✓)</span>";
        } else {
            echo " <span style='color:red'>(nicht beschreibbar ✗)</span>";
            
            // Versuchen, die Berechtigungen zu ändern
            if (chmod($dir, 0755)) {
                echo " <span style='color:green'>Berechtigungen wurden geändert ✓</span>";
            } else {
                echo " <span style='color:red'>Konnte Berechtigungen nicht ändern ✗</span>";
            }
        }
    } else {
        echo "<span style='color:red'>$dir existiert nicht ✗</span>";
        
        // Versuchen, das Verzeichnis zu erstellen
        if (mkdir($dir, 0755, true)) {
            echo " <span style='color:green'>Verzeichnis wurde erstellt ✓</span>";
        } else {
            echo " <span style='color:red'>Konnte Verzeichnis nicht erstellen ✗</span>";
        }
    }
    echo "</li>";
}

echo "</ul>";

// Template-Dateien überprüfen
echo "<h2>Template-Dateien</h2>";
echo "<ul>";

$templateFiles = [
    'admin/index.tpl' => $templateDir . 'admin/index.tpl',
    'admin/header.tpl' => $templateDir . 'admin/header.tpl',
    'admin/footer.tpl' => $templateDir . 'admin/footer.tpl',
    'admin/groups.tpl' => $templateDir . 'admin/groups.tpl'
];

foreach ($templateFiles as $name => $file) {
    echo "<li>$name: ";
    if (file_exists($file)) {
        echo "<span style='color:green'>$file existiert ✓</span>";
    } else {
        echo "<span style='color:red'>$file existiert nicht ✗</span>";
        
        // Verzeichnis erstellen, falls es nicht existiert
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo " <span style='color:green'>Verzeichnis wurde erstellt ✓</span>";
            } else {
                echo " <span style='color:red'>Konnte Verzeichnis nicht erstellen ✗</span>";
            }
        }
        
        // Einfache Template-Datei erstellen
        $content = "<!-- Einfaches Template für $name -->\n";
        if ($name === 'admin/index.tpl') {
            $content = <<<EOT
{include file="admin/header.tpl" title="Admin Dashboard"}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Admin Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5>Willkommen im Administrationsbereich</h5>
                    <p>Hier können Sie Benutzer, Gruppen und Einstellungen verwalten.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}
EOT;
        } else if ($name === 'admin/header.tpl') {
            $content = <<<EOT
<!DOCTYPE html>
<html lang="{$currentLang}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title|default:"Admin-Bereich"} - Login-System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Admin-Bereich</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Zurück zur Website</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="bi bi-house-door"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="bi bi-people"></i> Benutzer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php">
                                <i class="bi bi-people-fill"></i> Gruppen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="bi bi-gear"></i> Einstellungen
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Hauptinhalt -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
EOT;
        } else if ($name === 'admin/footer.tpl') {
            $content = <<<EOT
            </main>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle mit Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (für AJAX-Anfragen) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Admin JS -->
    <script src="../js/admin.js"></script>
</body>
</html>
EOT;
        }
        
        if (file_put_contents($file, $content)) {
            echo " <span style='color:green'>Einfache Template-Datei wurde erstellt ✓</span>";
        } else {
            echo " <span style='color:red'>Konnte Template-Datei nicht erstellen ✗</span>";
        }
    }
    echo "</li>";
}

echo "</ul>";

// Smarty-Test
echo "<h2>Smarty-Test</h2>";

try {
    $smarty->assign('test_var', 'Smarty funktioniert!');
    $testTemplate = <<<EOT
<!DOCTYPE html>
<html>
<head>
    <title>Smarty-Test</title>
</head>
<body>
    <h1>{$test_var}</h1>
</body>
</html>
EOT;
    
    $testFile = $templateDir . 'test.tpl';
    file_put_contents($testFile, $testTemplate);
    
    echo "<p>Test-Template erstellt: $testFile</p>";
    
    // Template kompilieren
    $smarty->display('test.tpl');
    
    echo "<p style='color:green'>Smarty-Test erfolgreich! ✓</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>Smarty-Test fehlgeschlagen: " . $e->getMessage() . " ✗</p>";
}

// Output Buffer leeren und beenden
ob_end_flush();
?>

