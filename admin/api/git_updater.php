<?php
require_once '../init.php';
require_once '../../includes/auth_check.php';
require_once '../classes/GitUpdater.php';

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

// Aktion prüfen
$action = isset($_POST['action']) ? $_POST['action'] : '';
$response = ['success' => false];

switch ($action) {
    case 'check_updates':
        $repoUrl = isset($_POST['repo_url']) ? $_POST['repo_url'] : '';
        $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : '';
        
        if (!empty($repoUrl)) {
            try {
                // GitHub-Updater initialisieren mit bestehender Datenbankverbindung
                $gitUpdater = new GitHub($repoUrl, $accessToken, Database::getInstance());
                
                // Neue Commits abrufen
                $newCommits = $gitUpdater->getNewCommits();
                
                $response = [
                    'success' => true,
                    'commits' => $newCommits,
                    'count' => count($newCommits),
                    'message' => count($newCommits) > 0 
                        ? count($newCommits) . ' neue Updates verfügbar.' 
                        : 'Keine neuen Updates verfügbar.'
                ];
            } catch (Exception $e) {
                $response = ['success' => false, 'error' => $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'error' => 'Repository-URL ist erforderlich.'];
        }
        break;
        
    case 'apply_update':
        $repoUrl = isset($_POST['repo_url']) ? $_POST['repo_url'] : '';
        $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : '';
        
        if (!empty($repoUrl)) {
            try {
                // GitHub-Updater initialisieren mit bestehender Datenbankverbindung
                $gitUpdater = new GitHub($repoUrl, $accessToken, Database::getInstance());
                
                // Update durchführen
                $updateResult = $gitUpdater->update();
                
                $response = [
                    'success' => $updateResult['success'],
                    'message' => $updateResult['message'],
                    'result' => $updateResult
                ];
            } catch (Exception $e) {
                $response = ['success' => false, 'error' => $e->getMessage()];
            }
        } else {
            $response = ['success' => false, 'error' => 'Repository-URL ist erforderlich.'];
        }
        break;
        
    default:
        $response = ['success' => false, 'error' => 'Ungültige Aktion.'];
}

// JSON-Antwort senden
header('Content-Type: application/json');
echo json_encode($response);

