{include file="header.tpl" title=$page_title}

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="forum.php">Forum</a></li>
            <li class="breadcrumb-item"><a href="forum_forum.php?id={$topic.forum_id}">{$topic.forum_name}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{$topic.title}</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{$topic.title}</h1>
        <div>
            {if !$topic.is_locked}
                <a href="forum_post.php?action=new&topic={$topic.id}" class="btn btn-primary">Antworten</a>
            {/if}
            
            {if isset($smarty.session.user_id)}
                {if $topic.is_subscribed}
                    <a href="forum_subscribe.php?action=unsubscribe_topic&topic={$topic.id}" class="btn btn-outline-secondary">
                        <i class="bi bi-bell-slash"></i> Abonnement beenden
                    </a>
                {else}
                    <a href="forum_subscribe.php?action=subscribe_topic&topic={$topic.id}" class="btn btn-outline-secondary">
                        <i class="bi bi-bell"></i> Thema abonnieren
                    </a>
                {/if}
            {/if}
            
            {if $is_moderator}
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="moderationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Moderation
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="moderationDropdown">
                        {if $topic.is_sticky}
                            <li><a class="dropdown-item" href="forum_moderate.php?action=unsticky&topic={$topic.id}">Nicht mehr anpinnen</a></li>
                        {else}
                            <li><a class="dropdown-item" href="forum_moderate.php?action=sticky&topic={$topic.id}">Anpinnen</a></li>
                        {/if}
                        
                        {if $topic.is_locked}
                            <li><a class="dropdown-item" href="forum_moderate.php?action=unlock&topic={$topic.id}">Entsperren</a></li>
                        {else}
                            <li><a class="dropdown-item" href="forum_moderate.php?action=lock&topic={$topic.id}">Sperren</a></li>
                        {/if}
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="forum_moderate.php?action=delete_topic&topic={$topic.id}" onclick="return confirm('Sind Sie sicher, dass Sie dieses Thema löschen möchten?')">Thema löschen</a></li>
                    </ul>
                </div>
            {/if}
        </div>
    </div>
    
    {foreach from=$posts item=post name=posts}
        <div class="card mb-4" id="post-{$post.id}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold">{$post.username}</span>
                    <span class="text-muted ms-2">{$post.created_at|date_format:"%d.%m.%Y, %H:%M"}</span>
                </div>
                <div>
                    <a href="#post-{$post.id}" class="btn btn-sm btn-outline-secondary">#</a>
                    
                    {if isset($smarty.session.user_id) && ($smarty.session.user_id == $post.user_id || $is_moderator)}
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="postActionDropdown{$post.id}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="postActionDropdown{$post.id}">
                                {if $smarty.session.user_id == $post.user_id && !$topic.is_locked}
                                    <li><a class="dropdown-item" href="forum_post.php?action=edit&post={$post.id}&topic={$topic.id}">Bearbeiten</a></li>
                                {/if}
                                {if $is_moderator}
                                    <li><a class="dropdown-item text-danger" href="forum_moderate.php?action=delete_post&post={$post.id}" onclick="return confirm('Sind Sie sicher, dass Sie diesen Beitrag löschen möchten?')">Löschen</a></li>
                                {/if}
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center border-end">
                        <img src="{$post.avatar|default:'/assets/img/default-avatar.png'}" alt="{$post.username}" class="rounded-circle mb-2" style="width: 80px; height: 80px;">
                        <div class="small text-muted">
                            Beiträge: {$post.user_post_count}
                            <br>
                            Mitglied seit: {$post.user_created_at|date_format:"%d.%m.%Y"}
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="post-content">
                            {$post.content}
                        </div>
                        {if $post.updated_at}
                            <div class="small text-muted mt-3">
                                Zuletzt bearbeitet: {$post.updated_at|date_format:"%d.%m.%Y, %H:%M"}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
    
    {if !$topic.is_locked && isset($smarty.session.user_id)}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Antwort schreiben</h5>
            </div>
            <div class="card-body">
                <form action="forum_post.php?id={$topic.id}&action=newpost&topic={$topic.id}" method="post">
                    <input type="hidden" name="action" value="newpost">
                    <input type="hidden" name="topic" value="{$topic.id}">
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Inhalt</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Antworten</button>
                </form>
            </div>
        </div>
    {elseif $topic.is_locked}
        <div class="alert alert-warning">
            <i class="bi bi-lock"></i> Dieses Thema ist gesperrt und kann nicht mehr beantwortet werden.
        </div>
    {elseif !isset($smarty.session.user_id)}
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Sie müssen <a href="login.php?redirect={$smarty.server.REQUEST_URI|escape:'url'}">angemeldet sein</a>, um auf dieses Thema antworten zu können.
        </div>
    {/if}
</div>

{include file="footer.tpl"}