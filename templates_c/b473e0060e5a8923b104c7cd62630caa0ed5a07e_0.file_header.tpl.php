<?php
/* Smarty version 5.4.3, created on 2025-03-05 09:05:18
  from 'file:header.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c805be6cba71_86942426',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b473e0060e5a8923b104c7cd62630caa0ed5a07e' => 
    array (
      0 => 'header.tpl',
      1 => 1741160665,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_67c805be6cba71_86942426 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
?><!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->getValue('currentLang');?>
">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"site_title"), $_smarty_tpl);?>
</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"site_name"), $_smarty_tpl);?>
</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($_smarty_tpl->getValue('isLoggedIn')) {?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"dashboard"), $_smarty_tpl);?>
</a>
                        </li>
                    <?php }?>
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
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach0DoElse = false;
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
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"profile"), $_smarty_tpl);?>

                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
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
    
    <div class="container">

<?php }
}
