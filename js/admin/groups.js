// Import necessary functions (assuming they are in separate files)
import { fetchAPI } from "./api.js" // Or wherever fetchAPI is defined
import { showAlert } from "./alerts.js" // Or wherever showAlert is defined
import { formatDateTime } from "./utils.js" // Or wherever formatDateTime is defined

document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const groupsTable = document.getElementById("groupsTable")
  const groupForm = document.getElementById("groupForm")
  const addGroupBtn = document.getElementById("addGroupBtn")
  const saveGroupBtn = document.getElementById("saveGroupBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")

  // Bootstrap-Modals
  const groupModal = new bootstrap.Modal(document.getElementById("groupModal"))
  const deleteGroupModal = new bootstrap.Modal(document.getElementById("deleteGroupModal"))

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

  // Funktionen
  async function loadGroups() {
    const response = await fetchAPI("../admin/api/groups.php")

    if (response.success) {
      renderGroups(response.data)
    } else {
      showAlert("Fehler beim Laden der Gruppen: " + response.message, "danger")
    }
  }

  function renderGroups(groups) {
    const tbody = groupsTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (groups.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">Keine Gruppen gefunden</td>
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
                <td>${formatDateTime(group.updated_at)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary edit-group" data-id="${group.id}">
                        <i class="bi bi-pencil"></i>
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

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-group").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deleteGroupId").value = button.dataset.id
        deleteGroupModal.show()
      })
    })
  }

  async function editGroup(id) {
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
  }

  async function saveGroup() {
    // Formularvalidierung
    if (!groupForm.checkValidity()) {
      groupForm.classList.add("was-validated")
      return
    }

    const groupId = document.getElementById("groupId").value
    const groupData = {
      name: document.getElementById("groupName").value,
      description: document.getElementById("groupDescription").value,
    }

    let response

    if (groupId) {
      // Gruppe aktualisieren
      groupData.id = groupId
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
  }

  async function deleteGroup() {
    const groupId = document.getElementById("deleteGroupId").value

    if (!groupId) {
      return
    }

    const response = await fetchAPI("../admin/api/groups.php", "DELETE", { id: groupId })

    if (response.success) {
      deleteGroupModal.hide()
      showAlert("Gruppe erfolgreich gelöscht.")
      loadGroups()
    } else {
      showAlert("Fehler beim Löschen der Gruppe: " + response.message, "danger")
    }
  }

  function resetGroupForm() {
    groupForm.reset()
    groupForm.classList.remove("was-validated")
    document.getElementById("groupId").value = ""
  }
})

