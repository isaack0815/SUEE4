<?php
/* Smarty version 5.4.3, created on 2025-03-06 12:25:07
  from 'file:includes/user/profil/01_security.php' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c98613522548_35185729',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c9d1b9d70bb742807df8e958b03589134bd6a195' => 
    array (
      0 => 'includes/user/profil/01_security.php',
      1 => 1741259587,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_67c98613522548_35185729 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/includes/user/profil';
echo '<?php'; ?>

/**
 * Sicherheitseinstellungen für das Benutzerprofil
 * 
 * @title Sicherheit
 * @icon shield-lock
 * @order 10
 */

// Benutzer-ID aus der Session abrufen
$userId = $user->getCurrentUser()['id'];

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
<?php echo '?>'; ?>


<form action="profile.php?tab=01_security" method="post">
    <input type="hidden" name="action" value="change_password">
    
    <div class="mb-3">
        <label for="current_password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"current_password"), $_smarty_tpl);?>
</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
    </div>
    
    <div class="mb-3">
        <label for="new_password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"new_password"), $_smarty_tpl);?>
</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
    </div>
    
    <div class="mb-3">
        <label for="confirm_password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_password"), $_smarty_tpl);?>
</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>

        </button>
    </div>
</form>

<?php }
}
