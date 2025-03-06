<?php
require_once 'init.php';

// Slug aus der URL abrufen
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    // Wenn kein Slug angegeben ist, zur Startseite umleiten
    header('Location: index.php');
    exit;
}

// CMS-Seite abrufen
$cms = new CMS();
$page = $cms->getPageBySlug($slug);

// Wenn die Seite nicht existiert oder nicht veröffentlicht ist
if (!$page || $page['status'] !== 'published') {
    // 404-Seite anzeigen
    header("HTTP/1.0 404 Not Found");
    $smarty->assign('error_message', 'page_not_found');
    $smarty->display('404.tpl');
    exit;
}

// Meta-Daten für die Seite setzen
$smarty->assign('page_title', $page['title']);
$smarty->assign('meta_description', $page['meta_description']);
$smarty->assign('meta_keywords', $page['meta_keywords']);

// Seitendaten an Smarty übergeben
$smarty->assign('page', $page);

// Seite anzeigen
$smarty->display('page.tpl');
?>

