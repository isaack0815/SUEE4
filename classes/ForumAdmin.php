<?php
/**
 * Forum Admin Class
 * 
 * This class handles all forum administration functions.
 */
class ForumAdmin {
    /**
     * Database connection
     */
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Get all categories
     * 
     * @return array Array of categories
     */
    public function getCategories() {
        $categories = [];
        
        $stmt = $this->db->prepare("SELECT * FROM forum_categories ORDER BY sort_order");
        $stmt->execute();
        
        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $category;
        }
        
        return $categories;
    }
    
    /**
     * Get category details
     * 
     * @param int $categoryId Category ID
     * @return array Category details
     */
    public function getCategoryDetails($categoryId) {
        $stmt = $this->db->prepare("SELECT * FROM forum_categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create a new category
     * 
     * @param string $name Category name
     * @param string $description Category description
     * @param int $sortOrder Sort order
     * @return bool True on success, false on failure
     */
    public function createCategory($name, $description, $sortOrder) {
        $stmt = $this->db->prepare("
            INSERT INTO forum_categories (name, description, sort_order, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$name, $description, $sortOrder]);
    }
    
    /**
     * Update a category
     * 
     * @param int $categoryId Category ID
     * @param string $name Category name
     * @param string $description Category description
     * @param int $sortOrder Sort order
     * @return bool True on success, false on failure
     */
    public function updateCategory($categoryId, $name, $description, $sortOrder) {
        $stmt = $this->db->prepare("
            UPDATE forum_categories 
            SET name = ?, 
                description = ?, 
                sort_order = ?, 
                updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $sortOrder, $categoryId]);
    }
    
    /**
     * Delete a category
     * 
     * @param int $categoryId Category ID
     * @return bool True on success, false on failure
     */
    public function deleteCategory($categoryId) {
        try {
            // Start a transaction
            $this->db->beginTransaction();
            
            // Get all forums in this category
            $stmt = $this->db->prepare("SELECT id FROM forum_forums WHERE category_id = ?");
            $stmt->execute([$categoryId]);
            $forums = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Delete all topics and posts in these forums
            foreach ($forums as $forumId) {
                // Get all topics in this forum
                $stmt = $this->db->prepare("SELECT id FROM forum_topics WHERE forum_id = ?");
                $stmt->execute([$forumId]);
                $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Delete all posts in these topics
                foreach ($topics as $topicId) {
                    $stmt = $this->db->prepare("DELETE FROM forum_posts WHERE topic_id = ?");
                    $stmt->execute([$topicId]);
                }
                
                // Delete all topics in this forum
                $stmt = $this->db->prepare("DELETE FROM forum_topics WHERE forum_id = ?");
                $stmt->execute([$forumId]);
                
                // Delete all forum subscriptions
                $stmt = $this->db->prepare("DELETE FROM forum_forum_subscriptions WHERE forum_id = ?");
                $stmt->execute([$forumId]);
            }
            
            // Delete all forums in this category
            $stmt = $this->db->prepare("DELETE FROM forum_forums WHERE category_id = ?");
            $stmt->execute([$categoryId]);
            
            // Delete the category
            $stmt = $this->db->prepare("DELETE FROM forum_categories WHERE id = ?");
            $stmt->execute([$categoryId]);
            
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
     * Get all forums
     * 
     * @return array Array of forums
     */
    public function getForums() {
        $forums = [];
        
        $stmt = $this->db->prepare("
            SELECT f.*, c.name AS category_name 
            FROM forum_forums f 
            JOIN forum_categories c ON f.category_id = c.id 
            ORDER BY c.sort_order, f.sort_order
        ");
        $stmt->execute();
        
        while ($forum = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $forums[] = $forum;
        }
        
        return $forums;
    }
    
    /**
     * Get forum details
     * 
     * @param int $forumId Forum ID
     * @return array Forum details
     */
    public function getForumDetails($forumId) {
        $stmt = $this->db->prepare("SELECT * FROM forum_forums WHERE id = ?");
        $stmt->execute([$forumId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Create a new forum
     * 
     * @param int $categoryId Category ID
     * @param string $name Forum name
     * @param string $description Forum description
     * @param int $sortOrder Sort order
     * @return bool True on success, false on failure
     */
    public function createForum($categoryId, $name, $description, $sortOrder) {
        $stmt = $this->db->prepare("
            INSERT INTO forum_forums (category_id, name, description, sort_order, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$categoryId, $name, $description, $sortOrder]);
    }
    
    /**
     * Update a forum
     * 
     * @param int $forumId Forum ID
     * @param int $categoryId Category ID
     * @param string $name Forum name
     * @param string $description Forum description
     * @param int $sortOrder Sort order
     * @return bool True on success, false on failure
     */
    public function updateForum($forumId, $categoryId, $name, $description, $sortOrder) {
        $stmt = $this->db->prepare("
            UPDATE forum_forums 
            SET category_id = ?, 
                name = ?, 
                description = ?, 
                sort_order = ?, 
                updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$categoryId, $name, $description, $sortOrder, $forumId]);
    }
    
    /**
     * Delete a forum
     * 
     * @param int $forumId Forum ID
     * @return bool True on success, false on failure
     */
    public function deleteForum($forumId) {
        try {
            // Start a transaction
            $this->db->beginTransaction();
            
            // Get all topics in this forum
            $stmt = $this->db->prepare("SELECT id FROM forum_topics WHERE forum_id = ?");
            $stmt->execute([$forumId]);
            $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Delete all posts in these topics
            foreach ($topics as $topicId) {
                $stmt = $this->db->prepare("DELETE FROM forum_posts WHERE topic_id = ?");
                $stmt->execute([$topicId]);
                
                // Delete all topic subscriptions
                $stmt = $this->db->prepare("DELETE FROM forum_topic_subscriptions WHERE topic_id = ?");
                $stmt->execute([$topicId]);
            }
            
            // Delete all topics in this forum
            $stmt = $this->db->prepare("DELETE FROM forum_topics WHERE forum_id = ?");
            $stmt->execute([$forumId]);
            
            // Delete all forum subscriptions
            $stmt = $this->db->prepare("DELETE FROM forum_forum_subscriptions WHERE forum_id = ?");
            $stmt->execute([$forumId]);
            
            // Delete the forum
            $stmt = $this->db->prepare("DELETE FROM forum_forums WHERE id = ?");
            $stmt->execute([$forumId]);
            
            // Commit the transaction
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->rollBack();
            return false;
        }
    }
}

