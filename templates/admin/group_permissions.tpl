{include file="admin/header.tpl" title={translate key="assign_permissions"}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{translate key="assign_permissions"} - {$group.name}</h4>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <form id="permissionsForm">
                    <input type="hidden" id="groupId" value="{$group.id}">
                    
                    <div class="mb-3">
                        <label class="form-label">{translate key="permissions"}</label>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="50px"></th>
                                        <th>{translate key="permission_name"}</th>
                                        <th>{translate key="permission_description"}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$permissions item=permission}
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                           id="permission_{$permission.id}" 
                                                           name="permissions[]" 
                                                           value="{$permission.id}"
                                                           {if $permission.assigned}checked{/if}>
                                                </div>
                                            </td>
                                            <td>
                                                <label class="form-check-label" for="permission_{$permission.id}">
                                                    {$permission.name}
                                                </label>
                                            </td>
                                            <td>{$permission.description}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="groups.php" class="btn btn-secondary">{translate key="cancel"}</a>
                        <button type="button" id="savePermissionsBtn" class="btn btn-primary">{translate key="save"}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../js/admin/group_permissions.js"></script>

{include file="admin/footer.tpl"}