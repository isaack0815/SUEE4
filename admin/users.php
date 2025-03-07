<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
requirePermission($user, 'user.view', false, '../index.php');

// Fehlermeldung aus der Session löschen, falls vorhanden
if (isset($_SESSION['error_message'])) {
   unset($_SESSION['error_message']);
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Gruppen laden für die Auswahl
$group = new Group();
$groups = $group->getAllGroups();

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'users');
$smarty->assign('groups', $groups);

// Seite anzeigen
$smarty->display('admin/users.tpl');
?>

