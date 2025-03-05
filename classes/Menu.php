<?php
class Menu {
    private $db;
    private $user;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->user = new User();
    }
    
    public function getMenuItems($parentId = null) {
        $userGroups = [];
        
        // Wenn Benutzer eingeloggt ist, Benutzergruppen abrufen
        if ($this->user->isLoggedIn()) {
            $userId = $this->user->getCurrentUser()['id'];
            $group = new Group();
            $userGroups = array_column($group->getUserGroups($userId), 'id');
        }
        
        $params = [];
        $whereClause = "is_active = 1";
        
        if ($parentId === null) {
            $whereClause .= " AND parent_id IS NULL";
        } else {
            $whereClause .= " AND parent_id = ?";
            $params[] = $parentId;
        }
        
        $sql = "SELECT * FROM menu_items WHERE {$whereClause} ORDER BY sort_order";
        $menuItems = $this->db->select($sql, $params);
        
        // Menüeinträge filtern, die eine bestimmte Gruppe erfordern
        $filteredItems = [];
        foreach ($menuItems as $item) {
            if ($item['required_group_id'] === null || in_array($item['required_group_id'], $userGroups)) {
                // Untermenüs abrufen
                $item['children'] = $this->getMenuItems($item['id']);
                $filteredItems[] = $item;
            }
        }
        
        return $filteredItems;
    }
    
    public function getAllMenuItems() {
        $sql = "SELECT m.*, p.name as parent_name, g.name as required_group 
                FROM menu_items m
                LEFT JOIN menu_items p ON m.parent_id = p.id
                LEFT JOIN user_groups g ON m.required_group_id = g.id
                ORDER BY m.sort_order";
        return $this->db->select($sql);
    }
    
    public function getMenuItem($id) {
        $sql = "SELECT * FROM menu_items WHERE id = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    public function createMenuItem($data) {
        $menuId = $this->db->insert('menu_items', [
            'parent_id' => $data['parent_id'] ?: null,
            'name' => $data['name'],
            'description' => $data['description'] ?: null,
            'url' => $data['url'],
            'icon' => $data['icon'] ?: null,
            'sort_order' => $data['sort_order'] ?: 0,
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'required_group_id' => $data['required_group_id'] ?: null
        ]);
        
        if ($menuId) {
            return ['success' => true, 'menu_id' => $menuId];
        } else {
            return ['success' => false, 'message' => 'error_occurred'];
        }
    }
    
    public function updateMenuItem($id, $data) {
        $this->db->update('menu_items', 
            [
                'parent_id' => $data['parent_id'] ?: null,
                'name' => $data['name'],
                'description' => $data['description'] ?: null,
                'url' => $data['url'],
                'icon' => $data['icon'] ?: null,
                'sort_order' => $data['sort_order'] ?: 0,
                'is_active' => isset($data['is_active']) ? 1 : 0,
                'required_group_id' => $data['required_group_id'] ?: null
            ],
            'id = ?',
            [$id]
        );
        
        return ['success' => true];
    }
    
    public function deleteMenuItem($id) {
        // Prüfen, ob Menüeintrag existiert
        $menuItem = $this->getMenuItem($id);
        if (!$menuItem) {
            return ['success' => false, 'message' => 'menu_item_not_found'];
        }
        
        // Untermenüs auf NULL setzen
        $this->db->update('menu_items', 
            ['parent_id' => null],
            'parent_id = ?',
            [$id]
        );
        
        // Menüeintrag löschen
        $this->db->query("DELETE FROM menu_items WHERE id = ?", [$id]);
        
        return ['success' => true];
    }
}
