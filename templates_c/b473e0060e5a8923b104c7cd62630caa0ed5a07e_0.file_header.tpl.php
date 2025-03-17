<?php
/* Smarty version 5.4.3, created on 2025-03-14 16:21:58
  from 'file:header.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67d44996718298_32899103',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b473e0060e5a8923b104c7cd62630caa0ed5a07e' => 
    array (
      0 => 'header.tpl',
      1 => 1741948858,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_67d44996718298_32899103 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
?><!DOCTYPE html>
<html lang="<?php echo (($tmp = $_smarty_tpl->getValue('currentLang') ?? null)===null||$tmp==='' ? 'de' ?? null : $tmp);?>
">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="site_name" content="<?php echo $_smarty_tpl->getValue('generalSettings')['site_name'];?>
">
   <title><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"site_title"), $_smarty_tpl);?>
</title>
   
      <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('metadata'), 'value', false, 'key');
$foreach6DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('key')->value => $_smarty_tpl->getVariable('value')->value) {
$foreach6DoElse = false;
?>
   <meta name="<?php echo $_smarty_tpl->getValue('key');?>
" content="<?php echo $_smarty_tpl->getValue('value');?>
">
   <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
   
   <!-- Bootstrap 5 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Bootstrap Icons -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
   <!-- SortableJS für Drag & Drop -->
   <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"><?php echo '</script'; ?>
>
   <!-- Custom CSS -->
   <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
            <div class="container">
                <a class="navbar-brand" href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"site_name"), $_smarty_tpl);?>
</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('mainMenu'), 'item');
$foreach7DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach7DoElse = false;
?>
                            <?php if (( !true || empty($_smarty_tpl->getValue('item')['children']))) {?>
                                <li class="nav-item">
                                    <a class="nav-link <?php if ((true && ($_smarty_tpl->hasVariable('activeMenuItem') && null !== ($_smarty_tpl->getValue('activeMenuItem') ?? null))) && (true && (true && null !== ($_smarty_tpl->getValue('activeMenuItem')['item'] ?? null))) && $_smarty_tpl->getValue('activeMenuItem')['item']['id'] == $_smarty_tpl->getValue('item')['id']) {?>active<?php }?>" href="<?php echo $_smarty_tpl->getValue('item')['url'];?>
">
                                        <?php if ($_smarty_tpl->getValue('item')['icon']) {?><i class="bi bi-<?php echo $_smarty_tpl->getValue('item')['icon'];?>
 me-1"></i><?php }?>
                                        <?php echo $_smarty_tpl->getValue('item')['name'];?>

                                    </a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle <?php if ((true && ($_smarty_tpl->hasVariable('activeMenuIds') && null !== ($_smarty_tpl->getValue('activeMenuIds') ?? null))) && is_array($_smarty_tpl->getValue('activeMenuIds')) && $_smarty_tpl->getSmarty()->getModifierCallback('in_array')($_smarty_tpl->getValue('item')['id'],$_smarty_tpl->getValue('activeMenuIds'))) {?>active<?php }?>" 
                                       href="#" id="navbarDropdown<?php echo $_smarty_tpl->getValue('item')['id'];?>
" role="button" 
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php if ($_smarty_tpl->getValue('item')['icon']) {?><i class="bi bi-<?php echo $_smarty_tpl->getValue('item')['icon'];?>
 me-1"></i><?php }?>
                                        <?php echo $_smarty_tpl->getValue('item')['name'];?>

                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $_smarty_tpl->getValue('item')['id'];?>
">
                                        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('item')['children'], 'child');
$foreach8DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('child')->value) {
$foreach8DoElse = false;
?>
                                            <li>
                                                <a class="dropdown-item <?php if ((true && ($_smarty_tpl->hasVariable('activeMenuItem') && null !== ($_smarty_tpl->getValue('activeMenuItem') ?? null))) && (true && (true && null !== ($_smarty_tpl->getValue('activeMenuItem')['item'] ?? null))) && $_smarty_tpl->getValue('activeMenuItem')['item']['id'] == $_smarty_tpl->getValue('child')['id']) {?>active<?php }?>" 
                                                   href="<?php echo $_smarty_tpl->getValue('child')['url'];?>
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
                                </li>
                            <?php }?>
                        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
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
$foreach9DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach9DoElse = false;
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
                        
                        <?php if ($_smarty_tpl->getValue('isLoggedIn')) {?>
                            <!-- Benutzermenü -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo $_smarty_tpl->getValue('currentUser')['username'];?>

                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('userMenu'), 'item');
$foreach10DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('item')->value) {
$foreach10DoElse = false;
?>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo $_smarty_tpl->getValue('item')['url'];?>
">
                                                <?php if ($_smarty_tpl->getValue('item')['icon']) {?><i class="bi bi-<?php echo $_smarty_tpl->getValue('item')['icon'];?>
 me-1"></i><?php }?>
                                                <?php echo $_smarty_tpl->getValue('item')['name'];?>

                                            </a>
                                        </li>
                                    <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="bi bi-box-arrow-right me-1"></i>
                                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"logout"), $_smarty_tpl);?>

                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"login"), $_smarty_tpl);?>
</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"register"), $_smarty_tpl);?>
</a>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <main>

<?php }
}
