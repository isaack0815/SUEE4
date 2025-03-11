document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".settings-form").forEach((form) => {
      form.addEventListener("submit", (e) => {
        e.preventDefault()
        var formData = new FormData(form)
        fetch(form.getAttribute("data-api-endpoint"), {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            var messageDiv = document.getElementById("settingsMessages")
            messageDiv.innerHTML =
              '<div class="alert alert-' + (data.success ? "success" : "danger") + '">' + data.message + "</div>"
            messageDiv.scrollIntoView({ behavior: "smooth" })
          })
          .catch((error) => {
            console.error("Error:", error)
            var messageDiv = document.getElementById("settingsMessages")
            messageDiv.innerHTML =
              '<div class="alert alert-danger">Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.</div>'
            messageDiv.scrollIntoView({ behavior: "smooth" })
          })
      })
    })
  })
  
  