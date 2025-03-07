/**
 * Dashboard-Grid initialisieren
 */
function initDashboardGrid() {
    console.log("Initialisiere Dashboard-Grid")
  
    // Module im Grid positionieren
    const grid = document.getElementById("dashboard-grid")
    if (!grid) {
      console.error("Dashboard-Grid nicht gefunden")
      return
    }
  
    const modules = grid.querySelectorAll(".dashboard-module")
    console.log(`${modules.length} Module gefunden`)
  
    modules.forEach((module) => {
      const gridX = Number.parseInt(module.dataset.gridX) || 0
      const gridY = Number.parseInt(module.dataset.gridY) || 0
      const gridWidth = Number.parseInt(module.dataset.gridWidth) || 6
      const gridHeight = Number.parseInt(module.dataset.gridHeight) || 2
  
      console.log(`Positioniere Modul ${module.id}: x=${gridX}, y=${gridY}, width=${gridWidth}, height=${gridHeight}`)
  
      // Grid-Position setzen
      module.style.gridColumn = gridX + 1 + " / span " + gridWidth
      module.style.gridRow = gridY + 1 + " / span " + gridHeight
  
      // Event-Listener für Modul-Aktionen
      setupModuleActions(module)
    })
  
    console.log("Dashboard-Grid initialisiert")
  }
  
  /**
   * Event-Listener für Modul-Aktionen einrichten
   */
  function setupModuleActions(module) {
    const toggleBtn = module.querySelector(".btn-module-toggle")
    const settingsBtn = module.querySelector(".btn-module-settings")
    const moduleContent = module.querySelector(".module-content")
  
    if (toggleBtn) {
      toggleBtn.addEventListener("click", () => {
        moduleContent.classList.toggle("collapsed")
  
        // Icon ändern
        const icon = toggleBtn.querySelector("i")
        if (icon) {
          if (moduleContent.classList.contains("collapsed")) {
            icon.classList.remove("fa-minus")
            icon.classList.add("fa-plus")
          } else {
            icon.classList.remove("fa-plus")
            icon.classList.add("fa-minus")
          }
        }
      })
    }
  
    if (settingsBtn) {
      settingsBtn.addEventListener("click", () => {
        const moduleId = module.dataset.moduleId
        // Hier könnte ein Modal mit Moduleinstellungen geöffnet werden
        alert("Einstellungen für Modul " + moduleId)
      })
    }
  }
  
  /**
   * Layout speichern
   */
  function saveLayout(grid) {
    console.log("Speichere Layout")
  
    // Alle Grid-Elemente abrufen
    var items = grid.getGridItems()
    var modules = {}
  
    // Daten für jedes Modul sammeln
    items.forEach((item) => {
      var el = item.el
      var moduleId = el.getAttribute("data-module-id")
  
      if (moduleId) {
        var node = item.gridstackNode
  
        modules[moduleId] = {
          position: Array.from(el.parentNode.children).indexOf(el),
          grid_x: node.x,
          grid_y: node.y,
          grid_width: node.width,
          grid_height: node.height,
          size: el.getAttribute("data-size") || "medium",
          is_visible: 1,
        }
      }
    })
  
    console.log("Module zum Speichern:", modules)
  
    // AJAX-Anfrage zum Speichern des Layouts
    fetch("/api/dashboard.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        action: "save_layout",
        modules: modules,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log("Layout gespeichert:", data)
        if (data.success) {
          // Optional: Erfolgsmeldung anzeigen
        } else {
          console.error("Fehler beim Speichern des Layouts:", data.message)
          // Optional: Fehlermeldung anzeigen
        }
      })
      .catch((error) => {
        console.error("Fehler beim Speichern des Layouts:", error)
        // Optional: Fehlermeldung anzeigen
      })
  }
  
  /**
   * CSS-Klassen für das Dashboard-Grid hinzufügen
   */
  function addDashboardStyles() {
    // Prüfen, ob die Styles bereits hinzugefügt wurden
    if (document.getElementById("dashboard-styles")) {
      return
    }
  
    // CSS für das Dashboard-Grid
    const styles = `
          .dashboard-grid {
              display: grid;
              grid-template-columns: repeat(12, 1fr);
              grid-gap: 15px;
              padding: 15px;
          }
          
          .dashboard-module {
              background-color: #fff;
              border-radius: 8px;
              box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
              overflow: hidden;
          }
          
          .module-header {
              display: flex;
              justify-content: space-between;
              align-items: center;
              padding: 10px 15px;
              background-color: #f8f9fa;
              border-bottom: 1px solid #e9ecef;
          }
          
          .module-header h3 {
              margin: 0;
              font-size: 16px;
              font-weight: 600;
          }
          
          .module-actions {
              display: flex;
              gap: 5px;
          }
          
          .module-actions button {
              background: none;
              border: none;
              cursor: pointer;
              font-size: 14px;
              color: #6c757d;
              padding: 2px 5px;
          }
          
          .module-actions button:hover {
              color: #343a40;
          }
          
          .module-content {
              padding: 15px;
              overflow: auto;
          }
          
          .module-content.collapsed {
              display: none;
          }
          
          /* Responsive Anpassungen */
          @media (max-width: 992px) {
              .dashboard-grid {
                  grid-template-columns: repeat(6, 1fr);
              }
          }
          
          @media (max-width: 576px) {
              .dashboard-grid {
                  grid-template-columns: repeat(1, 1fr);
              }
              
              .dashboard-module {
                  grid-column: 1 / -1 !important;
              }
          }
      `
  
    // Style-Element erstellen und einfügen
    const styleElement = document.createElement("style")
    styleElement.id = "dashboard-styles"
    styleElement.textContent = styles
    document.head.appendChild(styleElement)
  }
  
  // Dashboard-Grid initialisieren, wenn die Seite geladen ist
  document.addEventListener("DOMContentLoaded", () => {
    console.log("DOMContentLoaded-Event ausgelöst")
    addDashboardStyles()
    initDashboardGrid()
  })
  
  // Für Debugging-Zwecke
  console.log("dashboard.js geladen")
  
  