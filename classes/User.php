<?php
class User {
    private $db;
    private $userData = null;
    private $userGroups = null;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Prüfen, ob Benutzer bereits eingeloggt ist
        if (isset($_SESSION['user_id'])) {
            $this->userData = $this->getUserById($_SESSION['user_id']);
            
            // Benutzergruppen laden
            if ($this->userData) {
                $this->loadUserGroups();
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
            
            // Benutzergruppen laden
            $this->loadUserGroups();
            
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
        return $this->userGroups;
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
    
    private function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->selectOne($sql, [$userId]);
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
        $sql = "SELECT * FROM users ORDER BY username";
        return $this->db->select($sql);
    }
    
    public function updateUser($userId, $data) {
        $updateData = [];
        
        if (isset($data['username'])) {
            $updateData['username'] = $data['username'];
        }
        
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        
        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (!empty($updateData)) {
            $this->db->update('users', $updateData, 'id = ?', [$userId]);
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'no_changes'];
    }
    
    public function deleteUser($userId) {
        // Benutzer löschen
        $this->db->query("DELETE FROM users WHERE id = ?", [$userId]);
        return ['success' => true];
    }
}
// KEIN schließendes PHP-Tag hier!