<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';
require_once '../../classes/GitHub.php';

// Nur AJAX-Anfragen zulassen
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    header('HTTP/1.0 403 Forbidden');
    exit('Direkter Zugriff nicht erlaubt.');
}

// Berechtigungsprüfung
if (!$user->hasPermission('git_updater_execute')) {
    header('HTTP/1.0 403 Forbidden');
    exit(json_encode(['error' => 'Keine Berechtigung.']));
}

// Debug-Informationen
error_log('Git Updater API aufgerufen');
error_log('POST-Daten: ' . print_r($_POST, true));

// Daten aus der Anfrage abrufen
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

error_log('Verarbeitete Daten: ' . print_r($data, true));

// Aktion prüfen
$action = isset($data['action']) ? $data['action'] : '';

// Erforderliche Parameter prüfen
if (!isset($data['repo_url']) || empty($data['repo_url'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Repository-URL ist erforderlich'
    ]);
    exit;
}

try {
    $repoUrl = 'https://github.com/isaack0815/SUEE4.git';
    $accessToken = isset($data['access_token']) ? $data['access_token'] : '';
    // GitHub-Updater initialisieren
    $gitUpdater = new GitHub($repoUrl, 'main', $accessToken);
    
    if ($action === 'check_updates') {
        // Neue Commits abrufen
        $newCommits = $gitUpdater->getNewCommits();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => count($newCommits),
            'message' => count($newCommits) > 0 
                ? count($newCommits) . ' neue Updates verfügbar.' 
                : 'Keine neuen Updates verfügbar.'
        ]);
    } else {
        // Update durchführen
        $updateResult = $gitUpdater->update();
        
        header('Content-Type: application/json');
        echo json_encode($updateResult);
    }
} catch (Exception $e) {
    error_log('Fehler in Git Updater API: ' . $e->getMessage());
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Fehler: ' . $e->getMessage()
    ]);
}
?>

