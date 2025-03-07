<?php
/**
 * Statistik-Modul für das Dashboard
 * 
 * @title Statistiken
 * @description Benutzerstatistiken
 * @icon bar-chart
 * @order 30
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;

// Hier könnten wir Benutzerstatistiken aus einer Datenbank abrufen
// Für dieses Beispiel verwenden wir Dummy-Daten
$statistics = [
    'login_count' => rand(5, 50),
    'last_active_days' => rand(0, 30),
    'created_content' => rand(0, 20),
    'comments' => rand(0, 15)
];
?>

<div class="row g-3">
    <div class="col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h3 class="display-6"><?php echo $statistics['login_count']; ?></h3>
                <p class="text-muted mb-0">Logins</p>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h3 class="display-6"><?php echo $statistics['last_active_days']; ?></h3>
                <p class="text-muted mb-0">Tage aktiv</p>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h3 class="display-6"><?php echo $statistics['created_content']; ?></h3>
                <p class="text-muted mb-0">Inhalte erstellt</p>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <h3 class="display-6"><?php echo $statistics['comments']; ?></h3>
                <p class="text-muted mb-0">Kommentare</p>
            </div>
        </div>
    </div>
</div>

