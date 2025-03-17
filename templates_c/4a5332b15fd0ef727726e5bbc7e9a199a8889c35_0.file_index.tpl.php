<?php
/* Smarty version 5.4.3, created on 2025-03-17 13:29:04
  from 'file:admin/index.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67d81590059776_94771803',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4a5332b15fd0ef727726e5bbc7e9a199a8889c35' => 
    array (
      0 => 'admin/index.tpl',
      1 => 1741948847,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67d81590059776_94771803 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_dashboard"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_dashboard"), $_smarty_tpl);?>
</h1>
</div>

<div class="row">
    <!-- Statistiken -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"statistics"), $_smarty_tpl);?>
</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="h1"><?php echo $_smarty_tpl->getValue('userCount');?>
</div>
                        <div class="text-muted"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"users"), $_smarty_tpl);?>
</div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="h1"><?php echo $_smarty_tpl->getValue('groupCount');?>
</div>
                        <div class="text-muted"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"groups"), $_smarty_tpl);?>
</div>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <div class="h1"><?php echo $_smarty_tpl->getValue('permissionCount');?>
</div>
                        <div class="text-muted"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"permissions"), $_smarty_tpl);?>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Letzte Benutzeraktivitäten -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"recent_user_activity"), $_smarty_tpl);?>
</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('recentUsers'), 'user');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('user')->value) {
$foreach0DoElse = false;
?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo $_smarty_tpl->getValue('user')['username'];?>

                            <span class="badge bg-primary rounded-pill"><?php echo $_smarty_tpl->getSmarty()->getModifierCallback('date_format')($_smarty_tpl->getValue('user')['last_login'],"%d.%m.%Y %H:%M");?>
</span>
                        </li>
                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Systeminfo -->
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"system_info"), $_smarty_tpl);?>
</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        PHP Version
                        <span class="badge bg-secondary rounded-pill"><?php echo $_smarty_tpl->getValue('systemInfo')['php_version'];?>
</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Server
                        <span class="badge bg-secondary rounded-pill"><?php echo $_smarty_tpl->getValue('systemInfo')['server_software'];?>
</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Database
                        <span class="badge bg-secondary rounded-pill"><?php echo $_smarty_tpl->getValue('systemInfo')['database_version'];?>
</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Smarty
                        <span class="badge bg-secondary rounded-pill"><?php echo $_smarty_tpl->getValue('systemInfo')['smarty_version'];?>
</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Schnellzugriff -->
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"quick_access"), $_smarty_tpl);?>
</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if ($_smarty_tpl->getValue('user')->hasPermission('user.view')) {?>
                        <div class="col-md-4 mb-3">
                            <a href="users.php" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-people fs-1 mb-2"></i>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"user_management"), $_smarty_tpl);?>

                            </a>
                        </div>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('user')->hasPermission('group.view')) {?>
                        <div class="col-md-4 mb-3">
                            <a href="groups.php" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-diagram-3 fs-1 mb-2"></i>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"group_management"), $_smarty_tpl);?>

                            </a>
                        </div>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('user')->hasPermission('permission.view')) {?>
                        <div class="col-md-4 mb-3">
                            <a href="permissions.php" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-shield-lock fs-1 mb-2"></i>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"permission_management"), $_smarty_tpl);?>

                            </a>
                        </div>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('user')->hasPermission('menu.view')) {?>
                        <div class="col-md-4 mb-3">
                            <a href="menus.php" class="btn btn-outline-danger w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-list fs-1 mb-2"></i>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"menu_management"), $_smarty_tpl);?>

                            </a>
                        </div>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('user')->hasPermission('settings.view')) {?>
                        <div class="col-md-4 mb-3">
                            <a href="settings.php" class="btn btn-outline-dark w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                                <i class="bi bi-gear fs-1 mb-2"></i>
                                <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"settings"), $_smarty_tpl);?>

                            </a>
                        </div>
                    <?php }?>
                    
                    <div class="col-md-4 mb-3">
                        <a href="../index.php" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-3">
                            <i class="bi bi-house fs-1 mb-2"></i>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"back_to_site"), $_smarty_tpl);?>

                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Systeminformationen (erweitert) -->
    <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"advanced_system_info"), $_smarty_tpl);?>
</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <tbody>
                            <tr>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"memory_limit"), $_smarty_tpl);?>
</th>
                                <td><?php echo $_smarty_tpl->getValue('systemInfo')['memory_limit'];?>
</td>
                            </tr>
                            <tr>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"max_execution_time"), $_smarty_tpl);?>
</th>
                                <td><?php echo $_smarty_tpl->getValue('systemInfo')['max_execution_time'];?>
</td>
                            </tr>
                            <tr>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"upload_max_filesize"), $_smarty_tpl);?>
</th>
                                <td><?php echo $_smarty_tpl->getValue('systemInfo')['upload_max_filesize'];?>
</td>
                            </tr>
                            <tr>
                                <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"post_max_size"), $_smarty_tpl);?>
</th>
                                <td><?php echo $_smarty_tpl->getValue('systemInfo')['post_max_size'];?>
</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
