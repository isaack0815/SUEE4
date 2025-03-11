{* Forum Category Delete Template *}

<div class="container-fluid mt-4">
    <h1 class="mb-4">Kategorie löschen</h1>
    
    {if isset($error)}
        <div class="alert alert-danger">
            {$error}
        </div>
    {/if}
    
    <div class="card">
        <div class="card-body">
            <p class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Achtung: Wenn Sie diese Kategorie löschen, werden auch alle darin enthaltenen Foren, Themen und Beiträge gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.
            </p>
            
            <p>Sind Sie sicher, dass Sie die Kategorie <strong>{$category.name}</strong> löschen möchten?</p>
            
            <div class="d-flex justify-content-between">
                <a href="forum.php?action=category_delete_confirm&id={$category.id}" class="btn btn-danger">Ja, Kategorie löschen</a>
                <a href="forum.php?action=list" class="btn btn-outline-secondary">Abbrechen</a>
            </div>
        </div>
    </div>
</div>

