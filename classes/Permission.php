<?php
class Permission {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Alle Berechtigungen abrufen
     * 
     * @return array Liste aller Berechtigungen
     */
    public function getAllPermissions() {
        $sql = "SELECT * FROM permissions ORDER BY name";
        return $this->db->select($sql);
    }
    
    /**
     * Berechtigung anhand der ID abrufen
     * 
     * @param int $id Berechtigungs-ID
     * @return array|null Berechtigungsdaten oder null
     */
    public function getPermissionById($id) {
        $sql = "SELECT * FROM permissions WHERE id = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Berechtigung anhand des Namens abrufen
     * 
     * @param string $name Berechtigungsname
     * @return array|null Berechtigungsdaten oder null
     */
    public function getPermissionByName($name) {
        $sql = "SELECT * FROM permissions WHERE name = ?";
        return $this->db->selectOne($sql, [$name]);
    }
    
    /**
     * Neue Berechtigung erstellen
     * 
     * @param string $name Berechtigungsname
     * @param string $description Beschreibung
     * @return array Ergebnis der Operation
     */
    public function createPermission($name, $description) {
        // Prüfen, ob Berechtigungsname bereits existiert
        if ($this->permissionNameExists($name)) {
            return ['success' => false, 'message' => 'name_exists'];
        }
        
        $now = date('Y-m-d H:i:s');
        $permissionId = $this->db->insert('permissions', [
            'name' => $name,
            'description' => $description,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        
        if ($permissionId) {
            return ['success' => true, 'permission_id' => $permissionId];
        } else {
            return ['success' => false, 'message' => 'error_occurred'];
        }
    }
    
    /**
     * Berechtigung aktualisieren
     * 
     * @param int $id Berechtigungs-ID
     * @param string $name Berechtigungsname
     * @param string $description Beschreibung
     * @return array Ergebnis der Operation
     */
    public function updatePermission($id, $name, $description) {
        // Prüfen, ob Berechtigungsname bereits existiert (außer bei der aktuellen Berechtigung)
        if ($this->permissionNameExists($name, $id)) {
            return ['success' => false, 'message' => 'name_exists'];
        }
        
        $now = date('Y-m-d H:i:s');
        $this->db->update('permissions', 
            [
                'name' => $name,
                'description' => $description,
                'updated_at' => $now
            ],
            'id = ?',
            [$id]
        );
        
        return ['success' => true];
    }
    
    /**
     * Berechtigung löschen
     * 
     * @param int $id Berechtigungs-ID
     * @return array Ergebnis der Operation
     */
    public function deletePermission($id) {
        // Prüfen, ob Berechtigung existiert
        $permission = $this->getPermissionById($id);
        if (!$permission) {
            return ['success' => false, 'message' => 'permission_not_found'];
        }
        
        // Berechtigung löschen
        $this->db->query("DELETE FROM permissions WHERE id = ?", [$id]);
        
        return ['success' => true];
    }
    
    /**
     * Berechtigungen einer Gruppe abrufen
     * 
     * @param int $groupId Gruppen-ID
     * @return array Liste der Berechtigungen
     */
    public function getGroupPermissions($groupId) {
        $sql = "SELECT p.* FROM permissions p
                JOIN group_permissions gp ON p.id = gp.permission_id
                WHERE gp.group_id = ?
                ORDER BY p.name";
        return $this->db->select($sql, [$groupId]);
    }
    
    /**
     * Berechtigungen einer Gruppe zuweisen
     * 
     * @param int $groupId Gruppen-ID
     * @param array $permissionIds Liste der Berechtigungs-IDs
     * @return array Ergebnis der Operation
     */
    public function assignGroupPermissions($groupId, $permissionIds) {
        // Bestehende Berechtigungen löschen
        $this->db->query("DELETE FROM group_permissions WHERE group_id = ?", [$groupId]);
        
        // Neue Berechtigungen zuweisen
        foreach ($permissionIds as $permId) {
            $this->db->insert('group_permissions', [
                'group_id' => $groupId,
                'permission_id' => $permId
            ]);
        }
        
        return ['success' => true];
    }
    
    /**
     * Prüfen, ob ein Berechtigungsname bereits existiert
     * 
     * @param string $name Berechtigungsname
     * @param int|null $excludeId ID der zu ignorierenden Berechtigung
     * @return bool True, wenn der Name bereits existiert
     */
    private function permissionNameExists($name, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM permissions WHERE name = ? AND id != ?";
            $existing = $this->db->selectOne($sql, [$name, $excludeId]);
        } else {
            $sql = "SELECT * FROM permissions WHERE name = ?";
            $existing = $this->db->selectOne($sql, [$name]);
        }
        
        return $existing ? true : false;
    }
}
?>

