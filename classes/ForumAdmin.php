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
        
        $stmt = $this->db->query("SELECT * FROM forum_categories ORDER BY sort_order");
        
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
        $stmt = $this->db->query("SELECT * FROM forum_categories WHERE id = ?",[$categoryId]);
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
        $data = array(
            'name' => $name,
            'description' => $description,
            'sort_order' => $sortOrder,
            'created_at' => date('Y-m-d H:i:s')
        );
        return $this->db->insert('forum_categories', $data);
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
        //Example Update $stmt = $this->db->update($table, $data, $where, $whereParams = [])
        $stmt = $this->db->update('forum_categories', ['name' => $name, 'description' => $description, 'sort_order' => $sortOrder], 'id = ?', [$categoryId]);
        return $stmt;
    }
    
    /**
     * Delete a category
     * 
     * @param int $categoryId Category ID
     * @return bool True on success, false on failure
     */
    public function deleteCategory($categoryId) {
        try {
            
            // Get all forums in this category
            $stmt = $this->db->query("SELECT id FROM forum_forums WHERE category_id = ?",[$categoryId]);
            $forums = $stmt->fetchAll(PDO::FETCH_COLUMN);
            print_r($forums);
            echo '<br>';
            // Delete all topics and posts in these forums
            foreach ($forums as $forumId) {
                echo 'Forum ID'.$forumId;
                echo '<br>';
                // Get all topics in this forum
                $stmt = $this->db->query("SELECT id FROM forum_topics WHERE forum_id = ?",[$forumId]);
                $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Delete all posts in these topics
                foreach ($topics as $topicId) {
                    $this->db->delete('forum_posts', "topic_id = {$topicId}");
                }
                
                // Delete all topics in this forum
                $this->db->delete('forum_topics', "forum_id = {$forumId}");
                
                // Delete all forum subscriptions
                $this->db->delete('forum_forum_subscriptions', "forum_id = {$forumId}");
            }
            echo 'Cat id:'.$categoryId;
            echo '<br>';
            // Delete all forums in this category
            $this->db->delete('forum_forums', "category_id = {$categoryId}");
            
            // Delete the category
            $this->db->delete('forum_categories', "id = {$categoryId}");
            
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
        
        $stmt = $this->db->query("
            SELECT f.*, c.name AS category_name 
            FROM forum_forums f 
            JOIN forum_categories c ON f.category_id = c.id 
            ORDER BY c.sort_order, f.sort_order
        ");
        
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
        $stmt = $this->db->query("SELECT * FROM forum_forums WHERE id = ?",[$forumId]);
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
        $data = array(
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'sort_order' => $sortOrder,
            'created_at' => date('Y-m-d H:i:s')
        );
        return $this->db->insert('forum_forums', $data);
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
        //Example Update $stmt = $this->db->update($table, $data, $where, $whereParams = [])
        $stmt = $this->db->update('forum_forums', ['category_id' => $categoryId, 'name' => $name, 'description' => $description, 'sort_order' => $sortOrder], 'id = ?', [$forumId]);
        return $stmt;
    }
    
    /**
     * Delete a forum
     * 
     * @param int $forumId Forum ID
     * @return bool True on success, false on failure
     */
    public function deleteForum($forumId) {
        try {
            
            // Get all topics in this forum
            echo 'Forumid:'.$forumId;
            $stmt = $this->db->query("SELECT id FROM forum_topics WHERE forum_id = ?",[$forumId]);
            $topics = $stmt->fetchAll(PDO::FETCH_COLUMN);
            // Delete all posts in these topics
            foreach ($topics as $topicId) {
                //Delete Exemple
                $this->db->delete('forum_posts', ['topic_id' => $topicId]);
                // Delete all topic subscriptions
                $this->db->delete('forum_topic_subscriptions', ['topic_id' => $topicId]);
            }
            
            // Delete all topics in this forum
            $this->db->delete('forum_topics', ['forum_id' => $forumId]);
            
            // Delete all forum subscriptions
            $this->db->delete('forum_forum_subscriptions', ['forum_id' => $forumId]);
            
            // Delete the forum
            $this->db->delete('forum_forums', ['id' => $forumId]);
            
            return true;
        } catch (Exception $e) {
            // Rollback the transaction
            $this->db->rollBack();
            return false;
        }
    }
}

