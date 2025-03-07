<?php
// Dashboard-Klasse initialisieren
$dashboard = new Dashboard();

// Benutzer-ID abrufen
$userId = $user->getCurrentUser()['id'] ?? 0;

// Debug-Ausgabe
error_log("Dashboard wird für Benutzer $userId geladen");

// Dashboard-Einstellungen des Benutzers abrufen
$modules = $dashboard->getUserDashboardSettings($userId);

// Debug-Ausgabe
error_log("Anzahl der Module: " . count($modules));

// Wenn keine Einstellungen vorhanden sind, initialisieren
if (empty($modules)) {
    error_log("Keine Module gefunden, initialisiere Dashboard");
    $dashboard->initializeUserDashboard($userId);
    $modules = $dashboard->getUserDashboardSettings($userId);
    error_log("Nach Initialisierung: " . count($modules) . " Module");
}

// Prüfen, ob Module vorhanden sind
if (empty($modules)) {
    echo '<div class="alert alert-info">Keine Module verfügbar</div>';
} else {
    // Debug-Ausgabe der Module
    foreach ($modules as $module) {
        error_log("Modul: " . $module['module_id'] . ", Sichtbar: " . $module['is_visible']);
    }
    
    // Nur sichtbare Module anzeigen
    $visibleModules = array_filter($modules, function($module) {
        return $module['is_visible'] == 1;
    });
    
    if (empty($visibleModules)) {
        echo '<div class="alert alert-info">Keine sichtbaren Module verfügbar</div>';
    } else {
        // Grid-Container für das Dashboard
        echo '<div class="dashboard-grid" id="dashboard-grid">';
        
        // Module anzeigen
        foreach ($visibleModules as $module) {
            echo '<div class="dashboard-module" 
                      id="module-' . htmlspecialchars($module['module_id']) . '"
                      data-module-id="' . htmlspecialchars($module['module_id']) . '"
                      data-grid-x="' . htmlspecialchars($module['grid_x']) . '"
                      data-grid-y="' . htmlspecialchars($module['grid_y']) . '"
                      data-grid-width="' . htmlspecialchars($module['grid_width']) . '"
                      data-grid-height="' . htmlspecialchars($module['grid_height']) . '"
                      data-size="' . htmlspecialchars($module['size']) . '">';
            
            echo '<div class="module-header">';
            echo '<h3><i class="' . htmlspecialchars($module['icon']) . '"></i> ' . htmlspecialchars($module['name']) . '</h3>';
            echo '<div class="module-actions">';
            echo '<button class="btn-module-settings" title="Einstellungen"><i class="fas fa-cog"></i></button>';
            echo '<button class="btn-module-toggle" title="Minimieren/Maximieren"><i class="fas fa-minus"></i></button>';
            echo '</div>';
            echo '</div>';
            
            echo '<div class="module-content">';
            // Hier wird die Moduldatei eingebunden
            if (!empty($module['file_path'])) {
                $filePath = $module['file_path'];
                error_log("Versuche Datei einzubinden: $filePath");
                
                if (file_exists($filePath)) {
                    include $filePath;
                } else {
                    echo '<p class="text-danger">Modul-Datei nicht gefunden: ' . htmlspecialchars($filePath) . '</p>';
                    error_log("Datei nicht gefunden: $filePath");
                }
            } else {
                echo '<p class="text-warning">Kein Dateipfad für dieses Modul definiert</p>';
                error_log("Kein Dateipfad für Modul " . $module['module_id']);
            }
            echo '</div>';
            
            echo '</div>';
        }
        
        echo '</div>'; // Ende dashboard-grid
        
        // JavaScript für das Grid-Layout
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                initDashboardGrid();
            });
        </script>';
    }
}
?>

