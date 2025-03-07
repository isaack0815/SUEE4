<?php
require_once 'init.php';
require_once 'includes/auth_check.php';

// Benutzer-ID aus der Session abrufen
$userId = $user->getCurrentUser()['id'];

// Aktiven Tab aus der URL abrufen oder Standard setzen
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'personal';

// Erfolgs- oder Fehlermeldung aus der Session abrufen
$message = '';
$messageType = '';

if (isset($_SESSION['profile_message'])) {
    $message = $_SESSION['profile_message'];
    $messageType = $_SESSION['profile_message_type'] ?? 'success';
    unset($_SESSION['profile_message']);
    unset($_SESSION['profile_message_type']);
}

// Autoinclude-Dateien laden
$autoincludeModules = [];
$autoincludePath = __DIR__ . '/includes/user/profil/';
$tabContent = '';

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
                'order' => $moduleInfo['order'] ?? 999
            ];
        }
    }
    
    // Module nach Reihenfolge sortieren
    uasort($autoincludeModules, function($a, $b) {
        return $a['order'] <=> $b['order'];
    });
    
    // Wenn der angegebene Tab nicht existiert, den ersten Tab verwenden
    if (!isset($autoincludeModules[$activeTab]) && $activeTab !== 'personal') {
        $activeTab = 'personal';
    }
    
    // Inhalt für den aktiven Tab laden
    if ($activeTab !== 'personal' && isset($autoincludeModules[$activeTab])) {
        // Output-Buffering starten, um die Ausgabe der Datei zu erfassen
        ob_start();
        
        // Variablen für die Autoinclude-Datei bereitstellen
        $includeUserId = $userId;
        $includeUserData = $user->getCurrentUser();
        
        // Datei einbinden
        include $autoincludePath . $autoincludeModules[$activeTab]['file'];
        
        // Output-Buffering beenden und Ausgabe speichern
        $tabContent = ob_get_clean();
    }
}

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Wenn es sich um eine Aktion aus einem Tab handelt, diese verarbeiten
    if (isset($_POST['action']) && $_POST['action'] !== 'update_profile') {
        // Die Verarbeitung erfolgt in der jeweiligen Tab-Datei
        // Wir leiten hier nur zurück, falls die Tab-Datei das nicht selbst tut
        if (!headers_sent()) {
            header('Location: profile.php?tab=' . $activeTab);
            exit;
        }
    } else {
        // Profilbild hochladen
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = $user->uploadProfileImage($userId, $_FILES['profile_image']);
            
            if (!$result['success']) {
                $_SESSION['profile_message'] = $result['message'];
                $_SESSION['profile_message_type'] = 'danger';
                header('Location: profile.php?tab=' . $activeTab);
                exit;
            }
        }
        
        // Profilbild entfernen
        if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            $user->removeProfileImage($userId);
        }
        
        // Profildaten aktualisieren
        $updateData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'bio' => $_POST['bio'] ?? ''
        ];
        
        $result = $user->updateUser($userId, $updateData);
        
        if ($result['success']) {
            $_SESSION['profile_message'] = 'profile_saved';
        } else {
            $_SESSION['profile_message'] = $result['message'] ?? 'profile_save_error';
            $_SESSION['profile_message_type'] = 'danger';
        }
        
        // Zurück zur Profilseite
        header('Location: profile.php?tab=' . $activeTab);
        exit;
    }
}

// Variablen an Smarty übergeben
$smarty->assign('activeTab', $activeTab);
$smarty->assign('autoincludeModules', $autoincludeModules);
$smarty->assign('message', $message);
$smarty->assign('messageType', $messageType);
$smarty->assign('userData', $user->getCurrentUser());
$smarty->assign('tabContent', $tabContent);

// Seite anzeigen
$smarty->display('profile.tpl');

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

