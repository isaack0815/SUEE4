<?php
/* Smarty version 5.4.3, created on 2025-03-06 12:05:06
  from 'file:admin/user_details.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c98162e3bfa9_28579152',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a9539c0258ffc8369ad61f7b4907f5da8a780544' => 
    array (
      0 => 'admin/user_details.tpl',
      1 => 1741259063,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c98162e3bfa9_28579152 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_details"), $_smarty_tpl);
$_prefixVariable1=ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1.": ".((string)$_smarty_tpl->getValue('userData')['username'])), (int) 0, $_smarty_current_dir);
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_dashboard"), $_smarty_tpl);?>
</a></li>
                    <li class="breadcrumb-item"><a href="users.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_management"), $_smarty_tpl);?>
</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $_smarty_tpl->getValue('userData')['username'];?>
</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Benutzerdetails -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_details"), $_smarty_tpl);?>
</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-placeholder mb-3">
                            <i class="bi bi-person-circle" style="font-size: 5rem;"></i>
                        </div>
                        <h4><?php echo $_smarty_tpl->getValue('userData')['username'];?>
</h4>
                        <p class="text-muted"><?php echo $_smarty_tpl->getValue('userData')['email'];?>
</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_id"), $_smarty_tpl);?>
</strong>
                            <span><?php echo $_smarty_tpl->getValue('userData')['id'];?>
</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"created_at"), $_smarty_tpl);?>
</strong>
                            <span><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('userData')['created_at'],"%d.%m.%Y %H:%M");?>
</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"last_login"), $_smarty_tpl);?>
</strong>
                            <span><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('userData')['last_login'],"%d.%m.%Y %H:%M");?>
</span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="users.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"back"), $_smarty_tpl);?>

                        </a>
                        <?php if ($_smarty_tpl->getValue('user')->hasPermission('user.edit')) {?>
                            <button type="button" class="btn btn-primary" id="editUserBtn" data-id="<?php echo $_smarty_tpl->getValue('userData')['id'];?>
">
                                <i class="bi bi-pencil"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"edit_user"), $_smarty_tpl);?>

                            </button>
                        <?php }?>
                    </div>
                </div>
            </div>
            
            <!-- Benutzergruppen -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_groups"), $_smarty_tpl);?>
</h5>
                </div>
                <div class="card-body">
                    <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('userGroups')) > 0) {?>
                        <ul class="list-group">
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('userGroups'), 'group');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('group')->value) {
$foreach0DoElse = false;
?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $_smarty_tpl->getValue('group')['name'];?>

                                    <span class="badge bg-primary rounded-pill"><?php echo (($tmp = $_smarty_tpl->getValue('group')['description'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
</span>
                                </li>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        </ul>
                    <?php } else { ?>
                        <p class="text-muted text-center"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"no_groups_assigned"), $_smarty_tpl);?>
</p>
                    <?php }?>
                </div>
                <?php if ($_smarty_tpl->getValue('user')->hasPermission('user.edit')) {?>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary w-100" id="manageGroupsBtn" data-id="<?php echo $_smarty_tpl->getValue('userData')['id'];?>
">
                            <i class="bi bi-people"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"manage_groups"), $_smarty_tpl);?>

                        </button>
                    </div>
                <?php }?>
            </div>
        </div>
        
        <!-- Tab-Inhalte -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white p-0">
                    <!-- Tab-Navigation -->
                    <ul class="nav nav-tabs" id="userTabs" role="tablist">
                        <!-- Profil-Tab (immer vorhanden) -->
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php if ($_smarty_tpl->getValue('activeTab') == 'profile') {?>active<?php }?>" 
                               href="user_details.php?id=<?php echo $_smarty_tpl->getValue('userData')['id'];?>
&tab=profile" 
                               role="tab" aria-selected="<?php if ($_smarty_tpl->getValue('activeTab') == 'profile') {?>true<?php } else { ?>false<?php }?>">
                                <i class="bi bi-person"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"profile"), $_smarty_tpl);?>

                            </a>
                        </li>
                        
                        <!-- Dynamische Tabs aus Autoinclude-Dateien -->
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('autoincludeModules'), 'module', false, 'moduleId');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('moduleId')->value => $_smarty_tpl->getVariable('module')->value) {
$foreach1DoElse = false;
?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('moduleId')) {?>active<?php }?>" 
                                   href="user_details.php?id=<?php echo $_smarty_tpl->getValue('userData')['id'];?>
&tab=<?php echo $_smarty_tpl->getValue('moduleId');?>
" 
                                   role="tab" aria-selected="<?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('moduleId')) {?>true<?php } else { ?>false<?php }?>">
                                    <i class="bi bi-<?php echo $_smarty_tpl->getValue('module')['icon'];?>
"></i> <?php echo $_smarty_tpl->getValue('module')['title'];?>

                                </a>
                            </li>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Tab-Inhalte -->
                    <div class="tab-content">
                        <!-- Profil-Tab-Inhalt -->
                        <?php if ($_smarty_tpl->getValue('activeTab') == 'profile') {?>
                            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                                <h4 class="mb-4"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_profile"), $_smarty_tpl);?>
</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username"), $_smarty_tpl);?>
</label>
                                            <input type="text" class="form-control" value="<?php echo $_smarty_tpl->getValue('userData')['username'];?>
" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"email"), $_smarty_tpl);?>
</label>
                                            <input type="email" class="form-control" value="<?php echo $_smarty_tpl->getValue('userData')['email'];?>
" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"created_at"), $_smarty_tpl);?>
</label>
                                            <input type="text" class="form-control" value="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('userData')['created_at'],"%d.%m.%Y %H:%M");?>
" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"last_login"), $_smarty_tpl);?>
</label>
                                            <input type="text" class="form-control" value="<?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('userData')['last_login'],"%d.%m.%Y %H:%M");?>
" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <h5 class="mt-4 mb-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_groups"), $_smarty_tpl);?>
</h5>
                                <div class="mb-3">
                                    <?php if ($_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('userGroups')) > 0) {?>
                                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('userGroups'), 'group');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('group')->value) {
$foreach2DoElse = false;
?>
                                            <span class="badge bg-primary me-2 mb-2 p-2"><?php echo $_smarty_tpl->getValue('group')['name'];?>
</span>
                                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                                    <?php } else { ?>
                                        <p class="text-muted"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"no_groups_assigned"), $_smarty_tpl);?>
</p>
                                    <?php }?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <!-- Dynamischer Tab-Inhalt -->
                            <div class="tab-pane fade show active" id="<?php echo $_smarty_tpl->getValue('activeTab');?>
" role="tabpanel">
                                <?php echo $_smarty_tpl->getValue('autoincludeModules')[$_smarty_tpl->getValue('activeTab')]['content'];?>

                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal für Benutzer bearbeiten -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"edit_user"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id" value="<?php echo $_smarty_tpl->getValue('userData')['id'];?>
">
                    
                    <div class="mb-3">
                        <label for="editUsername" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="editUsername" name="username" value="<?php echo $_smarty_tpl->getValue('userData')['username'];?>
" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editEmail" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"email"), $_smarty_tpl);?>
</label>
                        <input type="email" class="form-control" id="editEmail" name="email" value="<?php echo $_smarty_tpl->getValue('userData')['email'];?>
" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editPassword" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password"), $_smarty_tpl);?>
</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                        <div class="form-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password_help_text"), $_smarty_tpl);?>
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

<!-- Modal für Gruppen verwalten -->
<div class="modal fade" id="manageGroupsModal" tabindex="-1" aria-labelledby="manageGroupsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageGroupsModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"manage_groups"), $_smarty_tpl);?>
</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="manageGroupsForm">
                    <input type="hidden" id="groupsUserId" name="id" value="<?php echo $_smarty_tpl->getValue('userData')['id'];?>
">
                    
                    <div id="groupsContainer">
                        <!-- Wird dynamisch mit JavaScript gefüllt -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
                <button type="button" class="btn btn-primary" id="saveGroupsBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
            </div>
        </div>
    </div>
</div>

<!-- CSS für die Tab-Navigation -->
<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link:hover {
    color: #495057;
    border-color: #e9ecef #e9ecef #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    font-weight: 500;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
    border-bottom: 2px solid #0d6efd;
}

.tab-content {
    padding: 1rem 0;
}
</style>

<?php echo '<script'; ?>
 src="../js/admin/user_details.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
