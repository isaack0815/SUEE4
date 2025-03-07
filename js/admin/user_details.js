document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const editUserBtn = document.getElementById("editUserBtn")
  const saveUserBtn = document.getElementById("saveUserBtn")
  const manageGroupsBtn = document.getElementById("manageGroupsBtn")
  const saveGroupsBtn = document.getElementById("saveGroupsBtn")
  const editUserForm = document.getElementById("editUserForm")
  const manageGroupsForm = document.getElementById("manageGroupsForm")
  const groupsContainer = document.getElementById("groupsContainer")

  // Bootstrap-Modals
  const editUserModal = new bootstrap.Modal(document.getElementById("editUserModal"))
  const manageGroupsModal = new bootstrap.Modal(document.getElementById("manageGroupsModal"))

  // Event-Listener
  if (editUserBtn) {
    editUserBtn.addEventListener("click", () => {
      editUserModal.show()
    })
  }

  if (manageGroupsBtn) {
    manageGroupsBtn.addEventListener("click", () => {
      loadGroups()
      manageGroupsModal.show()
    })
  }

  if (saveUserBtn) {
    saveUserBtn.addEventListener("click", saveUser)
  }

  if (saveGroupsBtn) {
    saveGroupsBtn.addEventListener("click", saveGroups)
  }

  // Funktionen
  async function loadGroups() {
    const userId = document.getElementById("groupsUserId").value

    try {
      // Alle verfügbaren Gruppen abrufen
      const groupsResponse = await fetch("../admin/api/groups.php")
      const groupsData = await groupsResponse.json()

      // Benutzergruppen abrufen
      const userResponse = await fetch(`../admin/api/users.php?id=${userId}`)
      const userData = await userResponse.json()

      if (groupsData.success && userData.success) {
        const allGroups = groupsData.data
        const userGroups = userData.data.groups || []

        // Gruppen-IDs des Benutzers extrahieren
        const userGroupIds = userGroups.map((group) => group.id)

        // Gruppen-Container leeren
        groupsContainer.innerHTML = ""

        // Gruppen-Checkboxen erstellen
        allGroups.forEach((group) => {
          const isChecked = userGroupIds.includes(group.id)

          const groupDiv = document.createElement("div")
          groupDiv.className = "form-check mb-2"
          groupDiv.innerHTML = `
            <input class="form-check-input group-checkbox" type="checkbox" id="group_${group.id}" 
                   name="groups[]" value="${group.id}" ${isChecked ? "checked" : ""}>
            <label class="form-check-label" for="group_${group.id}">
              ${group.name} ${group.description ? `<small class="text-muted">(${group.description})</small>` : ""}
            </label>
          `

          groupsContainer.appendChild(groupDiv)
        })
      } else {
        showAlert("Fehler beim Laden der Gruppen", "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Gruppen", "danger")
    }
  }

  async function saveUser() {
    // Formularvalidierung
    if (!editUserForm.checkValidity()) {
      editUserForm.classList.add("was-validated")
      return
    }

    // Formulardaten sammeln
    const formData = new FormData(editUserForm)
    const userData = {
      id: formData.get("id"),
      username: formData.get("username"),
      email: formData.get("email"),
      password: formData.get("password"),
    }

    try {
      const response = await fetch("../admin/api/users.php", {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(userData),
      })

      const data = await response.json()

      if (data.success) {
        editUserModal.hide()
        showAlert("Benutzer erfolgreich aktualisiert.")

        // Seite neu laden, um die Änderungen anzuzeigen
        setTimeout(() => {
          window.location.reload()
        }, 1500)
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern des Benutzers", "danger")
    }
  }

  async function saveGroups() {
    const userId = document.getElementById("groupsUserId").value

    // Ausgewählte Gruppen sammeln
    const selectedGroups = []
    document.querySelectorAll(".group-checkbox:checked").forEach((checkbox) => {
      selectedGroups.push(checkbox.value)
    })

    try {
      const response = await fetch("../admin/api/users.php", {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: userId,
          groups: selectedGroups,
        }),
      })

      const data = await response.json()

      if (data.success) {
        manageGroupsModal.hide()
        showAlert("Benutzergruppen erfolgreich aktualisiert.")

        // Seite neu laden, um die Änderungen anzuzeigen
        setTimeout(() => {
          window.location.reload()
        }, 1500)
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern der Benutzergruppen", "danger")
    }
  }
})

