<?php
require_once 'init.php';

// Wenn Benutzer bereits eingeloggt ist, zur Dashboard-Seite weiterleiten
if ($user->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Login-Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($username) || empty($password)) {
        $error = 'empty_fields';
    } else {
        // Login versuchen
        $loginSuccess = $user->login($username, $password);
        
        if ($loginSuccess) {
            // Remember-Me-Cookie setzen, falls gewünscht
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $userId = $user->getCurrentUser()['id'];
                
                // Token in Datenbank speichern
                $db = Database::getInstance();
                $db->insert('remember_tokens', [
                    'user_id' => $userId,
                    'token' => $token,
                    'expires' => date('Y-m-d H:i:s', strtotime('+30 days'))
                ]);
                
                // Cookie setzen
                setcookie('remember_token', $token, time() + 60*60*24*30, '/', '', false, true);
            }
            
            // Zur Dashboard-Seite weiterleiten
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'invalid_credentials';
        }
    }
}

// Fehlermeldung an Smarty übergeben
$smarty->assign('error', $error);

// Seite anzeigen
$smarty->display('login.tpl');
?>

