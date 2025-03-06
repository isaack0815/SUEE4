<?php
/* Smarty version 5.4.3, created on 2025-03-06 09:56:42
  from 'file:admin/languages.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.3',
  'unifunc' => 'content_67c9634ac925c1_12860916',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '37dfa9abe629b86f010e81451a8c705fb997b275' => 
    array (
      0 => 'admin/languages.tpl',
      1 => 1741251400,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:admin/header.tpl' => 1,
    'file:admin/footer.tpl' => 1,
  ),
))) {
function content_67c9634ac925c1_12860916 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/www/htdocs/w01ddc0a/suee4/SUEE4/templates/admin';
ob_start();
echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_management"), $_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->renderSubTemplate("file:admin/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('title'=>$_prefixVariable1), (int) 0, $_smarty_current_dir);
?>

<div class="row mb-4">
   <div class="col-md-12">
       <div class="card shadow">
           <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
               <h4 class="mb-0"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_management"), $_smarty_tpl);?>
</h4>
               <div>
                   <button type="button" class="btn btn-light btn-sm" id="addLanguageBtn" 
                           <?php if (!$_smarty_tpl->getValue('user')->hasPermission('language.create')) {?>disabled<?php }?>>
                       <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_language"), $_smarty_tpl);?>

                   </button>
                   <button type="button" class="btn btn-light btn-sm ms-2" id="addKeyBtn" 
                           <?php if (!$_smarty_tpl->getValue('user')->hasPermission('language.create')) {?>disabled<?php }?>>
                       <i class="bi bi-plus-circle"></i> <?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_language_key"), $_smarty_tpl);?>

                   </button>
               </div>
           </div>
           <div class="card-body">
               <!-- Benachrichtigungsbereich -->
               <div id="alertArea" class="mb-3" style="display: none;"></div>
               
               <!-- Sprachauswahl -->
               <div class="row mb-4">
                   <div class="col-md-6">
                       <div class="input-group">
                           <span class="input-group-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"filter_by_language"), $_smarty_tpl);?>
</span>
                           <select class="form-select" id="languageFilter">
                               <option value="all"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"all_languages"), $_smarty_tpl);?>
</option>
                               <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'lang');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach0DoElse = false;
?>
                                   <option value="<?php echo $_smarty_tpl->getValue('lang');?>
"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"lang_".((string)$_smarty_tpl->getValue('lang'))), $_smarty_tpl);?>
</option>
                               <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                           </select>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="input-group">
                           <span class="input-group-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"search"), $_smarty_tpl);?>
</span>
                           <input type="text" class="form-control" id="searchInput" placeholder="<?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"search_keys_or_values"), $_smarty_tpl);?>
">
                           <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn">
                               <i class="bi bi-x-circle"></i>
                           </button>
                       </div>
                   </div>
               </div>
               
               <!-- Sprachtabelle -->
               <div class="table-responsive">
                   <table class="table table-striped table-hover" id="languageTable">
                       <thead>
                           <tr>
                               <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_key"), $_smarty_tpl);?>
</th>
                               <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'lang');
$foreach1DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach1DoElse = false;
?>
                                   <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"lang_".((string)$_smarty_tpl->getValue('lang'))), $_smarty_tpl);?>
</th>
                               <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                               <th><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"actions"), $_smarty_tpl);?>
</th>
                           </tr>
                       </thead>
                       <tbody>
                           <!-- Wird dynamisch mit JavaScript gefüllt -->
                       </tbody>
                   </table>
               </div>
               
               <!-- Paginierung -->
               <nav aria-label="Page navigation" class="mt-4">
                   <ul class="pagination justify-content-center" id="pagination">
                       <!-- Wird dynamisch mit JavaScript gefüllt -->
                   </ul>
               </nav>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprache hinzufügen -->
<div class="modal fade" id="addLanguageModal" tabindex="-1" aria-labelledby="addLanguageModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="addLanguageModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_language"), $_smarty_tpl);?>
</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="addLanguageForm">
                   <div class="mb-3">
                       <label for="languageCode" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_code"), $_smarty_tpl);?>
</label>
                       <input type="text" class="form-control" id="languageCode" name="lang_code" required 
                              placeholder="z.B. de, en, fr, es" maxlength="5">
                       <div class="form-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_code_hint"), $_smarty_tpl);?>
</div>
                   </div>
                   <div class="mb-3">
                       <label for="languageName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_name"), $_smarty_tpl);?>
</label>
                       <input type="text" class="form-control" id="languageName" name="lang_name" required>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
               <button type="button" class="btn btn-primary" id="saveLanguageBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprachschlüssel hinzufügen -->
<div class="modal fade" id="addKeyModal" tabindex="-1" aria-labelledby="addKeyModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="addKeyModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"add_language_key"), $_smarty_tpl);?>
</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="addKeyForm">
                   <div class="mb-3">
                       <label for="keyName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_key"), $_smarty_tpl);?>
</label>
                       <input type="text" class="form-control" id="keyName" name="lang_key" required>
                       <div class="form-text"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_key_hint"), $_smarty_tpl);?>
</div>
                   </div>
                   
                   <div class="row">
                       <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'lang');
$foreach2DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach2DoElse = false;
?>
                           <div class="col-md-6 mb-3">
                               <label for="keyValue_<?php echo $_smarty_tpl->getValue('lang');?>
" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"lang_".((string)$_smarty_tpl->getValue('lang'))), $_smarty_tpl);?>
</label>
                               <textarea class="form-control" id="keyValue_<?php echo $_smarty_tpl->getValue('lang');?>
" name="lang_value_<?php echo $_smarty_tpl->getValue('lang');?>
" rows="3" required></textarea>
                           </div>
                       <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
               <button type="button" class="btn btn-primary" id="saveKeyBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprachschlüssel bearbeiten -->
<div class="modal fade" id="editKeyModal" tabindex="-1" aria-labelledby="editKeyModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="editKeyModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"edit_language_key"), $_smarty_tpl);?>
</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="editKeyForm">
                   <div class="mb-3">
                       <label for="editKeyName" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"language_key"), $_smarty_tpl);?>
</label>
                       <input type="text" class="form-control" id="editKeyName" name="lang_key" readonly>
                   </div>
                   
                   <div class="row">
                       <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('availableLanguages'), 'lang');
$foreach3DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('lang')->value) {
$foreach3DoElse = false;
?>
                           <div class="col-md-6 mb-3">
                               <label for="editKeyValue_<?php echo $_smarty_tpl->getValue('lang');?>
" class="form-label"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"lang_".((string)$_smarty_tpl->getValue('lang'))), $_smarty_tpl);?>
</label>
                               <textarea class="form-control" id="editKeyValue_<?php echo $_smarty_tpl->getValue('lang');?>
" name="lang_value_<?php echo $_smarty_tpl->getValue('lang');?>
" rows="3"></textarea>
                           </div>
                       <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
               <button type="button" class="btn btn-primary" id="updateKeyBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"save"), $_smarty_tpl);?>
</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprachschlüssel löschen -->
<div class="modal fade" id="deleteKeyModal" tabindex="-1" aria-labelledby="deleteKeyModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="deleteKeyModalLabel"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete_language_key"), $_smarty_tpl);?>
</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <p><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"confirm_delete_key"), $_smarty_tpl);?>
</p>
               <p><strong id="deleteKeyName"></strong></p>
               <input type="hidden" id="deleteKeyValue" value="">
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"cancel"), $_smarty_tpl);?>
</button>
               <button type="button" class="btn btn-danger" id="confirmDeleteKeyBtn"><?php echo $_smarty_tpl->getSmarty()->getFunctionHandler('translate')->handle(array('key'=>"delete"), $_smarty_tpl);?>
</button>
           </div>
       </div>
   </div>
</div>

<?php echo '<script'; ?>
 src="../js/admin/languages.js"><?php echo '</script'; ?>
>

<?php $_smarty_tpl->renderSubTemplate("file:admin/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

<?php }
}
