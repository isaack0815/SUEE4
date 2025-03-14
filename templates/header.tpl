<!DOCTYPE html>
<html lang="{$currentLang|default:'de'}">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="site_name" content="{$generalSettings.site_name}">
   <title>{translate key="site_title"}</title>
   
   {* Dynamische Metadaten *}
   {foreach $metadata as $key => $value}
   <meta name="{$key}" content="{$value}">
   {/foreach}
   
   <!-- Bootstrap 5 CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Bootstrap Icons -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
   <!-- SortableJS für Drag & Drop -->
   <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
   <!-- Custom CSS -->
   <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
            <div class="container">
                <a class="navbar-brand" href="index.php">{translate key="site_name"}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        {foreach from=$mainMenu item=item}
                            {if empty($item.children)}
                                <li class="nav-item">
                                    <a class="nav-link {if isset($activeMenuItem) && isset($activeMenuItem.item) && $activeMenuItem.item.id == $item.id}active{/if}" href="{$item.url}">
                                        {if $item.icon}<i class="bi bi-{$item.icon} me-1"></i>{/if}
                                        {$item.name}
                                    </a>
                                </li>
                            {else}
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle {if isset($activeMenuIds) && is_array($activeMenuIds) && in_array($item.id, $activeMenuIds)}active{/if}" 
                                       href="#" id="navbarDropdown{$item.id}" role="button" 
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        {if $item.icon}<i class="bi bi-{$item.icon} me-1"></i>{/if}
                                        {$item.name}
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown{$item.id}">
                                        {foreach from=$item.children item=child}
                                            <li>
                                                <a class="dropdown-item {if isset($activeMenuItem) && isset($activeMenuItem.item) && $activeMenuItem.item.id == $child.id}active{/if}" 
                                                   href="{$child.url}">
                                                    {if $child.icon}<i class="bi bi-{$child.icon} me-1"></i>{/if}
                                                    {$child.name}
                                                </a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                    <ul class="navbar-nav">
                        <!-- Sprachauswahl -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {translate key="language"}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                {foreach from=$availableLanguages item=lang}
                                    <li>
                                        <a class="dropdown-item {if $currentLang == $lang}active{/if}" href="?lang={$lang}">
                                            {translate key="lang_`$lang`"}
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                        
                        {if $isLoggedIn}
                            <!-- Benutzermenü -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {$currentUser.username}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    {foreach from=$userMenu item=item}
                                        <li>
                                            <a class="dropdown-item" href="{$item.url}">
                                                {if $item.icon}<i class="bi bi-{$item.icon} me-1"></i>{/if}
                                                {$item.name}
                                            </a>
                                        </li>
                                    {/foreach}
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="logout.php">
                                            <i class="bi bi-box-arrow-right me-1"></i>
                                            {translate key="logout"}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        {else}
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">{translate key="login"}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">{translate key="register"}</a>
                            </li>
                        {/if}
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <main>

