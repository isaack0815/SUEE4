<?php
/**
 * Forum Module Installation Script
 * 
 * This script creates the necessary database tables for the forum module.
 */

// Check if the script is being run directly
if (!defined('INSTALL_SCRIPT')) {
    die('Direct access to this script is not allowed.');
}

// Create the forum_categories table
$db->query("
CREATE TABLE IF NOT EXISTS `forum_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create the forum_forums table
$db->query("
CREATE TABLE IF NOT EXISTS `forum_forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `forum_forums_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `forum_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create the forum_topics table
$db->query("
CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `is_sticky` tinyint(1) NOT NULL DEFAULT '0',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_post_time` datetime NOT NULL,
  `last_post_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`),
  KEY `last_post_user_id` (`last_post_user_id`),
  CONSTRAINT `forum_topics_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forum_forums` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_topics_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_topics_ibfk_3` FOREIGN KEY (`last_post_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create the forum_posts table
$db->query("
CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create the forum_forum_subscriptions table
$db->query("
CREATE TABLE IF NOT EXISTS `forum_forum_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_forum_id` (`user_id`,`forum_id`),
  KEY `forum_id` (`forum_id`),
  CONSTRAINT `forum_forum_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_forum_subscriptions_ibfk_2` FOREIGN KEY (`forum_id`) REFERENCES `forum_forums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create the forum_topic_subscriptions table
$db->query("
CREATE TABLE IF NOT EXISTS `forum_topic_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_topic_id` (`user_id`,`topic_id`),
  KEY `topic_id` (`topic_id`),
  CONSTRAINT `forum_topic_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forum_topic_subscriptions_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `forum_topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Add forum permissions
$db->query("
INSERT INTO `permissions` (`name`, `description`, `category`) VALUES
('forum_view', 'Erlaubt das Ansehen des Forums', 'Forum'),
('forum_create_topic', 'Erlaubt das Erstellen von Themen', 'Forum'),
('forum_reply', 'Erlaubt das Antworten auf Themen', 'Forum'),
('forum_edit_own', 'Erlaubt das Bearbeiten eigener Beiträge', 'Forum'),
('forum_delete_own', 'Erlaubt das Löschen eigener Beiträge', 'Forum'),
('forum_moderator', 'Erlaubt Moderationsfunktionen im Forum', 'Forum'),
('forum_admin', 'Erlaubt die Administration des Forums', 'Forum');
");

// Add forum menu items
$db->query("
INSERT INTO `menu_items` (`parent_id`, `name`, `url`, `icon`, `sort_order`) VALUES
(NULL, 'Forum', 'forum.php', 'comments', 30),
(NULL, 'Forum Administration', 'admin/forum.php', 'comments', 30);
");

// Create a default category and forum
$db->query("
INSERT INTO `forum_categories` (`name`, `description`, `sort_order`, `created_at`) VALUES
('Allgemein', 'Allgemeine Diskussionen', 1, NOW());
");

$categoryId = $db->lastInsertId();

$db->query("
INSERT INTO `forum_forums` (`category_id`, `name`, `description`, `sort_order`, `created_at`) VALUES
($categoryId, 'Willkommen', 'Vorstellungen und allgemeine Diskussionen', 1, NOW());
");

// Success message
echo "Forum-Modul wurde erfolgreich installiert.";

