<?php
return [
    'title' => 'Allgemeine Einstellungen',
    'template' => 'admin/settings/general.tpl',
    'load' => function() {
        global $db;
        $settings = [];
        $result = $db->query("SELECT * FROM settings WHERE category = 'general'");
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    },
    'save' => function($data) {
        global $db;
        $allowedKeys = ['site_name', 'site_description', 'admin_email', 'items_per_page'];
        foreach ($allowedKeys as $key) {
            if (isset($data[$key])) {
                $value = $db->real_escape_string($data[$key]);
                $db->query("INSERT INTO settings (category, setting_key, setting_value) 
                            VALUES ('general', '$key', '$value') 
                            ON DUPLICATE KEY UPDATE setting_value = '$value'");
            }
        }
    }
];

