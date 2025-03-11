{* Forum Categories Admin Template *}

<div class="container-fluid mt-4">
    <h1 class="mb-4">Kategorien verwalten</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kategorien</h5>
            <a href="forum.php?action=category_form" class="btn btn-primary">Neue Kategorie</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Beschreibung</th>
                        <th>Sortierung</th>
                        <th>Erstellt am</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$categories item=category}
                        <tr>
                            <td>{$category.name}</td>
                            <td>{$category.description|truncate:100:"..."}</td>
                            <td>{$category.sort_order}</td>
                            <td>{$category.created_at|date_format:"%d.%m.%Y, %H:%M"}</td>
                            <td>
                                <a href="forum.php?action=category_form&id={$category.id}" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                                <a href="forum.php?action=category_delete&id={$category.id}" class="btn btn-sm btn-outline-danger">Löschen</a>
                            </td>
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="5" class="text-center">Keine Kategorien vorhanden</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>

