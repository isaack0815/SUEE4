/**
 * API-Anfrage senden
 *
 * @param {string} url URL der API
 * @param {string} method HTTP-Methode (GET, POST, PUT, DELETE)
 * @param {object} data Daten für die Anfrage
 * @returns {Promise<object>} Antwort der API
 */
async function fetchAPI(url, method = "GET", data = null) {
    const headers = { "Content-Type": "application/json" }
    const options = { method, headers }
  
    if (data) {
      options.body = JSON.stringify(data)
    }
  
    try {
      const response = await fetch(url, options)
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      return await response.json()
    } catch (error) {
      console.error("Error fetching data:", error)
      return { success: false, message: "fetch_error" }
    }
  }
  
  /**
   * Benachrichtigung anzeigen
   *
   * @param {string} message Nachricht
   * @param {string} type Typ der Benachrichtigung (info, success, warning, danger)
   */
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
  
  /**
   * Datum formatieren
   *
   * @param {string} dateTimeStr Datum als String
   * @returns {string} Formatiertes Datum
   */
  function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return "-"
  
    const date = new Date(dateTimeStr)
    if (isNaN(date.getTime())) return dateTimeStr
  
    return date.toLocaleDateString() + " " + date.toLocaleTimeString()
  }
  
  export { fetchAPI, showAlert, formatDateTime }
  
  