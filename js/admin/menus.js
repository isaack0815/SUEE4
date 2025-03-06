document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const areaSelect = document.getElementById("areaSelect")
  const menuTableBody = document.getElementById("menuTableBody")
  const addMenuBtn = document.getElementById("addMenuBtn")
  const saveMenuBtn = document.getElementById("saveMenuBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const menuForm = document.getElementById("menuForm")
  const menuArea = document.getElementById("menuArea")
  const menuParent = document.getElementById("menuParent")

  // Bootstrap-Modals
  const menuModal = new bootstrap.Modal(document.getElementById("menuModal"))
  const deleteMenuModal = new bootstrap.Modal(document.getElementById("deleteMenuModal"))

  // Sortable-Instanzen für Drag & Drop
  const sortableGroups = {}

  // Menüpunkte laden
  loadMenuItems()

  // Event-Listener
  areaSelect.addEventListener("change", loadMenuItems)
  addMenuBtn.addEventListener("click", () => {
    resetMenuForm()
    document.getElementById("menuModalLabel").textContent = "Menüpunkt hinzufügen"
    menuModal.show()
  })
  saveMenuBtn.addEventListener("click", saveMenuItem)
  confirmDeleteBtn.addEventListener("click", deleteMenuItem)
  menuArea.addEventListener("change", loadParentOptions)

  // Funktionen
  async function loadMenuItems() {
    const area = areaSelect.value
    try {
      const response = await fetch(`../admin/api/menus.php?area=${area}`)
      const data = await response.json()

      if (data.success) {
        renderMenuItems(data.data)
        initSortable()
      } else {
        showAlert("Fehler beim Laden der Menüpunkte: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Menüpunkte", "danger")
    }
  }

  function renderMenuItems(menuItems) {
    // Menüpunkte nach Hierarchie sortieren
    const hierarchicalItems = buildHierarchy(menuItems)

    // Tabelle leeren
    menuTableBody.innerHTML = ""

    if (hierarchicalItems.length === 0) {
      menuTableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center">Keine Menüpunkte gefunden</td>
        </tr>
      `
      return
    }

    // Menüpunkte rekursiv rendern
    renderMenuItemsRecursive(hierarchicalItems, 0)
  }

  function buildHierarchy(items) {
    // Elemente nach ID indizieren
    const itemsById = {}
    items.forEach((item) => {
      itemsById[item.id] = { ...item, children: [] }
    })

    // Hierarchie aufbauen
    const rootItems = []
    items.forEach((item) => {
      if (item.parent_id) {
        if (itemsById[item.parent_id]) {
          itemsById[item.parent_id].children.push(itemsById[item.id])
        } else {
          rootItems.push(itemsById[item.id])
        }
      } else {
        rootItems.push(itemsById[item.id])
      }
    })

    // Nach sort_order sortieren
    const sortByOrder = (a, b) => a.sort_order - b.sort_order
    rootItems.sort(sortByOrder)
    items.forEach((item) => {
      if (itemsById[item.id].children.length > 0) {
        itemsById[item.id].children.sort(sortByOrder)
      }
    })

    return rootItems
  }

  function renderMenuItemsRecursive(items, level) {
    items.forEach((item) => {
      const tr = document.createElement("tr")
      tr.dataset.id = item.id
      tr.dataset.parentId = item.parent_id || "root"

      // Einrückung für Untermenüs
      const indent = level > 0 ? `<span style="padding-left: ${level * 20}px;"></span>` : ""

      // Icon für Untermenüs
      const hasChildren = item.children && item.children.length > 0
      const toggleIcon = hasChildren
        ? '<i class="bi bi-caret-down-fill toggle-icon"></i>'
        : '<i class="bi bi-dash"></i>'

      tr.innerHTML = `
        <td class="drag-handle"><i class="bi bi-grip-vertical"></i></td>
        <td>
          ${indent}
          <span class="menu-toggle ${hasChildren ? "has-children" : ""}" data-id="${item.id}">
            ${toggleIcon}
          </span>
          ${item.name}
        </td>
        <td>${item.url}</td>
        <td>${item.sort_order}</td>
        <td>${item.is_active == 1 ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-danger">Nein</span>'}</td>
        <td>
          <button type="button" class="btn btn-sm btn-primary edit-menu" data-id="${item.id}">
            <i class="bi bi-pencil"></i>
          </button>
          <button type="button" class="btn btn-sm btn-danger delete-menu" data-id="${item.id}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `
      menuTableBody.appendChild(tr)

      // Untermenüs rekursiv rendern
      if (hasChildren) {
        renderMenuItemsRecursive(item.children, level + 1)
      }
    })
  }

  function initSortable() {
    // Wenn bereits eine Sortable-Instanz existiert, diese zerstören
    if (window.menuSortable) {
      window.menuSortable.destroy()
    }

    // Neue Sortable-Instanz erstellen
    window.menuSortable = new Sortable(menuTableBody, {
      handle: ".drag-handle",
      animation: 150,
      ghostClass: "bg-light",
      onEnd: (evt) => {
        console.log("Sortable onEnd triggered")
        updateMenuOrder()
      },
    })
  }

  async function updateMenuOrder() {
    console.log("updateMenuOrder called")
    const rows = menuTableBody.querySelectorAll("tr[data-id]")
    const items = []

    // Aktuelle Hierarchie bestimmen
    const hierarchy = {}
    const currentParentId = null
    const currentLevel = 0

    rows.forEach((row, index) => {
      const id = row.dataset.id
      const indent = row.querySelector("td:nth-child(2) span[style]")

      // Level aus Einrückung bestimmen
      let level = 0
      if (indent) {
        const paddingLeft = indent.style.paddingLeft
        if (paddingLeft) {
          level = Number.parseInt(paddingLeft) / 20
        }
      }

      // Übergeordnetes Element bestimmen
      let parentId = null
      if (level > 0) {
        // Suche das nächsthöhere Element mit niedrigerem Level
        for (let i = index - 1; i >= 0; i--) {
          const prevRow = rows[i]
          const prevIndent = prevRow.querySelector("td:nth-child(2) span[style]")
          let prevLevel = 0
          if (prevIndent) {
            const prevPaddingLeft = prevIndent.style.paddingLeft
            if (prevPaddingLeft) {
              prevLevel = Number.parseInt(prevPaddingLeft) / 20
            }
          }

          if (prevLevel < level) {
            parentId = prevRow.dataset.id
            break
          }
        }
      }

      items.push({
        id: id,
        parent_id: parentId,
        sort_order: index + 1,
      })
    })

    console.log("Items to update:", items)

    try {
      const response = await fetch("../admin/api/menus.php", {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "update_order",
          items: items,
        }),
      })

      const data = await response.json()

      if (data.success) {
        showAlert("Menüreihenfolge erfolgreich aktualisiert.")
        // Neu laden, um die aktualisierten Sortierungsnummern anzuzeigen
        loadMenuItems()
      } else {
        showAlert("Fehler beim Aktualisieren der Menüreihenfolge: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Aktualisieren der Menüreihenfolge", "danger")
    }
  }

  async function loadParentOptions() {
    const area = menuArea.value
    try {
      const response = await fetch(`../admin/api/menus.php?parent_options=1&area=${area}`)
      const data = await response.json()

      if (data.success) {
        const parentItems = data.data
        menuParent.innerHTML = `<option value="">{translate key="no_parent"}</option>`

        parentItems.forEach((item) => {
          menuParent.innerHTML += `<option value="${item.id}">${item.name}</option>`
        })
      }
    } catch (error) {
      console.error("Error:", error)
    }
  }

  async function editMenuItem(id) {
    try {
      const response = await fetch(`../admin/api/menus.php?id=${id}`)
      const data = await response.json()

      if (data.success) {
        const item = data.data
        resetMenuForm()

        document.getElementById("menuId").value = item.id
        document.getElementById("menuArea").value = item.area
        document.getElementById("menuName").value = item.name
        document.getElementById("menuUrl").value = item.url
        document.getElementById("menuIcon").value = item.icon || ""
        document.getElementById("menuOrder").value = item.sort_order
        document.getElementById("menuDescription").value = item.description || ""
        document.getElementById("menuModule").value = item.module || ""
        document.getElementById("menuActive").checked = item.is_active == 1

        // Übergeordnete Menüpunkte laden
        await loadParentOptions()
        document.getElementById("menuParent").value = item.parent_id || ""

        // Gruppe und Berechtigung setzen
        document.getElementById("menuGroup").value = item.required_group_id || ""
        document.getElementById("menuPermission").value = item.required_permission_id || ""

        document.getElementById("menuModalLabel").textContent = "Menüpunkt bearbeiten"
        menuModal.show()
      } else {
        showAlert("Fehler beim Laden des Menüpunkts: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden des Menüpunkts", "danger")
    }
  }

  async function saveMenuItem() {
    // Formularvalidierung
    if (!menuForm.checkValidity()) {
      menuForm.classList.add("was-validated")
      return
    }

    // Formulardaten sammeln
    const formData = new FormData(menuForm)
    const menuData = {}

    for (const [key, value] of formData.entries()) {
      if (key === "is_active") {
        menuData[key] = 1
      } else {
        menuData[key] = value
      }
    }

    // Wenn is_active nicht im FormData ist (Checkbox nicht angehakt)
    if (!formData.has("is_active")) {
      menuData.is_active = 0
    }

    try {
      let response
      const menuId = document.getElementById("menuId").value

      if (menuId) {
        // Menüpunkt aktualisieren
        menuData.id = menuId
        response = await fetch("../admin/api/menus.php", {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(menuData),
        })
      } else {
        // Neuen Menüpunkt erstellen
        response = await fetch("../admin/api/menus.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(menuData),
        })
      }

      const data = await response.json()

      if (data.success) {
        menuModal.hide()
        showAlert(menuId ? "Menüpunkt erfolgreich aktualisiert." : "Menüpunkt erfolgreich hinzugefügt.")
        loadMenuItems()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern des Menüpunkts", "danger")
    }
  }

  async function deleteMenuItem() {
    const menuId = document.getElementById("deleteMenuId").value

    if (!menuId) {
      return
    }

    try {
      const response = await fetch("../admin/api/menus.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: menuId }),
      })

      const data = await response.json()

      if (data.success) {
        deleteMenuModal.hide()
        showAlert("Menüpunkt erfolgreich gelöscht.")
        loadMenuItems()
      } else {
        showAlert("Fehler beim Löschen des Menüpunkts: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Löschen des Menüpunkts", "danger")
    }
  }

  function resetMenuForm() {
    menuForm.reset()
    menuForm.classList.remove("was-validated")
    document.getElementById("menuId").value = ""
    loadParentOptions()
  }

  // Event-Delegation für dynamisch erzeugte Elemente
  document.addEventListener("click", (e) => {
    // Event-Handler für Bearbeiten-Buttons
    if (e.target.closest(".edit-menu")) {
      const button = e.target.closest(".edit-menu")
      editMenuItem(button.dataset.id)
    }

    // Event-Handler für Löschen-Buttons
    if (e.target.closest(".delete-menu")) {
      const button = e.target.closest(".delete-menu")
      document.getElementById("deleteMenuId").value = button.dataset.id
      deleteMenuModal.show()
    }

    // Event-Handler für Ein-/Ausklappen der Untermenüs
    if (e.target.closest(".menu-toggle.has-children") || e.target.closest(".toggle-icon")) {
      const toggle = e.target.closest(".menu-toggle.has-children") || e.target.closest(".toggle-icon").parentNode
      const menuId = toggle.dataset.id

      // Alle Untermenüs dieses Elements finden
      const rows = menuTableBody.querySelectorAll(`tr[data-id="${menuId}"] ~ tr`)
      const childRows = []
      const isChild = false
      const childLevel = null

      // Bestimme das Level des aktuellen Elements
      const currentRow = toggle.closest("tr")
      const currentIndent = currentRow.querySelector("td:nth-child(2) span[style]")
      let currentLevel = 0
      if (currentIndent) {
        const paddingLeft = currentIndent.style.paddingLeft
        if (paddingLeft) {
          currentLevel = Number.parseInt(paddingLeft) / 20
        }
      }

      // Sammle alle direkten und indirekten Kinder
      for (const row of rows) {
        const indent = row.querySelector("td:nth-child(2) span[style]")
        let level = 0
        if (indent) {
          const paddingLeft = indent.style.paddingLeft
          if (paddingLeft) {
            level = Number.parseInt(paddingLeft) / 20
          }
        }

        // Wenn das Level höher ist als das aktuelle, ist es ein Kind
        if (level > currentLevel) {
          childRows.push(row)
          row.classList.toggle("d-none")
        } else {
          // Sobald wir ein Element mit gleichem oder niedrigerem Level finden, sind wir fertig
          break
        }
      }

      // Icon umschalten
      const icon = toggle.querySelector("i")
      if (icon.classList.contains("bi-caret-down-fill")) {
        icon.classList.remove("bi-caret-down-fill")
        icon.classList.add("bi-caret-right-fill")
      } else {
        icon.classList.remove("bi-caret-right-fill")
        icon.classList.add("bi-caret-down-fill")
      }
    }
  })
})

