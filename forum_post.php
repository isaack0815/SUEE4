<?php
/**
 * Post Creation/Editing
 * 
 * This file handles the creation and editing of posts.
 */

// Include necessary files
require_once 'init.php';
require_once 'includes/auth_check.php';

$db = Database::getInstance();
require_once 'classes/Forum.php';

// Initialize the Forum class
$forum = new Forum($db);

// Get the action from the URL
$action = isset($_GET['action']) ? $_GET['action'] : 'new';

// Get the topic ID from the URL
$topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;

// Get the post ID from the URL (for editing)
$postId = isset($_GET['post']) ? (int)$_GET['post'] : 0;

//Get the Forum id
$forumId = isset($_GET['forum']) ? (int)$_GET['forum'] : 0;

//Get the Topic Titel
$titel = isset($_POST['title']) ? $_POST['title'] : '';

//Get the Forum Content
$content = isset($_POST['content']) ? $_POST['content'] : '';

//Set Topic Details
$ForumDetails = $forum->getForumDetails($forumId,$_SESSION['user_id']);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'edit' && $postId > 0) {
        // Update the post
        $result = $forum->updatePost($postId, $content);
        
        if ($result) {
            // Redirect to the topic page
            header('Location: forum_topic.php?id=' . $topicId);
            exit;
        }
    } elseif($action === 'new' && $forumId > 0 && $topicId == 0) {
        $ret = $forum->CreateForumTopic($forumId,$content,$titel);
        if($ret != false){
            // Redirect to the forum page
            header('Location: forum_forum.php?id=' . $forumId);
            exit;
        }
    }elseif($action === 'newpost' && $topicId > 0){
        // Create a new post
        $result = $forum->createPost($topicId, $content);
        print_r($result);
        if ($result != false) {
            echo 'Location: forum_topic.php?id=' . $topicId;
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
$smarty->assign('details', $ForumDetails);
$smarty->assign('topicId', $topicId);
$smarty->assign('forumId', $forumId);
$smarty->assign('post', $post);
$smarty->assign('pageTitle', $action === 'edit' ? 'Beitrag bearbeiten' : 'Neuer Beitrag');

// Display the template
if($action === 'new'){
    $smarty->display('forum_new_topic.tpl');
}elseif($action === 'newpost'){
    $smarty->display('forum_topic.tpl');
}else{
    $smarty->display('forum_post.tpl');
}
