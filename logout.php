<?php
require_once 'init.php';

// Benutzer ausloggen
$user->logout();

// Remember-Me-Cookie löschen
if (isset($_COOKIE['remember_token'])) {
    // Token aus Datenbank entfernen
    $db = Database::getInstance();
    $db->query("DELETE FROM remember_tokens WHERE token = ?", [$_COOKIE['remember_token']]);
    
    // Cookie löschen
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Zur Login-Seite weiterleiten
header('Location: index.php');
exit;
?>

