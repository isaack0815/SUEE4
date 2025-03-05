{include file="admin/header.tpl" title={translate key="permission_management"}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{translate key="permission_management"}</h4>
                <button type="button" class="btn btn-light btn-sm" id="addPermissionBtn" 
                        {if !$user->hasPermission('permission.create')}disabled{/if}>
                    <i class="bi bi-plus-circle"></i> {translate key="add_permission"}
                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Berechtigungstabelle -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="permissionTable">
                        <thead>
                            <tr>
                                <th>{translate key="permission_name"}</th>
                                <th>{translate key="description"}</th>
                                <th>{translate key="created_at"}</th>
                                <th>{translate key="actions"}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Wird dynamisch mit JavaScript gefüllt -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Berechtigung hinzufügen/bearbeiten -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel">{translate key="add_permission"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permissionForm">
                    <input type="hidden" id="permissionId" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">{translate key="permission_name"}</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="permissionDescription" class="form-label">{translate key="description"}</label>
                        <textarea class="form-control" id="permissionDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="savePermissionBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Berechtigung löschen -->
<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePermissionModalLabel">{translate key="delete_permission"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{translate key="confirm_delete"}</p>
                <input type="hidden" id="deletePermissionId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{translate key="delete"}</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/admin/permissions.js"></script>

{include file="admin/footer.tpl"}

