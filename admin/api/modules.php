<?php
require_once '../../init.php';

// Check if user is logged in and has admin permissions
if (!$user->isLoggedIn() || !$user->hasPermission('admin_access')) {
    echo json_encode(['success' => false, 'message' => $lang->get('common', 'access_denied')]);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'list') {
        $modules = $moduleManager->getAllModules();
        echo json_encode($modules);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'get' && isset($_GET['id'])) {
        $module = $moduleManager->getModule($_GET['id']);
        echo json_encode($module);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'upload') {
        if (isset($_FILES['module']) && $_FILES['module']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../upload/modules/';
            $tempFile = $_FILES['module']['tmp_name'];
            $originalName = $_FILES['module']['name'];
            
            // Generiere einen eindeutigen Dateinamen
            $uniqueName = uniqid('module_') . '.zip';
            $uploadFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($tempFile, $uploadFile)) {
                $zip = new ZipArchive;
                if ($zip->open($uploadFile) === TRUE) {
                    $infoFile = $zip->getFromName('info.php');
                    if ($infoFile !== false) {
                        $moduleInfo = eval('?>' . $infoFile);
                        if (is_array($moduleInfo)) {
                            // Füge das Modul zur Datenbank hinzu
                            $stmt = $pdo->prepare("INSERT INTO system_modules (name, description, version, file_path, status) VALUES (?, ?, ?, ?, 'inactive')");
                            $stmt->execute([$moduleInfo['name'], $moduleInfo['description'], $moduleInfo['version'], $uniqueName]);
                            
                            echo json_encode(['success' => true, 'message' => 'Modul erfolgreich hochgeladen.']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Ungültige info.php Datei.']);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'info.php nicht gefunden in der ZIP-Datei.']);
                    }
                    $zip->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Fehler beim Öffnen der ZIP-Datei.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Fehler beim Hochladen der Datei.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Keine Datei hochgeladen oder Fehler beim Upload.']);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'install' && isset($_POST['id'])) {
        $result = $moduleManager->installModule($_POST['id']);
        echo json_encode($result);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'uninstall' && isset($_POST['id'])) {
        $result = $moduleManager->uninstallModule($_POST['id']);
        echo json_encode($result);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'activate' && isset($_POST['id'])) {
        $result = $moduleManager->activateModule($_POST['id']);
        echo json_encode($result);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'deactivate' && isset($_POST['id'])) {
        $result = $moduleManager->deactivateModule($_POST['id']);
        echo json_encode($result);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteVars);
    if (isset($deleteVars['id'])) {
        $result = $moduleManager->deleteModule($deleteVars['id']);
        echo json_encode($result);
    }
}

