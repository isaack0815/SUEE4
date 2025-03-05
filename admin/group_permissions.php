<?php
// Datei: admin/group_permissions.php

require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist
if (!$user->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

// Prüfen, ob Benutzer Admin-Rechte hat
checkPermission('admin_access');

// Prüfen, ob Gruppen-ID übergeben wurde
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: groups.php');
    exit;
}

$groupId = $_GET['id'];

// Gruppe laden
$group = new Group();
$groupData = $group->getGroupById($groupId);

if (!$groupData) {
    header('Location: groups.php');
    exit;
}

// Berechtigungen laden
$permission = new Permission();
$allPermissions = $permission->getAllPermissions();
$groupPermissions = $permission->getGroupPermissions($groupId);

// Berechtigungen mit Zuweisungsstatus versehen
$permissionsWithStatus = [];
foreach ($allPermissions as $perm) {
    $assigned = false;
    foreach ($groupPermissions as $groupPerm) {
        if ($perm['id'] == $groupPerm['id']) {
            $assigned = true;
            break;
        }
    }
    
    $perm['assigned'] = $assigned;
    $permissionsWithStatus[] = $perm;
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems();

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'groups');
$smarty->assign('group', $groupData);
$smarty->assign('permissions', $permissionsWithStatus);

// Seite anzeigen
$smarty->display('admin/group_permissions.tpl');
?>