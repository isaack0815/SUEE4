document.addEventListener("DOMContentLoaded", () => {
    // Elemente abrufen
    const checkUpdatesBtn = document.getElementById("checkUpdates")
    const updateForm = document.getElementById("updateForm")
    const repoUrlInput = document.getElementById("repo_url")
    const accessTokenInput = document.getElementById("access_token")
  
    // Stelle sicher, dass die translations-Variable deklariert ist.
    // Hier wird angenommen, dass 'translations' global verfügbar ist oder von irgendwoher importiert wird.
    // Falls 'translations' von einem Modul importiert werden muss, füge hier die Import-Anweisung hinzu.
    // Beispiel: import translations from './translations';
    if (typeof translations === "undefined") {
      console.error("Die translations-Variable ist nicht deklariert.")
      return // Beende die Ausführung, wenn die Übersetzungen fehlen.
    }
  
    // Event-Listener für "Updates prüfen"-Button
    if (checkUpdatesBtn) {
      checkUpdatesBtn.addEventListener("click", () => {
        checkForUpdates()
      })
    }
  
    // Event-Listener für das Formular
    if (updateForm) {
      updateForm.addEventListener("submit", (e) => {
        if (!confirm(translations.git_updater_confirm_update)) {
          e.preventDefault()
        }
      })
    }
  
    /**
     * Prüft, ob Updates verfügbar sind
     */
    function checkForUpdates() {
      const repoUrl = repoUrlInput.value.trim()
  
      if (!repoUrl) {
        alert(translations.git_updater_repo_url_required)
        return
      }
  
      // Button deaktivieren und Ladeindikator anzeigen
      checkUpdatesBtn.disabled = true
      checkUpdatesBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> ' + translations.git_updater_checking
  
      // AJAX-Anfrage senden
      const formData = new FormData()
      formData.append("action", "check_updates")
      formData.append("repo_url", repoUrl)
      formData.append("access_token", accessTokenInput.value)
  
      fetch("api/git_updater.php", {
        method: "POST",
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.json())
        .then((data) => {
          // Button zurücksetzen
          checkUpdatesBtn.disabled = false
          checkUpdatesBtn.innerHTML = '<i class="fas fa-search me-1"></i> ' + translations.git_updater_check_updates
  
          if (data.success) {
            // Erfolgreiche Antwort
            if (data.count > 0) {
              // Updates verfügbar
              const message = data.message + "\n\n" + translations.git_updater_apply_updates_now
              if (confirm(message)) {
                updateForm.submit()
              }
            } else {
              // Keine Updates verfügbar
              alert(data.message)
            }
          } else {
            // Fehler
            alert(translations.git_updater_error + ": " + (data.error || translations.git_updater_unknown_error))
          }
        })
        .catch((error) => {
          // Button zurücksetzen
          checkUpdatesBtn.disabled = false
          checkUpdatesBtn.innerHTML = '<i class="fas fa-search me-1"></i> ' + translations.git_updater_check_updates
  
          // Fehler anzeigen
          console.error("Error:", error)
          alert(translations.git_updater_error + ": " + translations.git_updater_network_error)
        })
    }
  })
  
  