<?php
/**
 * Module Management API
 */
require_once '../../init.php';

// Check if user is logged in and has admin permissions
if (!$auth->isLoggedIn() || !$auth->hasPermission('admin_access')) {
    echo json_encode(['success' => false, 'message' => $lang->get('common', 'access_denied')]);
    exit;
}

// Load language file
$lang->load('modules');

// Create ModuleManager instance
$moduleManager = new ModuleManager($db, $lang);

// Get action
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Response array
$response = ['success' => false, 'message' => $lang->get('modules', 'invalid_action')];

switch ($action) {
    case 'list':
        // Get all modules
        $modules = $moduleManager->getAllModules();
        $response = ['success' => true, 'modules' => $modules];
        break;
        
    case 'upload':
        // Check if file was uploaded
        if (!isset($_FILES['module_file']) || $_FILES['module_file']['error'] !== UPLOAD_ERR_OK) {
            $response['message'] = $lang->get('modules', 'error_upload');
            break;
        }
        
        // Upload and install module
        if ($moduleManager->uploadModule($_FILES['module_file'])) {
            $response = ['success' => true, 'message' => $lang->get('modules', 'upload_success')];
        } else {
            $response = ['success' => false, 'message' => implode('<br>', $moduleManager->getErrors())];
        }
        break;
        
    case 'activate':
        // Check if module ID is provided
        if (!isset($_POST['module_id']) || !is_numeric($_POST['module_id'])) {
            $response['message'] = $lang->get('modules', 'invalid_module_id');
            break;
        }
        
        // Activate module
        if ($moduleManager->activateModule($_POST['module_id'])) {
            $response = ['success' => true, 'message' => $lang->get('modules', 'activate_success')];
        } else {
            $response = ['success' => false, 'message' => implode('<br>', $moduleManager->getErrors())];
        }
        break;
        
    case 'deactivate':
        // Check if module ID is provided
        if (!isset($_POST['module_id']) || !is_numeric($_POST['module_id'])) {
            $response['message'] = $lang->get('modules', 'invalid_module_id');
            break;
        }
        
        // Deactivate module
        if ($moduleManager->deactivateModule($_POST['module_id'])) {
            $response = ['success' => true, 'message' => $lang->get('modules', 'deactivate_success')];
        } else {
            $response = ['success' => false, 'message' => implode('<br>', $moduleManager->getErrors())];
        }
        break;
        
    case 'uninstall':
        // Check if module ID is provided
        if (!isset($_POST['module_id']) || !is_numeric($_POST['module_id'])) {
            $response['message'] = $lang->get('modules', 'invalid_module_id');
            break;
        }
        
        // Uninstall module
        if ($moduleManager->uninstallModule($_POST['module_id'])) {
            $response = ['success' => true, 'message' => $lang->get('modules', 'uninstall_success')];
        } else {
            $response = ['success' => false, 'message' => implode('<br>', $moduleManager->getErrors())];
        }
        break;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

