<div class="mb-3">
    <label for="smtp_host" class="form-label">{$translations.smtp_host}</label>
    <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="{$settings.email.values.smtp_host|escape}">
</div>
<div class="mb-3">
    <label for="smtp_port" class="form-label">{$translations.smtp_port}</label>
    <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="{$settings.email.values.smtp_port|escape}">
</div>
<div class="mb-3">
    <label for="smtp_username" class="form-label">{$translations.smtp_username}</label>
    <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="{$settings.email.values.smtp_username|escape}">
</div>
<div class="mb-3">
    <label for="smtp_password" class="form-label">{$translations.smtp_password}</label>
    <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="{$settings.email.values.smtp_password|escape}">
</div>

