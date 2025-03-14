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
    private $useToken = true;

    /**
     * Konstruktor
     * 
     * @param string $repoUrl Die URL des Git-Repositories (Format: https://github.com/owner/repo)
     * @param string $personalAccessToken GitHub Personal Access Token für API-Zugriff (optional)
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
        $this->useToken = !empty($personalAccessToken);
        
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
        
        // Prüfen, ob das Repository existiert
        $this->testRepositoryAccess();
    }
    
    /**
     * Prüft, ob das Repository existiert und zugänglich ist
     */
    private function testRepositoryAccess()
    {
        try {
            // Versuche, die Repository-Informationen abzurufen
            $this->apiRequest("/repos/{$this->repoOwner}/{$this->repoName}", false);
            $this->log("Repository ist zugänglich: {$this->repoOwner}/{$this->repoName}");
        } catch (Exception $e) {
            // Wenn der Fehler 401 ist und wir einen Token verwenden, versuchen wir es ohne Token
            if (strpos($e->getMessage(), '401') !== false && $this->useToken) {
                $this->log("Token-Authentifizierung fehlgeschlagen, versuche ohne Token");
                $this->useToken = false;
                
                try {
                    $this->apiRequest("/repos/{$this->repoOwner}/{$this->repoName}", false);
                    $this->log("Repository ist ohne Token zugänglich: {$this->repoOwner}/{$this->repoName}");
                } catch (Exception $e2) {
                    throw new Exception("Repository nicht zugänglich: {$this->repoOwner}/{$this->repoName}. Fehler: " . $e2->getMessage());
                }
            } else {
                throw $e;
            }
        }
    }
    
    /**
     * Nachricht in die Log-Datei schreiben
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
     * @param bool $useToken Ob der Token verwendet werden soll (falls vorhanden)
     * @return array Die API-Antwort als Array
     */
    private function apiRequest($endpoint, $useToken = true)
    {
        $url = "https://api.github.com" . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Git Updater');
        
        // Headers vorbereiten
        $headers = ['Accept: application/vnd.github.v3+json'];
        
        // Token hinzufügen, wenn vorhanden und gewünscht
        if ($useToken && $this->useToken && !empty($this->personalAccessToken)) {
            $headers[] = 'Authorization: token ' . $this->personalAccessToken;
            $this->log("API-Anfrage mit Token: $url");
        } else {
            $this->log("API-Anfrage ohne Token: $url");
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL-Fehler: " . $error);
        }
        
        curl_close($ch);
        
        $responseData = json_decode($response, true);
        
        // Fehlerbehandlung
        if ($httpCode !== 200) {
            $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Unbekannter Fehler';
            $this->log("API-Fehler: HTTP $httpCode, Nachricht: $errorMessage");
            
            if ($httpCode === 403 && strpos($response, 'rate limit exceeded') !== false) {
                throw new Exception("GitHub API Rate-Limit überschritten. Bitte versuchen Sie es später erneut oder verwenden Sie einen Personal Access Token.");
            }
            
            if ($httpCode === 404) {
                throw new Exception("Repository oder Ressource nicht gefunden: {$this->repoOwner}/{$this->repoName}");
            }
            
            if ($httpCode === 401 && $useToken && $this->useToken) {
                // Wenn wir einen Token verwenden und 401 bekommen, versuchen wir es ohne Token
                $this->log("Token-Authentifizierung fehlgeschlagen, versuche ohne Token");
                $this->useToken = false;
                return $this->apiRequest($endpoint, false);
            }
            
            // Für andere Fehler geben wir eine allgemeine Meldung zurück
            throw new Exception("GitHub API-Fehler ({$httpCode}): {$errorMessage}");
        }
        
        return $responseData;
    }

    /**
     * Datei von einer URL herunterladen
     */
    private function downloadFile($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Git Updater');
        
        // Headers vorbereiten
        $headers = ['Accept: application/vnd.github.v3.raw'];
        
        // Token hinzufügen, wenn vorhanden und aktiviert
        if ($this->useToken && !empty($this->personalAccessToken)) {
            $headers[] = 'Authorization: token ' . $this->personalAccessToken;
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($content === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Download-Fehler: " . $error);
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("Datei-Download-Fehler: HTTP {$httpCode}");
        }
        
        return $content;
    }

    /**
     * Letzten bekannten Commit-Hash abrufen
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
     */
    private function saveLastCommit($commitHash)
    {
        file_put_contents($this->lastCommitFile, $commitHash);
    }

    /**
     * Backup des aktuellen Systems erstellen
     */
    public function createBackup()
    {
        $backupFile = $this->backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.zip';
        $this->log("Erstelle Backup: $backupFile");
        
        $zip = new ZipArchive();
        if ($zip->open($backupFile, ZipArchive::CREATE) !== true) {
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
     */
    public function getNewCommits()
    {
        $lastCommit = $this->getLastCommit();
        $endpoint = "/repos/{$this->repoOwner}/{$this->repoName}/commits";
        
        $this->log("Rufe Commits ab für {$this->repoOwner}/{$this->repoName}");
        
        try {
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
            
        } catch (Exception $e) {
            // Wenn es ein Rate-Limit-Problem ist, geben wir eine spezifischere Meldung aus
            if (strpos($e->getMessage(), 'rate limit exceeded') !== false) {
                throw new Exception(
                    "GitHub API Rate-Limit überschritten. " .
                    "Bitte versuchen Sie es später erneut oder verwenden Sie einen Personal Access Token. " .
                    "Öffentliche Repositories sind auf 60 Anfragen pro Stunde limitiert."
                );
            }
            throw $e;
        }
    }

    /**
     * Geänderte Dateien für einen Commit abrufen
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
                'status' => $file['status'],
                'raw_url' => isset($file['raw_url']) ? $file['raw_url'] : null
            ];
        }
        
        return $changedFiles;
    }

    /**
     * Datei aus dem Repository herunterladen
     */
    public function getFileContent($path, $commitSha)
    {
        $url = "https://raw.githubusercontent.com/{$this->repoOwner}/{$this->repoName}/{$commitSha}/{$path}";
        $this->log("Lade Datei herunter: $path (Commit: $commitSha)");
        
        return $this->downloadFile($url);
    }

    /**
     * Änderungen auf das lokale System anwenden
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
                        if (!file_exists($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }
                        
                        $content = $this->getFileContent($file['filename'], $commitSha);
                        if (file_put_contents($localPath, $content) === false) {
                            throw new Exception("Konnte Datei nicht schreiben: " . $file['filename']);
                        }
                        
                        if ($file['status'] === 'added') {
                            $results['added'][] = $file['filename'];
                        } else {
                            $results['modified'][] = $file['filename'];
                        }
                        break;
                        
                    case 'removed':
                        if (file_exists($localPath)) {
                            if (!unlink($localPath)) {
                                throw new Exception("Konnte Datei nicht löschen: " . $file['filename']);
                            }
                            $results['removed'][] = $file['filename'];
                        }
                        break;
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
     */
    public function executeSqlFiles($sqlFiles)
    {
        if (!$this->db) {
            throw new Exception("Keine Datenbankverbindung verfügbar");
        }
        
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

