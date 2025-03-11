{include file="header.tpl" title=$page_title}

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="forum.php">Forum</a></li>
            <li class="breadcrumb-item active" aria-current="page">{$forum.name}</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{$forum.name}</h1>
        <div>
            <a href="forum_post.php?action=new&topic=0&forum={$forum.id}" class="btn btn-primary">Neues Thema</a>
            {if isset($smarty.session.user_id)}
                {if $forum.is_subscribed}
                    <a href="forum_subscribe.php?action=unsubscribe_forum&forum={$forum.id}" class="btn btn-outline-secondary">
                        <i class="bi bi-bell-slash"></i> Abonnement beenden
                    </a>
                {else}
                    <a href="forum_subscribe.php?action=subscribe_forum&forum={$forum.id}" class="btn btn-outline-secondary">
                        <i class="bi bi-bell"></i> Forum abonnieren
                    </a>
                {/if}
            {/if}
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            {$forum.description}
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <div class="row">
                <div class="col-md-6">Thema</div>
                <div class="col-md-1 text-center">Antworten</div>
                <div class="col-md-1 text-center">Aufrufe</div>
                <div class="col-md-4">Letzter Beitrag</div>
            </div>
        </div> 
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                {foreach from=$topics item=topic}
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    {if $topic.is_sticky}
                                        <span class="badge bg-warning me-2" title="Angepinnt">
                                            <i class="bi bi-pin-angle"></i>
                                        </span>
                                    {/if}
                                    {if $topic.is_locked}
                                        <span class="badge bg-secondary me-2" title="Gesperrt">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    {/if}
                                    <div>
                                        <a href="forum_topic.php?id={$topic.id}" class="fw-bold">{$topic.title}</a>
                                        <div class="small text-muted">
                                            von {$topic.username}
                                            <br>
                                            {$topic.created_at|date_format:"%d.%m.%Y, %H:%M"}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 text-center">
                                {$topic.post_count - 1}
                            </div>
                            <div class="col-md-1 text-center">
                                {$topic.views}
                            </div>
                            <div class="col-md-4">
                                {if $topic.latest_post}
                                    <div class="small">
                                        von {$topic.latest_post.username}
                                        <br>
                                        {$topic.latest_post.created_at|date_format:"%d.%m.%Y, %H:%M"}
                                    </div>
                                {else}
                                    <span class="text-muted">Keine Beiträge</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                {foreachelse}
                    <div class="list-group-item text-center py-4">
                        Keine Themen in diesem Forum
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>

{include file="footer.tpl"}