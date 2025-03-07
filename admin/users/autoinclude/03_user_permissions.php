<?php
/**
 * Benutzerberechtigungen-Modul für die Benutzerdetailseite
 * 
 * @title Berechtigungen
 * @icon shield-lock
 * @order 30
 * 
 * Verfügbare Variablen:
 * - $includeUserId: ID des angezeigten Benutzers
 * - $includeUserData: Daten des angezeigten Benutzers
 */

// Berechtigungen des Benutzers abrufen
$db = Database::getInstance();
$permissions = $db->select("
    SELECT DISTINCT p.name, p.description
    FROM permissions p
    JOIN group_permissions gp ON p.id = gp.permission_id
    JOIN user_group_members ugm ON gp.group_id = ugm.group_id
    WHERE ugm.user_id = ?
    ORDER BY p.name
", [$includeUserId]);
?>

<h4 class="mb-4">Benutzerberechtigungen</h4>

<?php if (count($permissions) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Berechtigung</th>
                    <th>Beschreibung</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permissions as $permission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($permission['name']); ?></td>
                        <td><?php echo htmlspecialchars($permission['description'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-center text-muted">Keine Berechtigungen gefunden</p>
<?php endif; ?>

