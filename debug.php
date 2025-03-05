<?php
/**
 * Einfache Debugging-Funktionen
 */

/**
 * Schreibt eine Nachricht ins Fehlerprotokoll
 *
 * @param mixed $message Die Nachricht oder das Objekt, das protokolliert werden soll
 * @param string $level Das Loglevel (INFO, WARNING, ERROR)
 * @return void
 */
function debug_log($message, $level = 'INFO') {
    // Nachricht formatieren
    if (is_array($message) || is_object($message)) {
        $message = print_r($message, true);
    }
    
    // Zeitstempel hinzufügen
    $timestamp = date('Y-m-d H:i:s');
    
    // Backtrace hinzufügen
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];
    $file = isset($caller['file']) ? $caller['file'] : 'unknown';
    $line = isset($caller['line']) ? $caller['line'] : 'unknown';
    
    // Nachricht formatieren
    $logMessage = "[$timestamp] [$level] [$file:$line] $message\n";
    
    // In Datei schreiben
    $logFile = dirname(__FILE__) . '/debug.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Gibt eine Variable aus und beendet das Skript
 *
 * @param mixed $var Die Variable, die ausgegeben werden soll
 * @param bool $die Ob das Skript beendet werden soll (Standard: true)
 * @return void
 */
function debug_dump($var, $die = true) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    
    if ($die) {
        die("Debug-Ausgabe beendet");
    }
}

/**
 * Gibt den aktuellen Ausführungspunkt aus
 *
 * @param string $message Eine optionale Nachricht
 * @param bool $die Ob das Skript beendet werden soll (Standard: false)
 * @return void
 */
function debug_here($message = "Hier", $die = false) {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $caller = $backtrace[0];
    $file = isset($caller['file']) ? $caller['file'] : 'unknown';
    $line = isset($caller['line']) ? $caller['line'] : 'unknown';
    
    echo "<div style='background-color: #ffff00; padding: 5px; margin: 5px; border: 1px solid #ff0000;'>";
    echo "<strong>DEBUG:</strong> $message in $file on line $line";
    echo "</div>";
    
    if ($die) {
        die("Debug-Ausgabe beendet");
    }
}
?>

