<!DOCTYPE html>
<html lang="{$currentLang}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title|default:{translate key="admin_area"}} - {translate key="site_name"}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/admin.css" rel="stylesheet">
    <!-- SortableJS für Drag & Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">{translate key="admin_area"}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">{translate key="back_to_site"}</a>
                    </li>
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
                    
                    <!-- Benutzermenü -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {$currentUser.username}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="../profile.php">
                                    {translate key="profile"}
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="../logout.php">
                                    {translate key="logout"}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        {foreach from=$adminMenu item=item}
                            <li class="nav-item">
                                <a class="nav-link {if isset($activeMenu) && $activeMenu == $item.id}active{/if}" href="{$item.url}">
                                    {if $item.icon}<i class="bi bi-{$item.icon} me-1"></i>{/if}
                                    {$item.name}
                                </a>
                                
                                {if !empty($item.children)}
                                    <ul class="nav flex-column ms-3">
                                        {foreach from=$item.children item=child}
                                            <li class="nav-item">
                                                <a class="nav-link {if isset($activeMenu) && $activeMenu == $child.id}active{/if}" href="{$child.url}">
                                                    {if $child.icon}<i class="bi bi-{$child.icon} me-1"></i>{/if}
                                                    {$child.name}
                                                </a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
            
            <!-- Hauptinhalt -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

