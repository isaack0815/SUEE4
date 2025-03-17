<?php
/* Smarty version 5.4.3, created on 2025-03-17 10:29:31
  from 'file:dashboard.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67d7eb7b75d587_17889026',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cb70334e2f43abb1a352ff61a2312644f5a153ab' => 
    array (
      0 => 'dashboard.tpl',
      1 => 1741948857,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67d7eb7b75d587_17889026 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>"Dashboard"), (int) 0, $_smarty_current_dir);
?>

<!-- JavaScript für das Dashboard einbinden -->
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->getValue('baseUrl');?>
/js/dashboard.js"><?php echo '</script'; ?>
>

<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard</h1>
    
    <?php if (( !$_smarty_tpl->hasVariable('dashboardModules') || empty($_smarty_tpl->getValue('dashboardModules')))) {?>
        <div class="alert alert-info">Keine Module verfügbar</div>
    <?php } else { ?>
        <div class="dashboard-grid" id="dashboard-grid">
            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('dashboardModules'), 'module');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('module')->value) {
$foreach0DoElse = false;
?>
                <div class="dashboard-module" 
                     id="module-<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['id'], ENT_QUOTES, 'UTF-8', true);?>
" 
                     data-module-id="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['id'], ENT_QUOTES, 'UTF-8', true);?>
"
                     data-grid-x="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['grid_x'], ENT_QUOTES, 'UTF-8', true);?>
"
                     data-grid-y="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['grid_y'], ENT_QUOTES, 'UTF-8', true);?>
"
                     data-grid-width="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['grid_width'], ENT_QUOTES, 'UTF-8', true);?>
"
                     data-grid-height="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['grid_height'], ENT_QUOTES, 'UTF-8', true);?>
"
                     data-size="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['size'], ENT_QUOTES, 'UTF-8', true);?>
">
                    
                    <div class="module-header">
                        <h3>
                            <?php if ($_smarty_tpl->getValue('module')['icon']) {?><i class="<?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['icon'], ENT_QUOTES, 'UTF-8', true);?>
"></i><?php }?>
                            <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('module')['title'], ENT_QUOTES, 'UTF-8', true);?>

                        </h3>
                        <div class="module-actions">
                            <button class="btn-module-settings" title="Einstellungen"><i class="fas fa-cog"></i></button>
                            <button class="btn-module-toggle" title="Minimieren/Maximieren"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    
                    <div class="module-content">
                        <?php echo $_smarty_tpl->getValue('module')['content'];?>

                    </div>
                </div>
            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
        </div>
    <?php }?>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
