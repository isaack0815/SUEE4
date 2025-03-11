<?php
/**
 * Forum Subscription
 * 
 * This file handles forum subscription functions.
 */

// Include necessary files
require_once 'classes/Forum.php';

// Initialize the Forum class
$forum = new Forum();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get the action from the URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different subscription actions
switch ($action) {
    case 'subscribe_forum':
        // Subscribe to a forum
        $forumId = isset($_GET['forum']) ? (int)$_GET['forum'] : 0;
        $forum->subscribeToForum($_SESSION['user_id'], $forumId);
        header('Location: forum_forum.php?id=' . $forumId);
        break;
        
    case 'unsubscribe_forum':
        // Unsubscribe from a forum
        $forumId = isset($_GET['forum']) ? (int)$_GET['forum'] : 0;
        $forum->unsubscribeFromForum($_SESSION['user_id'], $forumId);
        header('Location: forum_forum.php?id=' . $forumId);
        break;
        
    case 'subscribe_topic':
        // Subscribe to a topic
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forum->subscribeToTopic($_SESSION['user_id'], $topicId);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'unsubscribe_topic':
        // Unsubscribe from a topic
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forum->unsubscribeFromTopic($_SESSION['user_id'], $topicId);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'list':
        // List all subscriptions
        $forumSubscriptions = $forum->getForumSubscriptions($_SESSION['user_id']);
        $topicSubscriptions = $forum->getTopicSubscriptions($_SESSION['user_id']);
        
        // Set template variables
        $smarty->assign('forumSubscriptions', $forumSubscriptions);
        $smarty->assign('topicSubscriptions', $topicSubscriptions);
        $smarty->assign('pageTitle', 'Meine Abonnements');
        
        // Display the template
        $smarty->display('forum_subscriptions.tpl');
        break;
        
    default:
        // Redirect to the main forum page
        header('Location: forum.php');
        break;
}

