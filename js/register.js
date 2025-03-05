document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm")
  
    if (registerForm) {
      registerForm.addEventListener("submit", (event) => {
        let isValid = true
  
        // Benutzername validieren
        const username = document.getElementById("username")
        if (!username.value.trim()) {
          username.classList.add("is-invalid")
          isValid = false
        } else {
          username.classList.remove("is-invalid")
        }
  
        // E-Mail validieren
        const email = document.getElementById("email")
        if (!email.value.trim() || !isValidEmail(email.value.trim())) {
          email.classList.add("is-invalid")
          isValid = false
        } else {
          email.classList.remove("is-invalid")
        }
  
        // Passwort validieren
        const password = document.getElementById("password")
        if (!password.value || password.value.length < 8) {
          password.classList.add("is-invalid")
          isValid = false
        } else {
          password.classList.remove("is-invalid")
        }
  
        // Passwort-Bestätigung validieren
        const confirmPassword = document.getElementById("confirm_password")
        if (!confirmPassword.value || confirmPassword.value !== password.value) {
          confirmPassword.classList.add("is-invalid")
          isValid = false
        } else {
          confirmPassword.classList.remove("is-invalid")
        }
  
        if (!isValid) {
          event.preventDefault()
        }
      })
    }
  
    // E-Mail-Validierungsfunktion
    function isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    }
  })
  
  