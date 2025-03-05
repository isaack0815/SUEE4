<?php
// Diese Datei wird in jeder Seite eingebunden, um gemeinsame Funktionen auszuführen

// Fehlermeldung aus der Session löschen, falls sie angezeigt wurde
if (isset($_SESSION['error_message']) && isset($_GET['clear_error'])) {
    unset($_SESSION['error_message']);
}

// Weitere gemeinsame Funktionen können hier hinzugefügt werden
?>

