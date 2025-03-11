{include file="header.tpl" title=$page_title}

<div class="container mt-4">
    <h1 class="mb-4">Forum</h1>
    
    {foreach from=$categories item=category}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{$category.name}</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Forum</th>
                            <th class="text-center">Themen</th>
                            <th class="text-center">Beiträge</th>
                            <th>Letzter Beitrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$category.forums item=forum}
                            <tr>
                                <td>
                                    <a href="forum_forum.php?id={$forum.id}" class="fw-bold">{$forum.name}</a>
                                    <div class="small text-muted">{$forum.description}</div>
                                </td>
                                <td class="text-center">{$forum.topic_count}</td>
                                <td class="text-center">{$forum.post_count}</td>
                                <td>
                                    {if $forum.latest_topic}
                                        <a href="forum_topic.php?id={$forum.latest_topic.id}">{$forum.latest_topic.title|truncate:30:"..."}</a>
                                        <div class="small text-muted">
                                            von {$forum.latest_topic.username}
                                            <br>
                                            {$forum.latest_topic.last_post_time|date_format:"%d.%m.%Y, %H:%M"}
                                        </div>
                                    {else}
                                        <span class="text-muted">Keine Beiträge</span>
                                    {/if}
                                </td>
                            </tr>
                        {foreachelse}
                            <tr>
                                <td colspan="4" class="text-center">Keine Foren in dieser Kategorie</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    {foreachelse}
        <div class="alert alert-info">
            Es sind noch keine Kategorien oder Foren vorhanden.
        </div>
    {/foreach}
</div>

{include file="footer.tpl"}