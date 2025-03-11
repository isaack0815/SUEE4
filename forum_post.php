<?php
/**
 * Post Creation/Editing
 * 
 * This file handles the creation and editing of posts.
 */

// Include necessary files
require_once 'classes/Forum.php';

// Initialize the Forum class
$forum = new Forum();

// Get the action from the URL
$action = isset($_GET['action']) ? $_GET['action'] : 'new';

// Get the topic ID from the URL
$topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;

// Get the post ID from the URL (for editing)
$postId = isset($_GET['post']) ? (int)$_GET['post'] : 0;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    
    if ($action === 'edit' && $postId > 0) {
        // Update the post
        $result = $forum->updatePost($postId, $content);
        
        if ($result) {
            // Redirect to the topic page
            header('Location: forum_topic.php?id=' . $topicId);
            exit;
        }
    } else {
        // Create a new post
        $result = $forum->createPost($topicId, $content);
        
        if ($result) {
            // Redirect to the topic page
            header('Location: forum_topic.php?id=' . $topicId);
            exit;
        }
    }
}

// Get the post details (for editing)
$post = [];
if ($action === 'edit' && $postId > 0) {
    $post = $forum->getPostDetails($postId);
    
    // Check if the post exists and the user has permission to edit it
    if (empty($post) || $post['user_id'] !== $_SESSION['user_id']) {
        // Redirect to the topic page
        header('Location: forum_topic.php?id=' . $topicId);
        exit;
    }
}

// Set template variables
$smarty->assign('action', $action);
$smarty->assign('topicId', $topicId);
$smarty->assign('post', $post);
$smarty->assign('pageTitle', $action === 'edit' ? 'Beitrag bearbeiten' : 'Neuer Beitrag');

// Display the template
$smarty->display('forum_post.tpl');

