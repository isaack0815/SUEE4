<?php
// Prüfen, ob Benutzer eingeloggt ist
if (!isset($user) || !$user->isLoggedIn()) {
    $_SESSION['error_message'] = 'login_required';
    header('Location: ' . (strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : '') . 'index.php');
    exit;
}

/**
 * Prüft, ob der Benutzer eine bestimmte Berechtigung hat
 * 
 * @param User $user Benutzerinstanz
 * @param string $permissionName Name der Berechtigung
 * @return bool True, wenn der Benutzer die Berechtigung hat
 */
function checkPermission($user, $permissionName) {
    return $user->hasPermission($permissionName);
}

/**
 * Erfordert eine bestimmte Berechtigung, leitet um, wenn nicht vorhanden
 * 
 * @param User $user Benutzerinstanz
 * @param string $permissionName Name der Berechtigung
 * @param bool $isAdmin Prüft zusätzlich, ob der Benutzer Admin ist
 * @param string $redirectUrl URL, zu der umgeleitet wird, wenn keine Berechtigung
 * @return void
 */
function requirePermission($user, $permissionName, $isAdmin = false, $redirectUrl = null) {
    if (($isAdmin && !$user->isAdmin()) || !$user->hasPermission($permissionName)) {
        $_SESSION['error_message'] = 'no_permission';
        
        if ($redirectUrl === null) {
            $redirectUrl = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : '') . 'index.php';
        }
        
        header('Location: ' . $redirectUrl);
        exit;
    }
}
?>

