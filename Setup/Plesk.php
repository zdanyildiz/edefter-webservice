<?php

class PleskAPI {
    private $serverUrl;
    private $username;
    private $password;

    public function __construct($serverUrl, $username, $password) {
        $this->serverUrl = rtrim($serverUrl, '/') . '/api/v2';
        $this->username = $username;
        $this->password = $password;
    }

    private function sendRequest($endpoint, $method = 'GET', $data = null) {
        $ch = curl_init();

        $url = $this->serverUrl . $endpoint;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [
            "Authorization: Basic " . base64_encode("$this->username:$this->password"),
            "Accept: application/json"
        ];

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = "Content-Type: application/json";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception("Curl error: " . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decodedResult = json_decode($result, true);

        if ($httpCode >= 400) {
            Log::write("PleskAPI,API Error (HTTP $httpCode): " . $result. ", endpoint: $endpoint, data: ". json_encode($data), "error");
            throw new Exception("API Error (HTTP $httpCode): " . $result);
        }

        return $decodedResult;
    }

    public function executeCliCommand($command, $params = []) {
        return $this->sendRequest("/cli/call", "POST", [
            "command" => $command,
            "params" => $params
        ]);
    }

    public function cloneGitRepository($domain, $repositoryUrl, $branch = 'main') {
        $targetPath = "/var/www/vhosts/$domain/httpdocs";

        // Clear the target directory first
        $this->executeCliCommand("rm", ["-rf", "$targetPath/*"]);

        // Perform git clone
        return $this->executeCliCommand("git", [
            "clone",
            "--branch", $branch,
            $repositoryUrl,
            $targetPath
        ]);
    }

    public function updateGitRepository($domain) {
        $targetPath = "/var/www/vhosts/$domain/httpdocs";

        return $this->executeCliCommand("git", [
            "-C",
            $targetPath,
            "pull"
        ]);
    }


    public function createDomain($domainName, $ftpUser, $ftpPassword, $ipAddress, $planName, $resellerId) {

        //$ftpUser uzunluğunu max 32 karakter yapalım
        if (strlen($ftpUser) > 32) {
            $ftpUser = substr($ftpUser, 0, 32);
        }
        $data = [
            "name" => $domainName,
            "hosting_type" => "virtual",
            "hosting_settings" => [
                "ftp_login" => $ftpUser,
                "ftp_password" => $ftpPassword
            ],
            "ip_addresses" => [$ipAddress],
            "plan" => ["name" => $planName],
            "owner_client" => ["id" => $resellerId]
        ];

        return $this->sendRequest('/domains', 'POST', $data);
    }

    public function updateFtpUser($ftpUser, $ftpPassword, $homeDirectory = "/", $quota = -1, $permissions = ["read" => true, "write" => true])
    {
        $data = [
            "name" => $ftpUser,
            "password" => $ftpPassword,
            "home" => $homeDirectory,
            "quota" => $quota,
            "permissions" => $permissions
        ];

        $endpoint = "/ftpusers/" . $ftpUser; // FTP kullanıcı adı path'te olmalı

        return $this->sendRequest($endpoint, 'PUT', $data);
    }


    public function createDatabase($domainId, $dbName, $serverId) {
        $data = [
            "name" => $dbName,
            "type" => "mysql",
            "parent_domain" => ["id" => $domainId],
            "server_id" => $serverId
        ];

        return $this->sendRequest('/databases', 'POST', $data);
    }

    public function createDatabaseUser($dbId, $dbUser, $dbPassword, $domainId) {
        $data = [
            "login" => $dbUser,
            "password" => $dbPassword,
            "database_id" => $dbId,
            "parent_domain" => ["id" => $domainId]
        ];

        return $this->sendRequest('/dbusers', 'POST', $data);
    }

    public function getServerMetaInfo() {
        return $this->sendRequest('/server');
    }

    public function getDatabaseServers() {
        return $this->sendRequest('/dbservers');
    }

    public function addGitRepository($domainName, $repositoryUrl, $branch = 'main', $deploymentType = 'auto') {
        // Git API endpoint'i için tam URL oluştur
        $endpoint = $this->serverUrl . '/api/v2/domains/' . urlencode($domainName) . '/git';

        $data = [
            "repository" => [
                "url" => $repositoryUrl,
                "branch" => $branch
            ],
            "deployment" => [
                "type" => $deploymentType
            ]
        ];

        return $this->sendRequest($endpoint, 'POST', $data);
    }

    public function cloneRepository($domainName, $repositoryUrl, $branch = 'main') {
        // Önce domain bilgilerini al
        $domainInfo = $this->getDomainInfo($domainName);

        if (!$domainInfo) {
            throw new Exception("Domain not found: $domainName");
        }

        $data = [
            "repository" => [
                "url" => $repositoryUrl,
                "branch" => $branch
            ],
            "deployment" => [
                "type" => "auto"
            ]
        ];

        $endpoint = "/domains/" . urlencode($domainName) . "/git/repositories";
        return $this->sendRequest($endpoint, 'POST', $data);
    }

    // SSH kullanarak git clone örneği
    function cloneRepositoryViaSSH($domain, $repositoryUrl, $sshUsername, $sshPassword) {
        $connection = ssh2_connect('plesk.globalpozitif.com.tr', 22);
        if (!$connection) throw new Exception("SSH connection failed");

        if (!ssh2_auth_password($connection, $sshUsername, $sshPassword)) {
            throw new Exception("SSH authentication failed");
        }

        $targetPath = "/var/www/vhosts/$domain/httpdocs";
        $command = "git clone $repositoryUrl $targetPath";

        $stream = ssh2_exec($connection, $command);
        if (!$stream) throw new Exception("Failed to execute command");

        stream_set_blocking($stream, true);
        $output = stream_get_contents($stream);

        return $output;
    }

    public function getDomainInfo($domainName) {
        return $this->sendRequest("/domains/" . urlencode($domainName));
    }

    public function getFtpUserInfo($domain, $ftpUserName) {
        // Önce /ftpusers?domain=example.com&name=ftpusername şeklinde istek atıyoruz
        $endpoint = '/ftpusers?domain=' . urlencode($domain) . '&name=' . urlencode($ftpUserName);
        $ftpUsers = $this->sendRequest($endpoint, 'GET');

        if (empty($ftpUsers)) {
            throw new Exception("No FTP user found for domain '$domain' with name '$ftpUserName'");
        }

        // varsayılan olarak ilk eşleşen kullanıcıyı alıyoruz
        $ftpUserInfo = $ftpUsers[0];

        // ftpUserInfo içerisindeki permissions alanını kontrol edebiliriz
        // permissions yapısı:
        // "permissions": {
        //   "write": "true" or "false",
        //   "read": "true" or "false"
        // }

        // Okuma/yazma yetkilerini öğrenmek:
        $hasRead = (isset($ftpUserInfo['permissions']['read']) && $ftpUserInfo['permissions']['read'] === 'true');
        $hasWrite = (isset($ftpUserInfo['permissions']['write']) && $ftpUserInfo['permissions']['write'] === 'true');

        // İsterseniz burada ek bir işlem yapabilir, ya da sadece bilgileri döndürebilirsiniz.
        return [
            'user' => $ftpUserName,
            'domain' => $domain,
            'home' => $ftpUserInfo['home'] ?? null,
            'quota' => $ftpUserInfo['quota'] ?? null,
            'hasReadPermission' => $hasRead,
            'hasWritePermission' => $hasWrite,
            'raw' => $ftpUserInfo // Tüm orijinal data
        ];
    }

    public function createFtpUser($domainName, $ftpUserName, $ftpPassword, $home = "/httpdocs", $quota = -1, $read = true, $write = true) {
        // Önce domain bilgilerini çekelim
        $domainInfo = $this->getDomainInfo($domainName);
        if (!$domainInfo || !isset($domainInfo['id'])) {
            throw new Exception("Domain '$domainName' bulunamadı veya ID alınamadı.");
        }

        // Yetkileri API'nin istediği formatta belirleyelim ('true'/'false' string olarak)
        $permissions = [
            "read" => $read ? "true" : "false",
            "write" => $write ? "true" : "false"
        ];

        // FTP kullanıcı oluşturma için gerekli JSON verisini oluşturalım
        // Aşağıda parent_domain bilgisi domain'e göre dolduruluyor.
        $data = [
            "name" => $ftpUserName,
            "password" => $ftpPassword,
            "home" => $home,
            "quota" => $quota,
            "permissions" => $permissions,
            "parent_domain" => [
                "id" => $domainInfo['id'],
                "name" => $domainInfo['name'],
                "guid" => $domainInfo['guid']
            ]
        ];

        // POST isteğini /ftpusers endpoint'ine yapıyoruz
        return $this->sendRequest('/ftpusers', 'POST', $data);
    }

    public function checkDnsRecord($domainName) {
        try {
            // DNS kayıtlarını sorgula
            $endpoint = '/dns/records?domain=' . urlencode($domainName);
            $response = $this->sendRequest($endpoint, 'GET');

            // Eğer kayıt varsa ve boş değilse true döndür
            if (isset($response) && !empty($response)) {
                return [
                    'exists' => true,
                    'records' => $response
                ];
            }

            return [
                'exists' => false,
                'records' => []
            ];

        } catch (Exception $e) {
            // Eğer kayıt bulunamazsa veya başka bir hata olursa
            if (strpos($e->getMessage(), '404') !== false) {
                return [
                    'exists' => false,
                    'records' => []
                ];
            }
            // Diğer hataları yukarı fırlat
            throw $e;
        }
    }

    // DNS kaydını silmek için yardımcı metot
    public function deleteDnsRecord($domainName) {
        $dnsCheck = $this->checkDnsRecord($domainName);

        if ($dnsCheck['exists']) {
            $endpoint = '/dns/records?domain=' . urlencode($domainName);
            return $this->sendRequest($endpoint, 'DELETE');
        }

        return false;
    }

}

class PleskSSHHelper
{
    private $serverUrl;
    private $username;
    private $password;

    public function __construct($serverUrl, $username, $password)
    {
        // Plesk API URL'sini doğru port ile ayarlayın (genellikle 8443)
        // Örnek: https://plesk.sunucu.com:8443
        $this->serverUrl = rtrim($serverUrl, '/') . '/api/v2';
        $this->username = $username;
        $this->password = $password;
    }

    // sendRequest metodu aynı kalabilir
    private function sendRequest($endpoint, $method = 'GET', $data = null)
    {
        $ch = curl_init();

        $url = $this->serverUrl . $endpoint;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Timeout süresini artırmak faydalı olabilir
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [
            "Authorization: Basic " . base64_encode("$this->username:$this->password"),
            "Accept: application/json" // Accept header'ı eklemek iyi bir pratik
        ];

        if ($data !== null) { // Veri varsa Content-Type ekle
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = "Content-Type: application/json";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Geliştirme ortamı için, prodüksiyonda true yapıp sertifika doğrulayın
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Geliştirme ortamı için, prodüksiyonda 2 yapın

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch); // Curl hatasını al

        curl_close($ch);

        if ($curlError) { // Önce curl hatasını kontrol et
            Log::write("PleskSSHHelper Curl Error: " . $curlError . ", endpoint: $endpoint, data: " . json_encode($data), "error");
            throw new Exception("Curl Error: " . $curlError);
        }

        $decodedResult = json_decode($result, true);

        // HTTP 200 veya 202 (bazı CLI komutları için) başarılı kabul edilebilir
        if ($httpCode >= 400) {
            // Hata mesajını log'a daha detaylı yazdır
            $errorMessage = $result; // Ham yanıtı logla
            if ($decodedResult && isset($decodedResult['message'])) {
                $errorMessage = $decodedResult['message'];
            } elseif ($decodedResult && isset($decodedResult['err_message'])) {
                $errorMessage = $decodedResult['err_message'];
            }
            Log::write("PleskSSHHelper API Error (HTTP $httpCode): " . $errorMessage . ", endpoint: $endpoint, data: " . json_encode($data), "error");
            // Exception mesajına da API'den gelen mesajı ekle
            throw new Exception("API Error (HTTP $httpCode): " . $errorMessage);
        }

        // Başarılı yanıtı veya CLI çıktısını döndür
        return $decodedResult ?? ['stdout' => $result, 'stderr' => '', 'code' => $httpCode]; // CLI yanıtları JSON olmayabilir
    }


    // getDomainInfo metodu aynı kalabilir
    public function getDomainInfo($domain)
    {
        try {
            $endpoint = '/domains?name=' . urlencode($domain);
            $domains = $this->sendRequest($endpoint);
            if (!empty($domains)) {
                // Tam eşleşme arayalım
                foreach ($domains as $d) {
                    if (isset($d['name']) && strtolower($d['name']) === strtolower($domain)) {
                        return $d;
                    }
                }
            }
            Log::write("PleskSSHHelper: Domain not found via API for '$domain'", "warning");
            return null; // Domain bulunamadı
        } catch (Exception $e) {
            // 404 hatası domainin bulunmadığı anlamına gelebilir, diğer hataları fırlat
            if (strpos($e->getMessage(), '404') !== false) {
                Log::write("PleskSSHHelper: Domain not found (404) for '$domain'. Error: " . $e->getMessage(), "warning");
                return null;
            }
            Log::write("PleskSSHHelper: Error getting domain info for '$domain'. Error: " . $e->getMessage(), "error");
            throw $e; // Diğer hataları tekrar fırlat
        }
    }

    public function getSystemUser($domain)
    {
        $domainInfo = $this->getDomainInfo($domain);
        if (!$domainInfo || !isset($domainInfo['hosting_settings']['ftp_login'])) {
            Log::write("PleskSSHHelper: Could not retrieve system user (ftp_login) for domain '$domain'", "error");
            // Varsayılan veya hata durumu
            // throw new Exception("System user (ftp_login) could not be retrieved for domain: $domain");
            // Geçici olarak eski davranışa dönülebilir veya hata fırlatılabilir
            Log::write("PleskSSHHelper: Falling back to default system user 'psaserv' for domain '$domain'", "warning");
            return ['username' => 'psaserv', 'group' => 'psacln']; // VEYA 'psaserv' / 'psaserv' olabilir, Plesk yapılandırmasına bağlı
        }

        // Genellikle FTP kullanıcısı aynı zamanda sistem kullanıcısıdır.
        $systemUsername = $domainInfo['hosting_settings']['ftp_login'];
        // Grup genellikle 'psacln' olur.
        $systemGroup = 'psacln';

        Log::write("PleskSSHHelper: System user for domain '$domain' determined as '$systemUsername:$systemGroup'", "info");
        return [
            'username' => $systemUsername,
            'group' => $systemGroup
        ];
    }

    // changeOwner metodunu filemng kullanacak şekilde güncelle
    public function changeOwner($domain, $path, $owner, $group)
    {
        $systemUser = $this->getSystemUser($domain); // Domain'in sistem kullanıcısını al
        if (empty($systemUser['username'])) {
            Log::write("PleskSSHHelper: Cannot change owner, system user for domain '$domain' is empty.", "error");
            throw new Exception("System user could not be determined for domain $domain");
        }

        // filemng komutunu ve parametrelerini hazırla
        // filemng <system_user> chown <path> <owner>:<group> [-R]
        $command = "filemng";
        $params = [
            $systemUser['username'], // Sistem kullanıcısı
            "chown",                 // chown alt komutu
            $path,                   // Değiştirilecek yol
            "$owner:$group",         // Yeni sahip:grup
            "-R"                     // Recursive flag
        ];

        Log::write("PleskSSHHelper: Attempting to change owner via API. Command: $command, Params: " . json_encode($params), "info");

        // callCliCommand ile filemng komutunu çalıştır
        return $this->callCliCommand($command, $params);
    }

    // callCliCommand metodu aynı kalabilir, sadece command'ın doğru geldiğinden emin olunmalı
    private function callCliCommand($command, $params = [])
    {
        $endpoint = "/cli/$command/call"; // Komut adını endpoint'e ekleyebiliriz ya da data içinde gönderebiliriz. Plesk versiyonuna göre değişebilir.
        // Alternatif: endpoint = "/cli/call"; $data = ["command" => $command, "params" => $params];

        // Plesk API'sinin /cli/{utility}/call yapısını deneyelim
        $data = [
            "params" => $params
        ];
        Log::write("PleskSSHHelper: Calling API endpoint: $endpoint with data: " . json_encode($data), "info");
        return $this->sendRequest($endpoint, "POST", $data);

        /* // Alternatif: /cli/call endpoint'i ile
         $endpoint = "/cli/call";
         $data = [
             "command" => $command,
             "params" => $params
         ];
         Log::write("PleskSSHHelper: Calling API endpoint: $endpoint with data: " . json_encode($data), "info");
         return $this->sendRequest($endpoint, "POST", $data);
        */
    }

    public function getServerInfo() {
        return $this->sendRequest('/server');
    }

    public function createSSHUser($username, $password, $domain) {
        // FTP kullanıcısı oluştur (SSH erişimi için)
        $data = [
            "name" => $username,
            "password" => $password,
            "home" => "/var/www/vhosts/$domain",
            "shell" => "/bin/bash",
            "parent_domain" => ["name" => $domain]
        ];

        return $this->sendRequest('/ftpusers', 'POST', $data);
    }
}


// Kullanım örneği
class GitDeployer {
    private $sshHelper;
    private $domain;

    public function __construct($pleskUrl, $pleskAdmin, $pleskPassword, $domain) {
        $this->sshHelper = new PleskSSHHelper($pleskUrl, $pleskAdmin, $pleskPassword);
        $this->domain = $domain;
    }

    public function setupAndDeploy($repositoryUrl, $branch = 'main') {
        try {
            // System user bilgilerini al
            $systemUser = $this->sshHelper->getSystemUser($this->domain);

            // SSH bağlantısı için phpseclib kullan
            require_once '../vendor/autoload.php';
            $ssh = new \phpseclib3\Net\SSH2('plesk.globalpozitif.com.tr');

            if (!$ssh->login($systemUser['username'], 'your-system-user-password')) {
                throw new Exception('SSH login failed');
            }

            // Git repository'yi klonla
            $targetPath = "/var/www/vhosts/{$this->domain}/httpdocs";
            $command = "git clone --branch $branch $repositoryUrl $targetPath";

            $result = $ssh->exec($command);

            return [
                'status' => 'success',
                'output' => $result
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}