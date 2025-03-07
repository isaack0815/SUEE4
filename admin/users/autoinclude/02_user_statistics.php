<?php
/**
 * Benutzerstatistik-Modul für die Benutzerdetailseite
 * 
 * @title Statistiken
 * @icon bar-chart
 * @order 20
 * 
 * Verfügbare Variablen:
 * - $includeUserId: ID des angezeigten Benutzers
 * - $includeUserData: Daten des angezeigten Benutzers
 */

// Hier könnten wir Benutzerstatistiken aus einer Datenbank abrufen
// Für dieses Beispiel verwenden wir Dummy-Daten
$statistics = [
    'login_count' => rand(5, 50),
    'last_active_days' => rand(0, 30),
    'created_content' => rand(0, 20),
    'comments' => rand(0, 15)
];
?>

<h4 class="mb-4">Benutzerstatistiken</h4>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4"><?php echo $statistics['login_count']; ?></h3>
                <p class="text-muted">Anzahl Logins</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4"><?php echo $statistics['last_active_days']; ?></h3>
                <p class="text-muted">Tage seit letzter Aktivität</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4"><?php echo $statistics['created_content']; ?></h3>
                <p class="text-muted">Erstellte Inhalte</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="display-4"><?php echo $statistics['comments']; ?></h3>
                <p class="text-muted">Kommentare</p>
            </div>
        </div>
    </div>
</div>

