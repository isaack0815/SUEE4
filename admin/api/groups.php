<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!checkPermission($user, 'group.view')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

// Gruppenverwaltung initialisieren
$group = new Group();
$permission = new Permission();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
    case 'GET':
        // Alle Gruppen abrufen oder eine bestimmte Gruppe
        if (isset($_GET['id'])) {
            $groupItem = $group->getGroupById($_GET['id']);
            if ($groupItem) {
                $response = ['success' => true, 'data' => $groupItem];
            } else {
                $response = ['success' => false, 'message' => 'group_not_found'];
            }
        } else if (isset($_GET['permissions']) && isset($_GET['group_id'])) {
            // Berechtigungen einer Gruppe abrufen
            $groupPermissions = $permission->getGroupPermissions($_GET['group_id']);
            $response = ['success' => true, 'data' => $groupPermissions];
        } else {
            // Alle Gruppen abrufen
            $groupItems = $group->getAllGroups();
            $response = ['success' => true, 'data' => $groupItems];
        }
        break;
        
    case 'POST':
        // Neue Gruppe erstellen
        if (!checkPermission($user, 'group.create')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty($data['name'])) {
            $response = ['success' => false, 'message' => 'name_required'];
            break;
        }
        
        $result = $group->createGroup($data['name'], $data['description'] ?? '');
        $response = $result;
        break;
        
    case 'PUT':
        // Gruppe aktualisieren
        if (!checkPermission($user, 'group.edit')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        if (!isset($data['name']) || empty($data['name'])) {
            $response = ['success' => false, 'message' => 'name_required'];
            break;
        }
        
        $result = $group->updateGroup($data['id'], $data['name'], $data['description'] ?? '');
        $response = $result;
        break;
        
    case 'DELETE':
        // Gruppe löschen
        if (!checkPermission($user, 'group.delete')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        $result = $group->deleteGroup($data['id']);
        $response = $result;
        break;
        
    case 'PATCH':
        // Berechtigungen einer Gruppe zuweisen
        if (!checkPermission($user, 'group.edit')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['group_id']) || empty($data['group_id'])) {
            $response = ['success' => false, 'message' => 'group_id_required'];
            break;
        }
        
        if (!isset($data['permission_ids']) || !is_array($data['permission_ids'])) {
            $response = ['success' => false, 'message' => 'permission_ids_required'];
            break;
        }
        
        $result = $permission->assignGroupPermissions($data['group_id'], $data['permission_ids']);
        $response = $result;
        break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
?>

