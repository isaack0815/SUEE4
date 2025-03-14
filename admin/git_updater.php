<?php
require_once '../init.php';
require_once '../includes/auth_check.php';
require_once '../classes/GitHub.php';


// Prüfen, ob der Benutzer Admin-Rechte hat
if (!$user->hasPermission('admin_access')) {
    header('Location: ../index.php');
    exit;
}

// Berechtigungsprüfung
if (!$user->hasPermission('git_updater_access')) {
    header('Location: index.php');
    exit;
}

// Verfügbare Backups abrufen
$backups = [];
$backupDir = '../backups/';
if (is_dir($backupDir)) {
    $files = glob($backupDir . 'backup_*.zip');
    foreach ($files as $file) {
        $filename = basename($file);
        $size = round(filesize($file) / (1024 * 1024), 2); // Größe in MB
        $date = date('Y-m-d H:i:s', filemtime($file));
        
        $backups[] = [
            'filename' => $filename,
            'size' => $size,
            'date' => $date
        ];
    }
    
    // Nach Datum sortieren (neueste zuerst)
    usort($backups, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

// Update durchführen
if (isset($_POST['update'])) {
    try {
        $repoUrl = isset($_POST['repo_url']) ? $_POST['repo_url'] : '';
        $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : '';
        
        if (empty($repoUrl)) {
            throw new Exception('Repository-URL ist erforderlich');
        }
        
        // GitHub-Updater initialisieren
        $gitUpdater = new GitHub($repoUrl, $accessToken, $db);
        
        // Update durchführen
        $updateResult = $gitUpdater->update();
        
        // Verfügbare Backups aktualisieren
        $backups = [];
        if (is_dir($backupDir)) {
            $files = glob($backupDir . 'backup_*.zip');
            foreach ($files as $file) {
                $filename = basename($file);
                $size = round(filesize($file) / (1024 * 1024), 2); // Größe in MB
                $date = date('Y-m-d H:i:s', filemtime($file));
                
                $backups[] = [
                    'filename' => $filename,
                    'size' => $size,
                    'date' => $date
                ];
            }
            
            // Nach Datum sortieren (neueste zuerst)
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Template anzeigen

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('backups', $backups);
if (isset($updateResult)) {
    $smarty->assign('updateResult', $updateResult);
}
if (isset($error)) {
    $smarty->assign('error', $error);
}
$smarty->display('admin/git_updater.tpl');

