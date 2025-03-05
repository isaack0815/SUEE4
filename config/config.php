<?php
// Datenbank-Konfiguration
define('DB_HOST', 'localhost');
define('DB_USER', 'd042fe9b');
define('DB_PASS', '54EX6HUXNWkDzbabQuZq');
define('DB_NAME', 'd042fe9b');

// Smarty-Konfiguration
define('SMARTY_DIR', 'vendor/smarty/smarty/'); // Angepasst für Composer
define('TEMPLATE_DIR', 'templates/');
define('COMPILE_DIR', 'templates_c/');
define('CACHE_DIR', 'cache/');
define('CONFIG_DIR', 'configs/');

// Verfügbare Sprachen
define('DEFAULT_LANGUAGE', 'de');
define('AVAILABLE_LANGUAGES', serialize(array('de', 'en', 'fr')));

// Session-Timeout in Sekunden (30 Minuten)
define('SESSION_TIMEOUT', 1800);

