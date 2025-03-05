<?php
/* Smarty version 5.4.3, created on 2025-03-05 09:23:51
  from 'file:dashboard.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c80a179f1606_09119577',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cb70334e2f43abb1a352ff61a2312644f5a153ab' => 
    array (
      0 => 'dashboard.tpl',
      1 => 1741160640,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67c80a179f1606_09119577 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"dashboard"), $_smarty_tpl);?>
</h4>
            </div>
            <div class="card-body">
                <h5><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"welcome",'username'=>$_smarty_tpl->getValue('currentUser')['username']), $_smarty_tpl);?>
</h5>
                <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"last_login"), $_smarty_tpl);?>
: <?php echo $_smarty_tpl->getValue('currentUser')['last_login'];?>
</p>
                
                <div class="alert alert-info">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"dashboard_info"), $_smarty_tpl);?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
