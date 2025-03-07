{include file="admin/header.tpl" title={translate key="user_management"}}

<div class="row">
   <div class="col-md-12">
       <div class="card shadow">
           <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
               <h4 class="mb-0">{translate key="user_management"}</h4>
               <button type="button" class="btn btn-light btn-sm" id="addUserBtn" 
                       {if !$user->hasPermission('user.create')}disabled{/if}>
                   <i class="bi bi-plus-circle"></i> {translate key="add_user"}
               </button>
           </div>
           <div class="card-body">
               <!-- Benachrichtigungsbereich -->
               <div id="alertArea" class="mb-3" style="display: none;"></div>
               
               <!-- Benutzertabelle -->
               <div class="table-responsive">
                   <table class="table table-striped table-hover" id="usersTable">
                       <thead>
                           <tr>
                               <th>{translate key="username"}</th>
                               <th>{translate key="email"}</th>
                               <th>{translate key="groups"}</th>
                               <th>{translate key="last_login"}</th>
                               <th>{translate key="actions"}</th>
                           </tr>
                       </thead>
                       <tbody>
                           <!-- Wird dynamisch mit JavaScript gefüllt -->
                       </tbody>
                   </table>
               </div>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Benutzer hinzufügen/bearbeiten -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="userModalLabel">{translate key="add_user"}</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="userForm">
                   <input type="hidden" id="userId" name="id" value="">
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="username" class="form-label">{translate key="username"}</label>
                           <input type="text" class="form-control" id="username" name="username" required>
                       </div>
                       <div class="col-md-6">
                           <label for="email" class="form-label">{translate key="email"}</label>
                           <input type="email" class="form-control" id="email" name="email" required>
                       </div>
                   </div>
                   
                   <div class="mb-3">
                       <label for="password" class="form-label">{translate key="password"}</label>
                       <input type="password" class="form-control" id="password" name="password">
                       <div class="form-text" id="passwordHelpText">{translate key="password_help_text"}</div>
                   </div>
                   
                   <div class="mb-3">
                       <label class="form-label">{translate key="groups"}</label>
                       <div class="row" id="groupsContainer">
                           {foreach from=$groups item=group}
                               <div class="col-md-6 mb-2">
                                   <div class="form-check">
                                       <input class="form-check-input group-checkbox" type="checkbox" id="group_{$group.id}" value="{$group.id}" name="groups[]">
                                       <label class="form-check-label" for="group_{$group.id}">
                                           {$group.name}
                                       </label>
                                   </div>
                               </div>
                           {/foreach}
                       </div>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
               <button type="button" class="btn btn-primary" id="saveUserBtn">{translate key="save"}</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal für Benutzer löschen -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="deleteUserModalLabel">{translate key="delete_user"}</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <p>{translate key="confirm_delete_user"}</p>
               <p><strong id="deleteUserName"></strong></p>
               <input type="hidden" id="deleteUserId" value="">
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
               <button type="button" class="btn btn-danger" id="confirmDeleteBtn">{translate key="delete"}</button>
           </div>
       </div>
   </div>
</div>

<script src="../js/admin/users.js"></script>

{include file="admin/footer.tpl"}

