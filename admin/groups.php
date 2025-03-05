<?php

// Relativen Pfad zum Hauptverzeichnis bestimmen
$rootPath = dirname(__FILE__) . '/../';



// Direktes Einbinden der Klassen, falls der Autoloader fehlschlägt
$classFiles = [
  $rootPath . 'classes/Database.php',
  $rootPath . 'classes/Language.php',
  $rootPath . 'classes/User.php',
  $rootPath . 'classes/Group.php',
  $rootPath . 'classes/Menu.php'
];

foreach ($classFiles as $file) {
  if (file_exists($file)) {
      require_once $file;
  } else {
      die("Fehler: Datei $file nicht gefunden.");
  }
}

require_once '../init.php';

// Prüfen, ob Benutzer eingeloggt ist und Admin-Rechte hat
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems();

// Menü an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'groups');

// Seite anzeigen
$smarty->display('admin/groups.tpl');
