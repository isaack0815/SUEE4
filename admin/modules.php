<?php
require_once '../init.php';
require_once '../includes/auth_check.php';
require_once '../classes/ModulManager.php';

// ModuleManager-Instanz erstellen
$moduleManager = new ModuleManager();

// Meldung initialisieren
$message = '';
$messageType = '';

// Modul hochladen und installieren
if (isset($_POST['upload']) && isset($_FILES['module'])) {
    $uploadResult = $moduleManager->uploadModule($_FILES['module']);
    
    if ($uploadResult['success']) {
        $installResult = $moduleManager->installModule($uploadResult);
        $message = $installResult['message'];
        $messageType = $installResult['success'] ? 'success' : 'danger';
    } else {
        $message = $uploadResult['message'];
        $messageType = 'danger';
    }
}

// Modul deinstallieren
if (isset($_POST['uninstall']) && isset($_POST['module_id'])) {
    $uninstallResult = $moduleManager->uninstallModule($_POST['module_id']);
    $message = $uninstallResult['message'];
    $messageType = $uninstallResult['success'] ? 'success' : 'danger';
}

// Modul aktivieren/deaktivieren
if (isset($_POST['toggle_active']) && isset($_POST['module_id'])) {
    $active = isset($_POST['active']) && $_POST['active'] == 1;
    $toggleResult = $moduleManager->toggleModuleActive($_POST['module_id'], $active);
    $message = $toggleResult['message'];
    $messageType = $toggleResult['success'] ? 'success' : 'danger';
}

// Alle Module abrufen
$modules = $moduleManager->getAllModules();

// Variablen an Smarty übergeben
$smarty->assign('modules', $modules);
$smarty->assign('message', $message);
$smarty->assign('messageType', $messageType);

// Seite anzeigen
$smarty->display('admin/modules.tpl');
?>

