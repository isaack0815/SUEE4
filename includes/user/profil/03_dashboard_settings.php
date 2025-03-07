<?php
/**
 * Dashboard-Einstellungen für das Benutzerprofil
 * 
 * @title Dashboard-Einstellungen
 * @icon grid
 * @order 30
 */

// Benutzer-ID aus der Session abrufen
$userId = $includeUserId;

// Dashboard-Instanz erstellen
$dashboard = new Dashboard();

// Dashboard für den Benutzer initialisieren, falls noch nicht geschehen
$dashboard->initializeUserDashboard($userId);

// Alle verfügbaren Module abrufen
$allModules = $dashboard->getAllModules();

// Benutzereinstellungen für die Module abrufen
$userModuleSettings = [];
foreach ($allModules as $module) {
    $settings = $dashboard->getUserModuleSettings($userId, $module['module_id']);
    if ($settings) {
        $userModuleSettings[$module['module_id']] = $settings;
    }
}

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_dashboard_settings') {
        // Einstellungen aus dem Formular sammeln
        $newSettings = [];
        
        if (isset($_POST['modules']) && is_array($_POST['modules'])) {
            foreach ($_POST['modules'] as $moduleId => $moduleData) {
                $newSettings[$moduleId] = [
                    'position' => isset($moduleData['position']) ? intval($moduleData['position']) : 0,
                    'grid_x' => isset($moduleData['x']) ? intval($moduleData['x']) : 0,
                    'grid_y' => isset($moduleData['y']) ? intval($moduleData['y']) : 0,
                    'grid_width' => isset($moduleData['width']) ? intval($moduleData['width']) : 3,
                    'grid_height' => isset($moduleData['height']) ? intval($moduleData['height']) : 2,
                    'is_visible' => isset($moduleData['visible']) ? 1 : 0
                ];
            }
        }
        
        // Einstellungen speichern
        $result = $dashboard->saveUserDashboardSettings($userId, $newSettings);
        
        if ($result) {
            $_SESSION['profile_message'] = 'dashboard_settings_saved';
        } else {
            $_SESSION['profile_message'] = 'dashboard_settings_error';
            $_SESSION['profile_message_type'] = 'danger';
        }
        
        // Zurück zur Profilseite
        header('Location: profile.php?tab=03_dashboard_settings');
        exit;
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reset_layout') {
        // Dashboard-Layout zurücksetzen
        $result = $dashboard->resetDashboardLayout($userId);
        
        if ($result) {
            $_SESSION['profile_message'] = 'dashboard_layout_reset';
        } else {
            $_SESSION['profile_message'] = 'dashboard_layout_reset_error';
            $_SESSION['profile_message_type'] = 'danger';
        }
        
        // Zurück zur Profilseite
        header('Location: profile.php?tab=03_dashboard_settings');
        exit;
    }
}
?>

<!-- GridStack CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@7.2.3/dist/gridstack.min.css" />
<!-- GridStack JS -->
<script src="https://cdn.jsdelivr.net/npm/gridstack@7.2.3/dist/gridstack-all.js"></script>

<style>
    .grid-stack-item-content {
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    
    .grid-stack-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .grid-stack-item.ui-draggable-dragging .grid-stack-item-content,
    .grid-stack-item.ui-resizable-resizing .grid-stack-item-content {
        opacity: 0.8;
    }
    
    .module-visibility {
        margin-top: 10px;
    }
    
    .dashboard-controls {
        margin-bottom: 20px;
    }
</style>

<div class="dashboard-controls d-flex justify-content-between align-items-center">
    <h4><?php echo $lang->translate('dashboard_layout'); ?></h4>
    <div>
        <button type="button" class="btn btn-outline-secondary" id="resetLayoutBtn">
            <i class="bi bi-arrow-counterclockwise me-1"></i> <?php echo $lang->translate('reset_layout'); ?>
        </button>
        <button type="button" class="btn btn-primary ms-2" id="saveLayoutBtn">
            <i class="bi bi-save me-1"></i> <?php echo $lang->translate('save_layout'); ?>
        </button>
    </div>
</div>

<div id="alertArea" class="mb-3" style="display: none;"></div>

<p class="text-muted mb-4"><?php echo $lang->translate('drag_resize_modules'); ?></p>

<div id="moduleInputs"></div>
<div class="grid-stack"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // GridStack initialisieren
    const grid = GridStack.init({
        column: 12,
        cellHeight: 50,
        animate: true,
        resizable: {
            handles: 'e,se,s,sw,w'
        },
        disableOneColumnMode: true,
        float: true
    });
    
    // Module laden
    const modules = <?php echo json_encode($allModules); ?>;
    const userSettings = <?php echo json_encode($userModuleSettings); ?>;
    
    // Module zur Grid hinzufügen
    modules.forEach(function(module) {
        const moduleId = module.module_id;
        const settings = userSettings[moduleId] || {};
        
        // Standardwerte, falls keine Einstellungen vorhanden
        const x = settings.grid_x !== undefined ? parseInt(settings.grid_x) : 0;
        const y = settings.grid_y !== undefined ? parseInt(settings.grid_y) : 0;
        const width = settings.grid_width !== undefined ? parseInt(settings.grid_width) : 6;
        const height = settings.grid_height !== undefined ? parseInt(settings.grid_height) : 2;
        const isVisible = settings.is_visible !== undefined ? parseInt(settings.is_visible) === 1 : true;
        
        if (isVisible) {
            // Modul zur Grid hinzufügen
            grid.addWidget({
                x: x,
                y: y,
                w: width,
                h: height,
                id: moduleId,
                content: `
                    <div class="grid-stack-item-header">
                        <div>
                            ${module.icon ? `<i class="bi bi-${module.icon} me-2"></i>` : ''}
                            <strong>${module.name}</strong>
                        </div>
                    </div>
                    <div class="module-content">
                        ${module.description || ''}
                    </div>
                    <div class="module-visibility form-check form-switch">
                        <input class="form-check-input module-visible-checkbox" type="checkbox" id="visible_${moduleId}" checked>
                        <label class="form-check-label" for="visible_${moduleId}"><?php echo $lang->translate('visible'); ?></label>
                    </div>
                `
            });
        } else {
            // Unsichtbares Modul zur Liste hinzufügen
            const hiddenModulesContainer = document.getElementById('moduleInputs');
            const hiddenModuleDiv = document.createElement('div');
            hiddenModuleDiv.className = 'mb-3 p-3 border rounded';
            hiddenModuleDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        ${module.icon ? `<i class="bi bi-${module.icon} me-2"></i>` : ''}
                        <strong>${module.name}</strong>
                        ${module.description ? `<div class="text-muted small">${module.description}</div>` : ''}
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input hidden-module-checkbox" type="checkbox" id="visible_hidden_${moduleId}" data-module-id="${moduleId}">
                        <label class="form-check-label" for="visible_hidden_${moduleId}"><?php echo $lang->translate('show_module'); ?></label>
                    </div>
                </div>
            `;
            hiddenModulesContainer.appendChild(hiddenModuleDiv);
        }
    });
    
    // Event-Listener für Änderungen im Grid
    grid.on('change', function(event, items) {
        // Nichts tun, Änderungen werden erst beim Speichern übernommen
    });
    
    // Event-Listener für Sichtbarkeits-Checkboxen
    document.querySelectorAll('.module-visible-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const gridItem = this.closest('.grid-stack-item');
            const moduleId = gridItem.getAttribute('gs-id');
            
            if (!this.checked) {
                // Modul aus Grid entfernen und zu unsichtbaren Modulen hinzufügen
                const node = grid.engine.nodes.find(n => n.id === moduleId);
                grid.removeWidget(gridItem, false);
                
                const hiddenModulesContainer = document.getElementById('moduleInputs');
                const hiddenModuleDiv = document.createElement('div');
                hiddenModuleDiv.className = 'mb-3 p-3 border rounded';
                hiddenModuleDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            ${node.el.querySelector('.grid-stack-item-header').innerHTML}
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input hidden-module-checkbox" type="checkbox" id="visible_hidden_${moduleId}" data-module-id="${moduleId}">
                            <label class="form-check-label" for="visible_hidden_${moduleId}"><?php echo $lang->translate('show_module'); ?></label>
                        </div>
                    </div>
                `;
                hiddenModulesContainer.appendChild(hiddenModuleDiv);
                
                // Event-Listener für die neue Checkbox hinzufügen
                const newCheckbox = hiddenModuleDiv.querySelector('.hidden-module-checkbox');
                newCheckbox.addEventListener('change', handleHiddenModuleCheckbox);
            }
        });
    });
    
    // Event-Listener für unsichtbare Module
    function handleHiddenModuleCheckbox() {
        if (this.checked) {
            const moduleId = this.dataset.moduleId;
            const moduleDiv = this.closest('div.mb-3');
            
            // Modul zur Grid hinzufügen
            const module = modules.find(m => m.module_id === moduleId);
            
            // Standardwerte für Position
            const x = 0;
            const y = 0;
            const width = 6;
            const height = 2;
            
            grid.addWidget({
                x: x,
                y: y,
                w: width,
                h: height,
                id: moduleId,
                content: `
                    <div class="grid-stack-item-header">
                        <div>
                            ${module.icon ? `<i class="bi bi-${module.icon} me-2"></i>` : ''}
                            <strong>${module.name}</strong>
                        </div>
                    </div>
                    <div class="module-content">
                        ${module.description || ''}
                    </div>
                    <div class="module-visibility form-check form-switch">
                        <input class="form-check-input module-visible-checkbox" type="checkbox" id="visible_${moduleId}" checked>
                        <label class="form-check-label" for="visible_${moduleId}"><?php echo $lang->translate('visible'); ?></label>
                    </div>
                `
            });
            
            // Event-Listener für die neue Checkbox hinzufügen
            const newGridItem = document.querySelector(`.grid-stack-item[gs-id="${moduleId}"]`);
            const newCheckbox = newGridItem.querySelector('.module-visible-checkbox');
            newCheckbox.addEventListener('change', function() {
                const gridItem = this.closest('.grid-stack-item');
                const moduleId = gridItem.getAttribute('gs-id');
                
                if (!this.checked) {
                    // Modul aus Grid entfernen und zu unsichtbaren Modulen hinzufügen
                    const node = grid.engine.nodes.find(n => n.id === moduleId);
                    grid.removeWidget(gridItem, false);
                    
                    const hiddenModulesContainer = document.getElementById('moduleInputs');
                    const hiddenModuleDiv = document.createElement('div');
                    hiddenModuleDiv.className = 'mb-3 p-3 border rounded';
                    hiddenModuleDiv.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                ${node.el.querySelector('.grid-stack-item-header').innerHTML}
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input hidden-module-checkbox" type="checkbox" id="visible_hidden_${moduleId}" data-module-id="${moduleId}">
                                <label class="form-check-label" for="visible_hidden_${moduleId}"><?php echo $lang->translate('show_module'); ?></label>
                            </div>
                        </div>
                    `;
                    hiddenModulesContainer.appendChild(hiddenModuleDiv);
                    
                    // Event-Listener für die neue Checkbox hinzufügen
                    const newCheckbox = hiddenModuleDiv.querySelector('.hidden-module-checkbox');
                    newCheckbox.addEventListener('change', handleHiddenModuleCheckbox);
                }
            });
            
            // Unsichtbares Modul entfernen
            moduleDiv.remove();
        }
    }
    
    // Event-Listener für unsichtbare Module hinzufügen
    document.querySelectorAll('.hidden-module-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', handleHiddenModuleCheckbox);
    });
    
    // Funktion zum Sammeln der aktuellen Layout-Daten
    function collectLayoutData() {
        const layoutData = {};
        
        // Sichtbare Module
        const nodes = grid.engine.nodes;
        nodes.forEach(node => {
            const moduleId = node.id;
            layoutData[moduleId] = {
                position: node.y * 100 + node.x, // Position basierend auf Koordinaten
                grid_x: node.x,
                grid_y: node.y,
                grid_width: node.w,
                grid_height: node.h,
                size: 'medium', // Standardgröße hinzufügen
                is_visible: 1
            };
        });
        
        // Unsichtbare Module
        document.querySelectorAll('#moduleInputs .mb-3').forEach(moduleDiv => {
            const checkbox = moduleDiv.querySelector('.hidden-module-checkbox');
            if (checkbox) {
                const moduleId = checkbox.dataset.moduleId;
                
                // Standardwerte für Position
                layoutData[moduleId] = {
                    position: 0,
                    grid_x: 0,
                    grid_y: 0,
                    grid_width: 6,
                    grid_height: 2,
                    size: 'medium',
                    is_visible: 0
                };
            }
        });
        
        return layoutData;
    }
    
    // Funktion zum Anzeigen von Benachrichtigungen
    function showAlert(message, type = 'success') {
        const alertArea = document.getElementById('alertArea');
        alertArea.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertArea.style.display = 'block';
        
        // Benachrichtigung nach 5 Sekunden ausblenden
        setTimeout(() => {
            const alert = alertArea.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => {
                    alertArea.style.display = 'none';
                }, 150);
            }
        }, 5000);
    }
    
    // Speichern-Button
    document.getElementById('saveLayoutBtn').addEventListener('click', async function() {
        // Button deaktivieren während des Speicherns
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Speichern...';
        
        try {
            const layoutData = collectLayoutData();
            
            const response = await fetch('../api/dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'save_layout',
                    modules: layoutData
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('<?php echo $lang->translate("dashboard_settings_saved"); ?>');
            } else {
                showAlert('<?php echo $lang->translate("dashboard_settings_error"); ?>', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('<?php echo $lang->translate("dashboard_settings_error"); ?>', 'danger');
        } finally {
            // Button wieder aktivieren
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-save me-1"></i> <?php echo $lang->translate("save_layout"); ?>';
        }
    });
    
    // Reset-Button
    document.getElementById('resetLayoutBtn').addEventListener('click', async function() {
        if (!confirm('<?php echo $lang->translate("confirm_reset_layout"); ?>')) {
            return;
        }
        
        // Button deaktivieren während des Zurücksetzens
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Zurücksetzen...';
        
        try {
            const response = await fetch('../api/dashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'reset_layout'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('<?php echo $lang->translate("dashboard_layout_reset"); ?>');
                // Seite neu laden, um das zurückgesetzte Layout anzuzeigen
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('<?php echo $lang->translate("dashboard_layout_reset_error"); ?>', 'danger');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('<?php echo $lang->translate("dashboard_layout_reset_error"); ?>', 'danger');
        } finally {
            // Button wieder aktivieren
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i> <?php echo $lang->translate("reset_layout"); ?>';
        }
    });
});
</script>

