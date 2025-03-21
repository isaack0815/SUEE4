<?php
/* Smarty version 5.4.3, created on 2025-03-14 11:42:01
  from 'file:admin/header.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67d407f91f23e5_45168246',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c470d7cac55b17deffd0159ffab1e79505e295e0' => 
    array (
      0 => 'admin/header.tpl',
      1 => 1741948846,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_67d407f91f23e5_45168246 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
?><!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->getValue('currentLang');?>
">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_area"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
echo (($tmp = $_smarty_tpl->getValue('title') ?? null)===null||$tmp==='' ? $_prefixVariable1 ?? null : $tmp);?>
 - <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"site_name"), $_smarty_tpl);?>
</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/admin.css" rel="stylesheet">
    <!-- SortableJS für Drag & Drop -->
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"><?php echo '</script'; ?>
>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"admin_area"), $_smarty_tpl);?>
</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"back_to_site"), $_smarty_tpl);?>
</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Sprachauswahl -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language"), $_smarty_tpl);?>

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'lang');
$foreach7DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach7DoElse = false;
?>
                                <li>
                                    <a class="dropdown-item <?php if ($_smarty_tpl->getValue('currentLang') == $_smarty_tpl->getValue('lang')) {?>active<?php }?>" href="?lang=<?php echo $_smarty_tpl->getValue('lang');?>
">
                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"lang_".((string)$_smarty_tpl->getValue('lang'))), $_smarty_tpl);?>

                                    </a>
                                </li>
                            <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                        </ul>
                    </li>
                    
                    <!-- Benutzermenü -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $_smarty_tpl->getValue('currentUser')['username'];?>

                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="../profile.php">
                                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"profile"), $_smarty_tpl);?>

                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="../logout.php">
                                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"logout"), $_smarty_tpl);?>

                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('adminMenu'), 'item');
$foreach8DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach8DoElse = false;
?>
                            <li class="nav-item">
                                <a class="nav-link <?php if ((true && ($_smarty_tpl->hasVariable('activeMenu') && null !== ($_smarty_tpl->getValue('activeMenu') ?? null))) && $_smarty_tpl->getValue('activeMenu') == $_smarty_tpl->getValue('item')['id']) {?>active<?php }?>" href="<?php echo $_smarty_tpl->getValue('item')['url'];?>
">
                                    <?php if ($_smarty_tpl->getValue('item')['icon']) {?><i class="bi bi-<?php echo $_smarty_tpl->getValue('item')['icon'];?>
 me-1"></i><?php }?>
                                    <?php echo $_smarty_tpl->getValue('item')['name'];?>

                                </a>
                                
                                <?php if (!( !true || empty($_smarty_tpl->getValue('item')['children']))) {?>
                                    <ul class="nav flex-column ms-3">
                                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('item')['children'], 'child');
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('child')->value) {
$foreach9DoElse = false;
?>
                                            <li class="nav-item">
                                                <a class="nav-link <?php if ((true && ($_smarty_tpl->hasVariable('activeMenu') && null !== ($_smarty_tpl->getValue('activeMenu') ?? null))) && $_smarty_tpl->getValue('activeMenu') == $_smarty_tpl->getValue('child')['id']) {?>active<?php }?>" href="<?php echo $_smarty_tpl->getValue('child')['url'];?>
">
                                                    <?php if ($_smarty_tpl->getValue('child')['icon']) {?><i class="bi bi-<?php echo $_smarty_tpl->getValue('child')['icon'];?>
 me-1"></i><?php }?>
                                                    <?php echo $_smarty_tpl->getValue('child')['name'];?>

                                                </a>
                                            </li>
                                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                                    </ul>
                                <?php }?>
                            </li>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                    </ul>
                </div>
            </div>
            
            <!-- Hauptinhalt -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

<?php }
}
