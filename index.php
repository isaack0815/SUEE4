<?php
require_once 'init.php';

// Fehlermeldung aus der Session löschen, falls vorhanden
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}

// Prüfen, ob Benutzer bereits eingeloggt ist
if ($user->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Login versuchen
    if ($user->login($username, $password)) {
        // Erfolgreich eingeloggt
        header('Location: dashboard.php');
        exit;
    } else {
        // Fehlgeschlagen
        $_SESSION['error_message'] = 'login_failed';
    }
}

// Seite anzeigen
$smarty->display('index.tpl');
?>

