<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Überprüfen, ob der Benutzer Admin-Rechte hat
if (!$user->hasPermission('admin_access')) {
    echo json_encode(['success' => false, 'message' => $lang['no_permission']]);
    exit;
}

$db = Database::getInstance();
$logger = Logger::getInstance();

$settings = [
    'site_name' => $_POST['site_name'] ?? '',
    'site_description' => $_POST['site_description'] ?? '',
    'admin_email' => $_POST['admin_email'] ?? '',
    'items_per_page' => $_POST['items_per_page'] ?? 10,
];

$success = true;
$message = $lang->getLanguageKey('general_settings_updated')['values'][$_SESSION['lang']];

try {
    $pdo = $db->getConnection();
    $pdo->beginTransaction();

    foreach ($settings as $key => $value) {
        $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ? AND category = 'general'";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$value, $key]);
        
        if (!$result) {
            throw new Exception(sprintf($lang->getLanguageKey('error_updating_setting')['values'][$_SESSION['lang']], $key));
        }
    }

    $pdo->commit();
    $logger->info($lang->getLanguageKey('general_settings_updated_log')['values'][$_SESSION['lang']], "settings");
} catch (Exception $e) {
    $pdo->rollBack();
    $success = false;
    $message = $lang->getLanguageKey('error_updating_general_settings')['values'][$_SESSION['lang']] . ': ' . $e->getMessage();
    $logger->error($lang->getLanguageKey('error_updating_general_settings_log')['values'][$_SESSION['lang']] . ': ' . $e->getMessage(), "settings");
}

echo json_encode(['success' => $success, 'message' => $message]);

