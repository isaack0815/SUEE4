<?php
/**
 * Sicherheitseinstellungen für das Benutzerprofil
 * 
 * @title Sicherheit
 * @icon shield-lock
 * @order 10
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Passwörter prüfen
    if ($newPassword !== $confirmPassword) {
        $_SESSION['profile_message'] = 'passwords_dont_match';
        $_SESSION['profile_message_type'] = 'danger';
    } else {
        // Passwort ändern
        $result = $user->changePassword($userId, $currentPassword, $newPassword);
        
        if ($result['success']) {
            $_SESSION['profile_message'] = 'password_changed';
        } else {
            $_SESSION['profile_message'] = $result['message'] ?? 'profile_save_error';
            $_SESSION['profile_message_type'] = 'danger';
        }
    }
    
    // Zurück zur Profilseite
    header('Location: profile.php?tab=01_security');
    exit;
}
?>

<form action="profile.php?tab=01_security" method="post">
    <input type="hidden" name="action" value="change_password">
    
    <div class="mb-3">
        <label for="current_password" class="form-label"><?php echo $lang->translate('current_password'); ?></label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
    </div>
    
    <div class="mb-3">
        <label for="new_password" class="form-label"><?php echo $lang->translate('new_password'); ?></label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
    </div>
    
    <div class="mb-3">
        <label for="confirm_password" class="form-label"><?php echo $lang->translate('confirm_password'); ?></label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> <?php echo $lang->translate('save'); ?>
        </button>
    </div>
</form>

