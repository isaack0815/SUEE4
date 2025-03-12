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
    private $logger;
    private $user;
    
    /**
     * Constructor
     */
    public function __construct(Database $db) {
        $this->db = $db;
        $this->logger = Logger::getInstance();
        $this->user = new User();
    }
    
    /**
     * Get all categories
     * 
     * @return array Array of categories with forums
     */
    public function getCategories() {
        $categories = [];
        // Get all categories
        $result = $this->db->select("SELECT * FROM forum_categories ORDER BY sort_order");
        
        foreach ($result as $category) {
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
        $result = $this->db->select("SELECT * FROM forum_forums WHERE category_id = ? ORDER BY sort_order", [$categoryId]);
        
        foreach ($result as $forum) {
            // Get the latest topic
            $latestTopic = $this->db->selectOne("
                SELECT t.*, u.username 
                FROM forum_topics t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.forum_id = ? 
                ORDER BY t.last_post_time DESC 
                LIMIT 1
            ", [$forum['id']]);
            $forum['latest_topic'] = $latestTopic;
            
            // Get the topic and post count
            $topicCount = $this->db->selectOne("SELECT COUNT(*) as count FROM forum_topics WHERE forum_id = ?", [$forum['id']]);
            $forum['topic_count'] = $topicCount['count'];
            
            $postCount = $this->db->selectOne("
                SELECT COUNT(*) as count
                FROM forum_posts p 
                JOIN forum_topics t ON p.topic_id = t.id 
                WHERE t.forum_id = ?
            ", [$forum['id']]);
            $forum['post_count'] = $postCount['count'];
            
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
    public function getForumDetails($forumId,$userId) {
        $stmt = $this->db->query("
                    SELECT 
                        t1.*,t2.id AS is_subscribed
                    FROM 
                        forum_forums t1
                    LEFT JOIN
                        forum_forum_subscriptions t2
                    ON
                        t2.user_id = ?
                    WHERE t1.id = ?",[$userId,$forumId]);
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
            $stmt2 = $this->db->query("
                SELECT p.*, u.username 
                FROM forum_posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.topic_id = ? 
                ORDER BY p.created_at DESC 
                LIMIT 1
            ",[$topic['id']]);
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
        $stmt = $this->db->query("SELECT COUNT(*) FROM forum_topics WHERE id = ?",[$topicId]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get topic details
     * 
     * @param int $topicId Topic ID
     * @return array Topic details
     */
    public function getTopicDetails($userId,$topicId) {
        $stmt = $this->db->query("
            SELECT 
                t.*, u.username, f.name AS forum_name, f.id AS forum_id, ts.id AS is_subscribed
            FROM 
                forum_topics t 
            JOIN 
                users u ON t.user_id = u.id 
            JOIN 
                forum_forums f ON t.forum_id = f.id
            LEFT JOIN 
                forum_topic_subscriptions ts ON ts.topic_id = t.id AND ts.user_id = ?
            WHERE t.id = ?
        ",[$userId,$topicId]);
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
        $stmt = $this->db->query("
            SELECT p.*, u.username, u.profile_image, u.created_at AS user_created_at,
                   (SELECT COUNT(*) FROM forum_posts WHERE user_id = p.user_id) AS user_post_count
            FROM forum_posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.topic_id = ? 
            ORDER BY p.created_at
        ",[$topicId]);
        
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
        $stmt = $this->db->query("UPDATE forum_topics SET views = views + 1 WHERE id = ?",[$topicId]);
    }

    public function CreateForumTopic($forumId, $content, $title) {
    try {
        // Start a transaction
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            throw new Exception("User not logged in");
        }

        $currentTime = date('Y-m-d H:i:s');
        
        // Insert the topic
        $topicInsert = [
            'forum_id' => $forumId,
            'user_id' => $currentUserId,
            'title' => $title,
            'created_at' => $currentTime,
            'last_post_time' => $currentTime,
            'last_post_user_id' => $currentUserId  // Set this to the current user's ID
        ];
        $topicId = $this->db->insert("forum_topics", $topicInsert);
        
        if (!$topicId) {
            throw new Exception("Failed to insert topic");
        }

        // Insert the post
        $postInsert = [
            'topic_id' => $topicId,
            'user_id' => $currentUserId,
            'content' => $content,
            'created_at' => $currentTime
        ];
        $postId = $this->db->insert("forum_posts", $postInsert);
        
        if (!$postId) {
            throw new Exception("Failed to insert post");
        }

        // Update the forum's last post time
        $forumUpdate = [
            'last_post_time' => $currentTime,
            'last_post_user_id' => $currentUserId
        ];
        $this->db->update('forum_forums', $forumUpdate, 'id = ?', [$forumId]);
        
        // Commit the transaction
        $pdo->commit();
        
        return $topicId;
    } catch (Exception $e) {
        // Rollback the transaction
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        $logger = Logger::getInstance();
        $logger->error("Error creating forum topic: " . $e->getMessage(), 'forum');
        throw new Exception("Fehler beim Erstellen des Forum-Themas: " . $e->getMessage());
    }
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
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        
        // Insert the post
        $insert = array(
            'topic_id' => $topicId,
            'user_id' => $_SESSION['user_id'],
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        // Debug: Log the insert data
        $this->logger->debug("Attempting to insert post", 'forum', $insert);
        
        $postId = $this->db->insert("forum_posts", $insert);
        
        if (!$postId) {
            throw new Exception("Failed to insert post");
        }
        
        // Debug: Log successful insert
        $this->logger->debug("Post inserted successfully", 'forum', ['post_id' => $postId]);
        
        // Update the topic's last post time
        $updateData = [
            'last_post_time' => date('Y-m-d H:i:s'),
            'last_post_user_id' => $_SESSION['user_id']
        ];
        
        // Debug: Log the update data
        $this->logger->debug("Attempting to update topic", 'forum', ['topic_id' => $topicId, 'update_data' => $updateData]);
        
        $this->db->update('forum_topics', $updateData, 'id = ?', [$topicId]);
        
        // Debug: Log successful update
        $this->logger->debug("Topic updated successfully", 'forum', ['topic_id' => $topicId]);
        
        // Commit the transaction
        $pdo->commit();
        
        return true;
    } catch (Exception $e) {
        // Rollback the transaction
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        
        // Log the error
        $this->logger->error("Error creating post: " . $e->getMessage(), 'forum', [
            'topic_id' => $topicId,
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
        
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
        $is_moderator = false;
        if(in_array('forum_moderator',$this->user->getUserPermissions())){
            $is_moderator = true;
        };

        return $is_moderator;
    }
    
    /**
     * Set a topic's sticky status
     * 
     * @param int $topicId Topic ID
     * @param bool $sticky Sticky status
     * @return bool True on success, false on failure
     */
    public function setTopicSticky($topicId, $sticky) {
        $stmt = $this->db->query("UPDATE forum_topics SET is_sticky = ? WHERE id = ?",[$sticky ? 1 : 0, $topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Set a topic's locked status
     * 
     * @param int $topicId Topic ID
     * @param bool $locked Locked status
     * @return bool True on success, false on failure
     */
    public function setTopicLocked($topicId, $locked) {
        $stmt = $this->db->query("UPDATE forum_topics SET is_locked = ? WHERE id = ?",[$locked ? 1 : 0, $topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a post's topic ID
     * 
     * @param int $postId Post ID
     * @return int Topic ID
     */
    public function getPostTopicId($postId) {
        $stmt = $this->db->query("SELECT topic_id FROM forum_posts WHERE id = ?",[$postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Delete a post
     * 
     * @param int $postId Post ID
     * @return bool True on success, false on failure
     */
    public function deletePost($postId) {
        $stmt = $this->db->query("DELETE FROM forum_posts WHERE id = ?",[$postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get a topic's forum ID
     * 
     * @param int $topicId Topic ID
     * @return int Forum ID
     */
    public function getTopicForumId($topicId) {
        $stmt = $this->db->query("SELECT forum_id FROM forum_topics WHERE id = ?",[$topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
            $pdo = $this->db->getConnection();
            $pdo->beginTransaction();
            
            // Delete all posts in this topic
            $stmt = $this->db->query("DELETE FROM forum_posts WHERE topic_id = ?",[$topicId]);
            $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete the topic
            $stmt = $this->db->query("DELETE FROM forum_topics WHERE id = ?",[$topicId]);
            $stmt->fetch(PDO::FETCH_ASSOC);
            
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
        $stmt = $this->db->query("
            SELECT COUNT(*) 
            FROM forum_forum_subscriptions 
            WHERE user_id = ? AND forum_id = ?
        ",[$userId, $forumId]);
        
        if ($stmt->fetch(PDO::FETCH_ASSOC) > 0) {
            return true; // Already subscribed
        }
        
        // Create the subscription
        $stmt = $this->db->query("
            INSERT INTO forum_forum_subscriptions (user_id, forum_id, created_at) 
            VALUES (?, ?, NOW())
        ",[$userId, $forumId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Unsubscribe from a forum
     * 
     * @param int $userId User ID
     * @param int $forumId Forum ID
     * @return bool True on success, false on failure
     */
    public function unsubscribeFromForum($userId, $forumId) {
        $stmt = $this->db->query("
            DELETE FROM forum_forum_subscriptions 
            WHERE user_id = ? AND forum_id = ?
        ",[$userId, $forumId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        $stmt = $this->db->query("
            SELECT COUNT(*) 
            FROM forum_topic_subscriptions 
            WHERE user_id = ? AND topic_id = ?
        ",[$userId, $topicId]);
        
        if ($stmt->fetchColumn() > 0) {
            return true; // Already subscribed
        }
        
        // Create the subscription
        $stmt = $this->db->query("
            INSERT INTO forum_topic_subscriptions (user_id, topic_id, created_at) 
            VALUES (?, ?, NOW())
        ",[$userId, $topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Unsubscribe from a topic
     * 
     * @param int $userId User ID
     * @param int $topicId Topic ID
     * @return bool True on success, false on failure
     */
    public function unsubscribeFromTopic($userId, $topicId) {
        $stmt = $this->db->query("
            DELETE FROM forum_topic_subscriptions 
            WHERE user_id = ? AND topic_id = ?
        ",[$userId, $topicId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get forum subscriptions
     * 
     * @param int $userId User ID
     * @return array Array of forum subscriptions
     */
    public function getForumSubscriptions($userId) {
        $subscriptions = [];
        
        $stmt = $this->db->query("
            SELECT fs.*, f.name 
            FROM forum_forum_subscriptions fs 
            JOIN forum_forums f ON fs.forum_id = f.id 
            WHERE fs.user_id = ?
        ",[$userId]);
        
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
        
        $stmt = $this->db->query("
            SELECT ts.*, t.title, f.name AS forum_name, f.id AS forum_id
            FROM forum_topic_subscriptions ts 
            JOIN forum_topics t ON ts.topic_id = t.id 
            JOIN forum_forums f ON t.forum_id = f.id
            WHERE ts.user_id = ?
        ",[$userId]);
        
        while ($subscription = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subscriptions[] = $subscription;
        }
        
        return $subscriptions;
    }
}

