{include file="header.tpl"}

<div class="container-fluid py-4">
    <div class="row">
        {* Linke Spalte - nur anzeigen, wenn Sektionen vorhanden sind *}
        {if count($leftSections) > 0}
        <div class="col-md-3">
            {foreach from=$leftSections item=section}
                {* Sprachspezifische Inhalte abrufen *}
                {assign var="sectionContent" value=$section.contents[$currentLang]|default:null}
                
                <div class="section mb-4" style="background-color: {$section.background_color|default:'#ffffff'}; color: {$section.text_color|default:'#000000'};">
                    <div class="p-3">
                        {if $section.type == 'hero' && $sectionContent}
                            <div class="text-center">
                                {if isset($sectionContent.title)}<h2>{$sectionContent.title}</h2>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h4 class="mb-3">{$sectionContent.subtitle}</h4>{/if}
                                {if isset($sectionContent.content) && $sectionContent.content}<div>{$sectionContent.content}</div>{/if}
                            </div>
                        {elseif $section.type == 'text' && $sectionContent}
                            <div>
                                {if isset($sectionContent.title)}<h3 class="mb-3">{$sectionContent.title}</h3>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h5 class="mb-2">{$sectionContent.subtitle}</h5>{/if}
                                {if isset($sectionContent.content) && $sectionContent.content}<div>{$sectionContent.content}</div>{/if}
                            </div>
                        {elseif $section.type == 'cards' && isset($section.cards)}
                            <div class="text-center mb-3">
                                {if isset($sectionContent.title)}<h3 class="mb-2">{$sectionContent.title}</h3>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h5 class="mb-3">{$sectionContent.subtitle}</h5>{/if}
                            </div>
                            {foreach from=$section.cards item=card}
                                {* Sprachspezifische Inhalte für Karten abrufen *}
                                {assign var="cardContent" value=$card.contents[$currentLang]|default:null}
                                
                                <div class="card mb-3" style="background-color: {$card.background_color|default:'#ffffff'}; color: {$card.text_color|default:'#000000'};">
                                    {if isset($card.icon) && $card.icon}
                                        <div class="card-img-top text-center pt-3">
                                            <i class="fas fa-{$card.icon} fa-2x" style="color: {$card.icon_color|default:'inherit'};"></i>
                                        </div>
                                    {/if}
                                    {if $cardContent}
                                        <div class="card-body">
                                            {if isset($cardContent.title)}<h5 class="card-title">{$cardContent.title}</h5>{/if}
                                            {if isset($cardContent.content) && $cardContent.content}<div class="card-text">{$cardContent.content}</div>{/if}
                                        </div>
                                        {if isset($cardContent.button_text) && isset($cardContent.button_url) && $cardContent.button_text && $cardContent.button_url}
                                            <div class="card-footer bg-transparent border-0">
                                                <a href="{$cardContent.button_url}" class="btn btn-primary btn-sm">{$cardContent.button_text}</a>
                                            </div>
                                        {/if}
                                    {/if}
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
        {/if}
        
        {* Mittlere Spalte - immer anzeigen, passt sich an verfügbare Breite an *}
        <div class="{if count($leftSections) > 0 && count($rightSections) > 0}col-md-6{elseif count($leftSections) > 0 || count($rightSections) > 0}col-md-9{else}col-md-12{/if}">
            {* Dynamische Sektionen in der mittleren Spalte anzeigen *}
            {foreach from=$centerSections item=section}
                {* Sprachspezifische Inhalte abrufen *}
                {assign var="sectionContent" value=$section.contents[$currentLang]|default:null}
                
                <div class="section mb-4" style="background-color: {$section.background_color|default:'#ffffff'}; color: {$section.text_color|default:'#000000'};">
                    <div class="p-4">
                        {if $section.type == 'hero' && $sectionContent}
                            <div class="text-center">
                                {if isset($sectionContent.title)}<h1>{$sectionContent.title}</h1>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h3 class="mb-4">{$sectionContent.subtitle}</h3>{/if}
                                {if isset($sectionContent.content) && $sectionContent.content}<div>{$sectionContent.content}</div>{/if}
                            </div>
                        {elseif $section.type == 'text' && $sectionContent}
                            <div>
                                {if isset($sectionContent.title)}<h2 class="mb-4">{$sectionContent.title}</h2>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h4 class="mb-3">{$sectionContent.subtitle}</h4>{/if}
                                {if isset($sectionContent.content) && $sectionContent.content}<div>{$sectionContent.content}</div>{/if}
                            </div>
                        {elseif $section.type == 'cards' && isset($section.cards)}
                            <div class="text-center mb-4">
                                {if isset($sectionContent.title)}<h2 class="mb-3">{$sectionContent.title}</h2>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h4 class="mb-4">{$sectionContent.subtitle}</h4>{/if}
                            </div>
                            <div class="row">
                                {foreach from=$section.cards item=card}
                                    {* Sprachspezifische Inhalte für Karten abrufen *}
                                    {assign var="cardContent" value=$card.contents[$currentLang]|default:null}
                                    
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100" style="background-color: {$card.background_color|default:'#ffffff'}; color: {$card.text_color|default:'#000000'};">
                                            {if isset($card.icon) && $card.icon}
                                                <div class="card-img-top text-center pt-4">
                                                    <i class="fas fa-{$card.icon} fa-3x" style="color: {$card.icon_color|default:'inherit'};"></i>
                                                </div>
                                            {/if}
                                            {if $cardContent}
                                                <div class="card-body">
                                                    {if isset($cardContent.title)}<h5 class="card-title">{$cardContent.title}</h5>{/if}
                                                    {if isset($cardContent.content) && $cardContent.content}<div class="card-text">{$cardContent.content}</div>{/if}
                                                </div>
                                                {if isset($cardContent.button_text) && isset($cardContent.button_url) && $cardContent.button_text && $cardContent.button_url}
                                                    <div class="card-footer bg-transparent border-0">
                                                        <a href="{$cardContent.button_url}" class="btn btn-primary">{$cardContent.button_text}</a>
                                                    </div>
                                                {/if}
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
            
            {* Login-Formular in der mittleren Spalte *}
            <div class="row justify-content-center">
                <div class="{if count($leftSections) > 0 || count($rightSections) > 0}col-md-10{else}col-md-6{/if}">
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
        </div>
        
        {* Rechte Spalte - nur anzeigen, wenn Sektionen vorhanden sind *}
        {if count($rightSections) > 0}
        <div class="col-md-3">
            {foreach from=$rightSections item=section}
                {* Sprachspezifische Inhalte abrufen *}
                {assign var="sectionContent" value=$section.contents[$currentLang]|default:null}
                
                <div class="section mb-4" style="background-color: {$section.background_color|default:'#ffffff'}; color: {$section.text_color|default:'#000000'};">
                    <div class="p-3">
                        {if $section.type == 'hero' && $sectionContent}
                            <div class="text-center">
                                {if isset($sectionContent.title)}<h2>{$sectionContent.title}</h2>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h4 class="mb-3">{$sectionContent.subtitle}</h4>{/if}
                                {if isset($sectionContent.content) && $sectionContent.content}<div>{$sectionContent.content}</div>{/if}
                            </div>
                        {elseif $section.type == 'text' && $sectionContent}
                            <div>
                                {if isset($sectionContent.title)}<h3 class="mb-3">{$sectionContent.title}</h3>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h5 class="mb-2">{$sectionContent.subtitle}</h5>{/if}
                                {if isset($sectionContent.content) && $sectionContent.content}<div>{$sectionContent.content}</div>{/if}
                            </div>
                        {elseif $section.type == 'cards' && isset($section.cards)}
                            <div class="text-center mb-3">
                                {if isset($sectionContent.title)}<h3 class="mb-2">{$sectionContent.title}</h3>{/if}
                                {if isset($sectionContent.subtitle) && $sectionContent.subtitle}<h5 class="mb-3">{$sectionContent.subtitle}</h5>{/if}
                            </div>
                            {foreach from=$section.cards item=card}
                                {* Sprachspezifische Inhalte für Karten abrufen *}
                                {assign var="cardContent" value=$card.contents[$currentLang]|default:null}
                                
                                <div class="card mb-3" style="background-color: {$card.background_color|default:'#ffffff'}; color: {$card.text_color|default:'#000000'};">
                                    {if isset($card.icon) && $card.icon}
                                        <div class="card-img-top text-center pt-3">
                                            <i class="fas fa-{$card.icon} fa-2x" style="color: {$card.icon_color|default:'inherit'};"></i>
                                        </div>
                                    {/if}
                                    {if $cardContent}
                                        <div class="card-body">
                                            {if isset($cardContent.title)}<h5 class="card-title">{$cardContent.title}</h5>{/if}
                                            {if isset($cardContent.content) && $cardContent.content}<div class="card-text">{$cardContent.content}</div>{/if}
                                        </div>
                                        {if isset($cardContent.button_text) && isset($cardContent.button_url) && $cardContent.button_text && $cardContent.button_url}
                                            <div class="card-footer bg-transparent border-0">
                                                <a href="{$cardContent.button_url}" class="btn btn-primary btn-sm">{$cardContent.button_text}</a>
                                            </div>
                                        {/if}
                                    {/if}
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
        {/if}
    </div>
</div>

<script src="js/login.js"></script>

{include file="footer.tpl"}