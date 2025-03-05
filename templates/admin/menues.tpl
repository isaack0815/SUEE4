{include file="admin/header.tpl" title={translate key="menu_management"}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{translate key="menu_management"}</h4>
                <button type="button" class="btn btn-light btn-sm" id="addMenuBtn" 
                        {if !$user->hasPermission('menu.create')}disabled{/if}>
                    <i class="bi bi-plus-circle"></i> {translate key="add_menu_item"}
                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Menübereich-Auswahl -->
                <div class="mb-3">
                    <label for="areaSelect" class="form-label">{translate key="menu_area"}:</label>
                    <select class="form-select" id="areaSelect">
                        <option value="main">{translate key="area_main"}</option>
                        <option value="admin">{translate key="area_admin"}</option>
                        <option value="user">{translate key="area_user"}</option>
                    </select>
                </div>
                
                <!-- Menütabelle -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="menuTable">
                        <thead>
                            <tr>
                                <th>{translate key="menu_name"}</th>
                                <th>{translate key="menu_url"}</th>
                                <th>{translate key="menu_parent"}</th>
                                <th>{translate key="menu_order"}</th>
                                <th>{translate key="menu_active"}</th>
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

<!-- Modal für Menüpunkt hinzufügen/bearbeiten -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="menuModalLabel">{translate key="add_menu_item"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="menuForm">
                    <input type="hidden" id="menuId" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuArea" class="form-label">{translate key="menu_area"}</label>
                            <select class="form-select" id="menuArea" name="area" required>
                                <option value="main">{translate key="area_main"}</option>
                                <option value="admin">{translate key="area_admin"}</option>
                                <option value="user">{translate key="area_user"}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="menuParent" class="form-label">{translate key="menu_parent"}</label>
                            <select class="form-select" id="menuParent" name="parent_id">
                                <option value="">{translate key="no_parent"}</option>
                                <!-- Wird dynamisch mit JavaScript gefüllt -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuName" class="form-label">{translate key="menu_name"}</label>
                            <input type="text" class="form-control" id="menuName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="menuUrl" class="form-label">{translate key="menu_url"}</label>
                            <input type="text" class="form-control" id="menuUrl" name="url" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuIcon" class="form-label">{translate key="menu_icon"}</label>
                            <input type="text" class="form-control" id="menuIcon" name="icon" placeholder="z.B. home, gear, user">
                        </div>
                        <div class="col-md-6">
                            <label for="menuOrder" class="form-label">{translate key="menu_order"}</label>
                            <input type="number" class="form-control" id="menuOrder" name="sort_order" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuDescription" class="form-label">{translate key="description"}</label>
                            <input type="text" class="form-control" id="menuDescription" name="description">
                        </div>
                        <div class="col-md-6">
                            <label for="menuModule" class="form-label">{translate key="menu_module"}</label>
                            <input type="text" class="form-control" id="menuModule" name="module">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuGroup" class="form-label">{translate key="menu_required_group"}</label>
                            <select class="form-select" id="menuGroup" name="required_group_id">
                                <option value="">{translate key="none"}</option>
                                {foreach from=$groups item=group}
                                    <option value="{$group.id}">{$group.name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="menuPermission" class="form-label">{translate key="menu_required_permission"}</label>
                            <select class="form-select" id="menuPermission" name="required_permission_id">
                                <option value="">{translate key="none"}</option>
                                {foreach from=$permissions item=perm}
                                    <option value="{$perm.id}">{$perm.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="menuActive" name="is_active" checked>
                        <label class="form-check-label" for="menuActive">{translate key="menu_active"}</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="saveMenuBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Menüpunkt löschen -->
<div class="modal fade" id="deleteMenuModal" tabindex="-1" aria-labelledby="deleteMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMenuModalLabel">{translate key="delete_menu_item"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{translate key="confirm_delete"}</p>
                <input type="hidden" id="deleteMenuId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{translate key="delete_menu_item"}</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/admin/menues.js"></script>

{include file="admin/footer.tpl"}