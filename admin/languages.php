<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
requirePermission($user, 'language.view', false, '../index.php');

// Fehlermeldung aus der Session löschen, falls vorhanden
if (isset($_SESSION['error_message'])) {
   unset($_SESSION['error_message']);
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Sprachen laden
$language = Language::getInstance();
$availableLanguages = $language->getAvailableLanguages();

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'languages');
$smarty->assign('availableLanguages', $availableLanguages);

// Seite anzeigen
$smarty->display('admin/languages.tpl');
?>

