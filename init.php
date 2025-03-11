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

// Relativen Pfad zum Hauptverzeichnis bestimmen
$rootPath = dirname(__FILE__).'/';

// Direktes Einbinden der Klassen, falls der Autoloader fehlschlägt
$classFiles = [
    $rootPath . 'classes/Database.php',
    $rootPath . 'classes/Language.php',
    $rootPath . 'classes/User.php',
    $rootPath . 'classes/Group.php',
    $rootPath . 'classes/Menu.php',
    $rootPath . 'classes/Permission.php',
    $rootPath . 'classes/UserPreferences.php',
    $rootPath . 'classes/Dashboard.php',
    $rootPath . 'classes/Theme.php',
    $rootPath . 'classes/Logger.php'
  ];

foreach ($classFiles as $file) {
  if (file_exists($file)) {
      require_once $file;
  } else {
      die("Fehler: Datei $file nicht gefunden.");
  }
}

// Konfiguration laden
require_once 'config/config.php';
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

// Theme setzen
$theme = new Theme();
$currentTheme = $theme->getCurrentTheme();
$smarty->assign('currentTheme', $currentTheme);

// Debug-Ausgabe für Smarty-Verzeichnisse
error_log("Smarty Template-Verzeichnis: " . $rootPath . TEMPLATE_DIR);
error_log("Smarty Compile-Verzeichnis: " . $rootPath . COMPILE_DIR);
error_log("Smarty Cache-Verzeichnis: " . $rootPath . CACHE_DIR);
error_log("Smarty Config-Verzeichnis: " . $rootPath . CONFIG_DIR);

// Sprachinstanz erstellen
$lang = Language::getInstance();

// Benutzerinstanz erstellen
$user = new User();

// Menüinstanz erstellen
$menu = new Menu();

// Menüs laden
$mainMenu = $menu->getMenuItems('main');
$userMenu = $menu->getMenuItems('user');

// Aktiven Menüpunkt ermitteln
$currentUrl = basename($_SERVER['PHP_SELF']);
$activeMenuItem = $menu->getActiveMenuItem($mainMenu, $currentUrl);
// Aktive Menüpunkte und deren Eltern markieren
$activeMenuIds = [];
if (!empty($activeMenuItem) && isset($activeMenuItem['item'])) {
  $activeMenuIds[] = $activeMenuItem['item']['id'];
  if (isset($activeMenuItem['parents']) && is_array($activeMenuItem['parents'])) {
    foreach ($activeMenuItem['parents'] as $parent) {
      if (isset($parent['id'])) {
        $activeMenuIds[] = $parent['id'];
      }
    }
  }
}
$smarty->assign('activeMenuIds', $activeMenuIds);

// Allgemeine Einstellungen laden
$db = Database::getInstance();
$generalSettings = [];
$result = $db->select("SELECT setting_key, setting_value FROM settings WHERE category = 'general'");
foreach ($result as $row) {
    $generalSettings[$row['setting_key']] = $row['setting_value'];
}

// Metadaten laden
$metadata = [];
$result = $db->select("SELECT `meta_key`, `meta_value` FROM metadata");
foreach ($result as $row) {
    $metadata[$row['meta_key']] = $row['meta_value'];
}

// Globale Smarty-Variablen setzen
$smarty->assign('currentLang', $lang->getCurrentLanguage());
$smarty->assign('availableLanguages', $lang->getAvailableLanguages());
$smarty->assign('isLoggedIn', $user->isLoggedIn());
if ($user->isLoggedIn()) {
  $smarty->assign('currentUser', $user->getCurrentUser());
}
$smarty->assign('user', $user);
$smarty->assign('mainMenu', $mainMenu);
$smarty->assign('userMenu', $userMenu);
$smarty->assign('activeMenuItem', $activeMenuItem);
$smarty->assign('generalSettings', $generalSettings);
$smarty->assign('metadata', $metadata);

// Übersetzungsfunktion für Smarty
$smarty->registerPlugin('function', 'translate', function($params, $smarty) {
  $lang = Language::getInstance();
  $key = isset($params['key']) ? $params['key'] : '';
  unset($params['key']);
  return $lang->translate($key, $params);
});

