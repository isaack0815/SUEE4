document.addEventListener("DOMContentLoaded", () => {
  // Hilfsfunktion zum sicheren Zugriff auf DOM-Elemente
  function getElement(id) {
    const element = document.getElementById(id)
    if (!element) {
      console.warn(`Element mit ID '${id}' wurde nicht gefunden`)
      return null
    }
    return element
  }

  // Hilfsfunktion zum Hinzufügen von Event-Listenern mit Fehlerbehandlung
  function addSafeEventListener(elementId, eventType, handler) {
    const element = getElement(elementId)
    if (element) {
      element.addEventListener(eventType, async function (event) {
        try {
          await handler.call(this, event)
        } catch (error) {
          console.error("Fehler im Event-Handler:", error)
        }
      })
    }
  }

  // Hilfsfunktion für Übersetzungen
  function translate(key) {
    // Diese Funktion sollte eigentlich die Übersetzungen aus einer globalen Variable oder API abrufen
    // Hier verwenden wir eine vereinfachte Version mit festen Werten für die JavaScript-Seite
    const translations = {
      loading: "Wird geladen...",
      error_loading_modules: "Fehler beim Laden der Module",
      confirm_remove_module: "Sind Sie sicher, dass Sie dieses Modul entfernen möchten?",
      error_saving_changes: "Fehler beim Speichern der Änderungen",
      section_created_success: "Sektion erfolgreich erstellt!",
      module_title: "Titel",
      module_subtitle: "Untertitel",
      module_content: "Inhalt",
      module_settings: "Moduleinstellungen",
      additional_settings: "Zusätzliche Einstellungen"
    }
    return translations[key] || key
  }

  // Funktion zum Laden der verfügbaren Module
  function loadAvailableModules() {
    fetch("../admin/ajax/index_customizer_ajax.php?action=getAvailableModules")
      .then((response) => response.json())
      .then((data) => {
        if (!data || !data.modules) {
          console.error("Ungültiges Datenformat für verfügbare Module")
          return
        }

        const moduleList = getElement("availableModulesList")
        if (!moduleList) return

        moduleList.innerHTML = ""

        data.modules.forEach((module) => {
          const moduleItem = document.createElement("div")
          moduleItem.className = "module-item"
          moduleItem.setAttribute("data-module-id", module.id)
          moduleItem.setAttribute("draggable", "true")
          moduleItem.innerHTML = `
                      <div class="module-header">
                          <div class="module-title">${module.title}</div>
                          <div class="module-actions">
                              <button class="btn btn-sm btn-outline-secondary available-module-edit" data-module-id="${module.id}">
                                  <i class="bi bi-pencil"></i>
                              </button>
                          </div>
                      </div>
                      <div class="module-description">${module.description || ""}</div>
                  `

          moduleItem.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("text/plain", module.id)
          })

          // Event-Listener für Bearbeiten-Button
          const editButton = moduleItem.querySelector(".available-module-edit")
          if (editButton) {
            editButton.addEventListener("click", (e) => {
              e.stopPropagation() // Verhindert, dass das Drag-Event ausgelöst wird
              editAvailableModule(module.id)
            })
          }

          moduleList.appendChild(moduleItem)
        })
      })
      .catch((error) => {
        console.error(`${translate("error_loading_modules")}:`, error)
      })
  }

  // Funktion zum Laden der aktiven Module
  function loadActiveModules() {
    fetch("../admin/ajax/index_customizer_ajax.php?action=getActiveModules")
      .then((response) => response.json())
      .then((data) => {
        if (!data || !data.modules) {
          console.error("Ungültiges Datenformat für aktive Module")
          return
        }

        const leftColumn = getElement("leftColumn")
        const rightColumn = getElement("rightColumn")
        const centerColumn = getElement("centerColumn")

        if (leftColumn) leftColumn.innerHTML = ""
        if (rightColumn) rightColumn.innerHTML = ""
        if (centerColumn) centerColumn.innerHTML = ""

        data.modules.forEach((module) => {
          const moduleItem = document.createElement("div")
          moduleItem.className = "active-module-item"
          moduleItem.setAttribute("data-module-id", module.id)
          moduleItem.setAttribute("data-position", module.position)
          moduleItem.setAttribute("data-position-vertical", module.position_vertical) // Neue Eigenschaft
          moduleItem.setAttribute("draggable", "true")
          
          // Hintergrundfarbe und Textfarbe anwenden
          if (module.background_color) {
            moduleItem.style.backgroundColor = module.background_color
          }
          if (module.text_color) {
            moduleItem.style.color = module.text_color
          }
          
          moduleItem.innerHTML = `
                      <div class="module-header">
                          <div class="module-title">${module.title}</div>
                          <div class="module-actions">
                              <button class="btn btn-sm btn-outline-secondary module-edit" data-module-id="${module.id}">
                                  <i class="bi bi-pencil"></i>
                              </button>
                              <button class="btn btn-sm btn-outline-danger module-remove" data-module-id="${module.id}">
                                  <i class="bi bi-trash"></i>
                              </button>
                          </div>
                      </div>
                      <div class="module-content">${module.content || ""}</div>
                  `

          moduleItem.addEventListener("dragstart", (e) => {
            e.dataTransfer.setData("text/plain", module.id)
            e.dataTransfer.setData(
              "application/json",
              JSON.stringify({
                id: module.id,
                position: module.position,
                position_vertical: module.position_vertical // Neue Eigenschaft
              }),
            )
          })

          // Event-Listener für Bearbeiten-Button
          const editButton = moduleItem.querySelector(".module-edit")
          if (editButton) {
            editButton.addEventListener("click", () => {
              editModule(module.id)
            })
          }

          // Event-Listener für Entfernen-Button
          const removeButton = moduleItem.querySelector(".module-remove")
          if (removeButton) {
            removeButton.addEventListener("click", () => {
              removeModule(module.id)
            })
          }

          // Modul zur entsprechenden Spalte basierend auf position_vertical hinzufügen
          if (module.position_vertical === 0 && leftColumn) {
            leftColumn.appendChild(moduleItem)
          } else if (module.position_vertical === 2 && rightColumn) {
            rightColumn.appendChild(moduleItem)
          } else if (module.position_vertical === 1 && centerColumn) {
            centerColumn.appendChild(moduleItem)
          }
        })
      })
      .catch((error) => {
        console.error(`${translate("error_loading_modules")}:`, error)
      })
  }

  // Funktion zum Laden der verfügbaren Sprachen
  function loadAvailableLanguages() {
    return fetch("../admin/ajax/index_customizer_ajax.php?action=getAvailableLanguages")
      .then((response) => response.json())
      .then((data) => {
        if (!data || !data.languages) {
          console.error("Ungültiges Datenformat für verfügbare Sprachen")
          return []
        }
        return data.languages
      })
      .catch((error) => {
        console.error("Fehler beim Laden der verfügbaren Sprachen:", error)
        return []
      })
  }

  // Funktion zum Erstellen von Sprach-Tabs
  function createLanguageTabs(tabsContainerId, contentContainerId, languages, activeLanguage = 'de') {
    const tabsContainer = getElement(tabsContainerId)
    const contentContainer = getElement(contentContainerId)
    
    if (!tabsContainer || !contentContainer) return
    
    tabsContainer.innerHTML = ''
    contentContainer.innerHTML = ''
    
    languages.forEach((lang, index) => {
      const isActive = lang.code === activeLanguage
      
      // Tab erstellen
      const tabItem = document.createElement('li')
      tabItem.className = 'nav-item'
      tabItem.role = 'presentation'
      
      const tabButton = document.createElement('button')
      tabButton.className = `nav-link ${isActive ? 'active' : ''}`
      tabButton.id = `${tabsContainerId}-${lang.code}`
      tabButton.setAttribute('data-bs-toggle', 'tab')
      tabButton.setAttribute('data-bs-target', `#${contentContainerId}-${lang.code}`)
      tabButton.type = 'button'
      tabButton.role = 'tab'
      tabButton.setAttribute('aria-controls', `${contentContainerId}-${lang.code}`)
      tabButton.setAttribute('aria-selected', isActive ? 'true' : 'false')
      tabButton.textContent = lang.name
      
      tabItem.appendChild(tabButton)
      tabsContainer.appendChild(tabItem)
      
      // Tab-Inhalt erstellen
      const tabContent = document.createElement('div')
      tabContent.className = `tab-pane fade ${isActive ? 'show active' : ''}`
      tabContent.id = `${contentContainerId}-${lang.code}`
      tabContent.role = 'tabpanel'
      tabContent.setAttribute('aria-labelledby', `${tabsContainerId}-${lang.code}`)
      
      contentContainer.appendChild(tabContent)
    })
  }

  // Funktion zum Erstellen von Formularfeldern für eine Sprache
  function createLanguageFields(containerId, langCode, data = {}) {
    const container = document.getElementById(`${containerId}-${langCode}`)
    if (!container) return
    
    container.innerHTML = `
      <input type="hidden" name="contents[${langCode}][lang_code]" value="${langCode}">
      
      <div class="mb-3">
        <label for="${containerId}_title_${langCode}" class="form-label">${translate('module_title')} (${langCode})</label>
        <input type="text" class="form-control" id="${containerId}_title_${langCode}" name="contents[${langCode}][title]" value="${data.title || ''}">
      </div>
      
      <div class="mb-3">
        <label for="${containerId}_subtitle_${langCode}" class="form-label">${translate('module_subtitle')} (${langCode})</label>
        <input type="text" class="form-control" id="${containerId}_subtitle_${langCode}" name="contents[${langCode}][subtitle]" value="${data.subtitle || ''}">
      </div>
      
      <div class="mb-3">
        <label for="${containerId}_content_${langCode}" class="form-label">${translate('module_content')} (${langCode})</label>
        <textarea class="form-control" id="${containerId}_content_${langCode}" name="contents[${langCode}][content]" rows="5">${data.content || ''}</textarea>
      </div>
    `
  }

  // Funktion zum Bearbeiten eines verfügbaren Moduls - angepasst für Mehrsprachigkeit
  function editAvailableModule(moduleId) {
    Promise.all([
      fetch(`../admin/ajax/index_customizer_ajax.php?action=getAvailableModuleDetails&moduleId=${moduleId}`).then(response => response.json()),
      loadAvailableLanguages()
    ])
      .then(([moduleData, languages]) => {
        if (!moduleData || !moduleData.module) {
          console.error("Ungültiges Datenformat für verfügbares Modul")
          return
        }

        // Modul-ID setzen
        const moduleIdInput = getElement("available_module_id")
        if (moduleIdInput) moduleIdInput.value = moduleId

        // Sprach-Tabs erstellen
        createLanguageTabs(
          "availableModuleLanguageTabs", 
          "availableModuleLanguageContent", 
          languages, 
          languages.length > 0 ? languages[0].code : 'de'
        )

        // Sprachspezifische Felder für jede Sprache erstellen
        languages.forEach(lang => {
          const langContent = moduleData.module.contents && moduleData.module.contents[lang.code] 
            ? moduleData.module.contents[lang.code] 
            : {}
          
          createLanguageFields("availableModuleLanguageContent", lang.code, langContent)
        })

        // Dynamische Einstellungen befüllen
        const settingsContainer = getElement("available_module_settings_container")
        if (settingsContainer) {
          settingsContainer.innerHTML = ""

          if (moduleData.module.settings && Object.keys(moduleData.module.settings).length > 0) {
            const settingsTitle = document.createElement("h6")
            settingsTitle.className = "mt-4 mb-3"
            settingsTitle.textContent = translate("module_settings")
            settingsContainer.appendChild(settingsTitle)

            Object.entries(moduleData.module.settings).forEach(([key, value]) => {
              const formGroup = document.createElement("div")
              formGroup.className = "mb-3"

              const label = document.createElement("label")
              label.htmlFor = `setting_${key}`
              label.className = "form-label"
              label.textContent = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, " ")

              const input = document.createElement("input")
              input.type = "text"
              input.className = "form-control"
              input.id = `setting_${key}`
              input.name = `settings[${key}]`
              input.value = value || ""

              formGroup.appendChild(label)
              formGroup.appendChild(input)
              settingsContainer.appendChild(formGroup)
            })
          }
        }

        // Modal anzeigen
        const availableModuleEditModalElement = getElement("availableModuleEditModal")
        if (availableModuleEditModalElement) {
          const availableModuleEditModal = new bootstrap.Modal(availableModuleEditModalElement)
          availableModuleEditModal.show()
        }
      })
      .catch((error) => {
        console.error("Fehler beim Laden der Moduldetails:", error)
      })
  }

  // Funktion zum Speichern der Änderungen an einem verfügbaren Modul
  function saveAvailableModuleChanges(event) {
    event.preventDefault()

    const form = getElement("availableModuleEditForm")
    if (!form) return

    const formData = new FormData(form)
    formData.append("action", "saveAvailableModuleChanges")

    fetch("../admin/ajax/index_customizer_ajax.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Modal schließen
          const availableModuleEditModalElement = getElement("availableModuleEditModal")
          if (availableModuleEditModalElement) {
            // Ensure bootstrap is available
            const availableModuleEditModal = bootstrap.Modal.getInstance(availableModuleEditModalElement)
            if (availableModuleEditModal) {
              availableModuleEditModal.hide()
            }
          }

          // Module neu laden
          loadAvailableModules()
        } else {
          alert(`${translate("error_saving_changes")}: ${data.message || "Unbekannter Fehler"}`)
        }
      })
      .catch((error) => {
        console.error(`${translate("error_saving_changes")}:`, error)
      })
  }

  // Funktion zum Bearbeiten eines Moduls - angepasst für Mehrsprachigkeit
  function editModule(moduleId) {
    Promise.all([
      fetch(`../admin/ajax/index_customizer_ajax.php?action=getModuleConfig&moduleId=${moduleId}`).then(response => response.json()),
      loadAvailableLanguages()
    ])
      .then(([moduleData, languages]) => {
        if (!moduleData || !moduleData.config) {
          console.error("Ungültiges Datenformat für Modulkonfiguration")
          return
        }

        console.log("Geladene Modulkonfiguration:", moduleData.config); // Debug-Ausgabe

        // Modul-ID setzen
        const moduleIdInput = getElement("module_id")
        if (moduleIdInput) moduleIdInput.value = moduleId

        // Allgemeine Einstellungen setzen
        const backgroundColorInput = getElement("module_background_color")
        const textColorInput = getElement("module_text_color")

        if (backgroundColorInput) backgroundColorInput.value = moduleData.config.background_color || "#ffffff"
        if (textColorInput) textColorInput.value = moduleData.config.text_color || "#000000"

        // Sprach-Tabs erstellen
        createLanguageTabs(
          "moduleLanguageTabs", 
          "moduleLanguageContent", 
          languages, 
          languages.length > 0 ? languages[0].code : 'de'
        )

        // Sprachspezifische Felder für jede Sprache erstellen
        languages.forEach(lang => {
          console.log(`Verarbeite Sprache: ${lang.code}`); // Debug-Ausgabe
          console.log(`Verfügbare Inhalte:`, moduleData.config.contents); // Debug-Ausgabe
          
          const langContent = moduleData.config.contents && moduleData.config.contents[lang.code] 
            ? moduleData.config.contents[lang.code] 
            : {}
          
          console.log(`Inhalte für ${lang.code}:`, langContent); // Debug-Ausgabe
          
          createLanguageFields("moduleLanguageContent", lang.code, langContent)
        })

        // Dynamische Einstellungen befüllen
        const settingsContainer = getElement("module_settings_container")
        if (settingsContainer) {
          settingsContainer.innerHTML = ""

          // Filtere die Standardfelder heraus, um nur die zusätzlichen Einstellungen zu erhalten
          const standardFields = ["title", "content", "subtitle", "background_color", "text_color", "type", "contents"]
          const additionalSettings = Object.entries(moduleData.config).filter(
            ([key]) => !standardFields.includes(key)
          )

          if (additionalSettings.length > 0) {
            const settingsTitle = document.createElement("h6")
            settingsTitle.className = "mt-4 mb-3"
            settingsTitle.textContent = translate("additional_settings")
            settingsContainer.appendChild(settingsTitle)

            additionalSettings.forEach(([key, value]) => {
              const formGroup = document.createElement("div")
              formGroup.className = "mb-3"

              const label = document.createElement("label")
              label.htmlFor = `config_${key}`
              label.className = "form-label"
              label.textContent = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, " ")

              const input = document.createElement("input")
              input.type = "text"
              input.className = "form-control"
              input.id = `config_${key}`
              input.name = `config[${key}]`
              input.value = value || ""

              formGroup.appendChild(label)
              formGroup.appendChild(input)
              settingsContainer.appendChild(formGroup)
            })
          }
        }

        // Modal anzeigen
        const moduleConfigModalElement = getElement("moduleConfigModal")
        if (moduleConfigModalElement) {
          const moduleConfigModal = new bootstrap.Modal(moduleConfigModalElement)
          moduleConfigModal.show()
        }
      })
      .catch((error) => {
        console.error("Fehler beim Laden der Modulkonfiguration:", error)
      })
  }

  // Funktion zum Entfernen eines Moduls
  function removeModule(moduleId) {
    if (confirm(translate("confirm_remove_module"))) {
      fetch("../admin/ajax/index_customizer_ajax.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `action=removeModule&moduleId=${moduleId}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            loadActiveModules()
          } else {
            alert(`${translate("error_saving_changes")}: ${data.message || "Unbekannter Fehler"}`)
          }
        })
        .catch((error) => {
          console.error(`${translate("error_saving_changes")}:`, error)
        })
    }
  }

  // Funktion zum Speichern der Modulkonfiguration
  function saveModuleConfig(event) {
    event.preventDefault()

    const form = getElement("moduleConfigForm")
    if (!form) return

    const formData = new FormData(form)
    formData.append("action", "saveModuleConfig")

    // Debug-Ausgabe der FormData
    console.log("FormData Einträge:");
    for (let [key, value] of formData.entries()) {
      console.log(`${key}: ${value}`);
    }

    fetch("../admin/ajax/index_customizer_ajax.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Modal schließen
          const moduleConfigModalElement = getElement("moduleConfigModal")
          if (moduleConfigModalElement) {
            // Ensure bootstrap is available
            const moduleConfigModal = bootstrap.Modal.getInstance(moduleConfigModalElement)
            if (moduleConfigModal) {
              moduleConfigModal.hide()
            }
          }

          // Module neu laden
          loadActiveModules()
        } else {
          alert(`${translate("error_saving_changes")}: ${data.message || "Unbekannter Fehler"}`)
        }
      })
      .catch((error) => {
        console.error(`${translate("error_saving_changes")}:`, error)
      })
  }

  // Funktion zum Hinzufügen eines Moduls
  function addModule(moduleId, column) {
    // Konvertiere Spaltenname in position_vertical Wert
    let positionVertical = 1; // Standard: Mitte
    if (column === "left") positionVertical = 0;
    else if (column === "right") positionVertical = 2;

    fetch("../admin/ajax/index_customizer_ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=addModule&moduleId=${moduleId}&column=${column}&position_vertical=${positionVertical}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          loadActiveModules()
        } else {
          alert(`${translate("error_saving_changes")}: ${data.message || "Unbekannter Fehler"}`)
        }
      })
      .catch((error) => {
        console.error(`${translate("error_saving_changes")}:`, error)
      })
  }

  // Funktion zum Aktualisieren der Modulposition
  function updateModulePosition(moduleId, fromPositionVertical, toPositionVertical, position) {
    fetch("../admin/ajax/index_customizer_ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=updateModulePosition&moduleId=${moduleId}&fromPositionVertical=${fromPositionVertical}&toPositionVertical=${toPositionVertical}&position=${position}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          loadActiveModules()
        } else {
          alert(`${translate("error_saving_changes")}: ${data.message || "Unbekannter Fehler"}`)
        }
      })
      .catch((error) => {
        console.error(`${translate("error_saving_changes")}:`, error)
      })
  }

  // Funktion zum Öffnen des Modals für neue Sektionen - angepasst für Mehrsprachigkeit
  function openNewSectionModal() {
    loadAvailableLanguages()
      .then(languages => {
        // Sprach-Tabs erstellen
        createLanguageTabs(
          "newSectionLanguageTabs", 
          "newSectionLanguageContent", 
          languages, 
          languages.length > 0 ? languages[0].code : 'de'
        )

        // Sprachspezifische Felder für jede Sprache erstellen
        languages.forEach(lang => {
          createLanguageFields("newSectionLanguageContent", lang.code, {})
        })

        // Modal anzeigen
        const newSectionModalElement = getElement("newSectionModal")
        if (newSectionModalElement) {
          const newSectionModal = new bootstrap.Modal(newSectionModalElement)
          newSectionModal.show()
        }
      })
      .catch(error => {
        console.error("Fehler beim Laden der Sprachen:", error)
      })
  }

  // Funktion zum Erstellen einer neuen Sektion - angepasst für Mehrsprachigkeit
  function createNewSection(event) {
    event.preventDefault()

    const form = getElement("newSectionForm")
    if (!form) return

    const formData = new FormData(form)
    const sectionType = formData.get("type")
    const backgroundColor = formData.get("background_color")
    const textColor = formData.get("text_color")

    // Sprachspezifische Inhalte sammeln
    const contents = {}
    
    // Alle Formularfelder durchlaufen
    for (const [key, value] of formData.entries()) {
      // Prüfen, ob es sich um ein sprachspezifisches Feld handelt
      if (key.startsWith('contents[')) {
        const matches = key.match(/contents\[([a-z]{2})\]\[([a-z_]+)\]/)
        if (matches && matches.length === 3) {
          const langCode = matches[1]
          const fieldName = matches[2]
          
          // Sprachspezifischen Inhalt initialisieren, falls noch nicht vorhanden
          if (!contents[langCode]) {
            contents[langCode] = {}
          }
          
          // Feldwert zum sprachspezifischen Inhalt hinzufügen
          contents[langCode][fieldName] = value
        }
      }
    }

    // Daten für die API vorbereiten
    const data = {
      action: "createSection",
      section: {
        type: sectionType,
        active: 1,
        background_color: backgroundColor,
        text_color: textColor
      },
      contents: contents
    }

    // API-Anfrage senden
    fetch("../admin/ajax/index_customizer_ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Modal schließen
          const newSectionModalElement = getElement("newSectionModal")
          if (newSectionModalElement) {
            const newSectionModal = bootstrap.Modal.getInstance(newSectionModalElement)
            if (newSectionModal) {
              newSectionModal.hide()
            }
          }

          // Formular zurücksetzen
          form.reset()

          // Module neu laden
          loadAvailableModules()

          // Erfolgsmeldung anzeigen
          alert(translate("section_created_success"))
        } else {
          alert(`${translate("error_saving_changes")}: ${data.message || "Unbekannter Fehler"}`)
        }
      })
      .catch((error) => {
        console.error(`${translate("error_saving_changes")}:`, error)
        alert(`${translate("error_saving_changes")}: ${error.message}`)
      })
  }

  // Event-Listener für Drag-and-Drop
  function setupDragAndDrop() {
    const columns = ["leftColumn", "centerColumn", "rightColumn"]
    const positionVerticalMap = {
      "leftColumn": 0,
      "centerColumn": 1,
      "rightColumn": 2
    }

    columns.forEach((columnId) => {
      const column = getElement(columnId)
      if (!column) return

      column.addEventListener("dragover", (e) => {
        e.preventDefault()
        e.dataTransfer.dropEffect = "move"
      })

      column.addEventListener("drop", (e) => {
        e.preventDefault()

        const moduleId = e.dataTransfer.getData("text/plain")
        if (!moduleId) return

        const toPositionVertical = positionVerticalMap[columnId]

        // Prüfen, ob es sich um ein vorhandenes Modul handelt
        try {
          const moduleData = JSON.parse(e.dataTransfer.getData("application/json"))
          if (moduleData && moduleData.id) {
            // Vorhandenes Modul verschieben
            const fromPositionVertical = moduleData.position_vertical !== undefined ? moduleData.position_vertical : 1 // Standardwert: Mitte
            updateModulePosition(moduleId, fromPositionVertical, toPositionVertical, 0)
          }
        } catch (error) {
          // Neues Modul hinzufügen
          const columnName = columnId.replace("Column", "")
          addModule(moduleId, columnName)
        }
      })
    })
  }

  // Initialisierung
  function init() {
    loadAvailableModules()
    loadActiveModules()
    setupDragAndDrop()
    
    // Event-Listener für Speichern-Button im Modal für aktive Module
    addSafeEventListener("saveModuleConfigBtn", "click", (e) => {
      const form = getElement("moduleConfigForm")
      if (form) {
        saveModuleConfig(e)
      }
    })
    
    // Event-Listener für Speichern-Button im Modal für verfügbare Module
    addSafeEventListener("saveAvailableModuleBtn", "click", (e) => {
      const form = getElement("availableModuleEditForm")
      if (form) {
        saveAvailableModuleChanges(e)
      }
    })
    
    // Event-Listener für den Button zum Hinzufügen neuer Sektionen
    addSafeEventListener("addNewSectionBtn", "click", openNewSectionModal)
    
    // Event-Listener für den Speichern-Button im Modal für neue Sektionen
    addSafeEventListener("saveNewSectionBtn", "click", createNewSection)
  }

  // Initialisierung aufrufen
  init()
})