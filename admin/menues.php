<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
requirePermission($user, 'menu.view', false, '../index.php');

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Gruppen laden für die Auswahl
$group = new Group();
$groups = $group->getAllGroups();

// Berechtigungen laden für die Auswahl
$permission = new Permission();
$permissions = $permission->getAllPermissions();

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'menus');
$smarty->assign('groups', $groups);
$smarty->assign('permissions', $permissions);

// Seite anzeigen
$smarty->display('admin/menues.tpl');
