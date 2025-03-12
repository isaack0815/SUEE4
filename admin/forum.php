<?php
/**
 * Forum Admin Page
 * 
 * This file displays the forum administration page.
 */

// Include necessary files
require_once '../init.php';
require_once '../includes/auth_check.php';
require_once '../classes/ForumAdmin.php';

if (!$user->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

checkPermission($_SESSION['user_id'],'forum_admin');


// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems('admin');

$smarty->assign('adminMenu', $adminMenu);

// Initialize the ForumAdmin class
$forumAdmin = new ForumAdmin();

// Get the action from the URL
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle different actions
switch ($action) {
    case 'list':
        // Get all categories and forums
        $categories = $forumAdmin->getCategories();
        $forums = $forumAdmin->getForums();
        
        // Set template variables
        $smarty->assign('categories', $categories);
        $smarty->assign('forums', $forums);
        $smarty->assign('pageTitle', 'Forum Administration');
        
        // Display the template
        $smarty->display('admin/forum.tpl');
        break;
        
    case 'category_form':
        // Get the category ID from the URL (for editing)
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Get the category details (for editing)
        $category = [];
        $category['id'] = $categoryId;
        if ($categoryId > 0) {
            $category = $forumAdmin->getCategoryDetails($categoryId);
        }
        
        // Set template variables
        $smarty->assign('category', $category);
        $smarty->assign('pageTitle', $categoryId > 0 ? 'Kategorie bearbeiten' : 'Neue Kategorie');
        
        // Display the template
        $smarty->display('admin/forum_category_form.tpl');
        break;
        
    case 'category_save':
        // Get the form data
        $categoryId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
        
        // Validate the form data
        if (empty($name)) {
            // Set error message
            $smarty->assign('error', 'Der Kategoriename darf nicht leer sein.');
            
            // Set template variables
            $smarty->assign('category', [
                'id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder
            ]);
            $smarty->assign('pageTitle', $categoryId > 0 ? 'Kategorie bearbeiten' : 'Neue Kategorie');
            
            // Display the template
            $smarty->display('admin/forum_category_form.tpl');
            break;
        }
        
        // Save the category
        if ($categoryId > 0) {
            $result = $forumAdmin->updateCategory($categoryId, $name, $description, $sortOrder);
        } else {
            $result = $forumAdmin->createCategory($name, $description, $sortOrder);
        }
        
        if ($result) {
            // Redirect to the category list
            header('Location: forum.php?action=list');
            exit;
        } else {
            // Set error message
            $smarty->assign('error', 'Beim Speichern der Kategorie ist ein Fehler aufgetreten.');
            
            // Set template variables
            $smarty->assign('category', [
                'id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder
            ]);
            $smarty->assign('pageTitle', $categoryId > 0 ? 'Kategorie bearbeiten' : 'Neue Kategorie');
            
            // Display the template
            $smarty->display('admin/forum_category_form.tpl');
        }
        break;
        
    case 'category_delete':
        // Get the category ID from the URL
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Get the category details
        $category = $forumAdmin->getCategoryDetails($categoryId);
        
        // Set template variables
        $smarty->assign('category', $category);
        $smarty->assign('pageTitle', 'Kategorie löschen');
        
        // Display the template
        $smarty->display('admin/forum_category_delete.tpl');
        break;
        
    case 'category_delete_confirm':
        
        // Get the category ID from the URL
        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Delete the category
        $result = $forumAdmin->deleteCategory($categoryId);
        print_r($result);
        if ($result) {
            // Redirect to the category list
            header('Location: forum.php?action=list');
            exit;
        } else {
            // Set error message
            $smarty->assign('error', 'Beim Löschen der Kategorie ist ein Fehler aufgetreten.');
            
            // Get the category details
            $category = $forumAdmin->getCategoryDetails($categoryId);
            
            // Set template variables
            $smarty->assign('category', $category);
            $smarty->assign('pageTitle', 'Kategorie löschen');
            
            // Display the template
            $smarty->display('admin/forum_category_delete.tpl');
        }
        break;
        
    case 'forum_form':
        // Get the forum ID from the URL (for editing)
        $forumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Get the forum details (for editing)
        $forum = [];
        $forum['id'] = $forumId;
        if ($forumId > 0) {
            $forum = $forumAdmin->getForumDetails($forumId);
        }
        
        // Get all categories
        $categories = $forumAdmin->getCategories();
        // Set template variables
        $smarty->assign('forum', $forum);
        $smarty->assign('categories', $categories);
        $smarty->assign('pageTitle', $forumId > 0 ? 'Forum bearbeiten' : 'Neues Forum');
        
        // Display the template
        $smarty->display('admin/forum_forum_form.tpl');
        break;
        
    case 'forum_save':
        // Get the form data
        $forumId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $sortOrder = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
        
        // Validate the form data
        if (empty($name)) {
            // Set error message
            $smarty->assign('error', 'Der Forumname darf nicht leer sein.');
            
            // Get all categories
            $categories = $forumAdmin->getCategories();
            
            // Set template variables
            $smarty->assign('forum', [
                'id' => $forumId,
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder
            ]);
            $smarty->assign('categories', $categories);
            $smarty->assign('pageTitle', $forumId > 0 ? 'Forum bearbeiten' : 'Neues Forum');
            
            // Display the template
            $smarty->display('admin/forum_forum_form.tpl');
            break;
        }
        
        // Save the forum
        if ($forumId > 0) {
            $result = $forumAdmin->updateForum($forumId, $categoryId, $name, $description, $sortOrder);
        } else {
            $result = $forumAdmin->createForum($categoryId, $name, $description, $sortOrder);
        }
        
        if ($result) {
            // Redirect to the forum list
            header('Location: forum.php?action=list');
            exit;
        } else {
            // Set error message
            $smarty->assign('error', 'Beim Speichern des Forums ist ein Fehler aufgetreten.');
            
            // Get all categories
            $categories = $forumAdmin->getCategories();
            
            // Set template variables
            $smarty->assign('forum', [
                'id' => $forumId,
                'category_id' => $categoryId,
                'name' => $name,
                'description' => $description,
                'sort_order' => $sortOrder
            ]);
            $smarty->assign('categories', $categories);
            $smarty->assign('pageTitle', $forumId > 0 ? 'Forum bearbeiten' : 'Neues Forum');
            
            // Display the template
            $smarty->display('admin/forum_forum_form.tpl');
        }
        break;
        
    case 'forum_delete':
        // Get the forum ID from the URL
        $forumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Get the forum details
        $forum = $forumAdmin->getForumDetails($forumId);
        
        // Set template variables
        $smarty->assign('forum', $forum);
        $smarty->assign('pageTitle', 'Forum löschen');
        
        // Display the template
        $smarty->display('admin/forum_forum_delete.tpl');
        break;
        
    case 'forum_delete_confirm':
        // Get the forum ID from the URL
        $forumId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        echo 'actionID: '.$forumId.'<br>';
        // Delete the forum
        $result = $forumAdmin->deleteForum($forumId);
        
        if ($result) {
            // Redirect to the forum list
            header('Location: forum.php?action=list');
            exit;
        } else {
            // Set error message
            $smarty->assign('error', 'Beim Löschen des Forums ist ein Fehler aufgetreten.');
            
            // Get the forum details
            $forum = $forumAdmin->getForumDetails($forumId);
            
            // Set template variables
            $smarty->assign('forum', $forum);
            $smarty->assign('pageTitle', 'Forum löschen');
            
            // Display the template
            $smarty->display('admin/forum_forum_delete.tpl');
        }
        break;
        
    default:
        // Redirect to the forum list
        header('Location: forum.php?action=list');
        break;
}

