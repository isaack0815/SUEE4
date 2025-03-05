<?php
/* Smarty version 5.4.3, created on 2025-03-05 10:44:27
  from 'file:admin/index.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c81cfb9265c1_91699394',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4a5332b15fd0ef727726e5bbc7e9a199a8889c35' => 
    array (
      0 => 'admin/index.tpl',
      1 => 1741164035,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c81cfb9265c1_91699394 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_dashboard"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_dashboard"), $_smarty_tpl);?>
</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5>Willkommen im Administrationsbereich</h5>
                    <p>Hier können Sie Benutzer, Gruppen und Einstellungen verwalten.</p>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 text-primary"></i>
                                <h5 class="mt-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"users"), $_smarty_tpl);?>
</h5>
                                <p>Benutzer verwalten</p>
                                <a href="users.php" class="btn btn-sm btn-primary">Öffnen</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill fs-1 text-success"></i>
                                <h5 class="mt-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"groups"), $_smarty_tpl);?>
</h5>
                                <p>Benutzergruppen verwalten</p>
                                <a href="groups.php" class="btn btn-sm btn-success">Öffnen</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-gear fs-1 text-secondary"></i>
                                <h5 class="mt-3"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"settings"), $_smarty_tpl);?>
</h5>
                                <p>Systemeinstellungen verwalten</p>
                                <a href="settings.php" class="btn btn-sm btn-secondary">Öffnen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
