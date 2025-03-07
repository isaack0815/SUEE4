<?php
/**
 * Schnellzugriff-Modul für das Dashboard
 * 
 * @title Schnellzugriff
 * @description Schnelle Links zu wichtigen Funktionen
 * @icon link
 * @order 20
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;
?>

<div class="row g-3">
    <div class="col-6">
        <a href="profile.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
            <i class="bi bi-person fs-3 mb-2"></i>
            <?php echo $lang->translate('profile'); ?>
        </a>
    </div>
    <div class="col-6">
        <a href="profile.php?tab=01_security" class="btn btn-outline-danger w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
            <i class="bi bi-shield-lock fs-3 mb-2"></i>
            <?php echo $lang->translate('security'); ?>
        </a>
    </div>
    <div class="col-6">
        <a href="profile.php?tab=02_preferences" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
            <i class="bi bi-gear fs-3 mb-2"></i>
            <?php echo $lang->translate('preferences'); ?>
        </a>
    </div>
    <div class="col-6">
        <a href="profile.php?tab=03_dashboard_settings" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
            <i class="bi bi-grid fs-3 mb-2"></i>
            <?php echo $lang->translate('dashboard_settings'); ?>
        </a>
    </div>
</div>

