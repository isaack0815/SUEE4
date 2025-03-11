<?php
/**
 * Forum Display
 * 
 * This file displays a specific forum with a list of topics.
 */

// Include necessary files
require_once 'init.php';
require_once 'includes/auth_check.php';
require_once 'classes/Forum.php';

$db = Database::getInstance();

// Initialize the Forum class
$forum = new Forum($db);

// Get the forum ID from the URL
$forumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if the forum exists
if (!$forum->forumExists($forumId)) {
    // Redirect to the main forum page
    header('Location: forum.php');
    exit;
}

// Get the forum details
$forumDetails = $forum->getForumDetails($forumId);

// Get the topics in this forum
$topics = $forum->getTopics($forumId);

// Set template variables
$smarty->assign('forum', $forumDetails);
$smarty->assign('topics', $topics);
$smarty->assign('pageTitle', $forumDetails['name']);

// Display the template
$smarty->display('forum_forum.tpl');

