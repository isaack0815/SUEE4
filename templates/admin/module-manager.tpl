{include file="admin/header.tpl" title="Modul-Manager"}

<div class="container-fluid px-4">
    <h1 class="mt-4">Modul-Manager</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Modul-Manager</li>
    </ol>
    
    {if $message}
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {$message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    {/if}
    
    {if $error}
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {$error}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    {/if}
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-upload me-1"></i>
            Neues Modul hochladen
        </div>
        <div class="card-body">
            <form action="module-manager.php?action=upload" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="module_file" class="form-label">{translate key="module_file_zip"}</label>
                    <input class="form-control" type="file" id="module_file" name="module_file" accept=".zip" required>
                </div>
                <div class="mb-3">
                    <label for="module_type" class="form-label">{translate key="module_typ"}</label>
                    <select class="form-select" id="module_type" name="module_type">
                        <option value="dashboard">{translate key="dashboard_modules"}</option>
                        <option value="system">{translate key="system_modules"}</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Hochladen</button>
            </form>
        </div>
    </div>
    
    <ul class="nav nav-tabs mb-4" id="moduleTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="true">{translate key="dashboard_modules"}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">{translate key="system_modules"}</button>
        </li>
    </ul>
    
    <div class="tab-content" id="moduleTabContent">
        <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-th-large me-1"></i>
                    {translate key="dashboard_modules"}
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
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
                            {if $dashboard_modules|@count > 0}
                                {foreach from=$dashboard_modules item=module}
                                <tr>
                                    <td>{$module.name}</td>
                                    <td>{$module.description}</td>
                                    <td>{$module.version|default:'--'}</td>
                                    <td>{$module.author|default:'--'}</td>
                                    <td>
                                        {if $module.installed == 1}
                                            {if $module.is_active}
                                                <span class="badge bg-success">{translate key="active"}</span>
                                            {else}
                                                <span class="badge bg-secondary">{translate key="inactive"}</span>
                                            {/if}
                                        {else}
                                            <span class="badge bg-warning">{translate key="not_installed"}</span>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $module.installed == 1}
                                            {if $module.is_active}
                                                <a href="module-manager.php?action=toggle&id={$module.module_id}&type=dashboard&active=0" class="btn btn-sm btn-warning" title="{translate key="deactivate"}">
                                                    <i class="fas fa-power-off"></i> {translate key="deactivate"}
                                                </a>
                                            {else}
                                                <a href="module-manager.php?action=toggle&id={$module.module_id}&type=dashboard&active=1" class="btn btn-sm btn-success" title="{translate key="activate"}">
                                                    <i class="fas fa-power-off"></i> {translate key="activate"}
                                                </a>
                                            {/if}
                                            <a href="module-manager.php?action=uninstall&id={$module.module_id}&type=dashboard" class="btn btn-sm btn-danger" title="{translate key="uninstall"}" onclick="return confirm('Sind Sie sicher, dass Sie dieses Modul deinstallieren möchten?');">
                                                <i class="fas fa-trash"></i> {translate key="uninstall"}
                                            </a>
                                        {else}
                                            <a href="module-manager.php?action=install&id={$module.module_id}&type=dashboard" class="btn btn-sm btn-primary" title="{translate key="install"}">
                                                <i class="fas fa-download"></i> {translate key="install"}
                                            </a>
                                            <a href="module-manager.php?action=delete&id={$module.module_id}&type=dashboard" class="btn btn-sm btn-danger" title="{translate key="delete"}" onclick="return confirm('Sind Sie sicher, dass Sie dieses Modul löschen möchten?');">
                                                <i class="fas fa-trash"></i> {translate key="delete"}
                                            </a>
                                        {/if}
                                    </td>
                                </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="6" class="text-center">Keine Dashboard-Module installiert.</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cogs me-1"></i>
                    {translate key="system_modules"}
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
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
                            {if $system_modules|@count > 0}
                                {foreach from=$system_modules item=module}
                                <tr>
                                    <td>{$module.name}</td>
                                    <td>{$module.description}</td>
                                    <td>{$module.version|default:'--'}</td>
                                    <td>{$module.author|default:'--'}</td>
                                    <td>
                                        {if $module.installed == 1}
                                            {if $module.is_active}
                                                <span class="badge bg-success">{translate key="active"}</span>
                                            {else}
                                                <span class="badge bg-secondary">{translate key="inactive"}</span>
                                            {/if}
                                        {else}
                                            <span class="badge bg-warning">{translate key="not_installed"}</span>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $module.installed == 1}
                                            {if $module.is_active}
                                                <a href="module-manager.php?action=toggle&id={$module.module_id}&type=system&active=0" class="btn btn-sm btn-warning" title="{translate key="deactivate"}">
                                                    <i class="fas fa-power-off"></i> {translate key="deactivate"}
                                                </a>
                                            {else}
                                                <a href="module-manager.php?action=toggle&id={$module.module_id}&type=system&active=1" class="btn btn-sm btn-success" title="{translate key="activate"}">
                                                    <i class="fas fa-power-off"></i> {translate key="activate"}
                                                </a>
                                            {/if}
                                            <a href="module-manager.php?action=uninstall&id={$module.module_id}&type=system" class="btn btn-sm btn-danger" title="{translate key="uninstall"}" onclick="return confirm('Sind Sie sicher, dass Sie dieses Modul deinstallieren möchten?');">
                                                <i class="fas fa-trash"></i> {translate key="uninstall"}
                                            </a>
                                        {else}
                                            <a href="module-manager.php?action=install&id={$module.module_id}&type=system" class="btn btn-sm btn-primary" title="{translate key="install"}">
                                                <i class="fas fa-download"></i> {translate key="install"}
                                            </a>
                                            <a href="module-manager.php?action=delete&id={$module.module_id}&type=system" class="btn btn-sm btn-danger" title="{translate key="delete"}" onclick="return confirm('Sind Sie sicher, dass Sie dieses Modul löschen möchten?');">
                                                <i class="fas fa-trash"></i> {translate key="delete"}
                                            </a>
                                        {/if}
                                    </td>
                                </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="6" class="text-center">Keine System-Module installiert.</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Installationsdetails Modal -->
<div class="modal fade" id="installationDetailsModal" tabindex="-1" aria-labelledby="installationDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="installationDetailsModalLabel">Installationsdetails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {if isset($installation_details)}
                    <div class="mb-4">
                        <h6>Modulinformationen</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Name:</th>
                                <td>{$installation_details.module.name}</td>
                            </tr>
                            <tr>
                                <th>Beschreibung:</th>
                                <td>{$installation_details.module.description}</td>
                            </tr>
                            <tr>
                                <th>Version:</th>
                                <td>{$installation_details.module.version|default:'--'}</td>
                            </tr>
                            <tr>
                                <th>Autor:</th>
                                <td>{$installation_details.module.author|default:'--'}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="mb-4">
                        <h6>Installierte Dateien</h6>
                        {if !empty($installation_details.files)}
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Datei</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$installation_details.files item=file}
                                            <tr>
                                                <td>{$file.path}</td>
                                                <td>
                                                    {if $file.success}
                                                        <span class="badge bg-success">Erfolgreich</span>
                                                    {else}
                                                        <span class="badge bg-danger">Fehler</span>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <div class="alert alert-warning">Keine Dateien installiert.</div>
                        {/if}
                    </div>
                    
                    {if !empty($installation_details.menu_items)}
                        <div class="mb-4">
                            <h6>Menüeinträge</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Bereich</th>
                                            <th>URL</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$installation_details.menu_items item=item}
                                            <tr>
                                                <td>{$item.title}</td>
                                                <td>{$item.area}</td>
                                                <td>{$item.url}</td>
                                                <td>
                                                    {if $item.success}
                                                        <span class="badge bg-success">Erfolgreich</span>
                                                    {else}
                                                        <span class="badge bg-danger">Fehler</span>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    {/if}
                    
                    {if !empty($installation_details.permissions)}
                        <div class="mb-4">
                            <h6>Berechtigungen</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Beschreibung</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$installation_details.permissions item=permission}
                                            <tr>
                                                <td>{$permission.name}</td>
                                                <td>{$permission.description}</td>
                                                <td>
                                                    {if $permission.success}
                                                        <span class="badge bg-success">Erfolgreich</span>
                                                    {else}
                                                        <span class="badge bg-danger">Fehler</span>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    {/if}
                    
                    {if !empty($installation_details.database_tables)}
                        <div class="mb-4">
                            <h6>Datenbanktabellen</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tabellenname</th>
                                            <th>Aktion</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$installation_details.database_tables item=table}
                                            <tr>
                                                <td>{$table.name}</td>
                                                <td>{$table.action}</td>
                                                <td>
                                                    {if $table.success}
                                                        <span class="badge bg-success">Erfolgreich</span>
                                                    {else}
                                                        <span class="badge bg-danger">Fehler</span>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    {/if}
                    
                    <div class="mb-4">
                        <h6>Installationsprotokoll</h6>
                        <div class="border p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            {foreach from=$installation_details.logs item=log}
                                <div class="log-entry {if $log.type == 'error'}text-danger{elseif $log.type == 'warning'}text-warning{elseif $log.type == 'success'}text-success{else}text-info{/if}">
                                    <i class="fas {if $log.type == 'error'}fa-times-circle{elseif $log.type == 'warning'}fa-exclamation-triangle{elseif $log.type == 'success'}fa-check-circle{else}fa-info-circle{/if} me-2"></i>
                                    {$log.message}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                {else}
                    <div class="alert alert-info">Keine Installationsdetails verfügbar.</div>
                {/if}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript für das Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal anzeigen, wenn Parameter vorhanden
    {if $show_details_modal}
        var installationDetailsModal = new bootstrap.Modal(document.getElementById('installationDetailsModal'));
        installationDetailsModal.show();
    {/if}
});
</script>
{include file="admin/footer.tpl"}

