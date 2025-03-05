<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    $_SESSION['error_message'] = 'no_permission';
    header('Location: ../index.php');
    exit;
}

// Fehlermeldung aus der Session löschen, falls vorhanden
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Statistiken laden
$db = Database::getInstance();

// Anzahl der Benutzer
$userCount = $db->selectOne("SELECT COUNT(*) as count FROM users");

// Anzahl der Gruppen
$groupCount = $db->selectOne("SELECT COUNT(*) as count FROM user_groups");

// Anzahl der Berechtigungen
$permissionCount = $db->selectOne("SELECT COUNT(*) as count FROM permissions");

// Letzte Benutzeraktivitäten
$recentUsers = $db->select("SELECT username, last_login FROM users ORDER BY last_login DESC LIMIT 5");

// Systeminfo
$systemInfo = [
    'php_version' => phpversion(),
    'server_software' => $_SERVER['SERVER_SOFTWARE'],
    'database_version' => $db->selectOne("SELECT VERSION() as version")['version'],
    'smarty_version' => Smarty\Smarty::SMARTY_VERSION,
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time') . ' seconds',
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size')
];

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'dashboard');
$smarty->assign('userCount', $userCount['count']);
$smarty->assign('groupCount', $groupCount['count']);
$smarty->assign('permissionCount', $permissionCount['count']);
$smarty->assign('recentUsers', $recentUsers);
$smarty->assign('systemInfo', $systemInfo);

// Seite anzeigen
$smarty->display('admin/index.tpl');
?>

