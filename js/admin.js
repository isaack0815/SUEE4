document.addEventListener("DOMContentLoaded", () => {
    // Allgemeine Admin-Funktionen hier
    console.log("Admin-Bereich geladen")
  
    // Sidebar-Toggle für mobile Ansicht
    const sidebarToggle = document.querySelector("#sidebarToggle")
    if (sidebarToggle) {
      sidebarToggle.addEventListener("click", () => {
        document.querySelector(".sidebar").classList.toggle("show")
      })
    }
  
    // Aktiven Menüpunkt hervorheben
    const currentPath = window.location.pathname
    const navLinks = document.querySelectorAll(".nav-link")
  
    navLinks.forEach((link) => {
      if (link.getAttribute("href") && currentPath.includes(link.getAttribute("href"))) {
        link.classList.add("active")
      }
    })
  })
  
  // Hilfsfunktion zum Anzeigen von Benachrichtigungen
  function showAlert(message, type = "success") {
    const alertArea = document.getElementById("alertArea")
    if (!alertArea) return
  
    alertArea.innerHTML = `
          <div class="alert alert-${type} alert-dismissible fade show" role="alert">
              ${message}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      `
    alertArea.style.display = "block"
  
    // Automatisch ausblenden nach 5 Sekunden
    setTimeout(() => {
      const alert = alertArea.querySelector(".alert")
      if (alert) {
        // Bootstrap wird hier verwendet, muss also importiert oder deklariert werden.
        const bsAlert = new bootstrap.Alert(alert) //bootstrap needs to be imported or declared.
        bsAlert.close()
      }
    }, 5000)
  }
  
  // Hilfsfunktion für AJAX-Anfragen
  async function fetchAPI(url, method = "GET", data = null) {
    const options = {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
    }
  
    if (data && (method === "POST" || method === "PUT" || method === "DELETE")) {
      options.body = JSON.stringify(data)
    }
  
    try {
      const response = await fetch(url, options)
      return await response.json()
    } catch (error) {
      console.error("API-Fehler:", error)
      return { success: false, message: "api_error" }
    }
  }
  
  // Hilfsfunktion zum Formatieren von Datum/Uhrzeit
  function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return ""
  
    const date = new Date(dateTimeStr)
    return date.toLocaleString()
  }
  
  