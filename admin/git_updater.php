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

// Formular wurde abgeschickt
$updateResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $repoUrl = isset($_POST['repo_url']) ? $_POST['repo_url'] : '';
    $accessToken = isset($_POST['access_token']) ? $_POST['access_token'] : '';
    
    if (!empty($repoUrl)) {
        try {
            // GitHub-Updater initialisieren mit bestehender Datenbankverbindung
            $gitUpdater = new GitHub($repoUrl, $accessToken, Database::getInstance());
            
            // Update durchführen
            $updateResult = $gitUpdater->update();
            
            // Smarty-Variable setzen
            $smarty->assign('updateResult', $updateResult);
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
        }
    } else {
        $smarty->assign('error', 'Repository-URL ist erforderlich.');
    }
}

// Verfügbare Backups abrufen
$backups = [];
$backupDir = '../backups/';
if (is_dir($backupDir)) {
    $files = glob($backupDir . 'backup_*.zip');
    foreach ($files as $file) {
        $backups[] = [
            'filename' => basename($file),
            'size' => round(filesize($file) / 1024 / 1024, 2), // Größe in MB
            'date' => date('Y-m-d H:i:s', filemtime($file))
        ];
    }
}

// Nach Datum sortieren (neueste zuerst)
usort($backups, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Template-Variablen setzen
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('backups', $backups);
$smarty->assign('title', 'Git Updater');

// Template anzeigen
$smarty->display('admin/git_updater.tpl');

