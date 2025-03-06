<?php
/* Smarty version 5.4.3, created on 2025-03-06 10:50:57
  from 'file:page.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c97001070308_56765836',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5358e8677eeca9fb5b2f69eef0baa3ee694a0a74' => 
    array (
      0 => 'page.tpl',
      1 => 1741253105,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67c97001070308_56765836 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_smarty_tpl->getValue('page_title')), (int) 0, $_smarty_current_dir);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo $_smarty_tpl->getValue('page')['title'];?>
</h1>
            <div class="cms-content">
                <?php echo $_smarty_tpl->getValue('page')['content'];?>

            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
