<?php
/**
 * Willkommensmodul für das Dashboard
 * 
 * @title Willkommen
 * @description Begrüßungsmodul für das Dashboard
 * @icon house
 * @order 10
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;
$userData = $includeUserData;
?>

<div class="text-center py-3">
    <h4 class="mb-3"><?php echo $lang->translate('welcome_dashboard'); ?></h4>
    <p class="lead">
        <?php echo $lang->translate('hello'); ?>, <strong><?php echo $userData['username']; ?></strong>!
    </p>
    <p>
        <?php echo $lang->translate('last_login'); ?>: 
        <strong><?php echo date('d.m.Y H:i', strtotime($userData['last_login'])); ?></strong>
    </p>
</div>

