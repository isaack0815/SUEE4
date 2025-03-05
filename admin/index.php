<?php
// Relativen Pfad zum Hauptverzeichnis bestimmen
$rootPath = dirname(__FILE__) . '/../';



// Direktes Einbinden der Klassen, falls der Autoloader fehlschlägt
$classFiles = [
  $rootPath . 'classes/Database.php',
  $rootPath . 'classes/Language.php',
  $rootPath . 'classes/User.php',
  $rootPath . 'classes/Group.php',
  $rootPath . 'classes/Menu.php'
];

foreach ($classFiles as $file) {
  if (file_exists($file)) {
      require_once $file;
  } else {
      die("Fehler: Datei $file nicht gefunden.");
  }
}

// Dann erst init.php laden
require_once $rootPath . 'init.php';

// Prüfen, ob Benutzer eingeloggt ist und Admin-Rechte hat
if (!$user->isLoggedIn() || !$user->isAdmin()) {
  header('Location: ../index.php');
  exit;
}

// Menü laden
$menu = new Menu();
$adminMenu = $menu->getMenuItems();

// Menü an Smarty übergeben
$smarty->assign('adminMenu', $adminMenu);

// Debug-Ausgabe
error_log("Versuche Template zu laden: admin/index.tpl");
error_log("Aktuelles Template-Verzeichnis: " . $smarty->getTemplateDir()[0]);

// Überprüfen, ob die Template-Datei existiert
$templateFile = $smarty->getTemplateDir()[0] . 'admin/index.tpl';
if (!file_exists($templateFile)) {
  error_log("Template-Datei nicht gefunden: $templateFile");
  
  // Versuchen, das Verzeichnis zu erstellen
  $adminTemplateDir = $smarty->getTemplateDir()[0] . 'admin';
  if (!is_dir($adminTemplateDir)) {
      if (mkdir($adminTemplateDir, 0755, true)) {
          error_log("Verzeichnis erstellt: $adminTemplateDir");
      } else {
          error_log("Konnte Verzeichnis nicht erstellen: $adminTemplateDir");
      }
  }
  
  // Einfaches Template erstellen, falls es nicht existiert
  $simpleTemplate = <<<EOT
{include file="admin/header.tpl" title="Admin Dashboard"}

<div class="row">
  <div class="col-md-12">
      <div class="card shadow">
          <div class="card-header bg-primary text-white">
              <h4 class="mb-0">Admin Dashboard</h4>
          </div>
          <div class="card-body">
              <div class="alert alert-info">
                  <h5>Willkommen im Administrationsbereich</h5>
                  <p>Hier können Sie Benutzer, Gruppen und Einstellungen verwalten.</p>
              </div>
          </div>
      </div>
  </div>
</div>

{include file="admin/footer.tpl"}
EOT;
  
  file_put_contents($templateFile, $simpleTemplate);
  error_log("Einfaches Template erstellt: $templateFile");
}

// Überprüfen, ob die Header- und Footer-Templates existieren
$headerFile = $smarty->getTemplateDir()[0] . 'admin/header.tpl';
$footerFile = $smarty->getTemplateDir()[0] . 'admin/footer.tpl';

// Seite anzeigen
try {
  $smarty->display('admin/index.tpl');
} catch (Exception $e) {
  echo "<h1>Fehler beim Laden des Templates</h1>";
  echo "<p>Fehlermeldung: " . $e->getMessage() . "</p>";
  echo "<p>In Datei: " . $e->getFile() . " in Zeile " . $e->getLine() . "</p>";
  
  // Verzeichnisstruktur anzeigen
  echo "<h2>Verzeichnisstruktur</h2>";
  echo "<pre>";
  echo "Template-Verzeichnis: " . $smarty->getTemplateDir()[0] . "\n";
  echo "Compile-Verzeichnis: " . $smarty->getCompileDir() . "\n";
  echo "Cache-Verzeichnis: " . $smarty->getCacheDir() . "\n";
  echo "Config-Verzeichnis: " . $smarty->getConfigDir() . "\n";
  echo "</pre>";
  
  // Dateien im Template-Verzeichnis anzeigen
  echo "<h2>Dateien im Template-Verzeichnis</h2>";
  echo "<pre>";
  $templateDir = $smarty->getTemplateDir()[0];
  if (is_dir($templateDir)) {
      $files = scandir($templateDir);
      foreach ($files as $file) {
          if ($file != '.' && $file != '..') {
              echo $file . "\n";
          }
      }
  } else {
      echo "Verzeichnis nicht gefunden: $templateDir";
  }
  echo "</pre>";
}

// Output Buffer leeren und beenden
ob_end_flush();
?>