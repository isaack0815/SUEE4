{include file="header.tpl" title="Module verwalten"}

<div class="container mt-4">
    <h1>Module verwalten</h1>
    
    {if $message}
        <div class="alert alert-{$messageType} alert-dismissible fade show" role="alert">
            {$message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Schließen"></button>
        </div>
    {/if}
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Neues Modul hochladen</h5>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="module" class="form-label">Modul-ZIP-Datei</label>
                    <input type="file" class="form-control" id="module" name="module" accept=".zip" required>
                    <div class="form-text">Wählen Sie eine ZIP-Datei aus, die ein gültiges Modul enthält.</div>
                </div>
                <button type="submit" name="upload" class="btn btn-primary">Hochladen und installieren</button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Installierte Module</h5>
        </div>
        <div class="card-body">
            {if $modules|@count > 0}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Beschreibung</th>
                                <th>Version</th>
                                <th>Autor</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$modules item=module}
                                <tr>
                                    <td>{$module.name}</td>
                                    <td>{$module.description}</td>
                                    <td>{$module.version}</td>
                                    <td>{$module.author}</td>
                                    <td>
                                        <span class="badge bg-{if $module.is_active}success{else}danger{/if}">
                                            {if $module.is_active}Aktiv{else}Inaktiv{/if}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="module_id" value="{$module.module_id}">
                                            <input type="hidden" name="active" value="{if $module.is_active}0{else}1{/if}">
                                            <button type="submit" name="toggle_active" class="btn btn-sm btn-{if $module.is_active}warning{else}success{/if}">
                                                {if $module.is_active}Deaktivieren{else}Aktivieren{/if}
                                            </button>
                                        </form>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Modul deinstallieren möchten?');">
                                            <input type="hidden" name="module_id" value="{$module.module_id}">
                                            <button type="submit" name="uninstall" class="btn btn-sm btn-danger">Deinstallieren</button>
                                        </form>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            {else}
                <div class="alert alert-info mb-0">
                    Es sind keine Module installiert.
                </div>
            {/if}
        </div>
    </div>
</div>

{include file="footer.tpl"}

