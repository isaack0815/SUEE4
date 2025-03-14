{include file="admin/header.tpl"}

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1>{translate key="index_customizer_title"}</h1>
            <p class="lead">{translate key="index_customizer_description"}</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{translate key="available_modules"}</h5>
                </div>
                <div class="card-body">
                    <p>{translate key="drag_modules_instruction"}</p>
                    <div id="availableModulesList" class="modules-list">
                        <!-- Verfügbare Module werden hier per JavaScript eingefügt -->
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{translate key="loading"}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button id="addNewSectionBtn" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> {translate key="add_new_section"}
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{translate key="active_modules"}</h5>
                </div>
                <div class="card-body">
                    <p>{translate key="active_modules_instruction"}</p>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="column-header">{translate key="left_column"}</div>
                            <div id="leftColumn" class="active-modules-column" data-column="left">
                                <!-- Aktive Module (links) werden hier per JavaScript eingefügt -->
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">{translate key="loading"}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="column-header">{translate key="center_column"}</div>
                            <div id="centerColumn" class="active-modules-column" data-column="center">
                                <!-- Aktive Module (mitte) werden hier per JavaScript eingefügt -->
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">{translate key="loading"}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="column-header">{translate key="right_column"}</div>
                            <div id="rightColumn" class="active-modules-column" data-column="right">
                                <!-- Aktive Module (rechts) werden hier per JavaScript eingefügt -->
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">{translate key="loading"}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bestehender Header-Code -->

<!-- Modal für die Bearbeitung von verfügbaren Modulen -->
<div class="modal fade" id="availableModuleEditModal" tabindex="-1" aria-labelledby="availableModuleEditModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="availableModuleEditModalLabel">{translate key="available_module_edit"}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{translate key="cancel"}"></button>
    </div>
    <div class="modal-body">
      <form id="availableModuleEditForm">
        <input type="hidden" id="available_module_id" name="moduleId">
        
        <!-- Tabs für Sprachen -->
        <ul class="nav nav-tabs mb-3" id="availableModuleLanguageTabs" role="tablist">
          <!-- Tabs werden dynamisch per JavaScript eingefügt -->
        </ul>
        
        <!-- Tab-Inhalte für Sprachen -->
        <div class="tab-content" id="availableModuleLanguageContent">
          <!-- Tab-Panels werden dynamisch per JavaScript eingefügt -->
        </div>
        
        <!-- Allgemeine Einstellungen -->
        <h6 class="mt-4 mb-3">{translate key="general_settings"}</h6>
        
        <!-- Container für dynamische Einstellungen -->
        <div id="available_module_settings_container"></div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
      <button type="button" class="btn btn-primary" id="saveAvailableModuleBtn">{translate key="save"}</button>
    </div>
  </div>
</div>
</div>

<!-- Modal für die Bearbeitung von aktiven Modulen -->
<div class="modal fade" id="moduleConfigModal" tabindex="-1" aria-labelledby="moduleConfigModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="moduleConfigModalLabel">{translate key="module_config"}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{translate key="cancel"}"></button>
    </div>
    <div class="modal-body">
      <form id="moduleConfigForm">
        <input type="hidden" id="module_id" name="moduleId">
        
        <!-- Tabs für Sprachen -->
        <ul class="nav nav-tabs mb-3" id="moduleLanguageTabs" role="tablist">
          <!-- Tabs werden dynamisch per JavaScript eingefügt -->
        </ul>
        
        <!-- Tab-Inhalte für Sprachen -->
        <div class="tab-content" id="moduleLanguageContent">
          <!-- Tab-Panels werden dynamisch per JavaScript eingefügt -->
        </div>
        
        <!-- Allgemeine Einstellungen -->
        <h6 class="mt-4 mb-3">{translate key="general_settings"}</h6>
        
        <div class="mb-3">
          <label for="module_background_color" class="form-label">{translate key="background_color"}</label>
          <input type="color" class="form-control" id="module_background_color" name="config[background_color]">
        </div>
        
        <div class="mb-3">
          <label for="module_text_color" class="form-label">{translate key="text_color"}</label>
          <input type="color" class="form-control" id="module_text_color" name="config[text_color]">
        </div>
        
        <!-- Container für dynamische Einstellungen -->
        <div id="module_settings_container"></div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
      <button type="button" class="btn btn-primary" id="saveModuleConfigBtn">{translate key="save"}</button>
    </div>
  </div>
</div>
</div>

<!-- Modal für neue Sektionen -->
<div class="modal fade" id="newSectionModal" tabindex="-1" aria-labelledby="newSectionModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="newSectionModalLabel">{translate key="new_section"}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{translate key="cancel"}"></button>
    </div>
    <div class="modal-body">
      <form id="newSectionForm">
        <div class="mb-3">
          <label for="section_type" class="form-label">{translate key="section_type"}</label>
          <select class="form-select" id="section_type" name="type">
            <option value="text">{translate key="section_type_text"}</option>
            <option value="hero">{translate key="section_type_hero"}</option>
            <option value="cards">{translate key="section_type_cards"}</option>
            <option value="gallery">{translate key="section_type_gallery"}</option>
            <option value="testimonial">{translate key="section_type_testimonial"}</option>
          </select>
        </div>
        
        <!-- Tabs für Sprachen -->
        <ul class="nav nav-tabs mb-3" id="newSectionLanguageTabs" role="tablist">
          <!-- Tabs werden dynamisch per JavaScript eingefügt -->
        </ul>
        
        <!-- Tab-Inhalte für Sprachen -->
        <div class="tab-content" id="newSectionLanguageContent">
          <!-- Tab-Panels werden dynamisch per JavaScript eingefügt -->
        </div>
        
        <!-- Allgemeine Einstellungen -->
        <h6 class="mt-4 mb-3">{translate key="general_settings"}</h6>
        
        <div class="mb-3">
          <label for="section_background_color" class="form-label">{translate key="background_color"}</label>
          <input type="color" class="form-control" id="section_background_color" name="background_color" value="#ffffff">
        </div>
        
        <div class="mb-3">
          <label for="section_text_color" class="form-label">{translate key="text_color"}</label>
          <input type="color" class="form-control" id="section_text_color" name="text_color" value="#000000">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{translate key="cancel"}</button>
      <button type="button" class="btn btn-primary" id="saveNewSectionBtn">{translate key="create"}</button>
    </div>
  </div>
</div>
</div>
<style>
    .available-modules {
        min-height: 200px;
    }
    
    .module-item {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: move;
        background-color: #f8f9fa;
    }
    
    .module-item:hover {
        background-color: #e9ecef;
    }
    
    .module-item .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .column-container {
        min-height: 200px;
        padding: 10px;
        border: 1px dashed #ddd;
        border-radius: 4px;
    }
    
    .active-module-item {
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #fff;
    }
    
    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .module-title {
        font-weight: bold;
    }
    
    .module-actions {
        display: flex;
        gap: 5px;
    }
    
    .module-content {
        font-size: 0.9em;
        color: #6c757d;
    }
</style>
<script src="../js/index_customizer.js"></script>

{include file="admin/footer.tpl"}