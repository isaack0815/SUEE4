<?php
class User {
 private $db;
 private $userData = null;
 private $userGroups = null;
 private $userPermissions = null;
 
 public function __construct() {
     $this->db = Database::getInstance();
     
     // Session wird bereits in init.php gestartet, daher hier nicht nochmal starten
     
     // Prüfen, ob Benutzer bereits eingeloggt ist
     if (isset($_SESSION['user_id'])) {
         $this->userData = $this->getUserById($_SESSION['user_id']);
         
         // Benutzergruppen und Berechtigungen laden
         if ($this->userData) {
             $this->loadUserGroups();
             $this->loadUserPermissions();
         }
     }
     
     // Session-Timeout prüfen
     $this->checkSessionTimeout();
 }
 
 public function login($username, $password) {
     $sql = "SELECT * FROM users WHERE username = ?";
     $user = $this->db->selectOne($sql, [$username]);
     
     if ($user && password_verify($password, $user['password'])) {
         // Login erfolgreich
         $_SESSION['user_id'] = $user['id'];
         $_SESSION['last_activity'] = time();
         $this->userData = $user;
         
         // Benutzergruppen und Berechtigungen laden
         $this->loadUserGroups();
         $this->loadUserPermissions();
         
         // Login-Zeit aktualisieren
         $this->db->update('users', 
             ['last_login' => date('Y-m-d H:i:s')], 
             'id = ?', 
             [$user['id']]
         );
         
         return true;
     }
     
     return false;
 }
 
 public function logout() {
     // Session-Variablen löschen
     $_SESSION = array();
     
     // Session-Cookie löschen
     if (ini_get("session.use_cookies")) {
         $params = session_get_cookie_params();
         setcookie(session_name(), '', time() - 42000,
             $params["path"], $params["domain"],
             $params["secure"], $params["httponly"]
         );
     }
     
     // Session zerstören
     session_destroy();
     $this->userData = null;
     $this->userGroups = null;
     $this->userPermissions = null;
 }
 
 public function register($username, $email, $password) {
     // Prüfen, ob Benutzername oder E-Mail bereits existiert
     $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
     $existingUser = $this->db->selectOne($sql, [$username, $email]);
     
     if ($existingUser) {
         if ($existingUser['username'] === $username) {
             return ['success' => false, 'message' => 'username_exists'];
         } else {
             return ['success' => false, 'message' => 'email_exists'];
         }
     }
     
     // Passwort hashen
     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
     
     // Benutzer in Datenbank einfügen
     $userId = $this->db->insert('users', [
         'username' => $username,
         'email' => $email,
         'password' => $hashedPassword,
         'created_at' => date('Y-m-d H:i:s'),
         'last_login' => date('Y-m-d H:i:s')
     ]);
     
     if ($userId) {
         // Standardgruppe "Benutzer" zuweisen
         $group = new Group();
         $userGroups = $this->db->select("SELECT * FROM user_groups WHERE name = ?", ['Benutzer']);
         if (count($userGroups) > 0) {
             $group->addUserToGroup($userId, $userGroups[0]['id']);
         }
         
         return ['success' => true, 'user_id' => $userId];
     } else {
         return ['success' => false, 'message' => 'registration_failed'];
     }
 }
 
 public function isLoggedIn() {
     return $this->userData !== null;
 }
 
 public function getCurrentUser() {
     return $this->userData;
 }
 
 public function getUserGroups() {
     return $this->userGroups ?: [];
 }
 
 /**
  * Benutzerberechtigungen abrufen
  * 
  * @return array Liste der Berechtigungsnamen
  */
 public function getUserPermissions() {
     return $this->userPermissions ?: [];
 }
 
 /**
  * Prüfen, ob der Benutzer eine bestimmte Berechtigung hat
  * 
  * @param string $permissionName Name der Berechtigung
  * @return bool True, wenn der Benutzer die Berechtigung hat
  */
 public function hasPermission($permissionName) {
     if (!$this->userPermissions) {
         return false;
     }
     
     return in_array($permissionName, $this->userPermissions);
 }
 
 public function isInGroup($groupName) {
     if (!$this->userGroups) {
         return false;
     }
     
     foreach ($this->userGroups as $group) {
         if ($group['name'] === $groupName) {
             return true;
         }
     }
     
     return false;
 }
 
 public function isAdmin() {
     return $this->isInGroup('Administratoren');
 }
 
 public function getUserById($userId) {
     $sql = "SELECT * FROM users WHERE id = ?";
     $user = $this->db->selectOne($sql, [$userId]);
     
     if ($user) {
         // Benutzergruppen abrufen
         $group = new Group();
         $user['groups'] = $group->getUserGroups($userId);
     }
     
     return $user;
 }
 
 private function loadUserGroups() {
     if (!$this->userData) {
         return;
     }
     
     $sql = "SELECT g.* FROM user_groups g
             JOIN user_group_members m ON g.id = m.group_id
             WHERE m.user_id = ?";
     $this->userGroups = $this->db->select($sql, [$this->userData['id']]);
 }
 
 /**
  * Benutzerberechtigungen laden
  * 
  * Lädt alle Berechtigungen, die dem Benutzer über Gruppen zugewiesen sind
  */
 private function loadUserPermissions() {
     if (!$this->userData) {
         return;
     }
     
     // Berechtigungen aus Gruppen laden
     $sql = "SELECT p.name FROM permissions p
             JOIN group_permissions gp ON p.id = gp.permission_id
             JOIN user_group_members ugm ON gp.group_id = ugm.group_id
             WHERE ugm.user_id = ?";
     $groupPermissions = $this->db->select($sql, [$this->userData['id']]);
     
     // Berechtigungsnamen extrahieren
     $permissions = [];
     foreach ($groupPermissions as $perm) {
         if (!in_array($perm['name'], $permissions)) {
             $permissions[] = $perm['name'];
         }
     }
     
     $this->userPermissions = $permissions;
 }
 
 private function checkSessionTimeout() {
     if (isset($_SESSION['last_activity']) && 
         (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
         // Session ist abgelaufen, Benutzer ausloggen
         $this->logout();
     } else if (isset($_SESSION['last_activity'])) {
         // Letzte Aktivität aktualisieren
         $_SESSION['last_activity'] = time();
     }
 }
 
 public function getAllUsers() {
     $sql = "SELECT u.*, GROUP_CONCAT(g.name SEPARATOR ', ') as group_names 
             FROM users u
             LEFT JOIN user_group_members ugm ON u.id = ugm.user_id
             LEFT JOIN user_groups g ON ugm.group_id = g.id
             GROUP BY u.id
             ORDER BY u.username";
     $users = $this->db->select($sql);
     
     // Benutzergruppen für jeden Benutzer abrufen
     $group = new Group();
     foreach ($users as &$user) {
         $user['groups'] = $group->getUserGroups($user['id']);
     }
     
     return $users;
 }
 
 public function updateUser($userId, $data) {
     $updateData = [];
     
     if (isset($data['username']) && !empty($data['username'])) {
         // Prüfen, ob Benutzername bereits existiert
         $sql = "SELECT * FROM users WHERE username = ? AND id != ?";
         $existingUser = $this->db->selectOne($sql, [$data['username'], $userId]);
         
         if ($existingUser) {
             return ['success' => false, 'message' => 'username_exists'];
         }
         
         $updateData['username'] = $data['username'];
     }
     
     if (isset($data['email']) && !empty($data['email'])) {
         // Prüfen, ob E-Mail bereits existiert
         $sql = "SELECT * FROM users WHERE email = ? AND id != ?";
         $existingUser = $this->db->selectOne($sql, [$data['email'], $userId]);
         
         if ($existingUser) {
             return ['success' => false, 'message' => 'email_exists'];
         }
         
         $updateData['email'] = $data['email'];
     }
     
     if (!empty($data['password'])) {
         $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
     }
     
     // Neue Profilfelder
     $profileFields = ['first_name', 'last_name', 'phone', 'bio', 'profile_image'];
     foreach ($profileFields as $field) {
         if (isset($data[$field])) {
             $updateData[$field] = $data[$field];
         }
     }
     
     if (!empty($updateData)) {
         $this->db->update('users', $updateData, 'id = ?', [$userId]);
         
         // Wenn der aktuelle Benutzer aktualisiert wurde, Benutzerdaten neu laden
         if ($this->userData && $this->userData['id'] == $userId) {
             $this->userData = $this->getUserById($userId);
         }
         
         return ['success' => true];
     }
     
     return ['success' => false, 'message' => 'no_changes'];
 }
 
 public function deleteUser($userId) {
     // Benutzer löschen
     $this->db->query("DELETE FROM users WHERE id = ?", [$userId]);
     return ['success' => true];
 }
 
 /**
  * Profilbild hochladen
  * 
  * @param int $userId Benutzer-ID
  * @param array $file Hochgeladene Datei ($_FILES['profile_image'])
  * @return array Ergebnis der Operation
  */
 public function uploadProfileImage($userId, $file) {
     // Prüfen, ob Datei vorhanden ist
     if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
         return ['success' => false, 'message' => 'image_upload_error'];
     }
     
     // Dateigröße prüfen (max. 2MB)
     if ($file['size'] > 2 * 1024 * 1024) {
         return ['success' => false, 'message' => 'image_too_large'];
     }
     
     // Dateityp prüfen
     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
     if (!in_array($file['type'], $allowedTypes)) {
         return ['success' => false, 'message' => 'invalid_image_format'];
     }
     
     // Zielverzeichnis
     $uploadDir = 'uploads/profile_images/';
     
     // Sicherstellen, dass das Verzeichnis existiert
     if (!is_dir($uploadDir)) {
         mkdir($uploadDir, 0755, true);
     }
     
     // Eindeutigen Dateinamen generieren
     $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
     $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
     $targetFile = $uploadDir . $filename;
     
     // Altes Profilbild löschen, falls vorhanden
     $user = $this->getUserById($userId);
     if ($user && !empty($user['profile_image']) && file_exists($user['profile_image'])) {
         unlink($user['profile_image']);
     }
     
     // Datei hochladen
     if (move_uploaded_file($file['tmp_name'], $targetFile)) {
         // Profilbild in der Datenbank aktualisieren
         $this->db->update('users', ['profile_image' => $targetFile], 'id = ?', [$userId]);
         
         // Wenn der aktuelle Benutzer aktualisiert wurde, Benutzerdaten neu laden
         if ($this->userData && $this->userData['id'] == $userId) {
             $this->userData = $this->getUserById($userId);
         }
         
         return ['success' => true, 'filename' => $targetFile];
     } else {
         return ['success' => false, 'message' => 'image_upload_error'];
     }
 }
 
 /**
  * Profilbild entfernen
  * 
  * @param int $userId Benutzer-ID
  * @return array Ergebnis der Operation
  */
 public function removeProfileImage($userId) {
     // Benutzer abrufen
     $user = $this->getUserById($userId);
     
     if (!$user) {
         return ['success' => false, 'message' => 'user_not_found'];
     }
     
     // Profilbild löschen, falls vorhanden
     if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
         unlink($user['profile_image']);
     }
     
     // Profilbild in der Datenbank zurücksetzen
     $this->db->update('users', ['profile_image' => null], 'id = ?', [$userId]);
     
     // Wenn der aktuelle Benutzer aktualisiert wurde, Benutzerdaten neu laden
     if ($this->userData && $this->userData['id'] == $userId) {
         $this->userData = $this->getUserById($userId);
     }
     
     return ['success' => true];
 }
 
 /**
  * Passwort ändern
  * 
  * @param int $userId Benutzer-ID
  * @param string $currentPassword Aktuelles Passwort
  * @param string $newPassword Neues Passwort
  * @return array Ergebnis der Operation
  */
 public function changePassword($userId, $currentPassword, $newPassword) {
     // Benutzer abrufen
     $user = $this->getUserById($userId);
     
     if (!$user) {
         return ['success' => false, 'message' => 'user_not_found'];
     }
     
     // Aktuelles Passwort prüfen
     if (!password_verify($currentPassword, $user['password'])) {
         return ['success' => false, 'message' => 'incorrect_password'];
     }
     
     // Neues Passwort setzen
     $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
     $this->db->update('users', ['password' => $hashedPassword], 'id = ?', [$userId]);
     
     return ['success' => true];
 }
}
?>

