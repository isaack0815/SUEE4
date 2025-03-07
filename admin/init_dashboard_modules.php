<?php
require_once '../init.php';

// Nur für Administratoren zugänglich
require_once '../includes/admin_check.php';

// Dashboard-Instanz erstellen
$dashboard = new Dashboard();

// Beispielmodule registrieren
$modules = [
    [
        'id' => 'welcome',
        'name' => 'Willkommen',
        'description' => 'Willkommensnachricht für den Benutzer',
        'icon' => 'fas fa-home',
        'file_path' => 'includes/dashboard_modules/welcome.php'
    ],
    [
        'id' => 'profile',
        'name' => 'Profil',
        'description' => 'Profilinformationen des Benutzers',
        'icon' => 'fas fa-user',
        'file_path' => 'includes/dashboard_modules/profile.php'
    ],
    [
        'id' => 'statistics',
        'name' => 'Statistiken',
        'description' => 'Benutzerstatistiken',
        'icon' => 'fas fa-chart-bar',
        'file_path' => 'includes/dashboard_modules/statistics.php'
    ],
    [
        'id' => 'notifications',
        'name' => 'Benachrichtigungen',
        'description' => 'Aktuelle Benachrichtigungen',
        'icon' => 'fas fa-bell',
        'file_path' => 'includes/dashboard_modules/notifications.php'
    ]
];

// Module registrieren
$success = true;
foreach ($modules as $module) {
    $result = $dashboard->registerModule(
        $module['id'],
        $module['name'],
        $module['description'],
        $module['icon'],
        $module['file_path']
    );
    
    if (!$result) {
        $success = false;
        echo "Fehler beim Registrieren des Moduls: " . $module['id'] . "<br>";
    } else {
        echo "Modul erfolgreich registriert: " . $module['id'] . "<br>";
    }
}

if ($success) {
    echo "<p>Alle Module wurden erfolgreich registriert.</p>";
} else {
    echo "<p>Es sind Fehler aufgetreten. Bitte überprüfen Sie die Fehlerprotokolle.</p>";
}
?>

