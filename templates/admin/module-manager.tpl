{include file="admin/header.tpl"}

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{translate key="module_manager"}</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="moduleTypeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {if $moduleType == 'dashboard'}active{/if}" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-modules" type="button" role="tab" aria-controls="dashboard-modules" aria-selected="{if $moduleType == 'dashboard'}true{else}false{/if}">{translate key="dashboard_modules"}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {if $moduleType == 'system'}active{/if}" id="system-tab" data-bs-toggle="tab" data-bs-target="#system-modules" type="button" role="tab" aria-controls="system-modules" aria-selected="{if $moduleType == 'system'}true{else}false{/if}">{translate key="system_modules"}</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="moduleTypeTabsContent">
                <!-- Dashboard-Module Tab -->
                <div class="tab-pane fade {if $moduleType == 'dashboard'}show active{/if}" id="dashboard-modules" role="tabpanel" aria-labelledby="dashboard-tab">
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDashboardModuleModal">
                            <i class="fas fa-upload"></i> {translate key="upload_dashboard_module"}
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="dashboardModulesTableSearch" class="form-control" placeholder="{translate key='search'}...">
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dashboardModulesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{translate key="name"}</th>
                                    <th>{translate key="description"}</th>
                                    <th>{translate key="version"}</th>
                                    <th>{translate key="author"}</th>
                                    <th>{translate key="status"}</th>
                                    <th>{translate key="actions"}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if $dashboardModules|@count > 0}
                                    {foreach from=$dashboardModules item=module}
                                        <tr>
                                            <td>
                                                <i class="fas fa-{$module.icon}"></i> {$module.name}
                                            </td>
                                            <td>{$module.description}</td>
                                            <td>{$module.version|default:'--'}</td>
                                            <td>{$module.author|default:'--'}</td>
                                            <td>
                                                {if $module.is_active}
                                                    <span class="badge bg-success">{translate key="active"}</span>
                                                {else}
                                                    <span class="badge bg-secondary">{translate key="inactive"}</span>
                                                {/if}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-{if $module.is_active}warning{else}success{/if} toggle-module" data-module-id="{$module.module_id}" data-is-active="{if $module.is_active}0{else}1{/if}" data-module-type="dashboard">
                                                    {if $module.is_active}
                                                        <i class="fas fa-pause"></i> {translate key="deactivate"}
                                                    {else}
                                                        <i class="fas fa-play"></i> {translate key="activate"}
                                                    {/if}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger uninstall-module" data-module-id="{$module.module_id}" data-module-name="{$module.name}" data-module-type="dashboard">
                                                    <i class="fas fa-trash"></i> {translate key="uninstall"}
                                                </button>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="6" class="text-center">{translate key="no_dashboard_modules"}</td>
                                    </tr>
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- System-Module Tab -->
                <div class="tab-pane fade {if $moduleType == 'system'}show active{/if}" id="system-modules" role="tabpanel" aria-labelledby="system-tab">
                    <div class="mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadSystemModuleModal">
                            <i class="fas fa-upload"></i> {translate key="upload_system_module"}
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="systemModulesTableSearch" class="form-control" placeholder="{translate key='search'}...">
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="systemModulesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{translate key="name"}</th>
                                    <th>{translate key="description"}</th>
                                    <th>{translate key="version"}</th>
                                    <th>{translate key="author"}</th>
                                    <th>{translate key="status"}</th>
                                    <th>{translate key="actions"}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if $systemModules|@count > 0}
                                    {foreach from=$systemModules item=module}
                                        <tr>
                                            <td>
                                                <i class="fas fa-{$module.icon}"></i> {$module.name}
                                            </td>
                                            <td>{$module.description}</td>
                                            <td>{$module.version|default:'--'}</td>
                                            <td>{$module.author|default:'--'}</td>
                                            <td>
                                                {if $module.is_active}
                                                    <span class="badge bg-success">{translate key="active"}</span>
                                                {else}
                                                    <span class="badge bg-secondary">{translate key="inactive"}</span>
                                                {/if}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-{if $module.is_active}warning{else}success{/if} toggle-module" data-module-id="{$module.module_id}" data-is-active="{if $module.is_active}0{else}1{/if}" data-module-type="system">
                                                    {if $module.is_active}
                                                        <i class="fas fa-pause"></i> {translate key="deactivate"}
                                                    {else}
                                                        <i class="fas fa-play"></i> {translate key="activate"}
                                                    {/if}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger uninstall-module" data-module-id="{$module.module_id}" data-module-name="{$module.name}" data-module-type="system">
                                                    <i class="fas fa-trash"></i> {translate key="uninstall"}
                                                </button>
                                            </td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="6" class="text-center">{translate key="no_system_modules"}</td>
                                    </tr>
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Dashboard-Modul Modal -->
<div class="modal fade" id="uploadDashboardModuleModal" tabindex="-1" aria-labelledby="uploadDashboardModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDashboardModuleModalLabel">{translate key="upload_dashboard_module"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDashboardModuleForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="module_type" value="dashboard">
                    <div class="mb-3">
                        <label for="dashboardModuleFile" class="form-label">{translate key="module_file_zip"}</label>
                        <input type="file" class="form-control" id="dashboardModuleFile" name="module" accept=".zip" required>
                    </div>
                </form>
                <div id="dashboardModuleUploadResult" class="mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="uploadDashboardModuleBtn">{translate key="upload"}</button>
                <button type="button" class="btn btn-success" id="installDashboardModuleBtn" style="display: none;">{translate key="install"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Upload System-Modul Modal -->
<div class="modal fade" id="uploadSystemModuleModal" tabindex="-1" aria-labelledby="uploadSystemModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadSystemModuleModalLabel">{translate key="upload_system_module"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadSystemModuleForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="module_type" value="system">
                    <div class="mb-3">
                        <label for="systemModuleFile" class="form-label">{translate key="module_file_zip"}</label>
                        <input type="file" class="form-control" id="systemModuleFile" name="module" accept=".zip" required>
                    </div>
                </form>
                <div id="systemModuleUploadResult" class="mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="uploadSystemModuleBtn">{translate key="upload"}</button>
                <button type="button" class="btn btn-success" id="installSystemModuleBtn" style="display: none;">{translate key="install"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Uninstall Module Modal -->
<div class="modal fade" id="uninstallModuleModal" tabindex="-1" aria-labelledby="uninstallModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uninstallModuleModalLabel">{translate key="uninstall_module"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{translate key="confirm_uninstall"} <strong id="uninstallModuleName"></strong>?</p>
                <p class="text-danger">{translate key="warning_module_data_deleted"}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-danger" id="confirmUninstallBtn">{translate key="uninstall"}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hilfsfunktion für AJAX-Anfragen
    function sendRequest(url, method, data, callback) {
        const options = {
            method: method,
            headers: {}
        };
        
        if (data instanceof FormData) {
            options.body = data;
        } else if (data) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }
        
        fetch(url, options)
            .then(response => response.json())
            .then(data => callback(data, null))
            .catch(error => callback(null, error));
    }
    
    // Dashboard-Modul hochladen
    const uploadDashboardModuleBtn = document.getElementById('uploadDashboardModuleBtn');
    if (uploadDashboardModuleBtn) {
        uploadDashboardModuleBtn.addEventListener('click', function() {
            const form = document.getElementById('uploadDashboardModuleForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('dashboardModuleUploadResult');
            
            sendRequest('module-manager.php', 'POST', formData, function(response, error) {
                if (error) {
                    resultDiv.innerHTML = '<div class="alert alert-danger">{translate key="error_occurred"}</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">' + response.message + '</div>';
                    resultDiv.style.display = 'block';
                    
                    if (response.success) {
                        uploadDashboardModuleBtn.style.display = 'none';
                        document.getElementById('installDashboardModuleBtn').style.display = 'inline-block';
                    }
                }
            });
        });
    }
    
    // System-Modul hochladen
    const uploadSystemModuleBtn = document.getElementById('uploadSystemModuleBtn');
    if (uploadSystemModuleBtn) {
        uploadSystemModuleBtn.addEventListener('click', function() {
            const form = document.getElementById('uploadSystemModuleForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('systemModuleUploadResult');
            
            sendRequest('module-manager.php', 'POST', formData, function(response, error) {
                if (error) {
                    resultDiv.innerHTML = '<div class="alert alert-danger">{translate key="error_occurred"}</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">' + response.message + '</div>';
                    resultDiv.style.display = 'block';
                    
                    if (response.success) {
                        uploadSystemModuleBtn.style.display = 'none';
                        document.getElementById('installSystemModuleBtn').style.display = 'inline-block';
                    }
                }
            });
        });
    }
    
    // Dashboard-Modul installieren
    const installDashboardModuleBtn = document.getElementById('installDashboardModuleBtn');
    if (installDashboardModuleBtn) {
        installDashboardModuleBtn.addEventListener('click', function() {
            const resultDiv = document.getElementById('dashboardModuleUploadResult');
            
            sendRequest('module-manager.php', 'POST', { action: 'install' }, function(response, error) {
                if (error) {
                    resultDiv.innerHTML = '<div class="alert alert-danger">{translate key="error_occurred"}</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">' + response.message + '</div>';
                    
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });
    }
    
    // System-Modul installieren
    const installSystemModuleBtn = document.getElementById('installSystemModuleBtn');
    if (installSystemModuleBtn) {
        installSystemModuleBtn.addEventListener('click', function() {
            const resultDiv = document.getElementById('systemModuleUploadResult');
            
            sendRequest('module-manager.php', 'POST', { action: 'install' }, function(response, error) {
                if (error) {
                    resultDiv.innerHTML = '<div class="alert alert-danger">{translate key="error_occurred"}</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-' + (response.success ? 'success' : 'danger') + '">' + response.message + '</div>';
                    
                    if (response.success) {
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });
    }
    
    // Modul deinstallieren (Modal öffnen)
    const uninstallButtons = document.querySelectorAll('.uninstall-module');
    uninstallButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const moduleId = this.getAttribute('data-module-id');
            const moduleName = this.getAttribute('data-module-name');
            const moduleType = this.getAttribute('data-module-type');
            
            document.getElementById('uninstallModuleName').textContent = moduleName;
            const confirmBtn = document.getElementById('confirmUninstallBtn');
            confirmBtn.setAttribute('data-module-id', moduleId);
            confirmBtn.setAttribute('data-module-type', moduleType);
            
            // Modal mit Bootstrap 5 öffnen
            const uninstallModal = new bootstrap.Modal(document.getElementById('uninstallModuleModal'));
            uninstallModal.show();
        });
    });
    
    // Modul deinstallieren (Bestätigung)
    const confirmUninstallBtn = document.getElementById('confirmUninstallBtn');
    if (confirmUninstallBtn) {
        confirmUninstallBtn.addEventListener('click', function() {
            const moduleId = this.getAttribute('data-module-id');
            const moduleType = this.getAttribute('data-module-type');
            
            sendRequest('module-manager.php', 'POST', {
                action: 'uninstall',
                module_id: moduleId,
                module_type: moduleType
            }, function(response, error) {
                if (error || !response.success) {
                    alert(error || response.message || '{translate key="error_occurred"}');
                } else {
                    // Modal mit Bootstrap 5 schließen
                    const uninstallModal = bootstrap.Modal.getInstance(document.getElementById('uninstallModuleModal'));
                    uninstallModal.hide();
                    location.reload();
                }
            });
        });
    }
    
    // Modul aktivieren/deaktivieren
    const toggleButtons = document.querySelectorAll('.toggle-module');
    toggleButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const moduleId = this.getAttribute('data-module-id');
            const isActive = this.getAttribute('data-is-active');
            const moduleType = this.getAttribute('data-module-type');
            
            sendRequest('module-manager.php', 'POST', {
                action: 'toggle_active',
                module_id: moduleId,
                is_active: isActive,
                module_type: moduleType
            }, function(response, error) {
                if (error || !response.success) {
                    alert(error || response.message || '{translate key="error_occurred"}');
                } else {
                    location.reload();
                }
            });
        });
    });
    
    // Einfache Tabellensortierung für die Modultabellen
    function initSortableTable(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const headers = table.querySelectorAll('th');
        headers.forEach(function(header, index) {
            header.addEventListener('click', function() {
                sortTable(table, index);
            });
            header.style.cursor = 'pointer';
            header.title = '{translate key="click_to_sort"}';
        });
    }
    
    function sortTable(table, columnIndex) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = table.getAttribute('data-sort-dir') !== 'asc';
        
        // Sortierrichtung umschalten
        table.setAttribute('data-sort-dir', isAscending ? 'asc' : 'desc');
        
        // Zeilen sortieren
        rows.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[columnIndex].textContent.trim();
            const cellB = rowB.querySelectorAll('td')[columnIndex].textContent.trim();
            
            if (isAscending) {
                return cellA.localeCompare(cellB, 'de');
            } else {
                return cellB.localeCompare(cellA, 'de');
            }
        });
        
        // Sortierte Zeilen wieder einfügen
        rows.forEach(function(row) {
            tbody.appendChild(row);
        });
    }
    
    // Tabellen initialisieren
    initSortableTable('dashboardModulesTable');
    initSortableTable('systemModulesTable');
    
    // Suchfunktion für Tabellen
    function initTableSearch(tableId, searchId) {
        const searchInput = document.getElementById(searchId);
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Suchfunktionen initialisieren
    initTableSearch('dashboardModulesTable', 'dashboardModulesTableSearch');
    initTableSearch('systemModulesTable', 'systemModulesTableSearch');
});
</script>

{include file="admin/footer.tpl"}

