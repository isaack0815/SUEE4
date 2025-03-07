{include file="header.tpl" title="Dashboard"}

<!-- JavaScript für das Dashboard einbinden -->
<script src="{$baseUrl}/js/dashboard.js"></script>

<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard</h1>
    
    {if empty($dashboardModules)}
        <div class="alert alert-info">Keine Module verfügbar</div>
    {else}
        <div class="dashboard-grid" id="dashboard-grid">
            {foreach from=$dashboardModules item=module}
                <div class="dashboard-module" 
                     id="module-{$module.id|escape}" 
                     data-module-id="{$module.id|escape}"
                     data-grid-x="{$module.grid_x|escape}"
                     data-grid-y="{$module.grid_y|escape}"
                     data-grid-width="{$module.grid_width|escape}"
                     data-grid-height="{$module.grid_height|escape}"
                     data-size="{$module.size|escape}">
                    
                    <div class="module-header">
                        <h3>
                            {if $module.icon}<i class="{$module.icon|escape}"></i>{/if}
                            {$module.title|escape}
                        </h3>
                        <div class="module-actions">
                            <button class="btn-module-settings" title="Einstellungen"><i class="fas fa-cog"></i></button>
                            <button class="btn-module-toggle" title="Minimieren/Maximieren"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    
                    <div class="module-content">
                        {$module.content}
                    </div>
                </div>
            {/foreach}
        </div>
    {/if}
</div>

{include file="footer.tpl"}

