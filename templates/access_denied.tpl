{include file="header.tpl"}

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">{translate key="access_denied"}</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <p>{translate key="permission_required"}</p>
                </div>
                <p class="text-center">
                    <a href="index.php" class="btn btn-primary">{translate key="back_to_home"}</a>
                </p>
            </div>
        </div>
    </div>
</div>

{include file="footer.tpl"}