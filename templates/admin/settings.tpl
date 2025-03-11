{include file="admin/header.tpl"}

<h1>Einstellungen</h1>

{if $success_message != ''}
    <div class="alert alert-success">{$success_message}</div>
{/if}

<form method="post" action="settings.php">
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
                {include file=$settingData.template}
            </div>
        {/foreach}
    </div>

    <button type="submit" class="btn btn-primary mt-3">Einstellungen speichern</button>
</form>

{include file="admin/footer.tpl"}

