<?php
/**
 * Benutzeraktivität-Modul für die Benutzerdetailseite
 * 
 * @title Aktivitäten
 * @icon activity
 * @order 10
 * 
 * Verfügbare Variablen:
 * - $includeUserId: ID des angezeigten Benutzers
 * - $includeUserData: Daten des angezeigten Benutzers
 */

// Hier könnten wir Benutzeraktivitäten aus einer Datenbank abrufen
// Für dieses Beispiel verwenden wir Dummy-Daten
$activities = [
    [
        'action' => 'Login',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'ip' => '192.168.1.1'
    ],
    [
        'action' => 'Passwort geändert',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days')),
        'ip' => '192.168.1.1'
    ],
    [
        'action' => 'Profil aktualisiert',
        'timestamp' => date('Y-m-d H:i:s', strtotime('-1 week')),
        'ip' => '192.168.1.2'
    ]
];
?>

<h4 class="mb-4">Benutzeraktivitäten</h4>

<?php if (count($activities) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Aktion</th>
                    <th>Zeitpunkt</th>
                    <th>IP-Adresse</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['action']); ?></td>
                        <td><?php echo htmlspecialchars($activity['timestamp']); ?></td>
                        <td><?php echo htmlspecialchars($activity['ip']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-center text-muted">Keine Aktivitäten gefunden</p>
<?php endif; ?>

