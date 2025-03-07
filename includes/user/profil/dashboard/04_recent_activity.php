<?php
/**
 * Aktivitätsmodul für das Dashboard
 * 
 * @title Letzte Aktivitäten
 * @description Zeigt die letzten Benutzeraktivitäten an
 * @icon activity
 * @order 40
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;

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

<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Aktion</th>
                <th>Zeitpunkt</th>
                <th>IP</th>
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

