<?php
/* Smarty version 5.4.3, created on 2025-03-06 12:25:13
  from 'file:includes/user/profil/02_preferences.php' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c986197fe1d5_31929321',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0015380a2f6a7a01b4ca9d95c707075f3edc72bb' => 
    array (
      0 => 'includes/user/profil/02_preferences.php',
      1 => 1741259603,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_67c986197fe1d5_31929321 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/includes/user/profil';
echo '<?php'; ?>

/**
 * Benutzereinstellungen für das Benutzerprofil
 * 
 * @title Einstellungen
 * @icon gear
 * @order 20
 */

// Benutzer-ID aus der Session abrufen
$userId = $user->getCurrentUser()['id'];

// Hier könnten wir Benutzereinstellungen aus einer Datenbank abrufen
// Für dieses Beispiel verwenden wir Dummy-Daten
$preferences = [
    'language' => $lang->getCurrentLanguage(),
    'notifications_enabled' => true,
    'email_notifications' => true,
    'dark_mode' => false
];

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_preferences') {
    // Hier könnten wir die Einstellungen speichern
    // Für dieses Beispiel geben wir nur eine Erfolgsmeldung aus
    
    $_SESSION['profile_message'] = 'preferences_saved';
    
    // Zurück zur Profilseite
    header('Location: profile.php?tab=02_preferences');
    exit;
}
<?php echo '?>'; ?>


<form action="profile.php?tab=02_preferences" method="post">
    <input type="hidden" name="action" value="save_preferences">
    
    <div class="mb-3">
        <label for="language" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language"), $_smarty_tpl);?>
</label>
        <select class="form-select" id="language" name="language">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'lang_code');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang_code')->value) {
$foreach0DoElse = false;
?>
                <option value="<?php echo $_smarty_tpl->getValue('lang_code');?>
" <?php if ($_smarty_tpl->getValue('preferences')['language'] == $_smarty_tpl->getValue('lang_code')) {?>selected<?php }?>>
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"lang_".((string)$_smarty_tpl->getValue('lang_code'))), $_smarty_tpl);?>

                </option>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </select>
    </div>
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="notifications_enabled" name="notifications_enabled" <?php if ($_smarty_tpl->getValue('preferences')['notifications_enabled']) {?>checked<?php }?>>
            <label class="form-check-label" for="notifications_enabled"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"enable_notifications"), $_smarty_tpl);?>
</label>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" <?php if ($_smarty_tpl->getValue('preferences')['email_notifications']) {?>checked<?php }?>>
            <label class="form-check-label" for="email_notifications"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"enable_email_notifications"), $_smarty_tpl);?>
</label>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" <?php if ($_smarty_tpl->getValue('preferences')['dark_mode']) {?>checked<?php }?>>
            <label class="form-check-label" for="dark_mode"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"dark_mode"), $_smarty_tpl);?>
</label>
        </div>
    </div>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>

        </button>
    </div>
</form>

<?php }
}
