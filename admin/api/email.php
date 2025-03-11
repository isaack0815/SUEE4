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
    'smtp_host' => $_POST['smtp_host'] ?? '',
    'smtp_port' => $_POST['smtp_port'] ?? '',
    'smtp_user' => $_POST['smtp_user'] ?? '',
    'smtp_pass' => $_POST['smtp_pass'] ?? '',
    'smtp_secure' => $_POST['smtp_secure'] ?? '',
    'from_email' => $_POST['from_email'] ?? '',
    'from_name' => $_POST['from_name'] ?? '',
];

$success = true;
$message = $lang->getLanguageKey('email_settings_updated')['values'][$_SESSION['lang']];

try {
    $pdo = $db->getConnection();
    $pdo->beginTransaction();

    foreach ($settings as $key => $value) {
        $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ? AND category = 'email'";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$value, $key]);
        
        if (!$result) {
            throw new Exception(sprintf($lang['error_updating_setting'], $key));
        }
    }

    $pdo->commit();
    $logger->info($message, "settings");
} catch (Exception $e) {
    $pdo->rollBack();
    $success = false;
    $message = $lang['error_updating_email_settings'] . ': ' . $e->getMessage();
    $logger->error($lang['error_updating_email_settings_log'] . ': ' . $e->getMessage(), "settings");
}

echo json_encode(['success' => $success, 'message' => $message]);

