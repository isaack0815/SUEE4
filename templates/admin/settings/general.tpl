<div class="mb-3">
    <label for="site_name" class="form-label">{$translations.site_name}</label>
    <input type="text" class="form-control" id="site_name" name="site_name" value="{$settings.general.values.site_name|escape}">
</div>
<div class="mb-3">
    <label for="site_description" class="form-label">{$translations.site_description}</label>
    <textarea class="form-control" id="site_description" name="site_description">{$settings.general.values.site_description|escape}</textarea>
</div>
<div class="mb-3">
    <label for="admin_email" class="form-label">{$translations.admin_email}</label>
    <input type="email" class="form-control" id="admin_email" name="admin_email" value="{$settings.general.values.admin_email|escape}">
</div>
<div class="mb-3">
    <label for="items_per_page" class="form-label">{$translations.items_per_page}</label>
    <input type="number" class="form-control" id="items_per_page" name="items_per_page" value="{$settings.general.values.items_per_page|escape}">
</div>

