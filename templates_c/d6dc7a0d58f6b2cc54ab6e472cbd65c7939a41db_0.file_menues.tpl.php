<?php
/* Smarty version 5.4.3, created on 2025-03-05 13:20:34
  from 'file:admin/menues.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c84192e078b7_64249893',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd6dc7a0d58f6b2cc54ab6e472cbd65c7939a41db' => 
    array (
      0 => 'admin/menues.tpl',
      1 => 1741177233,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c84192e078b7_64249893 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_management"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_management"), $_smarty_tpl);?>
</h4>
                <button type="button" class="btn btn-light btn-sm" id="addMenuBtn" 
                        <?php if (!$_smarty_tpl->getValue('user')->hasPermission('menu.create')) {?>disabled<?php }?>>
                    <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_menu_item"), $_smarty_tpl);?>

                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Menübereich-Auswahl -->
                <div class="mb-3">
                    <label for="areaSelect" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_area"), $_smarty_tpl);?>
:</label>
                    <select class="form-select" id="areaSelect">
                        <option value="main"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"area_main"), $_smarty_tpl);?>
</option>
                        <option value="admin"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"area_admin"), $_smarty_tpl);?>
</option>
                        <option value="user"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"area_user"), $_smarty_tpl);?>
</option>
                    </select>
                </div>
                
                <!-- Menütabelle -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="menuTable">
                        <thead>
                            <tr>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_name"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_url"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_parent"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_order"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_active"), $_smarty_tpl);?>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal für Menüpunkt hinzufügen/bearbeiten -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="menuModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_menu_item"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="menuForm">
                    <input type="hidden" id="menuId" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <label for="menuParent" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_parent"), $_smarty_tpl);?>
</label>
                            <select class="form-select" id="menuParent" name="parent_id">
                                <option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"no_parent"), $_smarty_tpl);?>
</option>
                                <!-- Wird dynamisch mit JavaScript gefüllt -->
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_name"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="menuName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="menuUrl" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_url"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="menuUrl" name="url" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuIcon" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_icon"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="menuIcon" name="icon" placeholder="z.B. home, gear, user">
                        </div>
                        <div class="col-md-6">
                            <label for="menuOrder" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_order"), $_smarty_tpl);?>
</label>
                            <input type="number" class="form-control" id="menuOrder" name="sort_order" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuDescription" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"description"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="menuDescription" name="description">
                        </div>
                        <div class="col-md-6">
                            <label for="menuModule" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_module"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="menuModule" name="module">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="menuGroup" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_required_group"), $_smarty_tpl);?>
</label>
                            <select class="form-select" id="menuGroup" name="required_group_id">
                                <option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"none"), $_smarty_tpl);?>
</option>
                                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groups'), 'group');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('group')->value) {
$foreach0DoElse = false;
?>
                                    <option value="<?php echo $_smarty_tpl->getValue('group')['id'];?>
"><?php echo $_smarty_tpl->getValue('group')['name'];?>
</option>
                                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="menuPermission" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_required_permission"), $_smarty_tpl);?>
</label>
                            <select class="form-select" id="menuPermission" name="required_permission_id">
                                <option value=""><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"none"), $_smarty_tpl);?>
</option>
                                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('permissions'), 'perm');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('perm')->value) {
$foreach1DoElse = false;
?>
                                    <option value="<?php echo $_smarty_tpl->getValue('perm')['id'];?>
"><?php echo $_smarty_tpl->getValue('perm')['name'];?>
</option>
                                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="menuActive" name="is_active" checked>
                        <label class="form-check-label" for="menuActive"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_active"), $_smarty_tpl);?>
</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-primary" id="saveMenuBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Menüpunkt löschen -->
<div class="modal fade" id="deleteMenuModal" tabindex="-1" aria-labelledby="deleteMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteMenuModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_menu_item"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_delete"), $_smarty_tpl);?>
</p>
                <input type="hidden" id="deleteMenuId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_menu_item"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
 src="../js/admin/menues.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
}
}
