<?php
// Fehlerberichterstattung aktivieren (in Produktion deaktivieren)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session starten - MUSS vor jeglicher Ausgabe geschehen
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Output Buffering starten - fängt alle Ausgaben ab, bis ob_end_flush() aufgerufen wird
ob_start();

// Konfiguration laden
require_once 'config/config.php';
require_once 'classes/Database.php';
require 'vendor/autoload.php';

// Autoloader für Klassen - Verbesserte Version mit Debugging
spl_autoload_register(function($class) {
  $file = 'classes/' . $class . '.php';
  
  // Debug-Ausgabe
  error_log("Versuche Klasse zu laden: $class, Datei: $file");
  
  if (file_exists($file)) {
      require_once $file;
      error_log("Klasse $class erfolgreich geladen");
      return true;
  } else {
      error_log("Klasse $class nicht gefunden in $file");
      return false;
  }
});

// Manuelles Laden der Kernklassen, falls der Autoloader fehlschlägt
$coreClasses = ['Database', 'User', 'Language', 'Group', 'Menu'];
foreach ($coreClasses as $class) {
  $file = 'classes/' . $class . '.php';
  if (file_exists($file) && !class_exists($class)) {
      require_once $file;
  }
}

// Smarty initialisieren
use Smarty\Smarty;
$smarty = new Smarty();

// Absolute Pfade für Smarty-Verzeichnisse
$rootPath = dirname(__FILE__) . '/';
$smarty->setTemplateDir($rootPath . TEMPLATE_DIR);
$smarty->setCompileDir($rootPath . COMPILE_DIR);
$smarty->setCacheDir($rootPath . CACHE_DIR);
$smarty->setConfigDir($rootPath . CONFIG_DIR);

// Debug-Ausgabe für Smarty-Verzeichnisse
error_log("Smarty Template-Verzeichnis: " . $rootPath . TEMPLATE_DIR);
error_log("Smarty Compile-Verzeichnis: " . $rootPath . COMPILE_DIR);
error_log("Smarty Cache-Verzeichnis: " . $rootPath . CACHE_DIR);
error_log("Smarty Config-Verzeichnis: " . $rootPath . CONFIG_DIR);

// Sprachinstanz erstellen
$lang = Language::getInstance();

// Benutzerinstanz erstellen
$user = new User();

// Globale Smarty-Variablen setzen
$smarty->assign('currentLang', $lang->getCurrentLanguage());
$smarty->assign('availableLanguages', $lang->getAvailableLanguages());
$smarty->assign('isLoggedIn', $user->isLoggedIn());
if ($user->isLoggedIn()) {
  $smarty->assign('currentUser', $user->getCurrentUser());
}

// Übersetzungsfunktion für Smarty
$smarty->registerPlugin('function', 'translate', function($params, $smarty) {
  $lang = Language::getInstance();
  $key = isset($params['key']) ? $params['key'] : '';
  unset($params['key']);
  return $lang->translate($key, $params);
});