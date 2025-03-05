{include file="admin/header.tpl" title={translate key="group_management"}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{translate key="group_management"}</h4>
                <button type="button" class="btn btn-light btn-sm" id="addGroupBtn">
                    <i class="bi bi-plus-circle"></i> {translate key="add_group"}
                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Gruppentabelle -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="groupsTable">
                        <thead>
                            <tr>
                                <th>{translate key="group_name"}</th>
                                <th>{translate key="group_description"}</th>
                                <th>{translate key="created_at"}</th>
                                <th>{translate key="updated_at"}</th>
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

<!-- Modal für Gruppe hinzufügen/bearbeiten -->
<div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalLabel">{translate key="add_group"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="groupForm">
                    <input type="hidden" id="groupId" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="groupName" class="form-label">{translate key="group_name"}</label>
                        <input type="text" class="form-control" id="groupName" name="name" required>
                        <div class="invalid-feedback">{translate key="name_required"}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="groupDescription" class="form-label">{translate key="group_description"}</label>
                        <textarea class="form-control" id="groupDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="saveGroupBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Gruppe löschen -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGroupModalLabel">{translate key="delete_group"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{translate key="confirm_delete"}</p>
                <input type="hidden" id="deleteGroupId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{translate key="delete_group"}</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/admin/groups.js"></script>

{include file="admin/footer.tpl"}

