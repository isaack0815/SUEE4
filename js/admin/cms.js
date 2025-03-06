document.addEventListener("DOMContentLoaded", () => {
  // DOM-Elemente
  const pagesTable = document.getElementById("pagesTable")
  const statusFilter = document.getElementById("statusFilter")
  const searchInput = document.getElementById("searchInput")
  const clearSearchBtn = document.getElementById("clearSearchBtn")
  const addPageBtn = document.getElementById("addPageBtn")
  const savePageBtn = document.getElementById("savePageBtn")
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn")
  const addToMenuBtn = document.getElementById("addToMenuBtn")
  const saveMenuItemBtn = document.getElementById("saveMenuItemBtn")
  const pagination = document.getElementById("pagination")

  // Bootstrap-Modals
  const pageModal = new bootstrap.Modal(document.getElementById("pageModal"))
  const deletePageModal = new bootstrap.Modal(document.getElementById("deletePageModal"))
  const addToMenuModal = new bootstrap.Modal(document.getElementById("addToMenuModal"))

  // Paginierungsvariablen
  let currentPage = 1
  const itemsPerPage = 10
  let totalItems = 0
  let totalPages = 0

  // Filter- und Suchvariablen
  let currentFilter = "all"
  let currentSearch = ""

  tinymce.init({
    selector: '#pageContent',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Your account includes a free trial of TinyMCE premium features
      // Try the most popular premium features until Mar 20, 2025:
      'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });

  // Seiten laden
  loadPages()

  // Event-Listener
  statusFilter.addEventListener("change", () => {
    currentFilter = statusFilter.value
    currentPage = 1
    loadPages()
  })

  searchInput.addEventListener("input", () => {
    currentSearch = searchInput.value
    currentPage = 1
    loadPages()
  })

  clearSearchBtn.addEventListener("click", () => {
    searchInput.value = ""
    currentSearch = ""
    loadPages()
  })

  addPageBtn.addEventListener("click", () => {
    resetPageForm()
    document.getElementById("pageModalLabel").textContent = "Seite hinzufügen"
    pageModal.show()
  })

  savePageBtn.addEventListener("click", savePage)
  confirmDeleteBtn.addEventListener("click", deletePage)
  addToMenuBtn.addEventListener("click", showAddToMenuModal)
  saveMenuItemBtn.addEventListener("click", saveMenuItem)

  // Funktionen
  async function loadPages() {
    try {
      let url = `../admin/api/cms.php?page=${currentPage}&limit=${itemsPerPage}`

      if (currentFilter !== "all") {
        url += `&status=${currentFilter}`
      }

      if (currentSearch) {
        url += `&search=${encodeURIComponent(currentSearch)}`
      }

      const response = await fetch(url)
      const data = await response.json()

      if (data.success) {
        renderPages(data.data)
        totalItems = data.total
        totalPages = Math.ceil(totalItems / itemsPerPage)
        renderPagination()
      } else {
        showAlert("Fehler beim Laden der Seiten: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Seiten", "danger")
    }
  }

  function renderPages(pages) {
    const tbody = pagesTable.querySelector("tbody")
    tbody.innerHTML = ""

    if (pages.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" class="text-center">Keine Seiten gefunden</td>
        </tr>
      `
      return
    }

    pages.forEach((page) => {
      const tr = document.createElement("tr")

      // Status-Badge
      let statusBadge = ""
      switch (page.status) {
        case "published":
          statusBadge = '<span class="badge bg-success">Veröffentlicht</span>'
          break
        case "draft":
          statusBadge = '<span class="badge bg-warning text-dark">Entwurf</span>'
          break
        case "archived":
          statusBadge = '<span class="badge bg-secondary">Archiviert</span>'
          break
      }

      tr.innerHTML = `
        <td>${page.title}</td>
        <td>${page.slug}</td>
        <td>${statusBadge}</td>
        <td>${formatDateTime(page.updated_at)}</td>
        <td>
          <button type="button" class="btn btn-sm btn-primary edit-page" data-id="${page.id}">
            <i class="bi bi-pencil"></i>
          </button>
          <a href="../page.php?slug=${page.slug}" class="btn btn-sm btn-info" target="_blank">
            <i class="bi bi-eye"></i>
          </a>
          <button type="button" class="btn btn-sm btn-success add-to-menu" data-id="${page.id}" data-title="${page.title}" data-slug="${page.slug}">
            <i class="bi bi-list"></i>
          </button>
          <button type="button" class="btn btn-sm btn-danger delete-page" data-id="${page.id}" data-title="${page.title}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `
      tbody.appendChild(tr)
    })

    // Event-Listener für Bearbeiten-Buttons
    document.querySelectorAll(".edit-page").forEach((button) => {
      button.addEventListener("click", () => editPage(button.dataset.id))
    })

    // Event-Listener für Zum-Menü-hinzufügen-Buttons
    document.querySelectorAll(".add-to-menu").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("menuPageId").value = button.dataset.id
        document.getElementById("menuName").value = button.dataset.title
        loadParentMenuItems()
        addToMenuModal.show()
      })
    })

    // Event-Listener für Löschen-Buttons
    document.querySelectorAll(".delete-page").forEach((button) => {
      button.addEventListener("click", () => {
        document.getElementById("deletePageTitle").textContent = button.dataset.title
        document.getElementById("deletePageId").value = button.dataset.id
        deletePageModal.show()
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
          loadPages()
        }
      })
    })
  }

  async function editPage(id) {
    try {
      const response = await fetch(`../admin/api/cms.php?id=${id}`)
      const data = await response.json()

      if (data.success) {
        const page = data.data

        resetPageForm()

        document.getElementById("pageId").value = page.id
        document.getElementById("pageTitle").value = page.title
        document.getElementById("pageSlug").value = page.slug
        document.getElementById("pageStatus").value = page.status
        document.getElementById("pageMetaDescription").value = page.meta_description || ""
        document.getElementById("pageMetaKeywords").value = page.meta_keywords || ""

        // TinyMCE-Inhalt setzen
        tinymce.get("pageContent").setContent(page.content || "")

        document.getElementById("pageModalLabel").textContent = "Seite bearbeiten"
        pageModal.show()
      } else {
        showAlert("Fehler beim Laden der Seite: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Laden der Seite", "danger")
    }
  }

  async function savePage() {
    const form = document.getElementById("pageForm")

    // Formularvalidierung
    if (!form.checkValidity()) {
      form.classList.add("was-validated")
      return
    }

    // TinyMCE-Inhalt abrufen
    const content = tinymce.get("pageContent").getContent()

    const formData = new FormData(form)
    const pageData = {
      id: formData.get("id"),
      title: formData.get("title"),
      slug: formData.get("slug"),
      content: content,
      meta_description: formData.get("meta_description"),
      meta_keywords: formData.get("meta_keywords"),
      status: formData.get("status"),
    }

    try {
      let response

      if (pageData.id) {
        // Seite aktualisieren
        response = await fetch("../admin/api/cms.php", {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(pageData),
        })
      } else {
        // Neue Seite erstellen
        response = await fetch("../admin/api/cms.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(pageData),
        })
      }

      const data = await response.json()

      if (data.success) {
        pageModal.hide()
        showAlert(pageData.id ? "Seite erfolgreich aktualisiert." : "Seite erfolgreich hinzugefügt.")
        loadPages()
      } else {
        showAlert("Fehler: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Speichern der Seite", "danger")
    }
  }

  async function deletePage() {
    const id = document.getElementById("deletePageId").value

    try {
      const response = await fetch("../admin/api/cms.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id }),
      })

      const data = await response.json()

      if (data.success) {
        deletePageModal.hide()
        showAlert("Seite erfolgreich gelöscht.")
        loadPages()
      } else {
        showAlert("Fehler beim Löschen der Seite: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Löschen der Seite", "danger")
    }
  }

  function showAddToMenuModal() {
    // Seitendaten aus dem Formular abrufen
    const pageId = document.getElementById("pageId").value
    const pageTitle = document.getElementById("pageTitle").value
    const pageSlug = document.getElementById("pageSlug").value

    if (!pageId && !pageSlug) {
      showAlert("Bitte speichern Sie die Seite zuerst, bevor Sie sie zum Menü hinzufügen.", "warning")
      return
    }

    // Formular für Menüpunkt vorbereiten
    document.getElementById("menuPageId").value = pageId
    document.getElementById("menuName").value = pageTitle

    // Übergeordnete Menüpunkte laden
    loadParentMenuItems()

    addToMenuModal.show()
  }

  async function loadParentMenuItems() {
    const menuArea = document.getElementById("menuArea").value

    try {
      const response = await fetch(`../admin/api/menus.php?parent_options=1&area=${menuArea}`)
      const data = await response.json()

      if (data.success) {
        const parentItems = data.data
        const menuParent = document.getElementById("menuParent")

        menuParent.innerHTML = `<option value="">{translate key="no_parent"}</option>`

        parentItems.forEach((item) => {
          menuParent.innerHTML += `<option value="${item.id}">${item.name}</option>`
        })
      }
    } catch (error) {
      console.error("Error:", error)
    }
  }

  async function saveMenuItem() {
    const form = document.getElementById("addToMenuForm")

    // Formularvalidierung
    if (!form.checkValidity()) {
      form.classList.add("was-validated")
      return
    }

    const formData = new FormData(form)
    const pageId = formData.get("page_id")

    // Seitendaten abrufen
    let pageSlug = ""
    if (pageId) {
      try {
        const response = await fetch(`../admin/api/cms.php?id=${pageId}`)
        const data = await response.json()

        if (data.success) {
          pageSlug = data.data.slug
        }
      } catch (error) {
        console.error("Error:", error)
      }
    } else {
      // Wenn keine Seiten-ID vorhanden ist, Slug aus dem Formular verwenden
      pageSlug = document.getElementById("pageSlug").value
    }

    // Menüdaten vorbereiten
    const menuData = {
      area: formData.get("area"),
      parent_id: formData.get("parent_id") || null,
      name: formData.get("name"),
      description: formData.get("name"), // Beschreibung hinzufügen
      url: `page.php?slug=${pageSlug}`,
      icon: formData.get("icon") || null,
      sort_order: 0,
      is_active: 1,
      required_group_id: null, // Explizit null setzen
      required_permission_id: null, // Explizit null setzen
      module: null, // Explizit null setzen
    }

    try {
      const response = await fetch("../admin/api/menus.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(menuData),
      })

      const data = await response.json()

      if (data.success) {
        addToMenuModal.hide()
        showAlert("Seite erfolgreich zum Menü hinzugefügt.")
      } else {
        showAlert("Fehler beim Hinzufügen zum Menü: " + data.message, "danger")
      }
    } catch (error) {
      console.error("Error:", error)
      showAlert("Fehler beim Hinzufügen zum Menü", "danger")
    }
  }

  function resetPageForm() {
    const form = document.getElementById("pageForm")
    form.reset()
    form.classList.remove("was-validated")
    document.getElementById("pageId").value = ""

    // TinyMCE zurücksetzen
    tinymce.get("pageContent").setContent("")
  }

  // Event-Listener für Menübereich-Änderung
  document.getElementById("menuArea").addEventListener("change", loadParentMenuItems)

  function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return "-"

    const date = new Date(dateTimeStr)
    if (isNaN(date.getTime())) return dateTimeStr

    return date.toLocaleDateString() + " " + date.toLocaleTimeString()
  }
})

