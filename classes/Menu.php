<?php
class Menu {
   private $db;
   private $user;
   
   public function __construct() {
       $this->db = Database::getInstance();
       $this->user = new User();
   }
   
   /**
    * Menüpunkte für einen bestimmten Bereich abrufen
    * 
    * @param string $area Menübereich ('main', 'admin', 'user')
    * @param int|null $parentId ID des übergeordneten Menüpunkts oder NULL für Hauptmenüpunkte
    * @return array Liste der Menüpunkte
    */
   public function getMenuItems($area = 'main', $parentId = null) {
       $userGroups = [];
       $userPermissions = [];
       
       // Wenn Benutzer eingeloggt ist, Benutzergruppen und -berechtigungen abrufen
       if ($this->user->isLoggedIn()) {
           $userGroups = $this->user->getUserGroups();
           $userPermissions = $this->user->getUserPermissions();
       }
       
       $params = [$area];
       $whereClause = "m.area = ? AND m.is_active = 1";
       
       if ($parentId === null) {
           $whereClause .= " AND m.parent_id IS NULL";
       } else {
           $whereClause .= " AND m.parent_id = ?";
           $params[] = $parentId;
       }
       
       $sql = "SELECT m.*, p.name as required_permission FROM menu_items m
               LEFT JOIN permissions p ON m.required_permission_id = p.id
               WHERE {$whereClause} ORDER BY m.sort_order";
       $menuItems = $this->db->select($sql, $params);
       
       // Menüeinträge filtern, die eine bestimmte Gruppe oder Berechtigung erfordern
       $filteredItems = [];
       foreach ($menuItems as $item) {
           $hasAccess = true;
           
           // Prüfen, ob eine bestimmte Gruppe erforderlich ist
           if ($item['required_group_id'] !== null) {
               $hasAccess = false;
               foreach ($userGroups as $group) {
                   if ($group['id'] == $item['required_group_id']) {
                       $hasAccess = true;
                       break;
                   }
               }
           }
           
           // Prüfen, ob eine bestimmte Berechtigung erforderlich ist
           if ($hasAccess && $item['required_permission'] !== null) {
               $hasAccess = in_array($item['required_permission'], $userPermissions);
           }
           
           if ($hasAccess) {
               // Untermenüs abrufen
               $item['children'] = $this->getMenuItems($area, $item['id']);
               $filteredItems[] = $item;
           }
       }
       
       return $filteredItems;
   }
   
   /**
    * Alle Menüpunkte für die Verwaltung abrufen
    * 
    * @param string $area Optional: Nur Menüpunkte eines bestimmten Bereichs abrufen
    * @return array Liste aller Menüpunkte
    */
   public function getAllMenuItems($area = null) {
       $params = [];
       $whereClause = "";
       
       if ($area !== null) {
           $whereClause = "WHERE m.area = ?";
           $params[] = $area;
       }
       
       $sql = "SELECT m.*, p.name as parent_name, g.name as required_group, 
               perm.name as required_permission 
               FROM menu_items m
               LEFT JOIN menu_items p ON m.parent_id = p.id
               LEFT JOIN user_groups g ON m.required_group_id = g.id
               LEFT JOIN permissions perm ON m.required_permission_id = perm.id
               {$whereClause}
               ORDER BY m.area, m.sort_order";
       return $this->db->select($sql, $params);
   }
   
   /**
    * Menüpunkt anhand der ID abrufen
    * 
    * @param int $id Menüpunkt-ID
    * @return array|null Menüpunktdaten oder null
    */
   public function getMenuItem($id) {
       $sql = "SELECT * FROM menu_items WHERE id = ?";
       return $this->db->selectOne($sql, [$id]);
   }
   
   /**
    * Neuen Menüpunkt erstellen
    * 
    * @param array $data Menüpunktdaten
    * @return array Ergebnis der Operation
    */
   public function createMenuItem($data) {
       $menuId = $this->db->insert('menu_items', [
           'area' => $data['area'],
           'parent_id' => $data['parent_id'] ?: null,
           'name' => $data['name'],
           'description' => $data['description'] ?: null,
           'module' => $data['module'] ?: null,
           'url' => $data['url'],
           'icon' => $data['icon'] ?: null,
           'sort_order' => $data['sort_order'] ?: 0,
           'is_active' => isset($data['is_active']) ? 1 : 0,
           'required_group_id' => $data['required_group_id'] ?: null,
           'required_permission_id' => $data['required_permission_id'] ?: null
       ]);
       
       if ($menuId) {
           return ['success' => true, 'menu_id' => $menuId];
       } else {
           return ['success' => false, 'message' => 'error_occurred'];
       }
   }
   
   /**
    * Menüpunkt aktualisieren
    * 
    * @param int $id Menüpunkt-ID
    * @param array $data Menüpunktdaten
    * @return array Ergebnis der Operation
    */
   public function updateMenuItem($id, $data) {
       $this->db->update('menu_items', 
           [
               'area' => $data['area'],
               'parent_id' => $data['parent_id'] ?: null,
               'name' => $data['name'],
               'description' => $data['description'] ?: null,
               'module' => $data['module'] ?: null,
               'url' => $data['url'],
               'icon' => $data['icon'] ?: null,
               'sort_order' => $data['sort_order'] ?: 0,
               'is_active' => isset($data['is_active']) ? 1 : 0,
               'required_group_id' => $data['required_group_id'] ?: null,
               'required_permission_id' => $data['required_permission_id'] ?: null
           ],
           'id = ?',
           [$id]
       );
       
       return ['success' => true];
   }
   
   /**
    * Menüpunkt löschen
    * 
    * @param int $id Menüpunkt-ID
    * @return array Ergebnis der Operation
    */
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
   
   /**
    * Übergeordnete Menüpunkte für einen bestimmten Bereich abrufen
    * 
    * @param string $area Menübereich
    * @return array Liste der übergeordneten Menüpunkte
    */
   public function getParentMenuItems($area) {
       $sql = "SELECT id, name FROM menu_items WHERE area = ? AND parent_id IS NULL ORDER BY sort_order";
       return $this->db->select($sql, [$area]);
   }
   
   /**
    * Aktiven Menüpunkt basierend auf der aktuellen URL ermitteln
    * 
    * @param array $menuItems Menüpunkte
    * @param string $currentUrl Aktuelle URL
    * @return array Aktiver Menüpunkt und seine übergeordneten Menüpunkte
    */
   public function getActiveMenuItem($menuItems, $currentUrl) {
       $active = [];
       $this->findActiveMenuItem($menuItems, $currentUrl, $active);
       return $active;
   }
   
   /**
    * Rekursiv den aktiven Menüpunkt suchen
    * 
    * @param array $menuItems Menüpunkte
    * @param string $currentUrl Aktuelle URL
    * @param array &$active Referenz auf das Ergebnis-Array
    * @param array $parents Übergeordnete Menüpunkte
    * @return bool True, wenn der aktive Menüpunkt gefunden wurde
    */
   private function findActiveMenuItem($menuItems, $currentUrl, &$active, $parents = []) {
       foreach ($menuItems as $item) {
           $itemUrl = $item['url'];
           $itemParents = array_merge($parents, [$item]);
           
           // Prüfen, ob dieser Menüpunkt aktiv ist
           if ($itemUrl == $currentUrl || 
               (strpos($currentUrl, $itemUrl) === 0 && $itemUrl != '#' && $itemUrl != '')) {
               $active = [
                   'item' => $item,
                   'parents' => $parents
               ];
               return true;
           }
           
           // Untermenüs prüfen
           if (!empty($item['children'])) {
               if ($this->findActiveMenuItem($item['children'], $currentUrl, $active, $itemParents)) {
                   return true;
               }
           }
       }
       
       return false;
   }
}