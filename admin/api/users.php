<?php
require_once '../../init.php';
require_once '../../includes/auth_check.php';

// Prüfen, ob Benutzer eingeloggt ist und die erforderlichen Rechte hat
if (!checkPermission($user, 'user.view')) {
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'unauthorized']);
   exit;
}

// Benutzerverwaltung initialisieren
$userManager = new User();
$groupManager = new Group();

// HTTP-Methode bestimmen
$method = $_SERVER['REQUEST_METHOD'];

// Antwort vorbereiten
$response = ['success' => false, 'message' => 'invalid_request'];

switch ($method) {
   case 'GET':
       // Alle Benutzer abrufen oder einen bestimmten Benutzer
       if (isset($_GET['id'])) {
           $userData = $userManager->getUserById($_GET['id']);
           if ($userData) {
               // Benutzergruppen abrufen
               $userGroups = $groupManager->getUserGroups($_GET['id']);
               $userData['groups'] = $userGroups;
               
               $response = ['success' => true, 'data' => $userData];
           } else {
               $response = ['success' => false, 'message' => 'user_not_found'];
           }
       } else {
           // Alle Benutzer abrufen
           $users = $userManager->getAllUsers();
           $response = ['success' => true, 'data' => $users];
       }
       break;
       
   case 'POST':
       // Neuen Benutzer erstellen
       if (!checkPermission($user, 'user.create')) {
           $response = ['success' => false, 'message' => 'no_permission'];
           break;
       }
       
       $data = json_decode(file_get_contents('php://input'), true);
       
       if (!isset($data['username']) || empty($data['username'])) {
           $response = ['success' => false, 'message' => 'username_required'];
           break;
       }
       
       if (!isset($data['email']) || empty($data['email'])) {
           $response = ['success' => false, 'message' => 'email_required'];
           break;
       }
       
       if (!isset($data['password']) || empty($data['password'])) {
           $response = ['success' => false, 'message' => 'password_required'];
           break;
       }
       
       $result = $userManager->register($data['username'], $data['email'], $data['password']);
       
       if ($result['success']) {
           // Benutzergruppen zuweisen, wenn angegeben
           if (isset($data['groups']) && is_array($data['groups'])) {
               foreach ($data['groups'] as $groupId) {
                   $groupManager->addUserToGroup($result['user_id'], $groupId);
               }
           }
           
           $response = ['success' => true, 'user_id' => $result['user_id']];
       } else {
           $response = $result;
       }
       break;
       
   case 'PUT':
       // Benutzer aktualisieren
       if (!checkPermission($user, 'user.edit')) {
           $response = ['success' => false, 'message' => 'no_permission'];
           break;
       }
       
       $data = json_decode(file_get_contents('php://input'), true);
       
       if (!isset($data['id']) || empty($data['id'])) {
           $response = ['success' => false, 'message' => 'id_required'];
           break;
       }
       
       // Benutzer aktualisieren
       $updateData = [
           'username' => $data['username'] ?? '',
           'email' => $data['email'] ?? '',
           'password' => $data['password'] ?? ''
       ];
       
       $result = $userManager->updateUser($data['id'], $updateData);
       
       if ($result['success']) {
           // Benutzergruppen aktualisieren, wenn angegeben
           if (isset($data['groups']) && is_array($data['groups'])) {
               // Bestehende Gruppenzuweisungen entfernen
               $groupManager->removeUserFromAllGroups($data['id']);
               
               // Neue Gruppenzuweisungen hinzufügen
               foreach ($data['groups'] as $groupId) {
                   $groupManager->addUserToGroup($data['id'], $groupId);
               }
           }
           
           $response = ['success' => true];
       } else {
           $response = $result;
       }
       break;
       
   case 'DELETE':
       // Benutzer löschen
       if (!checkPermission($user, 'user.delete')) {
           $response = ['success' => false, 'message' => 'no_permission'];
           break;
       }
       
       $data = json_decode(file_get_contents('php://input'), true);
       
       if (!isset($data['id']) || empty($data['id'])) {
           $response = ['success' => false, 'message' => 'id_required'];
           break;
       }
       
       // Prüfen, ob der Benutzer sich nicht selbst löscht
       if ($data['id'] == $user->getCurrentUser()['id']) {
           $response = ['success' => false, 'message' => 'cannot_delete_self'];
           break;
       }
       
       $result = $userManager->deleteUser($data['id']);
       $response = $result;
       break;
}

// Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
?>

