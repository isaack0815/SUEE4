<?php
require_once '../init.php';
require_once '../includes/auth_check.php';
require_once '../classes/ModuleManager.php'; // Korrigiert: "Module" statt "Modul"
require_once '../classes/Logger.php';

$moduleManager = new ModuleManager();
$logger = Logger::getInstance();

// Überprüfen, ob der Benutzer angemeldet ist
if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
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

// Aktuelle Aktion bestimmen
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Meldungen initialisieren
$message = '';
$error = '';

// Aktionen verarbeiten
switch ($action) {
    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['module_file']) && $_FILES['module_file']['error'] === UPLOAD_ERR_OK) {
                // Modultyp aus dem Formular abrufen
                $type = isset($_POST['module_type']) ? $_POST['module_type'] : ModuleManager::TYPE_DASHBOARD;
                
                // Modul hochladen und in der Datenbank registrieren
                $uploadResult = $moduleManager->uploadModule($_FILES['module_file'], $type);
                
                if ($uploadResult['success']) {
                    $message = 'Modul erfolgreich hochgeladen. Sie können es jetzt installieren.';
                    $logger->info("Modul erfolgreich hochgeladen", "admin", [
                        'name' => $uploadResult['details']['name'],
                        'type' => $type
                    ]);
                } else {
                    $error = 'Fehler beim Hochladen des Moduls: ' . $uploadResult['message'];
                    $logger->error("Fehler beim Hochladen des Moduls", "admin", [
                        'error' => $uploadResult['message']
                    ]);
                }
            } else {
                $uploadError = $_FILES['module_file']['error'] ?? 'Unbekannter Fehler';
                $error = 'Fehler beim Hochladen der Datei: ' . getUploadErrorMessage($uploadError);
                $logger->error("Fehler beim Hochladen der Datei", "admin", [
                    'error_code' => $uploadError
                ]);
            }
        }
        // Nach dem Upload zur Liste weiterleiten
        header('Location: module-manager.php?message=' . urlencode($message) . '&error=' . urlencode($error));
        exit;
        break;

    case 'install':
        if (isset($_GET['id']) && isset($_GET['type'])) {
            define('INSTALL_SCRIPT', true);
            $moduleId = $_GET['id'];
            $type = $_GET['type'];
            
            $installResult = $moduleManager->installModule($moduleId, $type);
            
            if ($installResult['success']) {
                $message = 'Modul erfolgreich installiert.';
                $logger->info("Modul erfolgreich installiert", "admin", [
                    'id' => $moduleId,
                    'type' => $type
                ]);
                
                // Installationsdetails für das Modal speichern
                $_SESSION['installation_details'] = $installResult['details'] ?? null;
                
                // Weiterleitung mit Parameter für das Modal
                header('Location: module-manager.php?message=' . urlencode($message) . '&show_details=1');
                exit;
            } else {
                $error = 'Fehler bei der Installation des Moduls: ' . $installResult['message'];
                $logger->error("Fehler bei der Installation des Moduls", "admin", [
                    'id' => $moduleId,
                    'type' => $type,
                    'error' => $installResult['message']
                ]);
                
                // Installationsdetails für das Modal speichern
                $_SESSION['installation_details'] = $installResult['details'] ?? null;
                
                // Weiterleitung mit Parameter für das Modal
                header('Location: module-manager.php?error=' . urlencode($error) . '&show_details=1');
                exit;
            }
        } else {
            $error = 'Fehlende Parameter für die Installation.';
            header('Location: module-manager.php?error=' . urlencode($error));
            exit;
        }
        break;

    case 'uninstall':
        if (isset($_GET['id']) && isset($_GET['type'])) {
            $moduleId = $_GET['id'];
            $type = $_GET['type'];
            
            $uninstallResult = $moduleManager->uninstallModule($moduleId, $type);
            
            if ($uninstallResult['success']) {
                $message = 'Modul erfolgreich deinstalliert.';
                $logger->info("Modul erfolgreich deinstalliert", "admin", [
                    'id' => $moduleId,
                    'type' => $type
                ]);
            } else {
                $error = 'Fehler bei der Deinstallation des Moduls: ' . $uninstallResult['message'];
                $logger->error("Fehler bei der Deinstallation des Moduls", "admin", [
                    'id' => $moduleId,
                    'type' => $type,
                    'error' => $uninstallResult['message']
                ]);
            }
        } else {
            $error = 'Fehlende Parameter für die Deinstallation.';
        }
        header('Location: module-manager.php?message=' . urlencode($message) . '&error=' . urlencode($error));
        exit;
        break;

    case 'delete':
        if (isset($_GET['id']) && isset($_GET['type'])) {
            $moduleId = $_GET['id'];
            $type = $_GET['type'];
            
            $deleteResult = $moduleManager->deleteModule($moduleId, $type);
            
            if ($deleteResult['success']) {
                $message = 'Modul erfolgreich gelöscht.';
                $logger->info("Modul erfolgreich gelöscht", "admin", [
                    'id' => $moduleId,
                    'type' => $type
                ]);
            } else {
                $error = 'Fehler beim Löschen des Moduls: ' . $deleteResult['message'];
                $logger->error("Fehler beim Löschen des Moduls", "admin", [
                    'id' => $moduleId,
                    'type' => $type,
                    'error' => $deleteResult['message']
                ]);
            }
        } else {
            $error = 'Fehlende Parameter für das Löschen.';
        }
        header('Location: module-manager.php?message=' . urlencode($message) . '&error=' . urlencode($error));
        exit;
        break;

    case 'toggle':
        if (isset($_GET['id']) && isset($_GET['type']) && isset($_GET['active'])) {
            $moduleId = $_GET['id'];
            $type = $_GET['type'];
            $active = $_GET['active'] == '1';
            
            $toggleResult = $moduleManager->toggleModuleActive($moduleId, $active, $type);
            
            if ($toggleResult['success']) {
                $message = $active ? 'Modul erfolgreich aktiviert.' : 'Modul erfolgreich deaktiviert.';
                $logger->info($message, "admin", [
                    'id' => $moduleId,
                    'type' => $type
                ]);
            } else {
                $error = 'Fehler beim ' . ($active ? 'Aktivieren' : 'Deaktivieren') . ' des Moduls: ' . $toggleResult['message'];
                $logger->error($error, "admin", [
                    'id' => $moduleId,
                    'type' => $type,
                    'error' => $toggleResult['message']
                ]);
            }
        } else {
            $error = 'Fehlende Parameter für die Aktivierung/Deaktivierung.';
        }
        header('Location: module-manager.php?message=' . urlencode($message) . '&error=' . urlencode($error));
        exit;
        break;

    case 'list':
    default:
        // Module nach Typ filtern
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        
        // Dashboard-Module abrufen
        $dashboardModules = $moduleManager->getAllModules(ModuleManager::TYPE_DASHBOARD);
        $smarty->assign('dashboard_modules', $dashboardModules);
        
        // System-Module abrufen
        $systemModules = $moduleManager->getAllModules(ModuleManager::TYPE_SYSTEM);
        $smarty->assign('system_modules', $systemModules);
        
        // Aktuellen Typ setzen
        $smarty->assign('current_type', $type);
        
        // Installationsdetails aus der Session abrufen und an Smarty übergeben
        if (isset($_SESSION['installation_details'])) {
            $smarty->assign('installation_details', $_SESSION['installation_details']);
            unset($_SESSION['installation_details']); // Details aus der Session entfernen
        }
        
        // Modal anzeigen, wenn Parameter vorhanden
        $smarty->assign('show_details_modal', isset($_GET['show_details']));
        break;
}

// Nachrichten aus URL-Parametern
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('message', $message);
$smarty->assign('error', $error);

// Template anzeigen
$smarty->display('admin/module-manager.tpl');

// Hilfsfunktion für Upload-Fehlermeldungen
function getUploadErrorMessage($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return 'Die hochgeladene Datei überschreitet die in der php.ini festgelegte upload_max_filesize Direktive.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'Die hochgeladene Datei überschreitet die im HTML-Formular festgelegte MAX_FILE_SIZE Direktive.';
        case UPLOAD_ERR_PARTIAL:
            return 'Die hochgeladene Datei wurde nur teilweise hochgeladen.';
        case UPLOAD_ERR_NO_FILE:
            return 'Es wurde keine Datei hochgeladen.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Temporärer Ordner fehlt.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Fehler beim Schreiben der Datei auf die Festplatte.';
        case UPLOAD_ERR_EXTENSION:
            return 'Eine PHP-Erweiterung hat den Upload der Datei gestoppt.';
        default:
            return 'Unbekannter Upload-Fehler.';
    }
}

