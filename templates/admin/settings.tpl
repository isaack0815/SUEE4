{include file="admin/header.tpl"}

<h1>Einstellungen</h1>

<div id="settingsMessages"></div>

<ul class="nav nav-tabs" id="settingsTabs" role="tablist">
    {foreach $settings as $settingName => $settingData}
        <li class="nav-item" role="presentation">
            <button class="nav-link {if $settingName@first}active{/if}" id="{$settingName}-tab" data-bs-toggle="tab" data-bs-target="#{$settingName}" type="button" role="tab" aria-controls="{$settingName}" aria-selected="{if $settingName@first}true{else}false{/if}">{$settingData.title}</button>
        </li>
    {/foreach}
</ul>

<div class="tab-content" id="settingsTabsContent">
    {foreach $settings as $settingName => $settingData}
        <div class="tab-pane fade {if $settingName@first}show active{/if}" id="{$settingName}" role="tabpanel" aria-labelledby="{$settingName}-tab">
            <h2>{$settingData.title}</h2>
            <form id="{$settingName}Form" class="settings-form" data-api-endpoint="/admin/api/{$settingName}.php">
                {include file=$settingData.template}
                <button type="submit" class="btn btn-primary mt-3">Einstellungen speichern</button>
            </form>
        </div>
    {/foreach}
</div>

<script src="/js/admin/admin-settings.js"></script>

{include file="admin/footer.tpl"}

