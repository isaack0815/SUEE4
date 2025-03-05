document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const permissionTable = document.getElementById("permissionTable")
  const addPermissionBtn = document.getElementById("addPermissionBtn")
  const savePermissionBtn = document.getElementById("savePermissionBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const permissionForm = document.getElementById("permissionForm")

  // Bootstrap-Modals
  const permissionModal = new bootstrap.Modal(document.getElementById("permissionModal"))
  const deletePermissionModal = new bootstrap.Modal(document.getElementById("deletePermissionModal"))

  // Berechtigungen laden
  loadPermissions()

  // Event-Listener
  addPermissionBtn.addEventListener("click", () => {
    resetPermissionForm()
    document.getElementById("permissionModalLabel").textContent = "Berechtigung hinzufügen"
    permissionModal.show()
  })

  savePermissionBtn.addEventListener("click", savePermission)
  confirmDeleteBtn.addEventListener("click", deletePermission)

  // Funktionen
  async function loadPermissions() {
    try {
      const response = await fetch("../admin/api/permissions.php")
      const data = await response.json()

      if (data.success) {
        renderPermissions(data.data)
      } else {
        showAlert("Fehler beim Laden der Berechtigungen: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Berechtigungen", "danger")
    }
  }

  function renderPermissions(permissions) {
    const tbody = permissionTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (permissions.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">Keine Berechtigungen gefunden</td>
                </tr>
            `
      return
    }

    permissions.forEach((permission) => {
      const tr = document.createElement("tr")
      tr.innerHTML = `
                <td>${permission.name}</td>
                <td>${permission.description || "-"}</td>
                <td>${formatDateTime(permission.created_at)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary edit-permission" data-id="${permission.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-permission" data-id="${permission.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `
      tbody.appendChild(tr)
    })

    // Event-Listener für Bearbeiten-Buttons
    document.querySelectorAll(".edit-permission").forEach((button) => {
      button.addEventListener("click", () => editPermission(button.dataset.id))
    })

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-permission").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deletePermissionId").value = button.dataset.id
        deletePermissionModal.show()
      })
    })
  }

  async function editPermission(id) {
    try {
      const response = await fetch(`../admin/api/permissions.php?id=${id}`)
      const data = await response.json()

      if (data.success) {
        const permission = data.data

        document.getElementById("permissionId").value = permission.id
        document.getElementById("permissionName").value = permission.name
        document.getElementById("permissionDescription").value = permission.description || ""

        document.getElementById("permissionModalLabel").textContent = "Berechtigung bearbeiten"
        permissionModal.show()
      } else {
        showAlert("Fehler beim Laden der Berechtigung: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Berechtigung", "danger")
    }
  }

  async function savePermission() {
    // Formularvalidierung
    if (!permissionForm.checkValidity()) {
      permissionForm.classList.add("was-validated")
      return
    }

    // Formulardaten sammeln
    const formData = new FormData(permissionForm)
    const permissionData = {}

    for (const [key, value] of formData.entries()) {
      permissionData[key] = value
    }

    try {
      let response
      const permissionId = document.getElementById("permissionId").value

      if (permissionId) {
        // Berechtigung aktualisieren
        response = await fetch("../admin/api/permissions.php", {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(permissionData),
        })
      } else {
        // Neue Berechtigung erstellen
        response = await fetch("../admin/api/permissions.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(permissionData),
        })
      }

      const data = await response.json()

      if (data.success) {
        permissionModal.hide()
        showAlert(permissionId ? "Berechtigung erfolgreich aktualisiert." : "Berechtigung erfolgreich hinzugefügt.")
        loadPermissions()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern der Berechtigung", "danger")
    }
  }

  async function deletePermission() {
    const permissionId = document.getElementById("deletePermissionId").value

    if (!permissionId) {
      return
    }

    try {
      const response = await fetch("../admin/api/permissions.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: permissionId }),
      })

      const data = await response.json()

      if (data.success) {
        deletePermissionModal.hide()
        showAlert("Berechtigung erfolgreich gelöscht.")
        loadPermissions()
      } else {
        showAlert("Fehler beim Löschen der Berechtigung: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Löschen der Berechtigung", "danger")
    }
  }

  function resetPermissionForm() {
    permissionForm.reset()
    permissionForm.classList.remove("was-validated")
    document.getElementById("permissionId").value = ""
  }

  function showAlert(message, type = "info") {
    const alertArea = document.getElementById("alertArea")
    alertArea.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `
    alertArea.style.display = "block"
  }

  function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return "-"

    const date = new Date(dateTimeStr)
    if (isNaN(date.getTime())) return dateTimeStr

    return date.toLocaleDateString() + " " + date.toLocaleTimeString()
  }
})

