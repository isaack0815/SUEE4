<?php
class Group {
   private $db;
   
   public function __construct() {
       $this->db = Database::getInstance();
   }
   
   /**
    * Alle Gruppen abrufen
    * 
    * @return array Liste aller Gruppen
    */
   public function getAllGroups() {
       $sql = "SELECT * FROM user_groups ORDER BY name";
       return $this->db->select($sql);
   }
   
   /**
    * Gruppe anhand der ID abrufen
    * 
    * @param int $id Gruppen-ID
    * @return array|null Gruppendaten oder null
    */
   public function getGroupById($id) {
       $sql = "SELECT * FROM user_groups WHERE id = ?";
       return $this->db->selectOne($sql, [$id]);
   }
   
   /**
    * Neue Gruppe erstellen
    * 
    * @param string $name Gruppenname
    * @param string $description Beschreibung
    * @return array Ergebnis der Operation
    */
   public function createGroup($name, $description) {
       // Prüfen, ob Gruppenname bereits existiert
       if ($this->groupNameExists($name)) {
           return ['success' => false, 'message' => 'name_exists'];
       }
       
       $now = date('Y-m-d H:i:s');
       $groupId = $this->db->insert('user_groups', [
           'name' => $name,
           'description' => $description,
           'created_at' => $now,
           'updated_at' => $now
       ]);
       
       if ($groupId) {
           return ['success' => true, 'group_id' => $groupId];
       } else {
           return ['success' => false, 'message' => 'error_occurred'];
       }
   }
   
   /**
    * Gruppe aktualisieren
    * 
    * @param int $id Gruppen-ID
    * @param string $name Gruppenname
    * @param string $description Beschreibung
    * @return array Ergebnis der Operation
    */
   public function updateGroup($id, $name, $description) {
       // Prüfen, ob Gruppenname bereits existiert (außer bei der aktuellen Gruppe)
       if ($this->groupNameExists($name, $id)) {
           return ['success' => false, 'message' => 'name_exists'];
       }
       
       $now = date('Y-m-d H:i:s');
       $this->db->update('user_groups', 
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
    * Gruppe löschen
    * 
    * @param int $id Gruppen-ID
    * @return array Ergebnis der Operation
    */
   public function deleteGroup($id) {
       // Prüfen, ob Gruppe existiert
       $group = $this->getGroupById($id);
       if (!$group) {
           return ['success' => false, 'message' => 'group_not_found'];
       }
       
       // Gruppe löschen
       $this->db->query("DELETE FROM user_groups WHERE id = ?", [$id]);
       
       return ['success' => true];
   }
   
   /**
    * Benutzer einer Gruppe hinzufügen
    * 
    * @param int $userId Benutzer-ID
    * @param int $groupId Gruppen-ID
    * @return array Ergebnis der Operation
    */
   public function addUserToGroup($userId, $groupId) {
       // Prüfen, ob Benutzer bereits in der Gruppe ist
       $sql = "SELECT * FROM user_group_members WHERE user_id = ? AND group_id = ?";
       $existing = $this->db->selectOne($sql, [$userId, $groupId]);
       
       if ($existing) {
           return ['success' => true]; // Benutzer ist bereits in der Gruppe
       }
       
       // Benutzer zur Gruppe hinzufügen
       $this->db->insert('user_group_members', [
           'user_id' => $userId,
           'group_id' => $groupId
       ]);
       
       return ['success' => true];
   }
   
   /**
    * Benutzer aus einer Gruppe entfernen
    * 
    * @param int $userId Benutzer-ID
    * @param int $groupId Gruppen-ID
    * @return array Ergebnis der Operation
    */
   public function removeUserFromGroup($userId, $groupId) {
       $this->db->query("DELETE FROM user_group_members WHERE user_id = ? AND group_id = ?", [$userId, $groupId]);
       return ['success' => true];
   }
   
   /**
    * Benutzer aus allen Gruppen entfernen
    * 
    * @param int $userId Benutzer-ID
    * @return array Ergebnis der Operation
    */
   public function removeUserFromAllGroups($userId) {
       $this->db->query("DELETE FROM user_group_members WHERE user_id = ?", [$userId]);
       return ['success' => true];
   }
   
   /**
    * Gruppen eines Benutzers abrufen
    * 
    * @param int $userId Benutzer-ID
    * @return array Liste der Gruppen des Benutzers
    */
   public function getUserGroups($userId) {
       $sql = "SELECT g.* FROM user_groups g
               JOIN user_group_members m ON g.id = m.group_id
               WHERE m.user_id = ?
               ORDER BY g.name";
       return $this->db->select($sql, [$userId]);
   }
   
   /**
    * Benutzer einer Gruppe abrufen
    * 
    * @param int $groupId Gruppen-ID
    * @return array Liste der Benutzer in der Gruppe
    */
   public function getGroupUsers($groupId) {
       $sql = "SELECT u.* FROM users u
               JOIN user_group_members m ON u.id = m.user_id
               WHERE m.group_id = ?
               ORDER BY u.username";
       return $this->db->select($sql, [$groupId]);
   }
   
   /**
    * Prüfen, ob ein Gruppenname bereits existiert
    * 
    * @param string $name Gruppenname
    * @param int|null $excludeId ID der zu ignorierenden Gruppe
    * @return bool True, wenn der Name bereits existiert
    */
   private function groupNameExists($name, $excludeId = null) {
       if ($excludeId) {
           $sql = "SELECT * FROM user_groups WHERE name = ? AND id != ?";
           $existing = $this->db->selectOne($sql, [$name, $excludeId]);
       } else {
           $sql = "SELECT * FROM user_groups WHERE name = ?";
           $existing = $this->db->selectOne($sql, [$name]);
       }
       
       return $existing ? true : false;
   }
}
?>

