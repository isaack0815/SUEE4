{include file="header.tpl"}

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{translate key="dashboard"}</h4>
            </div>
            <div class="card-body">
                <h5>{translate key="welcome" username=$currentUser.username}</h5>
                <p>{translate key="last_login"}: {$currentUser.last_login}</p>
                
                <div class="alert alert-info">
                    {translate key="dashboard_info"}
                </div>
            </div>
        </div>
    </div>
</div>

{include file="footer.tpl"}

