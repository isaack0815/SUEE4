<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Überprüfen, ob der Benutzer Admin-Rechte hat
if (!$user->hasPermission('admin_access')) {
    echo json_encode(['success' => false, 'message' => 'Keine Berechtigung']);
    exit;
}

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? '';
$key = $_POST['key'] ?? '';
$value = $_POST['value'] ?? '';
$description = $_POST['description'] ?? '';

$db = Database::getInstance();

switch ($action) {
    case 'add':
        $result = $db->insert('metadata', [
            'meta_key' => $key,
            'meta_value' => $value,
            'description' => $description
        ]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Metadaten erfolgreich hinzugefügt']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Hinzufügen der Metadaten']);
        }
        break;

    case 'edit':
        $result = $db->update('metadata', 
            ['meta_key' => $key, 'meta_value' => $value, 'description' => $description],
            'id = ?', [$id]
        );
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Metadaten erfolgreich aktualisiert']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Aktualisieren der Metadaten']);
        }
        break;

    case 'delete':
        $result = $db->query('DELETE FROM metadata WHERE id = ?', [$id]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Metadaten erfolgreich gelöscht']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Fehler beim Löschen der Metadaten']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Ungültige Aktion']);
        break;
}

