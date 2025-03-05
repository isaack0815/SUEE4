<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!checkPermission($user, 'menu.view')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

// Menüverwaltung initialisieren
$menu = new Menu();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
    case 'GET':
        // Alle Menüpunkte abrufen oder einen bestimmten Menüpunkt
        if (isset($_GET['id'])) {
            $menuItem = $menu->getMenuItem($_GET['id']);
            if ($menuItem) {
                $response = ['success' => true, 'data' => $menuItem];
            } else {
                $response = ['success' => false, 'message' => 'menu_item_not_found'];
            }
        } else if (isset($_GET['area'])) {
            // Menüpunkte eines bestimmten Bereichs abrufen
            $menuItems = $menu->getAllMenuItems($_GET['area']);
            $response = ['success' => true, 'data' => $menuItems];
        } else if (isset($_GET['parent_options']) && isset($_GET['area'])) {
            // Übergeordnete Menüpunkte für einen bestimmten Bereich abrufen
            $parentItems = $menu->getParentMenuItems($_GET['area']);
            $response = ['success' => true, 'data' => $parentItems];
        } else {
            // Alle Menüpunkte abrufen
            $menuItems = $menu->getAllMenuItems();
            $response = ['success' => true, 'data' => $menuItems];
        }
        break;
        
    case 'POST':
        // Neuen Menüpunkt erstellen
        if (!checkPermission($user, 'menu.create')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['name']) || empty($data['name'])) {
            $response = ['success' => false, 'message' => 'name_required'];
            break;
        }
        
        if (!isset($data['url'])) {
            $response = ['success' => false, 'message' => 'url_required'];
            break;
        }
        
        $result = $menu->createMenuItem($data);
        $response = $result;
        break;
        
    case 'PUT':
        // Menüpunkt aktualisieren
        if (!checkPermission($user, 'menu.edit')) {
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
        
        if (!isset($data['url'])) {
            $response = ['success' => false, 'message' => 'url_required'];
            break;
        }
        
        $result = $menu->updateMenuItem($data['id'], $data);
        $response = $result;
        break;
        
    case 'DELETE':
        // Menüpunkt löschen
        if (!checkPermission($user, 'menu.delete')) {
            $response = ['success' => false, 'message' => 'no_permission'];
            break;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || empty($data['id'])) {
            $response = ['success' => false, 'message' => 'id_required'];
            break;
        }
        
        $result = $menu->deleteMenuItem($data['id']);
        $response = $result;
        break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
?>