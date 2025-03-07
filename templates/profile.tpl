{include file="header.tpl" title={translate key="profile_edit"}}

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="profile-image-container mb-3">
                        {if $userData.profile_image}
                            <img src="{$userData.profile_image}" alt="{$userData.username}" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                        {else}
                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 150px; height: 150px; margin: 0 auto;">
                                <i class="bi bi-person-circle" style="font-size: 5rem;"></i>
                            </div>
                        {/if}
                    </div>
                    <h5 class="mb-1">{$userData.username}</h5>
                    <p class="text-muted mb-3">{$userData.email}</p>
                    
                    {if $userData.first_name || $userData.last_name}
                        <p class="mb-1">{$userData.first_name} {$userData.last_name}</p>
                    {/if}
                    
                    {if $userData.phone}
                        <p class="mb-1">{$userData.phone}</p>
                    {/if}
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="list-group shadow mb-4">
                <a href="profile.php?tab=personal" class="list-group-item list-group-item-action {if $activeTab == 'personal'}active{/if}">
                    <i class="bi bi-person me-2"></i> {translate key="personal_info"}
                </a>
                
                {foreach from=$autoincludeModules key=moduleId item=module}
                    <a href="profile.php?tab={$moduleId}" class="list-group-item list-group-item-action {if $activeTab == $moduleId}active{/if}">
                        <i class="bi bi-{$module.icon} me-2"></i> {$module.title}
                    </a>
                {/foreach}
            </div>
        </div>
        
        <div class="col-lg-9">
            {if $message}
                <div class="alert alert-{$messageType} alert-dismissible fade show" role="alert">
                    {translate key=$message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {/if}
            
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h4 class="mb-0">
                        {if $activeTab == 'personal'}
                            {translate key="personal_info"}
                        {else}
                            {$autoincludeModules[$activeTab].title}
                        {/if}
                    </h4>
                </div>
                <div class="card-body">
                    {if $activeTab == 'personal'}
                        <!-- Persönliche Informationen -->
                        <form action="profile.php?tab=personal" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="mb-4">
                                <label class="form-label">{translate key="profile_image"}</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        {if $userData.profile_image}
                                            <img src="{$userData.profile_image}" alt="{$userData.username}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                        {else}
                                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 100px; height: 100px;">
                                                <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                                            </div>
                                        {/if}
                                    </div>
                                    <div>
                                        <input type="file" class="form-control mb-2" name="profile_image" id="profile_image" accept="image/jpeg,image/png,image/gif">
                                        <div class="form-text">{translate key="image_upload_hint"}</div>
                                        
                                        {if $userData.profile_image}
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                                <label class="form-check-label" for="remove_image">
                                                    {translate key="remove_image"}
                                                </label>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">{translate key="first_name"}</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{$userData.first_name|default:''}">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">{translate key="last_name"}</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{$userData.last_name|default:''}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">{translate key="phone"}</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{$userData.phone|default:''}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">{translate key="bio"}</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4">{$userData.bio|default:''}</textarea>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> {translate key="save_profile"}
                                </button>
                            </div>
                        </form>
                    {else}
                        <!-- Dynamischer Tab-Inhalt -->
                        {$tabContent}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

{include file="footer.tpl"}

