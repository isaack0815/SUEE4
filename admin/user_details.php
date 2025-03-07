<?php
require_once '../init.php';
require_once '../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
requirePermission($user, 'user.view', false, '../index.php');

// Benutzer-ID aus der URL abrufen
$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId <= 0) {
    // Wenn keine gültige Benutzer-ID angegeben ist, zur Benutzerliste umleiten
    header('Location: users.php');
    exit;
}

// Benutzerdaten abrufen
$userManager = new User();
$userData = $userManager->getUserById($userId);

if (!$userData) {
    // Wenn der Benutzer nicht existiert, zur Benutzerliste umleiten
    $_SESSION['error_message'] = 'user_not_found';
    header('Location: users.php');
    exit;
}

// Gruppen des Benutzers abrufen
$groupManager = new Group();
$userGroups = $groupManager->getUserGroups($userId);

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

// Autoinclude-Dateien laden
$autoincludeModules = [];
$autoincludePath = __DIR__ . '/users/autoinclude/';

// Aktiven Tab aus der URL abrufen oder Standard setzen
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';

if (is_dir($autoincludePath)) {
    $files = scandir($autoincludePath);
    
    foreach ($files as $file) {
        if (is_file($autoincludePath . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            // Modulinformationen extrahieren
            $moduleInfo = getModuleInfo($autoincludePath . $file);
            
            // Eindeutige ID für das Modul generieren
            $moduleId = pathinfo($file, PATHINFO_FILENAME);
            
            $autoincludeModules[$moduleId] = [
                'id' => $moduleId,
                'file' => $file,
                'title' => $moduleInfo['title'] ?? ucfirst(str_replace('_', ' ', $moduleId)),
                'icon' => $moduleInfo['icon'] ?? 'info-circle',
                'order' => $moduleInfo['order'] ?? 999,
                'content' => null
            ];
        }
    }
    
    // Module nach Reihenfolge sortieren
    uasort($autoincludeModules, function($a, $b) {
        return $a['order'] <=> $b['order'];
    });
    
    // Wenn kein Tab angegeben oder der angegebene Tab nicht existiert, den ersten Tab verwenden
    if ($activeTab === 'profile' || !isset($autoincludeModules[$activeTab])) {
        // Profil ist immer der erste Tab
        $activeTab = 'profile';
    }
    
    // Inhalt für den aktiven Tab laden
    if ($activeTab !== 'profile') {
        // Output-Buffering starten, um die Ausgabe der Datei zu erfassen
        ob_start();
        
        // Variablen für die Autoinclude-Datei bereitstellen
        $includeUserId = $userId;
        $includeUserData = $userData;
        
        // Datei einbinden
        include $autoincludePath . $autoincludeModules[$activeTab]['file'];
        
        // Output-Buffering beenden und Ausgabe speichern
        $autoincludeModules[$activeTab]['content'] = ob_get_clean();
    }
}

// Fehlermeldung aus der Session löschen, falls vorhanden
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}

// Variablen an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);
$smarty->assign('activeMenu', 'users');
$smarty->assign('userData', $userData);
$smarty->assign('userGroups', $userGroups);
$smarty->assign('autoincludeModules', $autoincludeModules);
$smarty->assign('activeTab', $activeTab);

// Seite anzeigen
$smarty->display('admin/user_details.tpl');

/**
 * Modulinformationen aus einer PHP-Datei extrahieren
 * 
 * @param string $filePath Pfad zur PHP-Datei
 * @return array Modulinformationen
 */
function getModuleInfo($filePath) {
    $info = [
        'title' => '',
        'icon' => '',
        'order' => 999
    ];
    
    $content = file_get_contents($filePath);
    
    // Titel extrahieren
    if (preg_match('/@title\s+(.+)$/m', $content, $matches)) {
        $info['title'] = trim($matches[1]);
    }
    
    // Icon extrahieren
    if (preg_match('/@icon\s+(.+)$/m', $content, $matches)) {
        $info['icon'] = trim($matches[1]);
    }
    
    // Reihenfolge extrahieren
    if (preg_match('/@order\s+(\d+)$/m', $content, $matches)) {
        $info['order'] = intval($matches[1]);
    }
    
    return $info;
}
?>

