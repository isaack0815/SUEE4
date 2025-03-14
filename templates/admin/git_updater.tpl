{include file="admin/header.tpl" title="Git Updater"}

<div class="container-fluid px-4">
    <h1 class="mt-4">{translate key="git_updater"}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">{translate key="dashboard"}</a></li>
        <li class="breadcrumb-item active">{translate key="git_updater"}</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-code-branch me-1"></i>
                    {translate key="form_title"}
                </div>
                <div class="card-body">
                    <small>{translate key="git_updater_description"}</small>
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
                                    {translate key="applied_commits"}
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>{translate key="commit_hash"}</th>
                                                <th>{translate key="commit_message"}</th>
                                                <th>{translate key="commit_author"}</th>
                                                <th>{translate key="commit_date"}</th>
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
                                            {translate key="added_files"}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                {if count($updateResult.files.added) > 0}
                                                    {foreach from=$updateResult.files.added item=file}
                                                        <li class="list-group-item">{$file}</li>
                                                    {/foreach}
                                                {else}
                                                    <li class="list-group-item">{translate key="no_files"}</li>
                                                {/if}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-edit me-1"></i>
                                            {translate key="modified_files"}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                {if count($updateResult.files.modified) > 0}
                                                    {foreach from=$updateResult.files.modified item=file}
                                                        <li class="list-group-item">{$file}</li>
                                                    {/foreach}
                                                {else}
                                                    <li class="list-group-item">{translate key="no_files"}</li>
                                                {/if}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-trash-alt me-1"></i>
                                            {translate key="removed_files"}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group">
                                                {if count($updateResult.files.removed) > 0}
                                                    {foreach from=$updateResult.files.removed item=file}
                                                        <li class="list-group-item">{$file}</li>
                                                    {/foreach}
                                                {else}
                                                    <li class="list-group-item">{translate key="no_files"}</li>
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
                                        {translate key="sql_results"}
                                    </div>
                                    <div class="card-body">
                                        {if count($updateResult.sql.success) > 0}
                                            <h5>{translate key="sql_executed"}</h5>
                                            <ul class="list-group mb-3">
                                                {foreach from=$updateResult.sql.success item=file}
                                                    <li class="list-group-item">{$file}</li>
                                                {/foreach}
                                            </ul>
                                        {/if}
                                        
                                        {if count($updateResult.sql.errors) > 0}
                                            <h5>{translate key="sql_errors"}</h5>
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
                    <input type="hidden" name="repo_url" id="repo_url" value="https://github.com/isaack0815/SUEE4.git">
                    <input type="hidden" class="form-control" id="access_token" name="access_token" value="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">
                        <div class="mb-3">
                            <button type="button" class="btn btn-info" id="checkUpdates">
                                <i class="fas fa-search me-1"></i> {translate key="check_for_updates"}
                            </button>
                            <button type="submit" class="btn btn-primary" name="update" id="updateButton">
                                <i class="fas fa-sync me-1"></i> {translate key="update_system"}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    {translate key="backups"}
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
                                    <td colspan="4" class="text-center">{translate key="no_backups"}</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/admin/git_updater.js"></script>

{include file="admin/footer.tpl"}

