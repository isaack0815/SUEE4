{include file="header.tpl"}

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{translate key="login"}</h4>
            </div>
            <div class="card-body">
                {if $error}
                    <div class="alert alert-danger">
                        {translate key=$error}
                    </div>
                {/if}
                
                <form id="loginForm" action="login.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">{translate key="username"}</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">{translate key="username_required"}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">{translate key="password"}</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">{translate key="password_required"}</div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">{translate key="remember_me"}</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{translate key="login"}</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">{translate key="no_account"} <a href="register.php">{translate key="register_now"}</a></p>
                <p class="mt-2 mb-0"><a href="forgot-password.php">{translate key="forgot_password"}</a></p>
            </div>
        </div>
    </div>
</div>

<script src="js/login.js"></script>

{include file="footer.tpl"}

