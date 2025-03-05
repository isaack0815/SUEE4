<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!checkPermission($user, 'permission.view')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

// Berechtigungsverwaltung initialisieren
$permission = new Permission();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
    case 'GET':
        // Alle Berechtigungen abrufen oder eine bestimmte Berechtigung
        if (isset($_GET['id'])) {
            $permissionItem = $permission->getPermissionById($_GET['id']);
            if ($permissionItem) {
                $response = ['success' => true, 'data' => $permissionItem];
            } else {
                $response = ['success' => false, 'message' => 'permission_not_found'];
            }
        } else {
            // Alle Berechtigungen abrufen
            $permissionItems = $permission->getAllPermissions();
            $response = ['success' => true, 'data' => $permissionItems];
        }
        break;
        
    case 'POST':
        // Neue Berechtigung erstellen
        if (!checkPermission($user, 'permission.create')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty($data['name'])) {
            $response = ['success' => false, 'message' => 'name_required'];
            break;
        }
        
        $result = $permission->createPermission($data['name'], $data['description'] ?? '');
        $response = $result;
        break;
        
    case 'PUT':
        // Berechtigung aktualisieren
        if (!checkPermission($user, 'permission.edit')) {
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
        
        $result = $permission->updatePermission($data['id'], $data['name'], $data['description'] ?? '');
        $response = $result;
        break;
        
    case 'DELETE':
        // Berechtigung löschen
        if (!checkPermission($user, 'permission.delete')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        $result = $permission->deletePermission($data['id']);
        $response = $result;
        break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);