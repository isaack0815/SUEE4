<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
requirePermission($user, 'permission.view', false, '../index.php');

// Fehlermeldung aus der Session löschen, falls vorhanden
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'permissions');

// Seite anzeigen
$smarty->display('admin/permissions.tpl');
?>

