<?php
require_once '../init.php';
require_once '../includes/auth_check.php';
require_once '../classes/ModulManager.php';

// Überprüfen, ob der Benutzer angemeldet ist
if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Überprüfen, ob der Benutzer Admin-Rechte hat
if (!$user->hasPermission('admin_access')) {
    header('Location: ../index.php');
    exit;
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// ModuleManager-Instanz erstellen
$moduleManager = new ModuleManager();

// Standardmäßig Dashboard-Module anzeigen
$moduleType = isset($_GET['type']) ? $_GET['type'] : ModuleManager::TYPE_DASHBOARD;

// Aktion verarbeiten
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = ['success' => false, 'message' => 'Unbekannte Aktion'];
    
    switch ($action) {
        case 'upload':
            if (isset($_FILES['module']) && $_FILES['module']['error'] === UPLOAD_ERR_OK) {
                $uploadType = isset($_POST['module_type']) ? $_POST['module_type'] : ModuleManager::TYPE_DASHBOARD;
                $uploadResult = $moduleManager->uploadModule($_FILES['module'], $uploadType);
                
                if ($uploadResult['success']) {
                    // Modul-Details in der Session speichern
                    $_SESSION['module_upload'] = $uploadResult;
                    $response = ['success' => true, 'message' => 'Modul erfolgreich hochgeladen. Bitte bestätigen Sie die Installation.'];
                } else {
                    $response = $uploadResult;
                }
            } else {
                $response = ['success' => false, 'message' => 'Keine Datei hochgeladen oder Fehler beim Upload.'];
            }
            break;
            
        case 'install':
            if (isset($_SESSION['module_upload']) && $_SESSION['module_upload']['success']) {
                $installResult = $moduleManager->installModule($_SESSION['module_upload']);
                unset($_SESSION['module_upload']);
                
                $response = $installResult;
            } else {
                $response = ['success' => false, 'message' => 'Keine Modul-Informationen gefunden.'];
            }
            break;
            
        case 'uninstall':
            if (isset($_POST['module_id'])) {
                $moduleId = (int)$_POST['module_id'];
                $uninstallType = isset($_POST['module_type']) ? $_POST['module_type'] : ModuleManager::TYPE_DASHBOARD;
                $uninstallResult = $moduleManager->uninstallModule($moduleId, $uninstallType);
                $response = $uninstallResult;
            } else {
                $response = ['success' => false, 'message' => 'Keine Modul-ID angegeben.'];
            }
            break;
            
        case 'toggle_active':
            if (isset($_POST['module_id']) && isset($_POST['is_active'])) {
                $moduleId = (int)$_POST['module_id'];
                $isActive = (bool)$_POST['is_active'];
                $toggleType = isset($_POST['module_type']) ? $_POST['module_type'] : ModuleManager::TYPE_DASHBOARD;
                $toggleResult = $moduleManager->toggleModuleActive($moduleId, $isActive, $toggleType);
                $response = $toggleResult;
            } else {
                $response = ['success' => false, 'message' => 'Fehlende Parameter.'];
            }
            break;
    }
    
    // JSON-Antwort senden
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Module aus der Datenbank abrufen
$dashboardModules = $moduleManager->getAllModules(ModuleManager::TYPE_DASHBOARD);
$systemModules = $moduleManager->getAllModules(ModuleManager::TYPE_SYSTEM);

// Smarty-Template anzeigen
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'modules');
$smarty->assign('dashboardModules', $dashboardModules);
$smarty->assign('systemModules', $systemModules);
$smarty->assign('moduleType', $moduleType);
$smarty->assign('pageTitle', 'Modul-Manager');
$smarty->display('admin/module-manager.tpl');
?>

