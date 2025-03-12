{include file="header.tpl" title=$page_title}

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="forum.php">Forum</a></li>
            {if $action == 'edit'}
                <li class="breadcrumb-item"><a href="forum_forum.php?id={$details.forum_id}">{$details.forum_name}</a></li>
                <li class="breadcrumb-item"><a href="forum_topic.php?id={$topicId}">{$details.title}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Beitrag bearbeiten</li>
            {elseif $topicId > 0}
                <li class="breadcrumb-item"><a href="forum_forum.php?id={$details.forum_id}">{$details.forum_name}</a></li>
                <li class="breadcrumb-item"><a href="forum_topic.php?id={$details}">{$details.title}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Antwort schreiben</li>
            {else}
                <li class="breadcrumb-item"><a href="forum_forum.php?id={$forumId}">{$details.name}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Neues Thema</li>
            {/if}
        </ol>
    </nav>
    
    <h1 class="mb-4">
        {if $action == 'edit'}
            Beitrag bearbeiten
        {elseif $topicId > 0}
            Antwort schreiben
        {else}
            Neues Thema
        {/if}
    </h1>
    
    <div class="card">
        <div class="card-body">
            <form action="{if $action == 'edit'}forum_post.php?action=edit&post={$post.id}{else}forum_post.php?action=new&topic={$topicId}&forum={$forumId}{/if}" method="post">
                <input type="hidden" name="action" value="{$action}">
                {if $topicId > 0}
                    <input type="hidden" name="topic" value="{$topicId}">
                {else}
                    <input type="hidden" name="forum" value="{$forumId}">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Titel</label>
                        <input type="text" class="form-control" id="title" name="title" value="{$post.title|default:''}" required>
                    </div>
                {/if}
                
                <div class="mb-3">
                    <label for="content" class="form-label">Inhalt</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required>{$post.content|default:''}</textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        {if $action == 'edit'}
                            Speichern
                        {elseif $topicId > 0}
                            Antworten
                        {else}
                            Thema erstellen
                        {/if}
                    </button>
                    
                    <a href="{if $topicId > 0}forum_topic.php?id={$topicId}{elseif $forumId > 0}forum_forum.php?id={$forumId}{else}forum.php{/if}" class="btn btn-outline-secondary">Abbrechen</a>
                </div>
            </form>
        </div>
    </div>
</div>

