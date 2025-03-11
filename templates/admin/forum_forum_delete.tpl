{* Forum Forum Delete Template *}

<div class="container-fluid mt-4">
    <h1 class="mb-4">Forum löschen</h1>
    
    {if isset($error)}
        <div class="alert alert-danger">
            {$error}
        </div>
    {/if}
    
    <div class="card">
        <div class="card-body">
            <p class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Achtung: Wenn Sie dieses Forum löschen, werden auch alle darin enthaltenen Themen und Beiträge gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.
            </p>
            
            <p>Sind Sie sicher, dass Sie das Forum <strong>{$forum.name}</strong> löschen möchten?</p>
            
            <div class="d-flex justify-content-between">
                <a href="forum.php?action=forum_delete_confirm&id={$forum.id}" class="btn btn-danger">Ja, Forum löschen</a>
                <a href="forum.php?action=list" class="btn btn-outline-secondary">Abbrechen</a>
            </div>
        </div>
    </div>
</div>

