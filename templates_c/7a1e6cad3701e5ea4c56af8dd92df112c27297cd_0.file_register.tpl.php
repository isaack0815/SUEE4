<?php
/* Smarty version 5.4.3, created on 2025-03-05 09:05:24
  from 'file:register.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c805c413ff78_16398633',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7a1e6cad3701e5ea4c56af8dd92df112c27297cd' => 
    array (
      0 => 'register.tpl',
      1 => 1741160692,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67c805c413ff78_16398633 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"register"), $_smarty_tpl);?>
</h4>
            </div>
            <div class="card-body">
                <?php if ($_smarty_tpl->getValue('success')) {?>
                    <div class="alert alert-success">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"registration_success"), $_smarty_tpl);?>

                        <a href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"login_now"), $_smarty_tpl);?>
</a>
                    </div>
                <?php } else { ?>
                    <?php if ($_smarty_tpl->getValue('error')) {?>
                        <div class="alert alert-danger">
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>$_smarty_tpl->getValue('error')), $_smarty_tpl);?>

                        </div>
                    <?php }?>
                    
                    <form id="registerForm" action="register.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username"), $_smarty_tpl);?>
</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username_required"), $_smarty_tpl);?>
</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"email"), $_smarty_tpl);?>
</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"email_required"), $_smarty_tpl);?>
</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password"), $_smarty_tpl);?>
</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password_required"), $_smarty_tpl);?>
</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_password"), $_smarty_tpl);?>
</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_password_required"), $_smarty_tpl);?>
</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"register"), $_smarty_tpl);?>
</button>
                        </div>
                    </form>
                <?php }?>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"already_have_account"), $_smarty_tpl);?>
 <a href="index.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"login"), $_smarty_tpl);?>
</a></p>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
 src="js/register.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
