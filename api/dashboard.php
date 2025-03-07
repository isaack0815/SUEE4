<?php

require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist
if (!$user->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);


if (isset($data['action']) && $data['action'] === 'save_layout') {
    $userId = $user->getCurrentUser()['id'];
    $modules = $data['modules'] ?? [];
    
    // Direkte Speicherung in der Datenbank
    $db = Database::getInstance();
    $success = true;
    $now = date('Y-m-d H:i:s');
    
    foreach ($modules as $moduleId => $moduleData) {
        // Standardwerte setzen
        $position = $moduleData['position'] ?? 0;
        $gridX = $moduleData['grid_x'] ?? 0;
        $gridY = $moduleData['grid_y'] ?? 0;
        $gridWidth = $moduleData['grid_width'] ?? 6;
        $gridHeight = $moduleData['grid_height'] ?? 2;
        $size = $moduleData['size'] ?? 'medium';
        $isVisible = isset($moduleData['is_visible']) ? ($moduleData['is_visible'] ? 1 : 0) : 1;
        
        // Prüfen, ob Einstellungen bereits existieren
        $sql = "SELECT * FROM user_dashboard_settings WHERE user_id = ? AND module_id = ?";
        $existing = $db->selectOne($sql, [$userId, $moduleId]);
        
        if ($existing) {
            // Einstellungen aktualisieren
            $updateSql = "UPDATE user_dashboard_settings SET 
                position = ?, 
                grid_x = ?, 
                grid_y = ?, 
                grid_width = ?, 
                grid_height = ?, 
                size = ?, 
                is_visible = ?, 
                updated_at = ? 
                WHERE user_id = ? AND module_id = ?";
            
            $updateResult = $db->query($updateSql, [
                $position, $gridX, $gridY, $gridWidth, $gridHeight, $size, $isVisible, $now, $userId, $moduleId
            ]);
            
            if (!$updateResult) {
                $success = false;
            }
        } else {
            // Neue Einstellungen erstellen
            $insertSql = "INSERT INTO user_dashboard_settings 
                (user_id, module_id, position, grid_x, grid_y, grid_width, grid_height, size, is_visible, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $insertResult = $db->query($insertSql, [
                $userId, $moduleId, $position, $gridX, $gridY, $gridWidth, $gridHeight, $size, $isVisible, $now, $now
            ]);
            
            if (!$insertResult) {
                $success = false;
            }
        }
    }
    
    if ($success) {
        $response = ['success' => true, 'message' => 'dashboard_settings_saved'];
    } else {
        $response = ['success' => false, 'message' => 'dashboard_settings_error'];
    }
    echo json_encode($response);

}


?>

