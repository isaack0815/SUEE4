<?php
/**
 * Forum Module Update
 * 
 * This file handles the update of the forum module.
 */

// Sicherstellen, dass dieses Skript nur vom ModuleManager aufgerufen wird
if (!defined('MODULE_UPDATE')) {
    exit('Direct access not allowed');
}

// Datenbankverbindung abrufen
$db = $this->db;
$currentVersion = $this->getCurrentVersion();
$newVersion = $this->getNewVersion();

try {
    // Versionsspezifische Updates durchführen
    if (version_compare($currentVersion, '1.0.0', '<')) {
        // Update auf Version 1.0.0
        // Hier könnten Datenbankänderungen oder andere Updates durchgeführt werden
    }

    // Update from version 1.0.0 to 1.1.0
    if (version_compare($currentVersion, '1.1.0', '<')) {
        // Add new fields or tables for version 1.1.0
        $db->query("
            ALTER TABLE `forum_topics` 
            ADD COLUMN `is_announcement` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_locked`;
        ");
        
        // Update the version in the database
        $db->query("UPDATE `settings` SET `value` = '1.1.0' WHERE `key` = 'forum_version'");
        
        //echo "Forum-Modul wurde auf Version 1.1.0 aktualisiert.<br>"; // Removed echo, as this is a module update, not a direct script.
    }

    // Update from version 1.1.0 to 1.2.0
    if (version_compare($currentVersion, '1.2.0', '<')) {
        // Add new fields or tables for version 1.2.0
        $db->query("
            CREATE TABLE IF NOT EXISTS `forum_attachments` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `post_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `filename` varchar(255) NOT NULL,
              `filesize` int(11) NOT NULL,
              `filetype` varchar(100) NOT NULL,
              `downloads` int(11) NOT NULL DEFAULT '0',
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`),
              KEY `post_id` (`post_id`),
              KEY `user_id` (`user_id`),
              CONSTRAINT `forum_attachments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
              CONSTRAINT `forum_attachments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Update the version in the database
        $db->query("UPDATE `settings` SET `value` = '1.2.0' WHERE `key` = 'forum_version'");
        
        //echo "Forum-Modul wurde auf Version 1.2.0 aktualisiert.<br>"; // Removed echo, as this is a module update, not a direct script.
    }

    // Weitere Versionsupdates können hier hinzugefügt werden
    
    return [
        'success' => true,
        'message' => "Forum-Modul wurde erfolgreich von Version $currentVersion auf Version $newVersion aktualisiert."
    ];
} catch (PDOException $e) {
    return [
        'success' => false,
        'message' => 'Fehler beim Update des Forum-Moduls: ' . $e->getMessage()
    ];
}

