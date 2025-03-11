<?php
/**
 * Forum Class
 * 
 * This class handles all forum-related functions.
 */
class Forum {
    /**
     * Database instance
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Get all categories
     * 
     * @return array Array of categories with forums
     */
    public function getCategories() {
        $categories = [];
        // Get all categories
        $stmt = $this->db->query("SELECT * FROM forum_categories ORDER BY sort_order");
        
        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category['forums'] = $this->getForumsByCategory($category['id']);
            $categories[] = $category;
        }
        
        return $categories;
    }
    
    /**
     * Get forums by category
     * 
     * @param int $categoryId Category ID
     * @return array Array of forums
     */
    public function getForumsByCategory($categoryId) {
        $forums = [];
        
        // Get all forums in this category
        $stmt = $this->db->query("SELECT * FROM forum_forums WHERE category_id = ? ORDER BY sort_order", [$categoryId]);
        
        while ($forum = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Get the latest topic
            $stmt2 = $this->db->query("
                SELECT t.*, u.username 
                FROM forum_topics t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.forum_id = ? 
                ORDER BY t.last_post_time DESC 
                LIMIT 1
            ", [$forum['id']]);
            $forum['latest_topic'] = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            // Get the topic and post count
            $stmt3 = $this->db->query("SELECT COUNT(*) FROM forum_topics WHERE forum_id = ?", [$forum['id']]);
            $forum['topic_count'] = $stmt3->fetchColumn();
            
            $stmt4 = $this->db->query("
                SELECT COUNT(*) 
                FROM forum_posts p 
                JOIN forum_topics t ON p.topic_id = t.id 
                WHERE t.forum_id = ?
            ", [$forum['id']]);
            $forum['post_count'] = $stmt4->fetchColumn();
            
            $forums[] = $forum;
        }
        
        return $forums;
    }
    
    /**
     * Check if a forum exists
     * 
     * @param int $forumId Forum ID
     * @return bool True if the forum exists, false otherwise
     */
    public function forumExists($forumId) {
        $stmt = $this->db->query("SELECT COUNT(*) FROM forum_forums WHERE id = ?", [$forumId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get forum details
     * 
     * @param int $forumId Forum ID
     * @return array Forum details
     */
    public function getForumDetails($forumId) {
        $stmt = $this->db->query("SELECT * FROM forum_forums WHERE id = ?",[$forumId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get topics in a forum
     * 
     * @param int $forumId Forum ID
     * @return array Array of topics
     */
    public function getTopics($forumId) {
        $topics = [];
        
        // Get all topics in this forum
        $stmt = $this->db->query("
            SELECT t.*, u.username, 
                   (SELECT COUNT(*) FROM forum_posts WHERE topic_id = t.id) AS post_count
            FROM forum_topics t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.forum_id = ? 
            ORDER BY t.is_sticky DESC, t.last_post_time DESC
        ",[$forumId]);
        
        while ($topic = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Get the latest post
            $stmt2 = $this->db->prepare("
                SELECT p.*, u.username 
                FROM forum_posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.topic_id = ? 
                ORDER BY p.created_at DESC 
                LIMIT 1
            ");
            $stmt2->execute([$topic['id']]);
            $topic['latest_post'] = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $topics[] = $topic;
        }
        
        return $topics;
    }
    
    /**
     * Check if a topic exists
     * 
     * @param int $topicId Topic ID
     * @return bool True if the topic exists, false otherwise
     */
    public function topicExists($topicId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM forum_topics WHERE id = ?");
        $stmt->execute([$topicId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get topic details
     * 
     * @param int $topicId Topic ID
     * @return array Topic details
     */
    public function getTopicDetails($topicId) {
        $stmt = $this->db->prepare("
            SELECT t.*, u.username, f.name AS forum_name, f.id AS forum_id
            FROM forum_topics t 
            JOIN users u ON t.user_id = u.id 
            JOIN forum_forums f ON t.forum_id = f.id
            WHERE t.id = ?
        ");
        $stmt->execute([$topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get posts in a topic
     * 
     * @param int $topicId Topic ID
     * @return array Array of posts
     */
    public function getPosts($topicId) {
        $posts = [];
        
        // Get all posts in this topic
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, u.avatar, u.created_at AS user_created_at,
                   (SELECT COUNT(*) FROM forum_posts WHERE user_id = p.user_id) AS user_post_count
            FROM forum_posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.topic_id = ? 
            ORDER BY p.created_at
        ");
        $stmt->execute([$topicId]);
        
        while ($post = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $posts[] = $post;
        }
        
        return $posts;
    }
    
    /**
     * Update topic views
     * 
     * @param int $topicId Topic ID
     */
    public function updateTopicViews($topicId) {
        $stmt = $this->db->prepare("UPDATE forum_topics SET views = views + 1 WHERE id = ?");
        $stmt->execute([$topicId]);
    }
    
    /**
     * Create a new post
     * 
     * @param int $topicId Topic ID
     * @param string $content Post content
     * @return bool True on success, false on failure
     */
    public function createPost($topicId, $content) {
        try {
            // Start a transaction
            $this->db->beginTransaction();
            
            // Insert the post
            $stmt = $this->db->prepare("
                INSERT INTO forum_posts (topic_id, user_id, content, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$topicId, $_SESSION['user_id'], $content]);
            
            // Update the topic's last post time
            $stmt = $this->db->prepare("
                UPDATE forum_topics 
                SET last_post_time = NOW(), 
                    last_post_user_id = ? 
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $topicId]);
            
            // Commit the transaction
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Update a post
     * 
     * @param int $postId Post ID
     * @param string $content Post content
     * @return bool True on success, false on failure
     */
    public function updatePost($postId, $content) {
        $stmt = $this->db->prepare("
            UPDATE forum_posts 
            SET content = ?, 
                updated_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$content, $postId, $_SESSION['user_id']]);
    }
    
    /**
     * Get post details
     * 
     * @param int $postId Post ID
     * @return array Post details
     */
    public function getPostDetails($postId) {
        $stmt = $this->db->prepare("SELECT * FROM forum_posts WHERE id = ?");
        $stmt->execute([$postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if a user is a moderator
     * 
     * @param int $userId User ID
     * @return bool True if the user is a moderator, false otherwise
     */
    public function isModerator($userId) {
        // Check if the user has the 'forum_moderator' permission
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM user_permissions up 
            JOIN permissions p ON up.permission_id = p.id 
            WHERE up.user_id = ? AND p.name = 'forum_moderator'
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Set a topic's sticky status
     * 
     * @param int $topicId Topic ID
     * @param bool $sticky Sticky status
     * @return bool True on success, false on failure
     */
    public function setTopicSticky($topicId, $sticky) {
        $stmt = $this->db->prepare("UPDATE forum_topics SET is_sticky = ? WHERE id = ?");
        return $stmt->execute([$sticky ? 1 : 0, $topicId]);
    }
    
    /**
     * Set a topic's locked status
     * 
     * @param int $topicId Topic ID
     * @param bool $locked Locked status
     * @return bool True on success, false on failure
     */
    public function setTopicLocked($topicId, $locked) {
        $stmt = $this->db->prepare("UPDATE forum_topics SET is_locked = ? WHERE id = ?");
        return $stmt->execute([$locked ? 1 : 0, $topicId]);
    }
    
    /**
     * Get a post's topic ID
     * 
     * @param int $postId Post ID
     * @return int Topic ID
     */
    public function getPostTopicId($postId) {
        $stmt = $this->db->prepare("SELECT topic_id FROM forum_posts WHERE id = ?");
        $stmt->execute([$postId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Delete a post
     * 
     * @param int $postId Post ID
     * @return bool True on success, false on failure
     */
    public function deletePost($postId) {
        $stmt = $this->db->prepare("DELETE FROM forum_posts WHERE id = ?");
        return $stmt->execute([$postId]);
    }
    
    /**
     * Get a topic's forum ID
     * 
     * @param int $topicId Topic ID
     * @return int Forum ID
     */
    public function getTopicForumId($topicId) {
        $stmt = $this->db->prepare("SELECT forum_id FROM forum_topics WHERE id = ?");
        $stmt->execute([$topicId]);
        return $stmt->fetchColumn();
    }
    
    /**
     * Delete a topic
     * 
     * @param int $topicId Topic ID
     * @return bool True on success, false on failure
     */
    public function deleteTopic($topicId) {
        try {
            // Start a transaction
            $this->db->beginTransaction();
            
            // Delete all posts in this topic
            $stmt = $this->db->prepare("DELETE FROM forum_posts WHERE topic_id = ?");
            $stmt->execute([$topicId]);
            
            // Delete the topic
            $stmt = $this->db->prepare("DELETE FROM forum_topics WHERE id = ?");
            $stmt->execute([$topicId]);
            
            // Commit the transaction
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Subscribe to a forum
     * 
     * @param int $userId User ID
     * @param int $forumId Forum ID
     * @return bool True on success, false on failure
     */
    public function subscribeToForum($userId, $forumId) {
        // Check if the subscription already exists
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM forum_forum_subscriptions 
            WHERE user_id = ? AND forum_id = ?
        ");
        $stmt->execute([$userId, $forumId]);
        
        if ($stmt->fetchColumn() > 0) {
            return true; // Already subscribed
        }
        
        // Create the subscription
        $stmt = $this->db->prepare("
            INSERT INTO forum_forum_subscriptions (user_id, forum_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$userId, $forumId]);
    }
    
    /**
     * Unsubscribe from a forum
     * 
     * @param int $userId User ID
     * @param int $forumId Forum ID
     * @return bool True on success, false on failure
     */
    public function unsubscribeFromForum($userId, $forumId) {
        $stmt = $this->db->prepare("
            DELETE FROM forum_forum_subscriptions 
            WHERE user_id = ? AND forum_id = ?
        ");
        return $stmt->execute([$userId, $forumId]);
    }
    
    /**
     * Subscribe to a topic
     * 
     * @param int $userId User ID
     * @param int $topicId Topic ID
     * @return bool True on success, false on failure
     */
    public function subscribeToTopic($userId, $topicId) {
        // Check if the subscription already exists
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM forum_topic_subscriptions 
            WHERE user_id = ? AND topic_id = ?
        ");
        $stmt->execute([$userId, $topicId]);
        
        if ($stmt->fetchColumn() > 0) {
            return true; // Already subscribed
        }
        
        // Create the subscription
        $stmt = $this->db->prepare("
            INSERT INTO forum_topic_subscriptions (user_id, topic_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$userId, $topicId]);
    }
    
    /**
     * Unsubscribe from a topic
     * 
     * @param int $userId User ID
     * @param int $topicId Topic ID
     * @return bool True on success, false on failure
     */
    public function unsubscribeFromTopic($userId, $topicId) {
        $stmt = $this->db->prepare("
            DELETE FROM forum_topic_subscriptions 
            WHERE user_id = ? AND topic_id = ?
        ");
        return $stmt->execute([$userId, $topicId]);
    }
    
    /**
     * Get forum subscriptions
     * 
     * @param int $userId User ID
     * @return array Array of forum subscriptions
     */
    public function getForumSubscriptions($userId) {
        $subscriptions = [];
        
        $stmt = $this->db->prepare("
            SELECT fs.*, f.name 
            FROM forum_forum_subscriptions fs 
            JOIN forum_forums f ON fs.forum_id = f.id 
            WHERE fs.user_id = ?
        ");
        $stmt->execute([$userId]);
        
        while ($subscription = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subscriptions[] = $subscription;
        }
        
        return $subscriptions;
    }
    
    /**
     * Get topic subscriptions
     * 
     * @param int $userId User ID
     * @return array Array of topic subscriptions
     */
    public function getTopicSubscriptions($userId) {
        $subscriptions = [];
        
        $stmt = $this->db->prepare("
            SELECT ts.*, t.title, f.name AS forum_name, f.id AS forum_id
            FROM forum_topic_subscriptions ts 
            JOIN forum_topics t ON ts.topic_id = t.id 
            JOIN forum_forums f ON t.forum_id = f.id
            WHERE ts.user_id = ?
        ");
        $stmt->execute([$userId]);
        
        while ($subscription = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subscriptions[] = $subscription;
        }
        
        return $subscriptions;
    }
}

