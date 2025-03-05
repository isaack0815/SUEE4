document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm")
  
    if (loginForm) {
      loginForm.addEventListener("submit", (event) => {
        let isValid = true
  
        // Benutzername validieren
        const username = document.getElementById("username")
        if (!username.value.trim()) {
          username.classList.add("is-invalid")
          isValid = false
        } else {
          username.classList.remove("is-invalid")
        }
  
        // Passwort validieren
        const password = document.getElementById("password")
        if (!password.value) {
          password.classList.add("is-invalid")
          isValid = false
        } else {
          password.classList.remove("is-invalid")
        }
  
        if (!isValid) {
          event.preventDefault()
        }
      })
    }
  })
  
  