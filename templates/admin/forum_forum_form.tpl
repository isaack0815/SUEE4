{* Forum Forum Form Template *}

<div class="container-fluid mt-4">
    <h1 class="mb-4">{if $forum.id}Forum bearbeiten{else}Neues Forum{/if}</h1>
    
    {if isset($error)}
        <div class="alert alert-danger">
            {$error}
        </div>
    {/if}
    
    <div class="card">
        <div class="card-body">
            <form action="forum.php?action=forum_save" method="post">
                {if $forum.id}
                    <input type="hidden" name="id" value="{$forum.id}">
                {/if}
                
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategorie</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Kategorie auswählen --</option>
                        {foreach from=$categories item=category}
                            <option value="{$category.id}" {if $forum.category_id == $category.id}selected{/if}>{$category.name}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{$forum.name|default:''}" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Beschreibung</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{$forum.description|default:''}</textarea>
                </div>
                
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sortierung</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{$forum.sort_order|default:0}" min="0">
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                    <a href="forum.php?action=list" class="btn btn-outline-secondary">Abbrechen</a>
                </div>
            </form>
        </div>
    </div>
</div>

