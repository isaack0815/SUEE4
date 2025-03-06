<?php
class CMS {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Alle CMS-Seiten abrufen
     * 
     * @param string $status Optional: Nur Seiten mit einem bestimmten Status abrufen
     * @param int $limit Optional: Anzahl der Ergebnisse begrenzen
     * @param int $offset Optional: Offset für Paginierung
     * @return array Liste der CMS-Seiten
     */
    public function getAllPages($status = null, $limit = null, $offset = null) {
        $params = [];
        $whereClause = "";
        
        if ($status !== null) {
            $whereClause = "WHERE status = ?";
            $params[] = $status;
        }
        
        $limitClause = "";
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)($offset ?? 0);
            $limitClause = "LIMIT $limit OFFSET $offset";
        }
        
        $sql = "SELECT p.*, 
                creator.username as created_by_username,
                updater.username as updated_by_username
                FROM cms_pages p
                LEFT JOIN users creator ON p.created_by = creator.id
                LEFT JOIN users updater ON p.updated_by = updater.id
                $whereClause
                ORDER BY p.updated_at DESC
                $limitClause";
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Anzahl der CMS-Seiten zählen
     * 
     * @param string $status Optional: Nur Seiten mit einem bestimmten Status zählen
     * @return int Anzahl der CMS-Seiten
     */
    public function countPages($status = null) {
        $params = [];
        $whereClause = "";
        
        if ($status !== null) {
            $whereClause = "WHERE status = ?";
            $params[] = $status;
        }
        
        $sql = "SELECT COUNT(*) as count FROM cms_pages $whereClause";
        $result = $this->db->selectOne($sql, $params);
        
        return $result['count'];
    }
    
    /**
     * CMS-Seite anhand der ID abrufen
     * 
     * @param int $id Seiten-ID
     * @return array|null Seitendaten oder null
     */
    public function getPageById($id) {
        $sql = "SELECT * FROM cms_pages WHERE id = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * CMS-Seite anhand des Slugs abrufen
     * 
     * @param string $slug Seiten-Slug
     * @return array|null Seitendaten oder null
     */
    public function getPageBySlug($slug) {
        $sql = "SELECT * FROM cms_pages WHERE slug = ?";
        return $this->db->selectOne($sql, [$slug]);
    }
    
    /**
     * Neue CMS-Seite erstellen
     * 
     * @param array $data Seitendaten
     * @param int $userId ID des erstellenden Benutzers
     * @return array Ergebnis der Operation
     */
    public function createPage($data, $userId) {
        // Slug generieren, wenn nicht angegeben
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        } else {
            $data['slug'] = $this->sanitizeSlug($data['slug']);
        }
        
        // Prüfen, ob Slug bereits existiert
        if ($this->slugExists($data['slug'])) {
            return ['success' => false, 'message' => 'slug_exists'];
        }
        
        $now = date('Y-m-d H:i:s');
        $pageId = $this->db->insert('cms_pages', [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
            'meta_keywords' => $data['meta_keywords'] ?? '',
            'status' => $data['status'] ?? 'draft',
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        
        if ($pageId) {
            return ['success' => true, 'page_id' => $pageId, 'slug' => $data['slug']];
        } else {
            return ['success' => false, 'message' => 'error_occurred'];
        }
    }
    
    /**
     * CMS-Seite aktualisieren
     * 
     * @param int $id Seiten-ID
     * @param array $data Seitendaten
     * @param int $userId ID des aktualisierenden Benutzers
     * @return array Ergebnis der Operation
     */
    public function updatePage($id, $data, $userId) {
        // Bestehende Seite abrufen
        $page = $this->getPageById($id);
        if (!$page) {
            return ['success' => false, 'message' => 'page_not_found'];
        }
        
        // Slug aktualisieren, wenn angegeben
        if (isset($data['slug']) && $data['slug'] !== $page['slug']) {
            $data['slug'] = $this->sanitizeSlug($data['slug']);
            
            // Prüfen, ob Slug bereits existiert
            if ($this->slugExists($data['slug'], $id)) {
                return ['success' => false, 'message' => 'slug_exists'];
            }
        }
        
        $updateData = [
            'updated_by' => $userId,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Nur angegebene Felder aktualisieren
        $fields = ['title', 'slug', 'content', 'meta_description', 'meta_keywords', 'status'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        $this->db->update('cms_pages', $updateData, 'id = ?', [$id]);
        
        return ['success' => true, 'slug' => $data['slug'] ?? $page['slug']];
    }
    
    /**
     * CMS-Seite löschen
     * 
     * @param int $id Seiten-ID
     * @return array Ergebnis der Operation
     */
    public function deletePage($id) {
        // Prüfen, ob Seite existiert
        $page = $this->getPageById($id);
        if (!$page) {
            return ['success' => false, 'message' => 'page_not_found'];
        }
        
        // Seite löschen
        $this->db->query("DELETE FROM cms_pages WHERE id = ?", [$id]);
        
        return ['success' => true];
    }
    
    /**
     * Slug aus Titel generieren
     * 
     * @param string $title Seitentitel
     * @return string Generierter Slug
     */
    private function generateSlug($title) {
        // Umlaute und Sonderzeichen ersetzen
        $slug = $title;
        $slug = str_replace(['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'], ['ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss'], $slug);
        
        // Nur erlaubte Zeichen behalten und in Kleinbuchstaben umwandeln
        $slug = preg_replace('/[^a-zA-Z0-9\s-]/', '', $slug);
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Eindeutigen Slug sicherstellen
        $baseSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Slug bereinigen
     * 
     * @param string $slug Zu bereinigender Slug
     * @return string Bereinigter Slug
     */
    private function sanitizeSlug($slug) {
        // Umlaute und Sonderzeichen ersetzen
        $slug = str_replace(['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'], ['ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss'], $slug);
        
        // Nur erlaubte Zeichen behalten und in Kleinbuchstaben umwandeln
        $slug = preg_replace('/[^a-zA-Z0-9\s-]/', '', $slug);
        $slug = strtolower(trim($slug));
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        return $slug;
    }
    
    /**
     * Prüfen, ob ein Slug bereits existiert
     * 
     * @param string $slug Zu prüfender Slug
     * @param int|null $excludeId ID der zu ignorierenden Seite
     * @return bool True, wenn der Slug bereits existiert
     */
    private function slugExists($slug, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM cms_pages WHERE slug = ? AND id != ?";
            $existing = $this->db->selectOne($sql, [$slug, $excludeId]);
        } else {
            $sql = "SELECT * FROM cms_pages WHERE slug = ?";
            $existing = $this->db->selectOne($sql, [$slug]);
        }
        
        return $existing ? true : false;
    }
}
?>

