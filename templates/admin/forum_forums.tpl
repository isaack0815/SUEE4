{* Forum Forums Admin Template *}

<div class="container-fluid mt-4">
    <h1 class="mb-4">Foren verwalten</h1>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Foren</h5>
            <a href="forum.php?action=forum_form" class="btn btn-primary">Neues Forum</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Kategorie</th>
                        <th>Beschreibung</th>
                        <th>Sortierung</th>
                        <th>Erstellt am</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$forums item=forum}
                        <tr>
                            <td>{$forum.name}</td>
                            <td>{$forum.category_name}</td>
                            <td>{$forum.description|truncate:50:"..."}</td>
                            <td>{$forum.sort_order}</td>
                            <td>{$forum.created_at|date_format:"%d.%m.%Y, %H:%M"}</td>
                            <td>
                                <a href="forum.php?action=forum_form&id={$forum.id}" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                                <a href="forum.php?action=forum_delete&id={$forum.id}" class="btn btn-sm btn-outline-danger">Löschen</a>
                            </td>
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="6" class="text-center">Keine Foren vorhanden</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>

