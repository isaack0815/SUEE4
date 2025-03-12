<?php
/**
 * Topic Display
 * 
 * This file displays a specific topic with a list of posts.
 */

// Include necessary files
require_once 'init.php';
require_once 'includes/auth_check.php';

$db = Database::getInstance();
require_once 'classes/Forum.php';

// Initialize the Forum class
$forum = new Forum($db);

// Get the topic ID from the URL
$topicId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if the topic exists
if (!$forum->topicExists($topicId)) {
    // Redirect to the main forum page
    header('Location: forum.php');
    exit;
}


// Get the topic details
$topicDetails = $forum->getTopicDetails($_SESSION['user_id'],$topicId);

// Get the posts in this topic
$posts = $forum->getPosts($topicId);

// Update the view count
$forum->updateTopicViews($topicId);

// Set template variables
$smarty->assign('is_moderator', $forum->isModerator($_SESSION['user_id']));
$smarty->assign('topic', $topicDetails);
$smarty->assign('posts', $posts);
$smarty->assign('pageTitle', $topicDetails['title']);

// Display the template
$smarty->display('forum_topic.tpl');

