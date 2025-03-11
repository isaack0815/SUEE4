<?php
/**
 * Forum Module Information
 * 
 * This file contains the information about the forum module.
 */
return [
    'name' => 'Forum',
    'description' => 'Ein vollständiges Diskussionsforum für Benutzer',
    'version' => '1.0.0',
    'author' => 'System',
    'author_url' => 'https://example.com',
    'icon' => 'comments',
    'type' => 'system', // Dies ist ein Systemmodul
    'requires' => [
        'system_version' => '1.0.0',
        'php_version' => '7.4.0'
    ],
    'files' => [
        'forum.php',                    // Hauptseite des Forums
        'forum_forum.php',              // Anzeige eines Forums mit Themen
        'forum_topic.php',              // Anzeige eines Themas mit Beiträgen
        'forum_post.php',               // Erstellen/Bearbeiten von Beiträgen
        'forum_moderate.php',           // Moderationsfunktionen
        'forum_subscribe.php',          // Abonnement-Funktionen
        
        // Klassen
        'classes/Forum.php',            // Hauptklasse für Forumsfunktionen
        'classes/ForumAdmin.php',       // Klasse für Administrationsfunktionen
        
        // Admin-Bereich
        'admin/forum.php',              // Admin-Hauptseite
        
        // Templates
        'templates/forum.tpl',          // Hauptseite des Forums
        'templates/forum_forum.tpl',    // Anzeige eines Forums
        'templates/forum_topic.tpl',    // Anzeige eines Themas
        'templates/forum_post.tpl',     // Formular zum Erstellen/Bearbeiten
        'templates/admin/forum.tpl',    // Admin-Hauptseite
        'templates/admin/forum_categories.tpl',  // Kategorieverwaltung
        'templates/admin/forum_category_form.tpl', // Kategorieformular
        'templates/admin/forum_category_delete.tpl', // Kategorielöschung
        'templates/admin/forum_forums.tpl',      // Forenverwaltung
        'templates/admin/forum_forum_form.tpl',  // Forenformular
        'templates/admin/forum_forum_delete.tpl', // Forenlöschung
        
        // Installationsskripte
        'install.php',                  // Installationsskript
        'uninstall.php',                // Deinstallationsskript
        'update.php',                   // Aktualisierungsskript
    ]
];

