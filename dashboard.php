<?php
require_once 'init.php';
require_once 'includes/auth_check.php';

// Dashboard-Instanz erstellen
$dashboard = new Dashboard();

// Benutzer-ID aus der Session abrufen
$userId = $user->getCurrentUser()['id'];

// Dashboard für den Benutzer initialisieren, falls noch nicht geschehen
$dashboard->initializeUserDashboard($userId);

// Dashboard-Einstellungen des Benutzers abrufen
$userModules = $dashboard->getUserDashboardSettings($userId);

// Alle aktiven Module abrufen
$allModules = $dashboard->getAllModules(true);
$ret = array();
foreach ($allModules as $module) {
    $ret[$module['id']] = $module;
}

$allModules = $ret;

// Dashboard-Module für die Anzeige vorbereiten
$dashboardModules = [];
foreach ($userModules as $moduleSettings) {
    $moduleId = $moduleSettings['module_id'];
    // Prüfen, ob das Modul aktiv ist und in der Liste der verfügbaren Module existiert
    if (isset($allModules[$moduleId]) && $moduleSettings['is_visible']) {
        $moduleInfo = $allModules[$moduleId];
        // Prüfen, ob die Moduldatei existiert
        $modulePath = __DIR__ . '/includes/user/profil/dashboard/' . $moduleInfo['file_path'];
        if (file_exists($modulePath)) {
            // Output-Buffering starten, um die Ausgabe der Datei zu erfassen
            ob_start();
            
            // Variablen für die Moduldatei bereitstellen
            $includeUserId = $userId;
            $includeUserData = $user->getCurrentUser();
            
            // Datei einbinden
            include $modulePath;
            
            // Output-Buffering beenden und Ausgabe speichern
            $moduleContent = ob_get_clean();
            
            $dashboardModules[$moduleId] = [
                'id' => $moduleId,
                'title' => $moduleInfo['name'],
                'description' => $moduleInfo['description'],
                'icon' => $moduleInfo['icon'],
                'content' => $moduleContent,
                'position' => $moduleSettings['position'],
                'grid_x' => $moduleSettings['grid_x'],
                'grid_y' => $moduleSettings['grid_y'],
                'grid_width' => $moduleSettings['grid_width'],
                'grid_height' => $moduleSettings['grid_height'],
                'size' => $moduleSettings['size']
            ];
        }
    }
}

// Module nach Position sortieren
uasort($dashboardModules, function($a, $b) {
    return $a['position'] <=> $b['position'];
});

// Basis-URL für Assets
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptPath != '/') {
    $baseUrl .= $scriptPath;
}

// Variablen an Smarty übergeben
$smarty->assign('baseUrl', $baseUrl);
$smarty->assign('dashboardModules', $dashboardModules);

// Seite anzeigen
$smarty->display('dashboard.tpl');
?>

