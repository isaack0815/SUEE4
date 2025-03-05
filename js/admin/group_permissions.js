document.addEventListener("DOMContentLoaded", () => {
    // DOM-Elemente
    const permissionsForm = document.getElementById("permissionsForm");
    const savePermissionsBtn = document.getElementById("savePermissionsBtn");
    const groupId = document.getElementById("groupId").value;

    // Event-Listener
    savePermissionsBtn.addEventListener("click", savePermissions);

    // Funktionen
    async function savePermissions() {
        // Ausgewählte Berechtigungen sammeln
        const permissionCheckboxes = document.querySelectorAll(".permission-checkbox:checked");
        const permissionIds = Array.from(permissionCheckboxes).map(checkbox => checkbox.value);

        // Daten vorbereiten
        const data = {
            group_id: groupId,
            permission_ids: permissionIds
        };

        // API-Anfrage senden
        const response = await fetchAPI("../admin/api/permissions.php", "POST", data);

        if (response.success) {
            showAlert("Berechtigungen wurden erfolgreich zugewiesen.");
        } else {
            showAlert("Fehler beim Zuweisen der Berechtigungen: " + response.message, "danger");
        }
    }
});