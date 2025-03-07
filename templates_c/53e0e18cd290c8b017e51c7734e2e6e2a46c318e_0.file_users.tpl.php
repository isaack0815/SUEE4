<?php
/* Smarty version 5.4.3, created on 2025-03-06 11:24:58
  from 'file:admin/users.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c977fa2ec021_23927476',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '53e0e18cd290c8b017e51c7734e2e6e2a46c318e' => 
    array (
      0 => 'admin/users.tpl',
      1 => 1741256542,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c977fa2ec021_23927476 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_management"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row">
   <div class="col-md-12">
       <div class="card shadow">
           <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
               <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_management"), $_smarty_tpl);?>
</h4>
               <button type="button" class="btn btn-light btn-sm" id="addUserBtn" 
                       <?php if (!$_smarty_tpl->getValue('user')->hasPermission('user.create')) {?>disabled<?php }?>>
                   <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_user"), $_smarty_tpl);?>

               </button>
           </div>
           <div class="card-body">
               <!-- Benachrichtigungsbereich -->
               <div id="alertArea" class="mb-3" style="display: none;"></div>
               
               <!-- Benutzertabelle -->
               <div class="table-responsive">
                   <table class="table table-striped table-hover" id="usersTable">
                       <thead>
                           <tr>
                               <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username"), $_smarty_tpl);?>
</th>
                               <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"email"), $_smarty_tpl);?>
</th>
                               <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"groups"), $_smarty_tpl);?>
</th>
                               <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"last_login"), $_smarty_tpl);?>
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

<!-- Modal für Benutzer hinzufügen/bearbeiten -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="userModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_user"), $_smarty_tpl);?>
</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="userForm">
                   <input type="hidden" id="userId" name="id" value="">
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="username" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username"), $_smarty_tpl);?>
</label>
                           <input type="text" class="form-control" id="username" name="username" required>
                       </div>
                       <div class="col-md-6">
                           <label for="email" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"email"), $_smarty_tpl);?>
</label>
                           <input type="email" class="form-control" id="email" name="email" required>
                       </div>
                   </div>
                   
                   <div class="mb-3">
                       <label for="password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password"), $_smarty_tpl);?>
</label>
                       <input type="password" class="form-control" id="password" name="password">
                       <div class="form-text" id="passwordHelpText"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password_help_text"), $_smarty_tpl);?>
</div>
                   </div>
                   
                   <div class="mb-3">
                       <label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"groups"), $_smarty_tpl);?>
</label>
                       <div class="row" id="groupsContainer">
                           <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('groups'), 'group');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('group')->value) {
$foreach0DoElse = false;
?>
                               <div class="col-md-6 mb-2">
                                   <div class="form-check">
                                       <input class="form-check-input group-checkbox" type="checkbox" id="group_<?php echo $_smarty_tpl->getValue('group')['id'];?>
" value="<?php echo $_smarty_tpl->getValue('group')['id'];?>
" name="groups[]">
                                       <label class="form-check-label" for="group_<?php echo $_smarty_tpl->getValue('group')['id'];?>
">
                                           <?php echo $_smarty_tpl->getValue('group')['name'];?>

                                       </label>
                                   </div>
                               </div>
                           <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                       </div>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
               <button type="button" class="btn btn-primary" id="saveUserBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Benutzer löschen -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="deleteUserModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_user"), $_smarty_tpl);?>
</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_delete_user"), $_smarty_tpl);?>
</p>
               <p><strong id="deleteUserName"></strong></p>
               <input type="hidden" id="deleteUserId" value="">
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
 src="../js/admin/users.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
