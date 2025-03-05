document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const groupTable = document.getElementById("groupTable")
  const addGroupBtn = document.getElementById("addGroupBtn")
  const saveGroupBtn = document.getElementById("saveGroupBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const savePermissionsBtn = document.getElementById("savePermissionsBtn")
  const selectAllPermissions = document.getElementById("selectAllPermissions")
  const groupForm = document.getElementById("groupForm")

  // Bootstrap-Modals
  const groupModal = new bootstrap.Modal(document.getElementById("groupModal"))
  const deleteGroupModal = new bootstrap.Modal(document.getElementById("deleteGroupModal"))
  const permissionsModal = new bootstrap.Modal(document.getElementById("permissionsModal"))

  // Gruppen laden
  loadGroups()

  // Event-Listener
  addGroupBtn.addEventListener("click", () => {
    resetGroupForm()
    document.getElementById("groupModalLabel").textContent = "Gruppe hinzufügen"
    groupModal.show()
  })

  saveGroupBtn.addEventListener("click", saveGroup)
  confirmDeleteBtn.addEventListener("click", deleteGroup)
  savePermissionsBtn.addEventListener("click", savePermissions)

  // Event-Listener für "Alle auswählen" Checkbox
  selectAllPermissions.addEventListener("change", () => {
    const permissionCheckboxes = document.querySelectorAll(".permission-checkbox")
    permissionCheckboxes.forEach((checkbox) => {
      checkbox.checked = selectAllPermissions.checked
    })
  })

  // Funktionen
  async function loadGroups() {
    try {
      const response = await fetchAPI("../admin/api/groups.php")

      if (response.success) {
        renderGroups(response.data)
      } else {
        showAlert("Fehler beim Laden der Gruppen: " + response.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Gruppen", "danger")
    }
  }

  function renderGroups(groups) {
    const tbody = groupTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (groups.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">Keine Gruppen gefunden</td>
                </tr>
            `
      return
    }

    groups.forEach((group) => {
      const tr = document.createElement("tr")
      tr.innerHTML = `
                <td>${group.name}</td>
                <td>${group.description || "-"}</td>
                <td>${formatDateTime(group.created_at)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary edit-group" data-id="${group.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info permissions-group" data-id="${group.id}" data-name="${group.name}">
                        <i class="bi bi-shield-lock"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-group" data-id="${group.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `
      tbody.appendChild(tr)
    })

    // Event-Listener für Bearbeiten-Buttons
    document.querySelectorAll(".edit-group").forEach((button) => {
      button.addEventListener("click", () => editGroup(button.dataset.id))
    })

    // Event-Listener für Berechtigungen-Buttons
    document.querySelectorAll(".permissions-group").forEach((button) => {
      button.addEventListener("click", () => showPermissions(button.dataset.id, button.dataset.name))
    })

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-group").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deleteGroupId").value = button.dataset.id
        deleteGroupModal.show()
      })
    })
  }

  async function editGroup(id) {
    try {
      const response = await fetchAPI(`../admin/api/groups.php?id=${id}`)

      if (response.success) {
        const group = response.data

        document.getElementById("groupId").value = group.id
        document.getElementById("groupName").value = group.name
        document.getElementById("groupDescription").value = group.description || ""

        document.getElementById("groupModalLabel").textContent = "Gruppe bearbeiten"
        groupModal.show()
      } else {
        showAlert("Fehler beim Laden der Gruppe: " + response.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Gruppe", "danger")
    }
  }

  async function showPermissions(groupId, groupName) {
    document.getElementById("permissionsGroupId").value = groupId
    document.getElementById("permissionsModalLabel").textContent = `Berechtigungen für Gruppe: ${groupName}`

    // Alle Checkboxen zurücksetzen
    document.querySelectorAll(".permission-checkbox").forEach((checkbox) => {
      checkbox.checked = false
    })

    selectAllPermissions.checked = false

    try {
      const response = await fetchAPI(`../admin/api/groups.php?permissions=1&group_id=${groupId}`)

      if (response.success) {
        const groupPermissions = response.data

        // Berechtigungen der Gruppe markieren
        groupPermissions.forEach((permission) => {
          const checkbox = document.getElementById(`permission_${permission.id}`)
          if (checkbox) {
            checkbox.checked = true
          }
        })

        permissionsModal.show()
      } else {
        showAlert("Fehler beim Laden der Berechtigungen: " + response.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Berechtigungen", "danger")
    }
  }

  async function saveGroup() {
    // Formularvalidierung
    if (!groupForm.checkValidity()) {
      groupForm.classList.add("was-validated")
      return
    }

    // Formulardaten sammeln
    const formData = new FormData(groupForm)
    const groupData = {}

    for (const [key, value] of formData.entries()) {
      groupData[key] = value
    }

    try {
      const groupId = document.getElementById("groupId").value

      let response
      if (groupId) {
        // Gruppe aktualisieren
        response = await fetchAPI("../admin/api/groups.php", "PUT", groupData)
      } else {
        // Neue Gruppe erstellen
        response = await fetchAPI("../admin/api/groups.php", "POST", groupData)
      }

      if (response.success) {
        groupModal.hide()
        showAlert(groupId ? "Gruppe erfolgreich aktualisiert." : "Gruppe erfolgreich hinzugefügt.")
        loadGroups()
      } else {
        showAlert("Fehler: " + response.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern der Gruppe", "danger")
    }
  }

  async function savePermissions() {
    const groupId = document.getElementById("permissionsGroupId").value

    if (!groupId) {
      return
    }

    // Ausgewählte Berechtigungen sammeln
    const permissionIds = []
    document.querySelectorAll(".permission-checkbox:checked").forEach((checkbox) => {
      permissionIds.push(checkbox.value)
    })

    try {
      const response = await fetchAPI("../admin/api/groups.php", "PATCH", {
        group_id: groupId,
        permission_ids: permissionIds,
      })

      if (response.success) {
        permissionsModal.hide()
        showAlert("Berechtigungen erfolgreich zugewiesen.")
      } else {
        showAlert("Fehler beim Zuweisen der Berechtigungen: " + response.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Zuweisen der Berechtigungen", "danger")
    }
  }

  async function deleteGroup() {
    const groupId = document.getElementById("deleteGroupId").value

    if (!groupId) {
      return
    }

    try {
      const response = await fetchAPI("../admin/api/groups.php", "DELETE", { id: groupId })

      if (response.success) {
        deleteGroupModal.hide()
        showAlert("Gruppe erfolgreich gelöscht.")
        loadGroups()
      } else {
        showAlert("Fehler beim Löschen der Gruppe: " + response.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Löschen der Gruppe", "danger")
    }
  }

  function resetGroupForm() {
    groupForm.reset()
    groupForm.classList.remove("was-validated")
    document.getElementById("groupId").value = ""
  }

  function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return "-"

    const date = new Date(dateTimeStr)
    if (isNaN(date.getTime())) return dateTimeStr

    return date.toLocaleDateString() + " " + date.toLocaleTimeString()
  }
})

