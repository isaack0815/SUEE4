/**
 * Git Updater JavaScript
 */
document.addEventListener("DOMContentLoaded", () => {
    console.log("Git Updater JS geladen")
  
    // Elemente abrufen
    const checkUpdatesBtn = document.getElementById("checkUpdates")
    const updateForm = document.getElementById("updateForm")
    const repoUrlInput = document.getElementById("repo_url")
  
    // Prüfen, ob die Elemente existieren
    if (!checkUpdatesBtn) {
      console.error('Button "checkUpdates" nicht gefunden')
      return
    }
  
    if (!updateForm) {
      console.error('Formular "updateForm" nicht gefunden')
      return
    }
  
    if (!repoUrlInput) {
      console.error('Input "repo_url" nicht gefunden')
      return
    }
  
    console.log("Alle Elemente gefunden")
  
    // Einfache Übersetzungen direkt im JavaScript definieren
    // In einer vollständigen Implementierung würden diese aus einer externen Quelle geladen
    const translations = {
      check_for_updates: "Auf Updates prüfen",
      checking: "Prüfe...",
      repo_url_required: "Repository-URL ist erforderlich",
      confirm_update:
        "Möchten Sie das Update wirklich durchführen? Es wird ein Backup erstellt, bevor Änderungen vorgenommen werden.",
      apply_updates_now: "Möchten Sie die Updates jetzt anwenden?",
      error: "Fehler",
      unknown_error: "Unbekannter Fehler",
      network_error: "Netzwerkfehler",
    }
  
    // Event-Listener für "Updates prüfen"-Button
    checkUpdatesBtn.addEventListener("click", () => {
      console.log("Check Updates Button geklickt")
      checkForUpdates()
    })
  
    // Event-Listener für das Formular
    updateForm.addEventListener("submit", (e) => {
      if (!confirm(translations.confirm_update)) {
        e.preventDefault()
      }
    })
  
    /**
     * Prüft, ob Updates verfügbar sind
     */
    function checkForUpdates() {
      const repoUrl = repoUrlInput.value.trim()
  
      console.log("Prüfe Updates für Repository:", repoUrl)
  
      if (!repoUrl) {
        alert(translations.repo_url_required)
        return
      }
  
      // Button deaktivieren und Ladeindikator anzeigen
      checkUpdatesBtn.disabled = true
      checkUpdatesBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> ' + translations.checking
  
      // AJAX-Anfrage senden
      const formData = new FormData()
      formData.append("action", "check_updates")
      formData.append("repo_url", repoUrl)
  
      // Wenn ein Access-Token-Input existiert, diesen auch senden
      const accessTokenInput = document.getElementById("access_token")
      if (accessTokenInput) {
        formData.append("access_token", accessTokenInput.value)
      }
  
      console.log("Sende AJAX-Anfrage")
  
      fetch("api/git_updater.php", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => {
          console.log("Antwort erhalten:", response)
          if (!response.ok) {
            throw new Error("Netzwerkantwort war nicht ok")
          }
          return response.json()
        })
        .then((data) => {
          console.log("Daten erhalten:", data)
  
          // Button zurücksetzen
          checkUpdatesBtn.disabled = false
          checkUpdatesBtn.innerHTML = '<i class="fas fa-search me-1"></i> ' + translations.check_for_updates
  
          if (data.success) {
            // Erfolgreiche Antwort
            if (data.count > 0) {
              // Updates verfügbar
              const message = data.message + "\n\n" + translations.apply_updates_now
              if (confirm(message)) {
                updateForm.submit()
              }
            } else {
              // Keine Updates verfügbar
              alert(data.message)
            }
          } else {
            // Fehler
            alert(translations.error + ": " + (data.error || translations.unknown_error))
          }
        })
        .catch((error) => {
          console.error("Fehler bei der AJAX-Anfrage:", error)
  
          // Button zurücksetzen
          checkUpdatesBtn.disabled = false
          checkUpdatesBtn.innerHTML = '<i class="fas fa-search me-1"></i> ' + translations.check_for_updates
  
          // Fehler anzeigen
          alert(translations.error + ": " + translations.network_error)
        })
    }
  })
  
  