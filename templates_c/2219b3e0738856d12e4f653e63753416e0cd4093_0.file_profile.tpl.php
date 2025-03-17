<?php
/* Smarty version 5.4.3, created on 2025-03-17 13:41:27
  from 'file:profile.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67d81877594235_33991334',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2219b3e0738856d12e4f653e63753416e0cd4093' => 
    array (
      0 => 'profile.tpl',
      1 => 1741948862,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
))) {
function content_67d81877594235_33991334 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"profile_edit"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="profile-image-container mb-3">
                        <?php if ($_smarty_tpl->getValue('userData')['profile_image']) {?>
                            <img src="<?php echo $_smarty_tpl->getValue('userData')['profile_image'];?>
" alt="<?php echo $_smarty_tpl->getValue('userData')['username'];?>
" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php } else { ?>
                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 150px; height: 150px; margin: 0 auto;">
                                <i class="bi bi-person-circle" style="font-size: 5rem;"></i>
                            </div>
                        <?php }?>
                    </div>
                    <h5 class="mb-1"><?php echo $_smarty_tpl->getValue('userData')['username'];?>
</h5>
                    <p class="text-muted mb-3"><?php echo $_smarty_tpl->getValue('userData')['email'];?>
</p>
                    
                    <?php if ($_smarty_tpl->getValue('userData')['first_name'] || $_smarty_tpl->getValue('userData')['last_name']) {?>
                        <p class="mb-1"><?php echo $_smarty_tpl->getValue('userData')['first_name'];?>
 <?php echo $_smarty_tpl->getValue('userData')['last_name'];?>
</p>
                    <?php }?>
                    
                    <?php if ($_smarty_tpl->getValue('userData')['phone']) {?>
                        <p class="mb-1"><?php echo $_smarty_tpl->getValue('userData')['phone'];?>
</p>
                    <?php }?>
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="list-group shadow mb-4">
                <a href="profile.php?tab=personal" class="list-group-item list-group-item-action <?php if ($_smarty_tpl->getValue('activeTab') == 'personal') {?>active<?php }?>">
                    <i class="bi bi-person me-2"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"personal_info"), $_smarty_tpl);?>

                </a>
                
                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('autoincludeModules'), 'module', false, 'moduleId');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('moduleId')->value => $_smarty_tpl->getVariable('module')->value) {
$foreach0DoElse = false;
?>
                    <a href="profile.php?tab=<?php echo $_smarty_tpl->getValue('moduleId');?>
" class="list-group-item list-group-item-action <?php if ($_smarty_tpl->getValue('activeTab') == $_smarty_tpl->getValue('moduleId')) {?>active<?php }?>">
                        <i class="bi bi-<?php echo $_smarty_tpl->getValue('module')['icon'];?>
 me-2"></i> <?php echo $_smarty_tpl->getValue('module')['title'];?>

                    </a>
                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
            </div>
        </div>
        
        <div class="col-lg-9">
            <?php if ($_smarty_tpl->getValue('message')) {?>
                <div class="alert alert-<?php echo $_smarty_tpl->getValue('messageType');?>
 alert-dismissible fade show" role="alert">
                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>$_smarty_tpl->getValue('message')), $_smarty_tpl);?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php }?>
            
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h4 class="mb-0">
                        <?php if ($_smarty_tpl->getValue('activeTab') == 'personal') {?>
                            <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"personal_info"), $_smarty_tpl);?>

                        <?php } else { ?>
                            <?php echo $_smarty_tpl->getValue('autoincludeModules')[$_smarty_tpl->getValue('activeTab')]['title'];?>

                        <?php }?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if ($_smarty_tpl->getValue('activeTab') == 'personal') {?>
                        <!-- Persönliche Informationen -->
                        <form action="profile.php?tab=personal" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="mb-4">
                                <label class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"profile_image"), $_smarty_tpl);?>
</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if ($_smarty_tpl->getValue('userData')['profile_image']) {?>
                                            <img src="<?php echo $_smarty_tpl->getValue('userData')['profile_image'];?>
" alt="<?php echo $_smarty_tpl->getValue('userData')['username'];?>
" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                        <?php } else { ?>
                                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 100px; height: 100px;">
                                                <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <div>
                                        <input type="file" class="form-control mb-2" name="profile_image" id="profile_image" accept="image/jpeg,image/png,image/gif">
                                        <div class="form-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"image_upload_hint"), $_smarty_tpl);?>
</div>
                                        
                                        <?php if ($_smarty_tpl->getValue('userData')['profile_image']) {?>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                                                <label class="form-check-label" for="remove_image">
                                                    <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"remove_image"), $_smarty_tpl);?>

                                                </label>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"first_name"), $_smarty_tpl);?>
</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo (($tmp = $_smarty_tpl->getValue('userData')['first_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"last_name"), $_smarty_tpl);?>
</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo (($tmp = $_smarty_tpl->getValue('userData')['last_name'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"phone"), $_smarty_tpl);?>
</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo (($tmp = $_smarty_tpl->getValue('userData')['phone'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
">
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"bio"), $_smarty_tpl);?>
</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo (($tmp = $_smarty_tpl->getValue('userData')['bio'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);?>
</textarea>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save_profile"), $_smarty_tpl);?>

                                </button>
                            </div>
                        </form>
                    <?php } else { ?>
                        <!-- Dynamischer Tab-Inhalt -->
                        <?php echo $_smarty_tpl->getValue('tabContent');?>

                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->renderSubTemplate("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
