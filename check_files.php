<?php
// Fehlerberichterstattung aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Dateistruktur-Überprüfung</h1>";

// Verzeichnisse überprüfen
$directories = [
    'classes',
    'config',
    'templates',
    'templates_c',
    'cache',
    'configs',
    'js',
    'css',
    'admin',
    'admin/api'
];

echo "<h2>Verzeichnisse</h2>";
echo "<ul>";
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        echo "<li style='color:green'>$dir existiert ✓</li>";
    } else {
        echo "<li style='color:red'>$dir existiert nicht ✗</li>";
        // Versuchen, das Verzeichnis zu erstellen
        if (mkdir($dir, 0755, true)) {
            echo " - Verzeichnis wurde erstellt ✓";
        } else {
            echo " - Konnte Verzeichnis nicht erstellen ✗";
        }
    }
}
echo "</ul>";

// Wichtige Dateien überprüfen
$files = [
    'config/config.php',
    'classes/Database.php',
    'classes/User.php',
    'classes/Language.php',
    'classes/Group.php',
    'classes/Menu.php',
    'init.php',
    'index.php',
    'login.php'
];

echo "<h2>Dateien</h2>";
echo "<ul>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li style='color:green'>$file existiert ✓</li>";
    } else {
        echo "<li style='color:red'>$file existiert nicht ✗</li>";
    }
}
echo "</ul>";

// Berechtigungen überprüfen
echo "<h2>Berechtigungen</h2>";
echo "<ul>";
$writable_dirs = [
    'templates_c',
    'cache'
];

foreach ($writable_dirs as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<li style='color:green'>$dir ist beschreibbar ✓</li>";
        } else {
            echo "<li style='color:red'>$dir ist nicht beschreibbar ✗</li>";
            // Versuchen, die Berechtigungen zu ändern
            if (chmod($dir, 0755)) {
                echo " - Berechtigungen wurden geändert ✓";
            } else {
                echo " - Konnte Berechtigungen nicht ändern ✗";
            }
        }
    }
}
echo "</ul>";

// PHP-Informationen
echo "<h2>PHP-Informationen</h2>";
echo "<ul>";
echo "<li>PHP-Version: " . phpversion() . "</li>";
echo "<li>include_path: " . get_include_path() . "</li>";
echo "</ul>";

// Autoloader testen
echo "<h2>Autoloader-Test</h2>";
echo "<ul>";

function testAutoload($className) {
    $file = 'classes/' . $className . '.php';
    echo "<li>Versuche Klasse $className zu laden aus $file: ";
    
    if (file_exists($file)) {
        echo "<span style='color:green'>Datei existiert ✓</span><br>";
        
        // Dateiinhalt überprüfen
        $content = file_get_contents($file);
        if (strpos($content, "class $className") !== false) {
            echo "Klassendeklaration gefunden ✓<br>";
        } else {
            echo "<span style='color:red'>Klassendeklaration nicht gefunden ✗</span><br>";
        }
        
        // Versuchen, die Datei zu laden
        try {
            include_once $file;
            if (class_exists($className)) {
                echo "<span style='color:green'>Klasse erfolgreich geladen ✓</span>";
            } else {
                echo "<span style='color:red'>Klasse konnte nicht geladen werden ✗</span>";
            }
        } catch (Exception $e) {
            echo "<span style='color:red'>Fehler beim Laden: " . $e->getMessage() . " ✗</span>";
        }
    } else {
        echo "<span style='color:red'>Datei existiert nicht ✗</span>";
    }
    
    echo "</li>";
}

$classesToTest = ['Database', 'User', 'Language', 'Group', 'Menu'];
foreach ($classesToTest as $class) {
    testAutoload($class);
}

echo "</ul>";
?>

