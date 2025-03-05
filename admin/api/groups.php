<?php
require_once '../../init.php';

// Prüfen, ob Benutzer eingeloggt ist und Admin-Rechte hat
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

// Gruppenverwaltung initialisieren
$group = new Group();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
    case 'GET':
        // Alle Gruppen abrufen oder eine bestimmte Gruppe
        if (isset($_GET['id'])) {
            $groupData = $group->getGroupById($_GET['id']);
            if ($groupData) {
                $response = ['success' => true, 'data' => $groupData];
            } else {
                $response = ['success' => false, 'message' => 'group_not_found'];
            }
        } else {
            $groups = $group->getAllGroups();
            $response = ['success' => true, 'data' => $groups];
        }
        break;
        
    case 'POST':
        // Neue Gruppe erstellen
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty($data['name'])) {
            $response = ['success' => false, 'message' => 'name_required'];
            break;
        }
        
        $result = $group->createGroup(
            $data['name'],
            isset($data['description']) ? $data['description'] : ''
        );
        
        $response = $result;
        break;
        
    case 'PUT':
        // Gruppe aktualisieren
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        if (!isset($data['name']) || empty($data['name'])) {
            $response = ['success' => false, 'message' => 'name_required'];
            break;
        }
        
        $result = $group->updateGroup(
            $data['id'],
            $data['name'],
            isset($data['description']) ? $data['description'] : ''
        );
        
        $response = $result;
        break;
        
    case 'DELETE':
        // Gruppe löschen
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        $result = $group->deleteGroup($data['id']);
        $response = $result;
        break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
?>

