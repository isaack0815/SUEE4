<?php
/* Smarty version 5.4.3, created on 2025-03-05 13:36:39
  from 'file:admin/permissions.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c84557dc52e8_98542674',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3738db6ea7e1a23bbaf46f799875c6a3f81c0b03' => 
    array (
      0 => 'admin/permissions.tpl',
      1 => 1741178174,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c84557dc52e8_98542674 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"permission_management"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"permission_management"), $_smarty_tpl);?>
</h4>
                <button type="button" class="btn btn-light btn-sm" id="addPermissionBtn" 
                        <?php if (!$_smarty_tpl->getValue('user')->hasPermission('permission.create')) {?>disabled<?php }?>>
                    <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_permission"), $_smarty_tpl);?>

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
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"permission_name"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"description"), $_smarty_tpl);?>
</th>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"created_at"), $_smarty_tpl);?>
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

<!-- Modal für Berechtigung hinzufügen/bearbeiten -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_permission"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="permissionForm">
                    <input type="hidden" id="permissionId" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="permissionName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"permission_name"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="permissionName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="permissionDescription" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"description"), $_smarty_tpl);?>
</label>
                        <textarea class="form-control" id="permissionDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-primary" id="savePermissionBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Berechtigung löschen -->
<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePermissionModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_permission"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_delete"), $_smarty_tpl);?>
</p>
                <input type="hidden" id="deletePermissionId" value="">
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

<?php echo '<script'; ?>
 src="../js/admin/permissions.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
