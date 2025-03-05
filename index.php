<?php
require_once 'init.php';

// Wenn Benutzer bereits eingeloggt ist, zur Dashboard-Seite weiterleiten
if ($user->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Seite anzeigen
$smarty->display('login.tpl');
?>

