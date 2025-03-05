<?php
/* Smarty version 5.4.3, created on 2025-03-05 09:05:18
  from 'file:login.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c805be6c1b27_36337608',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '852c07a3dee0a9b7c03fd049132e4988ebbbc436' => 
    array (
      0 => 'login.tpl',
      1 => 1741160678,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67c805be6c1b27_36337608 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"login"), $_smarty_tpl);?>
</h4>
            </div>
            <div class="card-body">
                <?php if ($_smarty_tpl->getValue('error')) {?>
                    <div class="alert alert-danger">
                        <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>$_smarty_tpl->getValue('error')), $_smarty_tpl);?>

                    </div>
                <?php }?>
                
                <form id="loginForm" action="login.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username"), $_smarty_tpl);?>
</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"username_required"), $_smarty_tpl);?>
</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password"), $_smarty_tpl);?>
</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"password_required"), $_smarty_tpl);?>
</div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"remember_me"), $_smarty_tpl);?>
</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"login"), $_smarty_tpl);?>
</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"no_account"), $_smarty_tpl);?>
 <a href="register.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"register_now"), $_smarty_tpl);?>
</a></p>
                <p class="mt-2 mb-0"><a href="forgot-password.php"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"forgot_password"), $_smarty_tpl);?>
</a></p>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
 src="js/login.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
