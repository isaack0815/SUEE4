<?php
// Fehlerberichterstattung aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>BOM-Überprüfung</h1>";

/**
 * Überprüft, ob eine Datei ein BOM enthält und entfernt es
 *
 * @param string $file Pfad zur Datei
 * @return bool True, wenn ein BOM entfernt wurde, sonst False
 */
function check_and_remove_bom($file) {
    // Datei öffnen
    $content = file_get_contents($file);
    
    // Auf BOM prüfen (UTF-8 BOM: EF BB BF)
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        echo "<p>BOM gefunden in: $file</p>";
        
        // BOM entfernen
        $content = substr($content, 3);
        file_put_contents($file, $content);
        
        echo "<p>BOM entfernt aus: $file</p>";
        return true;
    }
    
    return false;
}

/**
 * Rekursiv alle PHP-Dateien in einem Verzeichnis durchsuchen
 *
 * @param string $dir Verzeichnis
 * @return void
 */
function check_directory($dir) {
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            check_directory($path);
        } else if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            check_and_remove_bom($path);
        }
    }
}

// Hauptverzeichnis überprüfen
check_directory('.');

echo "<h2>BOM-Überprüfung abgeschlossen</h2>";
?>