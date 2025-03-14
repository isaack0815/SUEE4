{include file="admin/header.tpl" title={translate key="forum_category_form"}}

<div class="container-fluid mt-4">
    <h1 class="mb-4">{if $category.id}Kategorie bearbeiten{else}Neue Kategorie{/if}</h1>
    
    {if isset($error)}
        <div class="alert alert-danger">
            {$error}
        </div>
    {/if}
    
    <div class="card">
        <div class="card-body">
            <form action="forum.php?action=category_save" method="post">
                {if $category.id}
                    <input type="hidden" name="id" value="{$category.id}">
                {/if}
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{$category.name|default:''}" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Beschreibung</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{$category.description|default:''}</textarea>
                </div>
                
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sortierung</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{$category.sort_order|default:0}" min="0">
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                    <a href="forum.php?action=list" class="btn btn-outline-secondary">Abbrechen</a>
                </div>
            </form>
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}