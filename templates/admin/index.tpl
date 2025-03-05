{include file="admin/header.tpl" title={translate key="admin_dashboard"}}

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{translate key="admin_dashboard"}</h1>
</div>

<div class="row">
    <!-- Statistiken -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{translate key="statistics"}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="h1">{$userCount}</div>
                        <div class="text-muted">{translate key="users"}</div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="h1">{$groupCount}</div>
                        <div class="text-muted">{translate key="groups"}</div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="h1">{$permissionCount}</div>
                        <div class="text-muted">{translate key="permissions"}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Letzte Benutzeraktivitäten -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">{translate key="recent_user_activity"}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    {foreach from=$recentUsers item=user}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {$user.username}
                            <span class="badge bg-primary rounded-pill">{$user.last_login|date_format:"%d.%m.%Y %H:%M"}</span>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Systeminfo -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">{translate key="system_info"}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        PHP Version
                        <span class="badge bg-secondary rounded-pill">{$systemInfo.php_version}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Server
                        <span class="badge bg-secondary rounded-pill">{$systemInfo.server_software}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Database
                        <span class="badge bg-secondary rounded-pill">{$systemInfo.database_version}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Smarty
                        <span class="badge bg-secondary rounded-pill">{$systemInfo.smarty_version}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Schnellzugriff -->
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0">{translate key="quick_access"}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {if $user->hasPermission('user.view')}
                        <div class="col-md-4 mb-3">
                            <a href="users.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                {translate key="user_management"}
                            </a>
                        </div>
                    {/if}
                    
                    {if $user->hasPermission('group.view')}
                        <div class="col-md-4 mb-3">
                            <a href="groups.php" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-diagram-3 fs-1 mb-2"></i>
                                {translate key="group_management"}
                            </a>
                        </div>
                    {/if}
                    
                    {if $user->hasPermission('permission.view')}
                        <div class="col-md-4 mb-3">
                            <a href="permissions.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-shield-lock fs-1 mb-2"></i>
                                {translate key="permission_management"}
                            </a>
                        </div>
                    {/if}
                    
                    {if $user->hasPermission('menu.view')}
                        <div class="col-md-4 mb-3">
                            <a href="menus.php" class="btn btn-outline-danger w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-list fs-1 mb-2"></i>
                                {translate key="menu_management"}
                            </a>
                        </div>
                    {/if}
                    
                    {if $user->hasPermission('settings.view')}
                        <div class="col-md-4 mb-3">
                            <a href="settings.php" class="btn btn-outline-dark w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-gear fs-1 mb-2"></i>
                                {translate key="settings"}
                            </a>
                        </div>
                    {/if}
                    
                    <div class="col-md-4 mb-3">
                        <a href="../index.php" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                            <i class="bi bi-house fs-1 mb-2"></i>
                            {translate key="back_to_site"}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Systeminformationen (erweitert) -->
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">{translate key="advanced_system_info"}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <tbody>
                            <tr>
                                <th>{translate key="memory_limit"}</th>
                                <td>{$systemInfo.memory_limit}</td>
                            </tr>
                            <tr>
                                <th>{translate key="max_execution_time"}</th>
                                <td>{$systemInfo.max_execution_time}</td>
                            </tr>
                            <tr>
                                <th>{translate key="upload_max_filesize"}</th>
                                <td>{$systemInfo.upload_max_filesize}</td>
                            </tr>
                            <tr>
                                <th>{translate key="post_max_size"}</th>
                                <td>{$systemInfo.post_max_size}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}

