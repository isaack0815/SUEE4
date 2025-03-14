<?php

class GitHub
{
    private $repoOwner;
    private $repoName;
    private $personalAccessToken;
    private $logFile;
    private $backupDir;
    private $lastCommitFile;
    private $db;

    /**
     * Konstruktor
     * 
     * @param string $repoUrl Die URL des Git-Repositories (Format: https://github.com/owner/repo)
     * @param string $personalAccessToken GitHub Personal Access Token für API-Zugriff
     * @param Database $db Datenbankverbindung (wird aus init.php übergeben)
     */
    public function __construct($repoUrl, $personalAccessToken = '', $db = null)
    {
        // Repository-Informationen aus der URL extrahieren
        if (preg_match('/github\.com\/([^\/]+)\/([^\/\.]+)/', $repoUrl, $matches)) {
            $this->repoOwner = $matches[1];
            $this->repoName = $matches[2];
        } else {
            throw new Exception("Ungültiges Repository-Format. Erwartet wird: https://github.com/owner/repo");
        }
        
        $this->personalAccessToken = $personalAccessToken;
        $this->db = $db;
        
        // Pfade für Logs, Backups und Commit-Datei
        $rootPath = dirname(dirname(__FILE__)) . '/';
        $this->logFile = $rootPath . 'logs/git_updater.log';
        $this->backupDir = $rootPath . 'backups/';
        $this->lastCommitFile = $rootPath . 'config/last_commit.txt';
        
        // Verzeichnisse erstellen, falls sie nicht existieren
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
        
        if (!is_dir(dirname($this->lastCommitFile))) {
            mkdir(dirname($this->lastCommitFile), 0777, true);
        }
    }

    /**
     * Nachricht in die Log-Datei schreiben
     * 
     * @param string $message Die zu protokollierende Nachricht
     * @return string Die protokollierte Nachricht mit Zeitstempel
     */
    private function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "{$timestamp} - {$message}\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        return $logMessage;
    }

    /**
     * HTTP-Anfrage an die GitHub API senden
     * 
     * @param string $endpoint Der API-Endpunkt
     * @return array Die API-Antwort als Array
     */
    private function apiRequest($endpoint)
    {
        $url = "https://api.github.com" . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Git Updater');
        
        // Access Token für private Repositories hinzufügen
        if (!empty($this->personalAccessToken)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: token ' . $this->personalAccessToken
            ]);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $this->log("API-Fehler: HTTP-Code $httpCode für $url");
            $this->log("Antwort: $response");
            throw new Exception("GitHub API-Fehler: $httpCode");
        }
        
        return json_decode($response, true);
    }

    /**
     * Datei von einer URL herunterladen
     * 
     * @param string $url Die URL der herunterzuladenden Datei
     * @return string Der Inhalt der Datei
     */
    private function downloadFile($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Git Updater');
        
        // Access Token für private Repositories hinzufügen
        if (!empty($this->personalAccessToken)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: token ' . $this->personalAccessToken,
                'Accept: application/vnd.github.v3.raw'
            ]);
        }
        
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            $this->log("Download-Fehler: HTTP-Code $httpCode für $url");
            throw new Exception("Datei-Download-Fehler: $httpCode");
        }
        
        return $content;
    }

    /**
     * Letzten bekannten Commit-Hash abrufen
     * 
     * @return string Der letzte bekannte Commit-Hash oder leer, wenn keiner existiert
     */
    public function getLastCommit()
    {
        if (file_exists($this->lastCommitFile)) {
            return trim(file_get_contents($this->lastCommitFile));
        }
        return '';
    }

    /**
     * Letzten bekannten Commit-Hash speichern
     * 
     * @param string $commitHash Der zu speichernde Commit-Hash
     */
    private function saveLastCommit($commitHash)
    {
        file_put_contents($this->lastCommitFile, $commitHash);
    }

    /**
     * Backup des aktuellen Systems erstellen
     * 
     * @return string Der Pfad zum erstellten Backup
     */
    public function createBackup()
    {
        $backupFile = $this->backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.zip';
        $this->log("Erstelle Backup: $backupFile");
        
        $zip = new ZipArchive();
        if ($zip->open($backupFile, ZipArchive::CREATE) !== true) {
            $this->log("Fehler beim Erstellen des Backups");
            throw new Exception("Backup konnte nicht erstellt werden");
        }
        
        $rootPath = dirname(dirname(__FILE__));
        $this->addFolderToZip($zip, $rootPath, '');
        $zip->close();
        
        $this->log("Backup erfolgreich erstellt");
        return $backupFile;
    }

    /**
     * Rekursiv einen Ordner zu einem ZIP-Archiv hinzufügen
     * 
     * @param ZipArchive $zip Das ZIP-Archiv
     * @param string $folder Der hinzuzufügende Ordner
     * @param string $zipFolder Der Pfad innerhalb des ZIP-Archivs
     */
    private function addFolderToZip($zip, $folder, $zipFolder)
    {
        $dir = opendir($folder);
        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..' || $file == 'backups' || $file == 'logs' || $file == 'temp') {
                continue;
            }
            
            $filePath = $folder . '/' . $file;
            $zipPath = $zipFolder . ($zipFolder ? '/' : '') . $file;
            
            if (is_dir($filePath)) {
                $zip->addEmptyDir($zipPath);
                $this->addFolderToZip($zip, $filePath, $zipPath);
            } else {
                $zip->addFile($filePath, $zipPath);
            }
        }
        closedir($dir);
    }

    /**
     * Commits seit dem letzten bekannten Commit abrufen
     * 
     * @return array Liste der Commits
     */
    public function getNewCommits()
    {
        $lastCommit = $this->getLastCommit();
        $endpoint = "/repos/{$this->repoOwner}/{$this->repoName}/commits";
        
        $this->log("Rufe Commits ab für {$this->repoOwner}/{$this->repoName}");
        $commits = $this->apiRequest($endpoint);
        
        $newCommits = [];
        foreach ($commits as $commit) {
            if ($commit['sha'] === $lastCommit) {
                break;
            }
            $newCommits[] = $commit;
        }
        
        $this->log("Gefunden: " . count($newCommits) . " neue Commits");
        return $newCommits;
    }

    /**
     * Geänderte Dateien für einen Commit abrufen
     * 
     * @param string $commitSha Der Commit-Hash
     * @return array Liste der geänderten Dateien
     */
    public function getChangedFiles($commitSha)
    {
        $endpoint = "/repos/{$this->repoOwner}/{$this->repoName}/commits/{$commitSha}";
        $this->log("Rufe geänderte Dateien für Commit $commitSha ab");
        
        $commitData = $this->apiRequest($endpoint);
        $files = $commitData['files'];
        
        $changedFiles = [];
        foreach ($files as $file) {
            $changedFiles[] = [
                'filename' => $file['filename'],
                'status' => $file['status'], // added, modified, removed
                'raw_url' => isset($file['raw_url']) ? $file['raw_url'] : null
            ];
        }
        
        return $changedFiles;
    }

    /**
     * Datei aus dem Repository herunterladen
     * 
     * @param string $path Der Pfad zur Datei im Repository
     * @param string $commitSha Der Commit-Hash
     * @return string Der Inhalt der Datei
     */
    public function getFileContent($path, $commitSha)
    {
        $url = "https://raw.githubusercontent.com/{$this->repoOwner}/{$this->repoName}/{$commitSha}/{$path}";
        $this->log("Lade Datei herunter: $path (Commit: $commitSha)");
        
        return $this->downloadFile($url);
    }

    /**
     * Änderungen auf das lokale System anwenden
     * 
     * @param array $changedFiles Liste der geänderten Dateien
     * @param string $commitSha Der Commit-Hash
     * @return array Ergebnis der Anwendung
     */
    public function applyChanges($changedFiles, $commitSha)
    {
        $results = [
            'added' => [],
            'modified' => [],
            'removed' => [],
            'errors' => []
        ];
        
        $rootPath = dirname(dirname(__FILE__)) . '/';
        
        foreach ($changedFiles as $file) {
            $localPath = $rootPath . $file['filename'];
            $dirPath = dirname($localPath);
            
            try {
                switch ($file['status']) {
                    case 'added':
                    case 'modified':
                        // Verzeichnis erstellen, falls es nicht existiert
                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }
                        
                        // Dateiinhalt herunterladen und speichern
                        $content = $this->getFileContent($file['filename'], $commitSha);
                        file_put_contents($localPath, $content);
                        
                        if ($file['status'] === 'added') {
                            $results['added'][] = $file['filename'];
                        } else {
                            $results['modified'][] = $file['filename'];
                        }
                        break;
                        
                    case 'removed':
                        if (file_exists($localPath)) {
                            unlink($localPath);
                            $results['removed'][] = $file['filename'];
                        }
                        break;
                        
                    default:
                        $this->log("Unbekannter Dateistatus: {$file['status']} für {$file['filename']}");
                }
            } catch (Exception $e) {
                $this->log("Fehler beim Anwenden der Änderungen für {$file['filename']}: " . $e->getMessage());
                $results['errors'][] = [
                    'file' => $file['filename'],
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * SQL-Dateien ausführen
     * 
     * @param array $sqlFiles Liste der SQL-Dateien
     * @return array Ergebnis der SQL-Ausführung
     */
    public function executeSqlFiles($sqlFiles)
    {
        $results = [
            'success' => [],
            'errors' => []
        ];
        
        $rootPath = dirname(dirname(__FILE__)) . '/';
        
        foreach ($sqlFiles as $file) {
            $localPath = $rootPath . $file;
            
            if (file_exists($localPath) && pathinfo($localPath, PATHINFO_EXTENSION) === 'sql') {
                try {
                    $this->log("Führe SQL-Datei aus: $file");
                    $sql = file_get_contents($localPath);
                    
                    // SQL-Befehle aufteilen und ausführen
                    $queries = explode(';', $sql);
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            $this->db->query($query);
                        }
                    }
                    
                    $results['success'][] = $file;
                } catch (Exception $e) {
                    $this->log("Fehler beim Ausführen der SQL-Datei $file: " . $e->getMessage());
                    $results['errors'][] = [
                        'file' => $file,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }
        
        return $results;
    }

    /**
     * Update durchführen
     * 
     * @return array Ergebnis des Updates
     */
    public function update()
    {
        $result = [
            'success' => true,
            'message' => '',
            'commits' => [],
            'files' => [
                'added' => [],
                'modified' => [],
                'removed' => []
            ],
            'sql' => [
                'success' => [],
                'errors' => []
            ],
            'errors' => []
        ];
        
        try {
            // Neue Commits abrufen
            $newCommits = $this->getNewCommits();
            
            if (empty($newCommits)) {
                $result['message'] = 'Keine neuen Updates verfügbar.';
                return $result;
            }
            
            // Backup erstellen
            $backupFile = $this->createBackup();
            $result['backup'] = $backupFile;
            
            // Commits in umgekehrter Reihenfolge durchgehen (älteste zuerst)
            $newCommits = array_reverse($newCommits);
            
            foreach ($newCommits as $commit) {
                $commitSha = $commit['sha'];
                $commitMessage = $commit['commit']['message'];
                
                $this->log("Verarbeite Commit: $commitSha - $commitMessage");
                
                // Geänderte Dateien abrufen
                $changedFiles = $this->getChangedFiles($commitSha);
                
                // Änderungen anwenden
                $applyResult = $this->applyChanges($changedFiles, $commitSha);
                
                // SQL-Dateien identifizieren
                $sqlFiles = [];
                foreach ($applyResult['added'] as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                        $sqlFiles[] = $file;
                    }
                }
                foreach ($applyResult['modified'] as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                        $sqlFiles[] = $file;
                    }
                }
                
                // SQL-Dateien ausführen
                if (!empty($sqlFiles)) {
                    $sqlResult = $this->executeSqlFiles($sqlFiles);
                    $result['sql']['success'] = array_merge($result['sql']['success'], $sqlResult['success']);
                    $result['sql']['errors'] = array_merge($result['sql']['errors'], $sqlResult['errors']);
                }
                
                // Ergebnisse sammeln
                $result['commits'][] = [
                    'sha' => $commitSha,
                    'message' => $commitMessage,
                    'date' => $commit['commit']['author']['date'],
                    'author' => $commit['commit']['author']['name']
                ];
                
                $result['files']['added'] = array_merge($result['files']['added'], $applyResult['added']);
                $result['files']['modified'] = array_merge($result['files']['modified'], $applyResult['modified']);
                $result['files']['removed'] = array_merge($result['files']['removed'], $applyResult['removed']);
                $result['errors'] = array_merge($result['errors'], $applyResult['errors']);
                
                // Letzten Commit speichern
                $this->saveLastCommit($commitSha);
            }
            
            $result['message'] = count($newCommits) . ' Commits erfolgreich angewendet.';
            
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = 'Fehler beim Update: ' . $e->getMessage();
            $this->log("Update-Fehler: " . $e->getMessage());
        }
        
        return $result;
    }
}

