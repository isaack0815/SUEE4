{include file="admin/header.tpl" title={translate key="cms_management"}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{translate key="cms_pages"}</h4>
                <button type="button" class="btn btn-light btn-sm" id="addPageBtn" 
                        {if !$user->hasPermission('cms.create')}disabled{/if}>
                    <i class="bi bi-plus-circle"></i> {translate key="add_page"}
                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Statusfilter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">{translate key="filter_by_status"}</span>
                            <select class="form-select" id="statusFilter">
                                <option value="all">{translate key="all_statuses"}</option>
                                <option value="published">{translate key="status_published"}</option>
                                <option value="draft">{translate key="status_draft"}</option>
                                <option value="archived">{translate key="status_archived"}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">{translate key="search"}</span>
                            <input type="text" class="form-control" id="searchInput" placeholder="{translate key="search_pages"}">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Seitentabelle -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="pagesTable">
                        <thead>
                            <tr>
                                <th>{translate key="page_title"}</th>
                                <th>{translate key="page_slug"}</th>
                                <th>{translate key="page_status"}</th>
                                <th>{translate key="page_updated"}</th>
                                <th>{translate key="actions"}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Wird dynamisch mit JavaScript gefüllt -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginierung -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Wird dynamisch mit JavaScript gefüllt -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Seite hinzufügen/bearbeiten -->
<div class="modal fade" id="pageModal" tabindex="-1" aria-labelledby="pageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pageModalLabel">{translate key="add_page"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pageForm">
                    <input type="hidden" id="pageId" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="pageTitle" class="form-label">{translate key="page_title"}</label>
                            <input type="text" class="form-control" id="pageTitle" name="title" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pageStatus" class="form-label">{translate key="page_status"}</label>
                            <select class="form-select" id="pageStatus" name="status">
                                <option value="published">{translate key="status_published"}</option>
                                <option value="draft" selected>{translate key="status_draft"}</option>
                                <option value="archived">{translate key="status_archived"}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pageSlug" class="form-label">{translate key="page_slug"}</label>
                        <input type="text" class="form-control" id="pageSlug" name="slug" placeholder="page-url">
                        <div class="form-text">{translate key="slug_hint"}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pageContent" class="form-label">{translate key="page_content"}</label>
                        <textarea class="form-control" id="pageContent" name="content" rows="10"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="pageMetaDescription" class="form-label">{translate key="meta_description"}</label>
                            <textarea class="form-control" id="pageMetaDescription" name="meta_description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="pageMetaKeywords" class="form-label">{translate key="meta_keywords"}</label>
                            <textarea class="form-control" id="pageMetaKeywords" name="meta_keywords" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-success" id="addToMenuBtn">
                    <i class="bi bi-list"></i> {translate key="add_to_menu"}
                </button>
                <button type="button" class="btn btn-primary" id="savePageBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Seite löschen -->
<div class="modal fade" id="deletePageModal" tabindex="-1" aria-labelledby="deletePageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePageModalLabel">{translate key="delete_page"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{translate key="confirm_delete"}</p>
                <p><strong id="deletePageTitle"></strong></p>
                <input type="hidden" id="deletePageId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{translate key="delete"}</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Menüpunkt hinzufügen -->
<div class="modal fade" id="addToMenuModal" tabindex="-1" aria-labelledby="addToMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToMenuModalLabel">{translate key="add_to_menu"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addToMenuForm">
                    <input type="hidden" id="menuPageId" name="page_id" value="">
                    
                    <div class="mb-3">
                        <label for="menuArea" class="form-label">{translate key="menu_area"}</label>
                        <select class="form-select" id="menuArea" name="area" required>
                            <option value="main">{translate key="area_main"}</option>
                            <option value="admin">{translate key="area_admin"}</option>
                            <option value="user">{translate key="area_user"}</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menuParent" class="form-label">{translate key="menu_parent"}</label>
                        <select class="form-select" id="menuParent" name="parent_id">
                            <option value="">{translate key="no_parent"}</option>
                            <!-- Wird dynamisch mit JavaScript gefüllt -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menuName" class="form-label">{translate key="menu_name"}</label>
                        <input type="text" class="form-control" id="menuName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menuIcon" class="form-label">{translate key="menu_icon"}</label>
                        <input type="text" class="form-control" id="menuIcon" name="icon" placeholder="z.B. file-text, link">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
                <button type="button" class="btn btn-primary" id="saveMenuItemBtn">{translate key="save"}</button>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE einbinden -->
<script src="https://cdn.tiny.cloud/1/vkt8wotb4yg0m5wroiy4v72c023wpd44i33m97wyaikyjhea/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="../js/admin/cms.js"></script>

{include file="admin/footer.tpl"}

