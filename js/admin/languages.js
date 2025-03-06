document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const languageTable = document.getElementById("languageTable")
  const languageFilter = document.getElementById("languageFilter")
  const searchInput = document.getElementById("searchInput")
  const clearSearchBtn = document.getElementById("clearSearchBtn")
  const addLanguageBtn = document.getElementById("addLanguageBtn")
  const addKeyBtn = document.getElementById("addKeyBtn")
  const saveLanguageBtn = document.getElementById("saveLanguageBtn")
  const saveKeyBtn = document.getElementById("saveKeyBtn")
  const updateKeyBtn = document.getElementById("updateKeyBtn")
  const confirmDeleteKeyBtn = document.getElementById("confirmDeleteKeyBtn")
  const pagination = document.getElementById("pagination")

  // Bootstrap-Modals
  const addLanguageModal = new bootstrap.Modal(document.getElementById("addLanguageModal"))
  const addKeyModal = new bootstrap.Modal(document.getElementById("addKeyModal"))
  const editKeyModal = new bootstrap.Modal(document.getElementById("editKeyModal"))
  const deleteKeyModal = new bootstrap.Modal(document.getElementById("deleteKeyModal"))

  // Paginierungsvariablen
  let currentPage = 1
  const itemsPerPage = 20
  let totalItems = 0
  let totalPages = 0

  // Filter- und Suchvariablen
  let currentFilter = "all"
  let currentSearch = ""

  // Verfügbare Sprachen
  let availableLanguages = []

  // Sprachschlüssel laden
  loadLanguageKeys()

  // Event-Listener
  languageFilter.addEventListener("change", () => {
    currentFilter = languageFilter.value
    currentPage = 1
    loadLanguageKeys()
  })

  searchInput.addEventListener("input", () => {
    currentSearch = searchInput.value
    currentPage = 1
    loadLanguageKeys()
  })

  clearSearchBtn.addEventListener("click", () => {
    searchInput.value = ""
    currentSearch = ""
    loadLanguageKeys()
  })

  addLanguageBtn.addEventListener("click", () => {
    document.getElementById("addLanguageForm").reset()
    addLanguageModal.show()
  })

  addKeyBtn.addEventListener("click", () => {
    document.getElementById("addKeyForm").reset()
    addKeyModal.show()
  })

  saveLanguageBtn.addEventListener("click", saveLanguage)
  saveKeyBtn.addEventListener("click", saveLanguageKey)
  updateKeyBtn.addEventListener("click", updateLanguageKey)
  confirmDeleteKeyBtn.addEventListener("click", deleteLanguageKey)

  // Funktionen
  async function loadLanguageKeys() {
    try {
      const url = `../admin/api/languages.php?page=${currentPage}&limit=${itemsPerPage}&filter=${currentFilter}&search=${encodeURIComponent(currentSearch)}`
      const response = await fetch(url)
      const data = await response.json()

      if (data.success) {
        // Verfügbare Sprachen aus den Daten extrahieren
        if (data.data.length > 0) {
          availableLanguages = Object.keys(data.data[0].values)
        }

        renderLanguageKeys(data.data)
        totalItems = data.total
        totalPages = Math.ceil(totalItems / itemsPerPage)
        renderPagination()
      } else {
        showAlert("Fehler beim Laden der Sprachschlüssel: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Sprachschlüssel", "danger")
    }
  }

  function renderLanguageKeys(keys) {
    const tbody = languageTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (keys.length === 0) {
      tbody.innerHTML = `
       <tr>
         <td colspan="${languageTable.querySelectorAll("th").length}" class="text-center">Keine Sprachschlüssel gefunden</td>
       </tr>
     `
      return
    }

    keys.forEach((key) => {
      const tr = document.createElement("tr")

      // Sprachschlüssel
      let html = `<td>${key.lang_key}</td>`

      // Werte für jede Sprache
      availableLanguages.forEach((langCode) => {
        const langValue = key.values[langCode] || ""
        html += `<td>${langValue}</td>`
      })

      // Aktionen
      html += `
       <td>
         <button type="button" class="btn btn-sm btn-primary edit-key" data-key="${key.lang_key}">
           <i class="bi bi-pencil"></i>
         </button>
         <button type="button" class="btn btn-sm btn-danger delete-key" data-key="${key.lang_key}">
           <i class="bi bi-trash"></i>
         </button>
       </td>
     `

      tr.innerHTML = html
      tbody.appendChild(tr)
    })

    // Event-Listener für Bearbeiten-Buttons
    document.querySelectorAll(".edit-key").forEach((button) => {
      button.addEventListener("click", () => editLanguageKey(button.dataset.key))
    })

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-key").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deleteKeyName").textContent = button.dataset.key
        document.getElementById("deleteKeyValue").value = button.dataset.key
        deleteKeyModal.show()
      })
    })
  }

  function renderPagination() {
    pagination.innerHTML = ""

    // Keine Paginierung anzeigen, wenn nur eine Seite vorhanden ist
    if (totalPages <= 1) {
      return
    }

    // Zurück-Button
    const prevLi = document.createElement("li")
    prevLi.className = `page-item ${currentPage === 1 ? "disabled" : ""}`
    prevLi.innerHTML = `<a class="page-link" href="#" data-page="${currentPage - 1}">Zurück</a>`
    pagination.appendChild(prevLi)

    // Seitenzahlen
    const maxPages = 5
    let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2))
    const endPage = Math.min(totalPages, startPage + maxPages - 1)

    if (endPage - startPage + 1 < maxPages) {
      startPage = Math.max(1, endPage - maxPages + 1)
    }

    for (let i = startPage; i <= endPage; i++) {
      const li = document.createElement("li")
      li.className = `page-item ${i === currentPage ? "active" : ""}`
      li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`
      pagination.appendChild(li)
    }

    // Weiter-Button
    const nextLi = document.createElement("li")
    nextLi.className = `page-item ${currentPage === totalPages ? "disabled" : ""}`
    nextLi.innerHTML = `<a class="page-link" href="#" data-page="${currentPage + 1}">Weiter</a>`
    pagination.appendChild(nextLi)

    // Event-Listener für Paginierung
    document.querySelectorAll(".page-link").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault()
        const page = Number.parseInt(link.dataset.page)
        if (page >= 1 && page <= totalPages) {
          currentPage = page
          loadLanguageKeys()
        }
      })
    })
  }

  async function saveLanguage() {
    const form = document.getElementById("addLanguageForm")

    // Formularvalidierung
    if (!form.checkValidity()) {
      form.classList.add("was-validated")
      return
    }

    const formData = new FormData(form)
    const languageData = {
      lang_code: formData.get("lang_code"),
      lang_name: formData.get("lang_name"),
    }

    try {
      const response = await fetch("../admin/api/languages.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(languageData),
      })

      const data = await response.json()

      if (data.success) {
        addLanguageModal.hide()
        showAlert("Sprache erfolgreich hinzugefügt.")
        // Seite neu laden, um die neue Sprache anzuzeigen
        window.location.reload()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern der Sprache", "danger")
    }
  }

  async function saveLanguageKey() {
    const form = document.getElementById("addKeyForm")

    // Formularvalidierung
    if (!form.checkValidity()) {
      form.classList.add("was-validated")
      return
    }

    const formData = new FormData(form)
    const keyData = {
      lang_key: formData.get("lang_key"),
      values: {},
    }

    // Werte für jede Sprache sammeln
    availableLanguages.forEach((langCode) => {
      const inputValue = formData.get(`lang_value_${langCode}`)
      if (inputValue !== null) {
        keyData.values[langCode] = inputValue
      }
    })

    try {
      const response = await fetch("../admin/api/languages.php?action=add_key", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(keyData),
      })

      const data = await response.json()

      if (data.success) {
        addKeyModal.hide()
        showAlert("Sprachschlüssel erfolgreich hinzugefügt.")
        loadLanguageKeys()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern des Sprachschlüssels", "danger")
    }
  }

  async function editLanguageKey(key) {
    try {
      const response = await fetch(`../admin/api/languages.php?action=get_key&key=${encodeURIComponent(key)}`)
      const data = await response.json()

      if (data.success) {
        const keyData = data.data
        console.log("Loaded key data:", keyData) // Debug-Ausgabe

        document.getElementById("editKeyName").value = keyData.lang_key

        // Werte für jede Sprache setzen
        availableLanguages.forEach((langCode) => {
          const inputElement = document.getElementById(`editKeyValue_${langCode}`)
          if (inputElement) {
            inputElement.value = keyData.values[langCode] || ""
          } else {
            console.warn(`Element with ID editKeyValue_${langCode} not found`) // Debug-Ausgabe
          }
        })

        editKeyModal.show()
      } else {
        showAlert("Fehler beim Laden des Sprachschlüssels: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden des Sprachschlüssels", "danger")
    }
  }

  async function updateLanguageKey() {
    const form = document.getElementById("editKeyForm")

    // Formularvalidierung
    if (!form.checkValidity()) {
      form.classList.add("was-validated")
      return
    }

    const formData = new FormData(form)
    const keyData = {
      lang_key: formData.get("lang_key"),
      values: {},
    }

    // Werte für jede Sprache sammeln
    availableLanguages.forEach((langCode) => {
      const inputElement = document.getElementById(`editKeyValue_${langCode}`)
      if (inputElement) {
        keyData.values[langCode] = inputElement.value
      }
    })

    try {
      const response = await fetch("../admin/api/languages.php?action=update_key", {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(keyData),
      })

      const data = await response.json()

      if (data.success) {
        editKeyModal.hide()
        showAlert("Sprachschlüssel erfolgreich aktualisiert.")
        loadLanguageKeys()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Aktualisieren des Sprachschlüssels", "danger")
    }
  }

  async function deleteLanguageKey() {
    const key = document.getElementById("deleteKeyValue").value

    try {
      const response = await fetch("../admin/api/languages.php?action=delete_key", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ lang_key: key }),
      })

      const data = await response.json()

      if (data.success) {
        deleteKeyModal.hide()
        showAlert("Sprachschlüssel erfolgreich gelöscht.")
        loadLanguageKeys()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Löschen des Sprachschlüssels", "danger")
    }
  }

  function showAlert(message, type = "success") {
    const alertArea = document.getElementById("alertArea")
    alertArea.innerHTML = `
     <div class="alert alert-${type} alert-dismissible fade show" role="alert">
       ${message}
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
     </div>
   `
    alertArea.style.display = "block"
  }
})

