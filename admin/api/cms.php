<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';
require_once '../../classes/CMS.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!checkPermission($user, 'cms.view')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

// CMS-Verwaltung initialisieren
$cms = new CMS();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
    case 'GET':
        // Alle Seiten abrufen oder eine bestimmte Seite
        if (isset($_GET['id'])) {
            $page = $cms->getPageById($_GET['id']);
            if ($page) {
                $response = ['success' => true, 'data' => $page];
            } else {
                $response = ['success' => false, 'message' => 'page_not_found'];
            }
        } else {
            // Paginierungsparameter
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $offset = ($page - 1) * $limit;
            
            // Status-Filter
            $status = isset($_GET['status']) ? $_GET['status'] : null;
            
            // Seiten abrufen
            $pages = $cms->getAllPages($status, $limit, $offset);
            $total = $cms->countPages($status);
            
            $response = [
                'success' => true,
                'data' => $pages,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ];
        }
        break;
        
    case 'POST':
        // Neue Seite erstellen
        if (!checkPermission($user, 'cms.create')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['title']) || empty($data['title'])) {
            $response = ['success' => false, 'message' => 'title_required'];
            break;
        }
        
        $result = $cms->createPage($data, $user->getCurrentUser()['id']);
        $response = $result;
        break;
        
    case 'PUT':
        // Seite aktualisieren
        if (!checkPermission($user, 'cms.edit')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        if (!isset($data['title']) || empty($data['title'])) {
            $response = ['success' => false, 'message' => 'title_required'];
            break;
        }
        
        $result = $cms->updatePage($data['id'], $data, $user->getCurrentUser()['id']);
        $response = $result;
        break;
        
    case 'DELETE':
        // Seite löschen
        if (!checkPermission($user, 'cms.delete')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        $result = $cms->deletePage($data['id']);
        $response = $result;
        break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
?>

