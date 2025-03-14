{include file="admin/header.tpl" title="Git Updater"}

<div class="container-fluid px-4">
    <h1 class="mt-4">{translate key="git_updater_title"}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">{translate key="dashboard"}</a></li>
        <li class="breadcrumb-item active">{translate key="git_updater_title"}</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-code-branch me-1"></i>
                    {translate key="git_updater_form_title"}
                </div>
                <div class="card-body">
                    {if isset($error)}
                        <div class="alert alert-danger">{$error}</div>
                    {/if}
                    
                    {if isset($updateResult)}
                        <div class="alert {if $updateResult.success}alert-success{else}alert-danger{/if}">
                            {$updateResult.message}
                        </div>
                        
                        {if $updateResult.success && isset($updateResult.commits) && count($updateResult.commits) > 0}
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-list me-1"></i>
                                    {translate key="git_updater_applied_commits"}
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{translate key="git_updater_commit_hash"}</th>
                                                <th>{translate key="git_updater_commit_message"}</th>
                                                <th>{translate key="git_updater_commit_author"}</th>
                                                <th>{translate key="git_updater_commit_date"}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$updateResult.commits item=commit}
                                                <tr>
                                                    <td>{$commit.sha|truncate:8:""}</td>
                                                    <td>{$commit.message}</td>
                                                    <td>{$commit.author}</td>
                                                    <td>{$commit.date|date_format:"%Y-%m-%d %H:%M:%S"}</td>
                                                </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            {translate key="git_updater_added_files"}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                {if count($updateResult.files.added) > 0}
                                                    {foreach from=$updateResult.files.added item=file}
                                                        <li class="list-group-item">{$file}</li>
                                                    {/foreach}
                                                {else}
                                                    <li class="list-group-item">{translate key="git_updater_no_files"}</li>
                                                {/if}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-edit me-1"></i>
                                            {translate key="git_updater_modified_files"}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                {if count($updateResult.files.modified) > 0}
                                                    {foreach from=$updateResult.files.modified item=file}
                                                        <li class="list-group-item">{$file}</li>
                                                    {/foreach}
                                                {else}
                                                    <li class="list-group-item">{translate key="git_updater_no_files"}</li>
                                                {/if}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-trash-alt me-1"></i>
                                            {translate key="git_updater_removed_files"}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                {if count($updateResult.files.removed) > 0}
                                                    {foreach from=$updateResult.files.removed item=file}
                                                        <li class="list-group-item">{$file}</li>
                                                    {/foreach}
                                                {else}
                                                    <li class="list-group-item">{translate key="git_updater_no_files"}</li>
                                                {/if}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {if count($updateResult.sql.success) > 0 || count($updateResult.sql.errors) > 0}
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-database me-1"></i>
                                        {translate key="git_updater_sql_results"}
                                    </div>
                                    <div class="card-body">
                                        {if count($updateResult.sql.success) > 0}
                                            <h5>{translate key="git_updater_sql_executed"}</h5>
                                            <ul class="list-group mb-3">
                                                {foreach from=$updateResult.sql.success item=file}
                                                    <li class="list-group-item">{$file}</li>
                                                {/foreach}
                                            </ul>
                                        {/if}
                                        
                                        {if count($updateResult.sql.errors) > 0}
                                            <h5>{translate key="git_updater_sql_errors"}</h5>
                                            <ul class="list-group">
                                                {foreach from=$updateResult.sql.errors item=error}
                                                    <li class="list-group-item list-group-item-danger">
                                                        {$error.file}: {$error.error}
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        {/if}
                                    </div>
                                </div>
                            {/if}
                        {/if}
                    {/if}
                    
                    <form method="post" action="git_updater.php" id="updateForm">
                        <div class="mb-3">
                            <label for="repo_url" class="form-label">{translate key="git_updater_repo_url"}</label>
                            <input type="text" class="form-control" id="repo_url" name="repo_url" required 
                                   placeholder="https://github.com/username/repository">
                            <div class="form-text">{translate key="git_updater_repo_url_help"}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="access_token" class="form-label">{translate key="git_updater_access_token"}</label>
                            <input type="password" class="form-control" id="access_token" name="access_token">
                            <div class="form-text">{translate key="git_updater_access_token_help"}</div>
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-info" id="checkUpdates">
                                <i class="fas fa-search me-1"></i> {translate key="git_updater_check_updates"}
                            </button>
                            <button type="submit" class="btn btn-primary" name="update" id="updateButton">
                                <i class="fas fa-sync me-1"></i> {translate key="git_updater_update_system"}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    {translate key="git_updater_backups"}
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{translate key="git_updater_backup_filename"}</th>
                                <th>{translate key="git_updater_backup_size"}</th>
                                <th>{translate key="git_updater_backup_date"}</th>
                                <th>{translate key="git_updater_backup_actions"}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if count($backups) > 0}
                                {foreach from=$backups item=backup}
                                    <tr>
                                        <td>{$backup.filename}</td>
                                        <td>{$backup.size} MB</td>
                                        <td>{$backup.date}</td>
                                        <td>
                                            <a href="../backups/{$backup.filename}" class="btn btn-sm btn-primary" download>
                                                <i class="fas fa-download me-1"></i> {translate key="download"}
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="4" class="text-center">{translate key="git_updater_no_backups"}</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/admin/git_updater.js"></script>

{include file="admin/footer.tpl"}

