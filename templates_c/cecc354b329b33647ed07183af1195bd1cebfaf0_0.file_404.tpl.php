<?php
/* Smarty version 5.4.3, created on 2025-03-06 10:49:28
  from 'file:404.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c96fa80acf54_03435488',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cecc354b329b33647ed07183af1195bd1cebfaf0' => 
    array (
      0 => '404.tpl',
      1 => 1741253118,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67c96fa80acf54_03435488 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>"404 - Seite nicht gefunden"), (int) 0, $_smarty_current_dir);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>404 - Seite nicht gefunden</h1>
            <p class="lead"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"page_not_found_message"), $_smarty_tpl);?>
</p>
            <a href="index.php" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"back_to_home"), $_smarty_tpl);?>
</a>
        </div>
    </div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
