<?php
/* Smarty version 5.4.3, created on 2025-03-06 10:32:39
  from 'file:admin/cms.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c96bb73e5b39_94982413',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bf2cb56825c75692c919181d56f7239ff81bc491' => 
    array (
      0 => 'admin/cms.tpl',
      1 => 1741253553,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c96bb73e5b39_94982413 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cms_management"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cms_pages"), $_smarty_tpl);?>
</h4>
                <button type="button" class="btn btn-light btn-sm" id="addPageBtn" 
                        <?php if (!$_smarty_tpl->getValue('user')->hasPermission('cms.create')) {?>disabled<?php }?>>
                    <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_page"), $_smarty_tpl);?>

                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Statusfilter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"filter_by_status"), $_smarty_tpl);?>
</span>
                            <select class="form-select" id="statusFilter">
                                <option value="all"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"all_statuses"), $_smarty_tpl);?>
</option>
                                <option value="published"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"status_published"), $_smarty_tpl);?>
</option>
                                <option value="draft"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"status_draft"), $_smarty_tpl);?>
</option>
                                <option value="archived"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"status_archived"), $_smarty_tpl);?>
</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"search"), $_smarty_tpl);?>
</span>
                            <input type="text" class="form-control" id="searchInput" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"search_pages"), $_smarty_tpl);?>
">
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
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_title"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_slug"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_status"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_updated"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"actions"), $_smarty_tpl);?>
</th>
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
                <h5 class="modal-title" id="pageModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_page"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pageForm">
                    <input type="hidden" id="pageId" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="pageTitle" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_title"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="pageTitle" name="title" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pageStatus" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_status"), $_smarty_tpl);?>
</label>
                            <select class="form-select" id="pageStatus" name="status">
                                <option value="published"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"status_published"), $_smarty_tpl);?>
</option>
                                <option value="draft" selected><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"status_draft"), $_smarty_tpl);?>
</option>
                                <option value="archived"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"status_archived"), $_smarty_tpl);?>
</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pageSlug" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_slug"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="pageSlug" name="slug" placeholder="page-url">
                        <div class="form-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"slug_hint"), $_smarty_tpl);?>
</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pageContent" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_content"), $_smarty_tpl);?>
</label>
                        <textarea class="form-control" id="pageContent" name="content" rows="10"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="pageMetaDescription" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"meta_description"), $_smarty_tpl);?>
</label>
                            <textarea class="form-control" id="pageMetaDescription" name="meta_description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="pageMetaKeywords" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"meta_keywords"), $_smarty_tpl);?>
</label>
                            <textarea class="form-control" id="pageMetaKeywords" name="meta_keywords" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-success" id="addToMenuBtn">
                    <i class="bi bi-list"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_to_menu"), $_smarty_tpl);?>

                </button>
                <button type="button" class="btn btn-primary" id="savePageBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Seite löschen -->
<div class="modal fade" id="deletePageModal" tabindex="-1" aria-labelledby="deletePageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePageModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_page"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_delete"), $_smarty_tpl);?>
</p>
                <p><strong id="deletePageTitle"></strong></p>
                <input type="hidden" id="deletePageId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Menüpunkt hinzufügen -->
<div class="modal fade" id="addToMenuModal" tabindex="-1" aria-labelledby="addToMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToMenuModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_to_menu"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addToMenuForm">
                    <input type="hidden" id="menuPageId" name="page_id" value="">
                    
                    <div class="mb-3">
                        <label for="menuArea" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_area"), $_smarty_tpl);?>
</label>
                        <select class="form-select" id="menuArea" name="area" required>
                            <option value="main"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"area_main"), $_smarty_tpl);?>
</option>
                            <option value="admin"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"area_admin"), $_smarty_tpl);?>
</option>
                            <option value="user"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"area_user"), $_smarty_tpl);?>
</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menuParent" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_parent"), $_smarty_tpl);?>
</label>
                        <select class="form-select" id="menuParent" name="parent_id">
                            <option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"no_parent"), $_smarty_tpl);?>
</option>
                            <!-- Wird dynamisch mit JavaScript gefüllt -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menuName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_name"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="menuName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="menuIcon" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_icon"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="menuIcon" name="icon" placeholder="z.B. file-text, link">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-primary" id="saveMenuItemBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE einbinden -->
<?php echo '<script'; ?>
 src="https://cdn.tiny.cloud/1/vkt8wotb4yg0m5wroiy4v72c023wpd44i33m97wyaikyjhea/tinymce/7/tinymce.min.js" referrerpolicy="origin"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="../js/admin/cms.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
