{include file="admin/header.tpl" title={translate key="language_management"}}

<div class="row mb-4">
   <div class="col-md-12">
       <div class="card shadow">
           <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
               <h4 class="mb-0">{translate key="language_management"}</h4>
               <div>
                   <button type="button" class="btn btn-light btn-sm" id="addLanguageBtn" 
                           {if !$user->hasPermission('language.create')}disabled{/if}>
                       <i class="bi bi-plus-circle"></i> {translate key="add_language"}
                   </button>
                   <button type="button" class="btn btn-light btn-sm ms-2" id="addKeyBtn" 
                           {if !$user->hasPermission('language.create')}disabled{/if}>
                       <i class="bi bi-plus-circle"></i> {translate key="add_language_key"}
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
                           <span class="input-group-text">{translate key="filter_by_language"}</span>
                           <select class="form-select" id="languageFilter">
                               <option value="all">{translate key="all_languages"}</option>
                               {foreach from=$availableLanguages item=lang}
                                   <option value="{$lang}">{translate key="lang_`$lang`"}</option>
                               {/foreach}
                           </select>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="input-group">
                           <span class="input-group-text">{translate key="search"}</span>
                           <input type="text" class="form-control" id="searchInput" placeholder="{translate key="search_keys_or_values"}">
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
                               <th>{translate key="language_key"}</th>
                               {foreach from=$availableLanguages item=lang}
                                   <th>{translate key="lang_`$lang`"}</th>
                               {/foreach}
                               <th>{translate key="actions"}</th>
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
               <h5 class="modal-title" id="addLanguageModalLabel">{translate key="add_language"}</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="addLanguageForm">
                   <div class="mb-3">
                       <label for="languageCode" class="form-label">{translate key="language_code"}</label>
                       <input type="text" class="form-control" id="languageCode" name="lang_code" required 
                              placeholder="z.B. de, en, fr, es" maxlength="5">
                       <div class="form-text">{translate key="language_code_hint"}</div>
                   </div>
                   <div class="mb-3">
                       <label for="languageName" class="form-label">{translate key="language_name"}</label>
                       <input type="text" class="form-control" id="languageName" name="lang_name" required>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
               <button type="button" class="btn btn-primary" id="saveLanguageBtn">{translate key="save"}</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprachschlüssel hinzufügen -->
<div class="modal fade" id="addKeyModal" tabindex="-1" aria-labelledby="addKeyModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="addKeyModalLabel">{translate key="add_language_key"}</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="addKeyForm">
                   <div class="mb-3">
                       <label for="keyName" class="form-label">{translate key="language_key"}</label>
                       <input type="text" class="form-control" id="keyName" name="lang_key" required>
                       <div class="form-text">{translate key="language_key_hint"}</div>
                   </div>
                   
                   <div class="row">
                       {foreach from=$availableLanguages item=lang}
                           <div class="col-md-6 mb-3">
                               <label for="keyValue_{$lang}" class="form-label">{translate key="lang_`$lang`"}</label>
                               <textarea class="form-control" id="keyValue_{$lang}" name="lang_value_{$lang}" rows="3" required></textarea>
                           </div>
                       {/foreach}
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
               <button type="button" class="btn btn-primary" id="saveKeyBtn">{translate key="save"}</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprachschlüssel bearbeiten -->
<div class="modal fade" id="editKeyModal" tabindex="-1" aria-labelledby="editKeyModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="editKeyModalLabel">{translate key="edit_language_key"}</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="editKeyForm">
                   <div class="mb-3">
                       <label for="editKeyName" class="form-label">{translate key="language_key"}</label>
                       <input type="text" class="form-control" id="editKeyName" name="lang_key" readonly>
                   </div>
                   
                   <div class="row">
                       {foreach from=$availableLanguages item=lang}
                           <div class="col-md-6 mb-3">
                               <label for="editKeyValue_{$lang}" class="form-label">{translate key="lang_`$lang`"}</label>
                               <textarea class="form-control" id="editKeyValue_{$lang}" name="lang_value_{$lang}" rows="3"></textarea>
                           </div>
                       {/foreach}
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
               <button type="button" class="btn btn-primary" id="updateKeyBtn">{translate key="save"}</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Sprachschlüssel löschen -->
<div class="modal fade" id="deleteKeyModal" tabindex="-1" aria-labelledby="deleteKeyModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="deleteKeyModalLabel">{translate key="delete_language_key"}</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <p>{translate key="confirm_delete_key"}</p>
               <p><strong id="deleteKeyName"></strong></p>
               <input type="hidden" id="deleteKeyValue" value="">
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
               <button type="button" class="btn btn-danger" id="confirmDeleteKeyBtn">{translate key="delete"}</button>
           </div>
       </div>
   </div>
</div>

<script src="../js/admin/languages.js"></script>

{include file="admin/footer.tpl"}

