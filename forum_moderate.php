<?php
/**
 * Forum Moderation
 * 
 * This file handles forum moderation functions.
 */

// Include necessary files
require_once 'classes/Forum.php';

// Initialize the Forum class
$forum = new Forum();

// Check if the user is logged in and has moderation permissions
if (!isset($_SESSION['user_id']) || !$forum->isModerator($_SESSION['user_id'])) {
    // Redirect to the main forum page
    header('Location: forum.php');
    exit;
}

// Get the action from the URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different moderation actions
switch ($action) {
    case 'sticky':
        // Make a topic sticky
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forum->setTopicSticky($topicId, true);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'unsticky':
        // Remove sticky status from a topic
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forum->setTopicSticky($topicId, false);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'lock':
        // Lock a topic
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forum->setTopicLocked($topicId, true);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'unlock':
        // Unlock a topic
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forum->setTopicLocked($topicId, false);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'delete_post':
        // Delete a post
        $postId = isset($_GET['post']) ? (int)$_GET['post'] : 0;
        $topicId = $forum->getPostTopicId($postId);
        $forum->deletePost($postId);
        header('Location: forum_topic.php?id=' . $topicId);
        break;
        
    case 'delete_topic':
        // Delete a topic
        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : 0;
        $forumId = $forum->getTopicForumId($topicId);
        $forum->deleteTopic($topicId);
        header('Location: forum_forum.php?id=' . $forumId);
        break;
        
    default:
        // Redirect to the main forum page
        header('Location: forum.php');
        break;
}

