<?php
/**
 * Forum Module Uninstallation Script
 * 
 * This script removes all database tables and data related to the forum module.
 */

// Check if the script is being run directly
if (!defined('UNINSTALL_SCRIPT')) {
    die('Direct access to this script is not allowed.');
}

// Drop the forum tables in the correct order to avoid foreign key constraints
$db->query("DROP TABLE IF EXISTS `forum_topic_subscriptions`;");
$db->query("DROP TABLE IF EXISTS `forum_forum_subscriptions`;");
$db->query("DROP TABLE IF EXISTS `forum_posts`;");
$db->query("DROP TABLE IF EXISTS `forum_topics`;");
$db->query("DROP TABLE IF EXISTS `forum_forums`;");
$db->query("DROP TABLE IF EXISTS `forum_categories`;");

// Remove forum permissions
$db->query("DELETE FROM `permissions` WHERE `category` = 'Forum';");

// Remove forum menu items
$db->query("DELETE FROM `menu_items` WHERE `name` = 'Forum' OR `name` = 'Forum Administration';");

// Success message
echo "Forum-Modul wurde erfolgreich deinstalliert.";

