<?php
require_once 'init.php';

// Wenn Benutzer bereits eingeloggt ist, zur Dashboard-Seite weiterleiten
if ($user->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = false;

// Registrierungsformular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'empty_fields';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'invalid_email';
    } else if (strlen($password) < 8) {
        $error = 'password_too_short';
    } else if ($password !== $confirmPassword) {
        $error = 'passwords_dont_match';
    } else {
        // Benutzer registrieren
        $result = $user->register($username, $email, $password);
        
        if ($result['success']) {
            $success = true;
        } else {
            $error = $result['message'];
        }
    }
}

// Variablen an Smarty übergeben
$smarty->assign('error', $error);
$smarty->assign('success', $success);

// Seite anzeigen
$smarty->display('register.tpl');
?>

