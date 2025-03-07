document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const usersTable = document.getElementById("usersTable")
  const addUserBtn = document.getElementById("addUserBtn")
  const saveUserBtn = document.getElementById("saveUserBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const userForm = document.getElementById("userForm")
  const passwordHelpText = document.getElementById("passwordHelpText")

  // Bootstrap-Modals
  const userModal = new bootstrap.Modal(document.getElementById("userModal"))
  const deleteUserModal = new bootstrap.Modal(document.getElementById("deleteUserModal"))

  // Benutzer laden
  loadUsers()

  // Event-Listener
  addUserBtn.addEventListener("click", () => {
    resetUserForm()
    document.getElementById("userModalLabel").textContent = "Benutzer hinzufügen"
    passwordHelpText.textContent = "Passwort ist erforderlich für neue Benutzer."
    document.getElementById("password").required = true
    userModal.show()
  })

  saveUserBtn.addEventListener("click", saveUser)
  confirmDeleteBtn.addEventListener("click", deleteUser)

  // Funktionen
  async function loadUsers() {
    try {
      const response = await fetch("../admin/api/users.php")
      const data = await response.json()

      if (data.success) {
        renderUsers(data.data)
      } else {
        showAlert("Fehler beim Laden der Benutzer: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Benutzer", "danger")
    }
  }

  function renderUsers(users) {
    const tbody = usersTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (users.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="text-center">Keine Benutzer gefunden</td>
        </tr>
      `
      return
    }

    users.forEach((user) => {
      const tr = document.createElement("tr")

      // Gruppen als Badges anzeigen
      let groupsHtml = ""
      if (user.groups && user.groups.length > 0) {
        user.groups.forEach((group) => {
          groupsHtml += `<span class="badge bg-info me-1">${group.name}</span>`
        })
      } else {
        groupsHtml = "<span class='text-muted'>Keine Gruppen</span>"
      }

      tr.innerHTML = `
      <td>
        <a href="user_details.php?id=${user.id}" class="text-decoration-none">
          ${user.username}
        </a>
      </td>
      <td>${user.email}</td>
      <td>${groupsHtml}</td>
      <td>${formatDateTime(user.last_login)}</td>
      <td>
        <a href="user_details.php?id=${user.id}" class="btn btn-sm btn-info">
          <i class="bi bi-eye"></i>
        </a>
        <button type="button" class="btn btn-sm btn-primary edit-user" data-id="${user.id}">
          <i class="bi bi-pencil"></i>
        </button>
        <button type="button" class="btn btn-sm btn-danger delete-user" data-id="${user.id}" data-username="${user.username}">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `
      tbody.appendChild(tr)
    })

    // Event-Listener für Bearbeiten-Buttons
    document.querySelectorAll(".edit-user").forEach((button) => {
      button.addEventListener("click", () => editUser(button.dataset.id))
    })

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-user").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deleteUserName").textContent = button.dataset.username
        document.getElementById("deleteUserId").value = button.dataset.id
        deleteUserModal.show()
      })
    })
  }

  async function editUser(id) {
    try {
      const response = await fetch(`../admin/api/users.php?id=${id}`)
      const data = await response.json()

      if (data.success) {
        const userData = data.data

        resetUserForm()

        document.getElementById("userId").value = userData.id
        document.getElementById("username").value = userData.username
        document.getElementById("email").value = userData.email

        // Passwort-Feld ist optional beim Bearbeiten
        document.getElementById("password").required = false
        passwordHelpText.textContent = "Lassen Sie dieses Feld leer, um das Passwort nicht zu ändern."

        // Gruppen auswählen
        if (userData.groups) {
          userData.groups.forEach((group) => {
            const checkbox = document.getElementById(`group_${group.id}`)
            if (checkbox) {
              checkbox.checked = true
            }
          })
        }

        document.getElementById("userModalLabel").textContent = "Benutzer bearbeiten"
        userModal.show()
      } else {
        showAlert("Fehler beim Laden des Benutzers: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden des Benutzers", "danger")
    }
  }

  async function saveUser() {
    // Formularvalidierung
    if (!userForm.checkValidity()) {
      userForm.classList.add("was-validated")
      return
    }

    // Formulardaten sammeln
    const formData = new FormData(userForm)
    const userData = {
      id: formData.get("id"),
      username: formData.get("username"),
      email: formData.get("email"),
      password: formData.get("password"),
      groups: [],
    }

    // Ausgewählte Gruppen sammeln
    document.querySelectorAll(".group-checkbox:checked").forEach((checkbox) => {
      userData.groups.push(checkbox.value)
    })

    try {
      let response

      if (userData.id) {
        // Benutzer aktualisieren
        response = await fetch("../admin/api/users.php", {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(userData),
        })
      } else {
        // Neuen Benutzer erstellen
        response = await fetch("../admin/api/users.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(userData),
        })
      }

      const data = await response.json()

      if (data.success) {
        userModal.hide()
        showAlert(userData.id ? "Benutzer erfolgreich aktualisiert." : "Benutzer erfolgreich hinzugefügt.")
        loadUsers()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern des Benutzers", "danger")
    }
  }

  async function deleteUser() {
    const id = document.getElementById("deleteUserId").value

    try {
      const response = await fetch("../admin/api/users.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id }),
      })

      const data = await response.json()

      if (data.success) {
        deleteUserModal.hide()
        showAlert("Benutzer erfolgreich gelöscht.")
        loadUsers()
      } else {
        showAlert("Fehler beim Löschen des Benutzers: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Löschen des Benutzers", "danger")
    }
  }

  function resetUserForm() {
    userForm.reset()
    userForm.classList.remove("was-validated")
    document.getElementById("userId").value = ""

    // Alle Gruppen-Checkboxen zurücksetzen
    document.querySelectorAll(".group-checkbox").forEach((checkbox) => {
      checkbox.checked = false
    })
  }
})

