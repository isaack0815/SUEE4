<!DOCTYPE html>
<html lang="{$currentLang}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{translate key="site_title"}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">{translate key="site_name"}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    {if $isLoggedIn}
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">{translate key="dashboard"}</a>
                        </li>
                    {/if}
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
                                <li>
                                    <a class="dropdown-item" href="profile.php">
                                        {translate key="profile"}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
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
    
    <div class="container">

