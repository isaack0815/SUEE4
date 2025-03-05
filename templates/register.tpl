{include file="header.tpl"}

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{translate key="register"}</h4>
            </div>
            <div class="card-body">
                {if $success}
                    <div class="alert alert-success">
                        {translate key="registration_success"}
                        <a href="index.php">{translate key="login_now"}</a>
                    </div>
                {else}
                    {if $error}
                        <div class="alert alert-danger">
                            {translate key=$error}
                        </div>
                    {/if}
                    
                    <form id="registerForm" action="register.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">{translate key="username"}</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="invalid-feedback">{translate key="username_required"}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">{translate key="email"}</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">{translate key="email_required"}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">{translate key="password"}</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">{translate key="password_required"}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">{translate key="confirm_password"}</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">{translate key="confirm_password_required"}</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">{translate key="register"}</button>
                        </div>
                    </form>
                {/if}
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">{translate key="already_have_account"} <a href="index.php">{translate key="login"}</a></p>
            </div>
        </div>
    </div>
</div>

<script src="js/register.js"></script>

{include file="footer.tpl"}

