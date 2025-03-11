<?php
return [
    'title' => 'E-Mail-Einstellungen',
    'template' => 'admin/settings/email.tpl',
    'load' => function() {
        global $db;
        $settings = [];
        $stmt = $db->query("SELECT * FROM settings WHERE category = 'email'");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    },
    'save' => function($data) {
        global $db;
        $allowedKeys = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password'];
        foreach ($allowedKeys as $key) {
            if (isset($data[$key])) {
                $value = $data[$key]; // PDO will handle escaping
                $stmt = $db->prepare("INSERT INTO settings (category, setting_key, setting_value) 
                            VALUES ('email', :key, :value) 
                            ON DUPLICATE KEY UPDATE setting_value = :value");
                $stmt->execute(['key' => $key, 'value' => $value]);
            }
        }
    }
];

