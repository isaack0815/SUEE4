<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Überprüfen, ob der Benutzer Admin-Rechte hat
if (!$user->hasPermission('admin_access')) {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance();

// Sprache laden
$lang = Language::getInstance();
$translations = $lang->getTranslations();

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Autoinclude für Einstellungen
$settingsFiles = glob('../includes/settings/*.php');
$settings = [];
foreach ($settingsFiles as $file) {
    $settingName = basename($file, '.php');
    $settingData = include $file;
    $settings[$settingName] = $settingData;
    
    // Laden der Einstellungen, wenn eine load-Funktion definiert ist
    if (isset($settingData['load']) && is_callable($settingData['load'])) {
        $settings[$settingName]['values'] = $settingData['load']();
    }
}

// Verarbeitung des Formulars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($settings as $settingName => $settingData) {
        if (isset($settingData['save']) && is_callable($settingData['save'])) {
            $settingData['save']($_POST);
        }
    }

    // Erfolgsmeldung setzen
    $_SESSION['success_message'] = $translations['settings_updated_successfully'];
    header('Location: settings.php');
    exit;
}

// Erfolgsmeldung abrufen und löschen
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);

// Template-Variablen
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'settings');
$smarty->assign('settings', $settings);
$smarty->assign('success_message', $success_message);
$smarty->assign('translations', $translations);

// Template anzeigen
$smarty->display('admin/settings.tpl');

