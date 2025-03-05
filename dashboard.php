<?php
require_once 'init.php';

// Wenn Benutzer nicht eingeloggt ist, zur Login-Seite weiterleiten
if (!$user->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Seite anzeigen
$smarty->display('dashboard.tpl');
?>

