{include file="admin/header.tpl" title={translate key="admin_forum"}}

<div class="container-fluid mt-4">
    <h1 class="mb-4">Forum Administration</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kategorien</h5>
                    <a href="forum.php?action=category_form" class="btn btn-sm btn-primary">Neues Forum</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Beschreibung</th>
                                <th>Sortierung</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$categories item=category}
                                <tr>
                                    <td>{$category.name}</td>
                                    <td>{$category.description|truncate:50:"..."}</td>
                                    <td>{$category.sort_order}</td>
                                    <td>
                                        <a href="forum.php?action=category_form&id={$category.id}" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                                        <a href="forum.php?action=category_delete&id={$category.id}" class="btn btn-sm btn-outline-danger">Löschen</a>
                                    </td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="4" class="text-center">Keine Kategorien vorhanden</td>
                                </tr>
                            {/foreach} 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Foren</h5>
                    <a href="forum.php?action=forum_form" class="btn btn-sm btn-primary">Neue Kategorie</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Kategorie</th>
                                <th>Sortierung</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$forums item=forum}
                                <tr>
                                    <td>{$forum.name}</td>
                                    <td>{$forum.category_name}</td>
                                    <td>{$forum.sort_order}</td>
                                    <td>
                                        <a href="forum.php?action=forum_form&id={$forum.id}" class="btn btn-sm btn-outline-primary">Bearbeiten</a>
                                        <a href="forum.php?action=forum_delete&id={$forum.id}" class="btn btn-sm btn-outline-danger">Löschen</a>
                                    </td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="4" class="text-center">Keine Foren vorhanden</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}