<?php
/* Smarty version 5.4.3, created on 2025-03-05 13:50:25
  from 'file:admin/groups.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c848912f2e46_78020334',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '131695c009ddc594338442d64c286b212b8f4e74' => 
    array (
      0 => 'admin/groups.tpl',
      1 => 1741178585,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c848912f2e46_78020334 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"group_management"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"group_management"), $_smarty_tpl);?>
</h4>
                <button type="button" class="btn btn-light btn-sm" id="addGroupBtn" 
                        <?php if (!$_smarty_tpl->getValue('user')->hasPermission('group.create')) {?>disabled<?php }?>>
                    <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_group"), $_smarty_tpl);?>

                </button>
            </div>
            <div class="card-body">
                <!-- Benachrichtigungsbereich -->
                <div id="alertArea" class="mb-3" style="display: none;"></div>
                
                <!-- Gruppentabelle -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="groupTable">
                        <thead>
                            <tr>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"group_name"), $_smarty_tpl);?>
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

<!-- Modal für Gruppe hinzufügen/bearbeiten -->
<div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_group"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="groupForm">
                    <input type="hidden" id="groupId" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="groupName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"group_name"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="groupName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="groupDescription" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"description"), $_smarty_tpl);?>
</label>
                        <textarea class="form-control" id="groupDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-primary" id="saveGroupBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Gruppe löschen -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-labelledby="deleteGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteGroupModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_group"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_delete"), $_smarty_tpl);?>
</p>
                <input type="hidden" id="deleteGroupId" value="">
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

<!-- Modal für Berechtigungen zuweisen -->
<div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionsModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"assign_group_permissions"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="permissionsGroupId" value="">
                <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"assign_permissions"), $_smarty_tpl);?>
</p>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                        <label class="form-check-label" for="selectAllPermissions">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"select_all"), $_smarty_tpl);?>

                        </label>
                    </div>
                </div>
                
                <div class="row" id="permissionsList">
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('permissions'), 'permission');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('permission')->value) {
$foreach0DoElse = false;
?>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input permission-checkbox" type="checkbox" id="permission_<?php echo $_smarty_tpl->getValue('permission')['id'];?>
" value="<?php echo $_smarty_tpl->getValue('permission')['id'];?>
">
                                <label class="form-check-label" for="permission_<?php echo $_smarty_tpl->getValue('permission')['id'];?>
">
                                    <?php echo $_smarty_tpl->getValue('permission')['name'];?>
 - <?php echo (($tmp = $_smarty_tpl->getValue('permission')['description'] ?? null)===null||$tmp==='' ? "-" ?? null : $tmp);?>

                                </label>
                            </div>
                        </div>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-primary" id="savePermissionsBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
 src="../js/admin/groups.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
