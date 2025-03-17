<?php
/**
 * Forum Main Page
 * 
 * This file displays the main forum page with a list of categories and forums.
 */

// Include necessary files
require_once 'init.php';
require_once 'classes/Forum.php';

$db = Database::getInstance();

// Initialize the Forum class
$forum = new Forum($db);

// Get all categories and forums
$categories = $forum->getCategories();

// Set template variables
$smarty->assign('categories', $categories);
$smarty->assign('pageTitle', 'Forum');

// Display the template
$smarty->display('forum.tpl');

?>

<ul>
    