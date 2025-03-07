<?php
/**
 * Benutzereinstellungen für das Benutzerprofil
 * 
 * @title Einstellungen
 * @icon gear
 * @order 20
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;

// UserPreferences-Klasse initialisieren
$userPreferences = new UserPreferences();

// Benutzereinstellungen abrufen
$preferences = [
    'language' => $userPreferences->getPreference($userId, 'language', $lang->getCurrentLanguage()),
    'notifications_enabled' => $userPreferences->getPreference($userId, 'notifications_enabled', '1') === '1',
    'email_notifications' => $userPreferences->getPreference($userId, 'email_notifications', '1') === '1',
    'dark_mode' => $userPreferences->getPreference($userId, 'dark_mode', '0') === '1'
];

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_preferences') {
    // Einstellungen aus dem Formular sammeln
    $newPreferences = [
        'language' => $_POST['language'] ?? $lang->getCurrentLanguage(),
        'notifications_enabled' => isset($_POST['notifications_enabled']) ? '1' : '0',
        'email_notifications' => isset($_POST['email_notifications']) ? '1' : '0',
        'dark_mode' => isset($_POST['dark_mode']) ? '1' : '0'
    ];
    
    // Einstellungen speichern
    $result = $userPreferences->savePreferences($userId, $newPreferences);
    
    if ($result) {
        $_SESSION['profile_message'] = 'preferences_saved';
        
        // Wenn die Sprache geändert wurde, diese in der Session aktualisieren
        if ($newPreferences['language'] !== $lang->getCurrentLanguage()) {
            $_SESSION['lang'] = $newPreferences['language'];
        }
    } else {
        $_SESSION['profile_message'] = 'preferences_save_error';
        $_SESSION['profile_message_type'] = 'danger';
    }
    
    // Zurück zur Profilseite
    header('Location: profile.php?tab=02_preferences');
    exit;
}
?>

<form action="profile.php?tab=02_preferences" method="post">
    <input type="hidden" name="action" value="save_preferences">
    
    <div class="mb-3">
        <label for="language" class="form-label"><?php echo $lang->translate('language'); ?></label>
        <select class="form-select" id="language" name="language">
            <?php foreach ($lang->getAvailableLanguages() as $lang_code): ?>
                <option value="<?php echo $lang_code; ?>" <?php echo ($preferences['language'] == $lang_code) ? 'selected' : ''; ?>>
                    <?php echo $lang->translate('lang_' . $lang_code); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="notifications_enabled" name="notifications_enabled" <?php echo $preferences['notifications_enabled'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="notifications_enabled"><?php echo $lang->translate('enable_notifications'); ?></label>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" <?php echo $preferences['email_notifications'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="email_notifications"><?php echo $lang->translate('enable_email_notifications'); ?></label>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" <?php echo $preferences['dark_mode'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="dark_mode"><?php echo $lang->translate('dark_mode'); ?></label>
        </div>
    </div>
    
    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> <?php echo $lang->translate('save'); ?>
        </button>
    </div>
</form>

