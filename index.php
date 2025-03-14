<?php
require_once 'init.php';
require_once 'classes/IndexCustomizer.php';

// Sprachinstanz abrufen
$language = Language::getInstance();
$currentLang = $language->getCurrentLanguage();

// Übersetzungsfunktion für Smarty registrieren
function smarty_function_translate($params, $smarty) {
    $language = Language::getInstance();
    $key = isset($params['key']) ? $params['key'] : '';
    $vars = isset($params['vars']) ? $params['vars'] : [];
    
    return $language->translate($key, $vars);
}

// Fehler aus der Session abrufen und löschen
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);

// Prüfen, ob Benutzer bereits eingeloggt ist
if (isset($_SESSION['user_id'])) {
    // Zu Dashboard weiterleiten
    header('Location: dashboard.php');
    exit;
}

// IndexCustomizer-Instanz erstellen
$indexCustomizer = new IndexCustomizer();
$allSections = $indexCustomizer->getSections();

// Sektionen nach Spalten gruppieren
$leftSections = [];
$centerSections = [];
$rightSections = [];

foreach ($allSections as $section) {
    // position_vertical: 0 = links, 1 = mitte, 2 = rechts
    if (isset($section['position_vertical'])) {
        if ($section['position_vertical'] == 0) {
            $leftSections[] = $section;
        } elseif ($section['position_vertical'] == 2) {
            $rightSections[] = $section;
        } else {
            // Standard ist Mitte (1)
            $centerSections[] = $section;
        }
    } else {
        // Wenn position_vertical nicht gesetzt ist, standardmäßig zur Mitte hinzufügen
        $centerSections[] = $section;
    }
}

// Variablen an Smarty übergeben
$smarty->assign('error', $error);
$smarty->assign('leftSections', $leftSections);
$smarty->assign('centerSections', $centerSections);
$smarty->assign('rightSections', $rightSections);
$smarty->assign('currentLang', $currentLang);
$smarty->assign('availableLanguages', $language->getAvailableLanguages());

// Template anzeigen
$smarty->display('index.tpl');
?>