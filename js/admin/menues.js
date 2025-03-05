document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const areaSelect = document.getElementById("areaSelect")
  const menuTable = document.getElementById("menuTable")
  const addMenuBtn = document.getElementById("addMenuBtn")
  const saveMenuBtn = document.getElementById("saveMenuBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const menuForm = document.getElementById("menuForm")
  const menuArea = document.getElementById("menuArea")
  const menuParent = document.getElementById("menuParent")

  // Bootstrap-Modals
  const menuModal = new bootstrap.Modal(document.getElementById("menuModal"))
  const deleteMenuModal = new bootstrap.Modal(document.getElementById("deleteMenuModal"))

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
    const response = await fetchAPI(`../admin/api/menu.php?area=${area}`)

    if (response.success) {
      renderMenuItems(response.data)
    } else {
      showAlert("Fehler beim Laden der Menüpunkte: " + response.message, "danger")
    }
  }

  function renderMenuItems(menuItems) {
    const tbody = menuTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (menuItems.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">Keine Menüpunkte gefunden</td>
                </tr>
            `
      return
    }

    menuItems.forEach((item) => {
      const tr = document.createElement("tr")
      tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.url}</td>
                <td>${item.parent_name || "-"}</td>
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
      tbody.appendChild(tr)
    })

    // Event-Listener für Bearbeiten-Buttons
    document.querySelectorAll(".edit-menu").forEach((button) => {
      button.addEventListener("click", () => editMenuItem(button.dataset.id))
    })

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-menu").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deleteMenuId").value = button.dataset.id
        deleteMenuModal.show()
      })
    })
  }

  async function loadParentOptions() {
    const area = menuArea.value
    const response = await fetchAPI(`../admin/api/menu.php?parent_options=1&area=${area}`)

    if (response.success) {
      const parentItems = response.data
      menuParent.innerHTML = `<option value="">{translate key="no_parent"}</option>`

      parentItems.forEach((item) => {
        menuParent.innerHTML += `<option value="${item.id}">${item.name}</option>`
      })
    }
  }

  async function editMenuItem(id) {
    const response = await fetchAPI(`../admin/api/menu.php?id=${id}`)

    if (response.success) {
      const item = response.data
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
      showAlert("Fehler beim Laden des Menüpunkts: " + response.message, "danger")
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

    let response
    const menuId = document.getElementById("menuId").value

    if (menuId) {
      // Menüpunkt aktualisieren
      menuData.id = menuId
      response = await fetchAPI("../admin/api/menu.php", "PUT", menuData)
    } else {
      // Neuen Menüpunkt erstellen
      response = await fetchAPI("../admin/api/menu.php", "POST", menuData)
    }

    if (response.success) {
      menuModal.hide()
      showAlert(menuId ? "Menüpunkt erfolgreich aktualisiert." : "Menüpunkt erfolgreich hinzugefügt.")
      loadMenuItems()
    } else {
      showAlert("Fehler: " + response.message, "danger")
    }
  }

  async function deleteMenuItem() {
    const menuId = document.getElementById("deleteMenuId").value

    if (!menuId) {
      return
    }

    const response = await fetchAPI("../admin/api/menu.php", "DELETE", { id: menuId })

    if (response.success) {
      deleteMenuModal.hide()
      showAlert("Menüpunkt erfolgreich gelöscht.")
      loadMenuItems()
    } else {
      showAlert("Fehler beim Löschen des Menüpunkts: " + response.message, "danger")
    }
  }

  function resetMenuForm() {
    menuForm.reset()
    menuForm.classList.remove("was-validated")
    document.getElementById("menuId").value = ""
    loadParentOptions()
  }
})