{include file="admin/header.tpl" title={translate key="admin_dashboard"}}

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{translate key="admin_dashboard"}</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5>Willkommen im Administrationsbereich</h5>
                    <p>Hier können Sie Benutzer, Gruppen und Einstellungen verwalten.</p>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 text-primary"></i>
                                <h5 class="mt-3">{translate key="users"}</h5>
                                <p>Benutzer verwalten</p>
                                <a href="users.php" class="btn btn-sm btn-primary">Öffnen</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill fs-1 text-success"></i>
                                <h5 class="mt-3">{translate key="groups"}</h5>
                                <p>Benutzergruppen verwalten</p>
                                <a href="groups.php" class="btn btn-sm btn-success">Öffnen</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="bi bi-gear fs-1 text-secondary"></i>
                                <h5 class="mt-3">{translate key="settings"}</h5>
                                <p>Systemeinstellungen verwalten</p>
                                <a href="settings.php" class="btn btn-sm btn-secondary">Öffnen</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="admin/footer.tpl"}

