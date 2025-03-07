{include file="admin/header.tpl" title="{translate key="user_details"}: {$userData.username}"}

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">{translate key="admin_dashboard"}</a></li>
                    <li class="breadcrumb-item"><a href="users.php">{translate key="user_management"}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{$userData.username}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Benutzerdetails -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{translate key="user_details"}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder mb-3">
                            <i class="bi bi-person-circle" style="font-size: 5rem;"></i>
                        </div>
                        <h4>{$userData.username}</h4>
                        <p class="text-muted">{$userData.email}</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{translate key="user_id"}</strong>
                            <span>{$userData.id}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{translate key="created_at"}</strong>
                            <span>{$userData.created_at|date_format:"%d.%m.%Y %H:%M"}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>{translate key="last_login"}</strong>
                            <span>{$userData.last_login|date_format:"%d.%m.%Y %H:%M"}</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="users.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> {translate key="back"}
                        </a>
                        {if $user->hasPermission('user.edit')}
                            <button type="button" class="btn btn-primary" id="editUserBtn" data-id="{$userData.id}">
                                <i class="bi bi-pencil"></i> {translate key="edit_user"}
                            </button>
                        {/if}
                    </div>
                </div>
            </div>
            
            <!-- Benutzergruppen -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">{translate key="user_groups"}</h5>
                </div>
                <div class="card-body">
                    {if $userGroups|@count > 0}
                        <ul class="list-group">
                            {foreach from=$userGroups item=group}
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {$group.name}
                                    <span class="badge bg-primary rounded-pill">{$group.description|default:""}</span>
                                </li>
                            {/foreach}
                        </ul>
                    {else}
                        <p class="text-muted text-center">{translate key="no_groups_assigned"}</p>
                    {/if}
                </div>
                {if $user->hasPermission('user.edit')}
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary w-100" id="manageGroupsBtn" data-id="{$userData.id}">
                            <i class="bi bi-people"></i> {translate key="manage_groups"}
                        </button>
                    </div>
                {/if}
            </div>
        </div>
        
        <!-- Tab-Inhalte -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white p-0">
                    <!-- Tab-Navigation -->
                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        <!-- Profil-Tab (immer vorhanden) -->
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {if $activeTab == 'profile'}active{/if}" 
                               href="user_details.php?id={$userData.id}&tab=profile" 
                               role="tab" aria-selected="{if $activeTab == 'profile'}true{else}false{/if}">
                                <i class="bi bi-person"></i> {translate key="profile"}
                            </a>
                        </li>
                        
                        <!-- Dynamische Tabs aus Autoinclude-Dateien -->
                        {foreach from=$autoincludeModules key=moduleId item=module}
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {if $activeTab == $moduleId}active{/if}" 
                                   href="user_details.php?id={$userData.id}&tab={$moduleId}" 
                                   role="tab" aria-selected="{if $activeTab == $moduleId}true{else}false{/if}">
                                    <i class="bi bi-{$module.icon}"></i> {$module.title}
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Tab-Inhalte -->
                    <div class="tab-content">
                        <!-- Profil-Tab-Inhalt -->
                        {if $activeTab == 'profile'}
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <h4 class="mb-4">{translate key="user_profile"}</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{translate key="username"}</label>
                                            <input type="text" class="form-control" value="{$userData.username}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{translate key="email"}</label>
                                            <input type="email" class="form-control" value="{$userData.email}" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{translate key="created_at"}</label>
                                            <input type="text" class="form-control" value="{$userData.created_at|date_format:"%d.%m.%Y %H:%M"}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{translate key="last_login"}</label>
                                            <input type="text" class="form-control" value="{$userData.last_login|date_format:"%d.%m.%Y %H:%M"}" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="mt-4 mb-3">{translate key="user_groups"}</h5>
                                <div class="mb-3">
                                    {if $userGroups|@count > 0}
                                        {foreach from=$userGroups item=group}
                                            <span class="badge bg-primary me-2 mb-2 p-2">{$group.name}</span>
                                        {/foreach}
                                    {else}
                                        <p class="text-muted">{translate key="no_groups_assigned"}</p>
                                    {/if}
                                </div>
                            </div>
                        {else}
                            <!-- Dynamischer Tab-Inhalt -->
                            <div class="tab-pane fade show active" id="{$activeTab}" role="tabpanel">
                                {$autoincludeModules[$activeTab].content}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Benutzer bearbeiten -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">{translate key="edit_user"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id" value="{$userData.id}">
                    
                    <div class="mb-3">
                        <label for="editUsername" class="form-label">{translate key="username"}</label>
                        <input type="text" class="form-control" id="editUsername" name="username" value="{$userData.username}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">{translate key="email"}</label>
                        <input type="email" class="form-control" id="editEmail" name="email" value="{$userData.email}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">{translate key="password"}</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                        <div class="form-text">{translate key="password_help_text"}</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Gruppen verwalten -->
<div class="modal fade" id="manageGroupsModal" tabindex="-1" aria-labelledby="manageGroupsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageGroupsModalLabel">{translate key="manage_groups"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="manageGroupsForm">
                    <input type="hidden" id="groupsUserId" name="id" value="{$userData.id}">
                    
                    <div id="groupsContainer">
                        <!-- Wird dynamisch mit JavaScript gefüllt -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="saveGroupsBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS für die Tab-Navigation -->
<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    color: #495057;
    border-color: #e9ecef #e9ecef #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 500;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #0d6efd;
}

.tab-content {
    padding: 1rem 0;
}
</style>

<script src="../js/admin/user_details.js"></script>

{include file="admin/footer.tpl"}

