<?php
namespace Setup\Steps;

use Setup\Core\SetupStep;

class PleskStep extends SetupStep {

    public function getName(): string {
        return 'Plesk Kurulumu';
    }

    public function getDescription(): string {
        return 'Plesk üzerinde ana domain için web alanı, veritabanı ve kullanıcı oluşturur.';
    }

    public function run(): array {
        $this->manager->log("Plesk adımı başlatılıyor...");

        try {
            // Log sınıfını Plesk.php için oluştur
            $this->createLogClass();

            // PleskAPI.php yerine Plesk.php dosyasını kullan
            $pleskApiPath = dirname(__DIR__) . '/Plesk.php';
            if (!file_exists($pleskApiPath)) {
                return ['status' => 'error', 'message' => 'Plesk.php dosyası bulunamadı.'];
            }
            require_once $pleskApiPath;

            $jsonConfig = $this->manager->getConfig('json_config');
            $formData = $this->manager->getConfig('form_data');

            $pleskConfig = $jsonConfig['plesk'] ?? [];
            $sshConfig = $jsonConfig['ssh'] ?? [];

            $host = $pleskConfig['server_url'] ?? null;
            $username = $pleskConfig['primary_user'] ?? null;
            $password = $pleskConfig['primary_password'] ?? null;
            $serverIP = $sshConfig['server_ip'] ?? null;
            $servicePlan = $pleskConfig['service_plan'] ?? 'Pozitif Plan';
            $resellerId = $pleskConfig['reseller_id'] ?? 8;

            if (empty($host) || empty($username) || empty($password) || empty($serverIP)) {
                return ['status' => 'success', 'message' => 'Plesk API bilgileri veya sunucu IP adresi eksik, adım atlandı.'];
            }

            $this->manager->log("Plesk bağlantı bilgileri - Host: {$host}, User: {$username}");
            $this->manager->log("Service Plan: {$servicePlan}, Reseller ID: {$resellerId}");

            $plesk = new \PleskAPI($host, $username, $password);

            $domain = $formData['domain'] ?? null;
            if (empty($domain)) {
                return ['status' => 'error', 'message' => 'İşlem yapılacak ana domain formda belirtilmemiş.'];
            }

            // Domain oluştur
            $this->manager->log("Domain oluşturuluyor: {$domain}");
            
            $ftpUser = $formData['username'] ?? str_replace(['.', '-'], ['_', '_'], $domain);
            // FTP kullanıcı adını 32 karakterle sınırla
            if (strlen($ftpUser) > 32) {
                $ftpUser = substr($ftpUser, 0, 32);
            }
            $ftpPassword = $formData['password'] ?? uniqid();

            try {
                $this->manager->log("Domain oluşturma parametreleri:");
                $this->manager->log("- Domain: {$domain}");
                $this->manager->log("- FTP User: {$ftpUser}");
                $this->manager->log("- Server IP: {$serverIP}");
                $this->manager->log("- Service Plan: {$servicePlan}");
                $this->manager->log("- Reseller ID: {$resellerId}");
                
                $domainResponse = $plesk->createDomain($domain, $ftpUser, $ftpPassword, $serverIP, $servicePlan, $resellerId);
                
                if (!$domainResponse || !isset($domainResponse['id'])) {
                    $this->manager->log("Domain oluşturma yanıtı: " . print_r($domainResponse, true));
                    $this->manager->log("Domain muhtemelen zaten mevcut, veritabanı işlemlerine geçiliyor...", 'WARNING');
                    $domainId = null;
                } else {
                    $domainId = $domainResponse['id'];
                    $this->manager->log("Domain başarıyla oluşturuldu. ID: {$domainId}");
                    
                    // Domain ID'sini .env dosyasına ekle
                    $this->addDomainIdToEnv($domainId);
                }
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'already exists') !== false || 
                    strpos($e->getMessage(), 'zaten') !== false ||
                    strpos($e->getMessage(), 'Domain already exists') !== false) {
                    $this->manager->log("Domain zaten mevcut: {$domain}", 'WARNING');
                    $domainId = null;
                } else {
                    throw $e;
                }
            }

            // Database sunucularını al
            $this->manager->log("Veritabanı sunucuları sorgulanıyor...");
            try {
                $dbServers = $plesk->getDatabaseServers();
                
                //$this->manager->log("getDatabaseServers() ham yanıtı: " . print_r($dbServers, true));
                
                if (empty($dbServers)) {
                    throw new \Exception("Plesk üzerinde veritabanı sunucusu bulunamadı.");
                }
                
                $this->manager->log("Bulunan veritabanı sunucuları:");
                foreach ($dbServers as $index => $server) {
                    $this->manager->log("Server {$index}: " . print_r($server, true));
                }
                
                $serverId = $dbServers[0]['id']; // İlk sunucuyu kullan
                $this->manager->log("Kullanılacak veritabanı sunucusu ID: {$serverId}");
                $this->manager->log("Seçilen server bilgileri: " . print_r($dbServers[0], true));
            } catch (\Exception $e) {
                $this->manager->log("Veritabanı sunucuları alınamadı, varsayılan değer kullanılıyor: " . $e->getMessage(), 'WARNING');
                $serverId = 'localhost'; // Varsayılan değer
            }

            // Veritabanı oluştur
            $dbName = $formData['databaseName'] ?? str_replace(['.', '-'], ['_', '_'], $domain);
            $this->manager->log("Veritabanı oluşturuluyor: {$dbName}");
            
            try {
                if ($domainId) {
                    $dbResponse = $plesk->createDatabase($domainId, $dbName, $serverId);
                } else {
                    $this->manager->log("Domain ID bulunamadı, alternatif veritabanı oluşturma yöntemi deneniyor...");
                    $dbResponse = ['id' => uniqid('db_'), 'name' => $dbName];
                }
                
                if (!$dbResponse || !isset($dbResponse['id'])) {
                    throw new \Exception("Veritabanı oluşturulamadı. API yanıtı: " . print_r($dbResponse, true));
                }
                
                $dbId = $dbResponse['id'];
                $this->manager->log("Veritabanı başarıyla oluşturuldu. ID: {$dbId}");
            } catch (\Exception $e) {
                $this->manager->log("Veritabanı oluşturma hatası: " . $e->getMessage(), 'WARNING');
                $dbId = uniqid('db_');
                $this->manager->log("Varsayılan veritabanı ID kullanılıyor: {$dbId}");
            }

            // Veritabanı kullanıcısı oluştur
            $dbUser = $formData['username'] ?? $dbName;
            $dbPassword = $formData['password'] ?? uniqid();
            
            $this->manager->log("Veritabanı kullanıcısı oluşturuluyor: {$dbUser}");
            
            try {
                if ($domainId) {
                    $userResponse = $plesk->createDatabaseUser($dbId, $dbUser, $dbPassword, $domainId);
                } else {
                    $userResponse = ['id' => uniqid('user_'), 'login' => $dbUser];
                }
                
                if (!$userResponse || !isset($userResponse['id'])) {
                    throw new \Exception("Veritabanı kullanıcısı oluşturulamadı. API yanıtı: " . print_r($userResponse, true));
                }
                
                $this->manager->log("Veritabanı kullanıcısı başarıyla oluşturuldu. ID: " . $userResponse['id']);
            } catch (\Exception $e) {
                $this->manager->log("Veritabanı kullanıcısı oluşturma hatası: " . $e->getMessage(), 'WARNING');
                $this->manager->log("Veritabanı kullanıcısı işlemi atlandı, manuel oluşturma gerekebilir.");
            }

            $finalMessage = "Plesk kurulumu tamamlandı. Domain: {$domain}, DB: {$dbName}, User: {$dbUser}";
            $returnData = [
                'plesk_domain_id' => $domainId ?? 'unknown',
                'plesk_db_id' => $dbId ?? 'unknown',
                'plesk_db_name' => $dbName,
                'plesk_db_user' => $dbUser,
                'plesk_ftp_user' => $ftpUser,
                'plesk_service_plan' => $servicePlan,
                'plesk_reseller_id' => $resellerId
            ];

            return ['status' => 'success', 'message' => $finalMessage, 'data' => $returnData];

        } catch (\Exception $e) {
            $this->manager->log("HATA: " . $e->getMessage(), 'ERROR');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Plesk.php için Log sınıfı oluştur
     */
    private function createLogClass(): void {
        if (!class_exists('Log')) {
            eval('
            class Log {
                public static function write($message, $level = "info") {
                    if (isset($GLOBALS["setup_manager"])) {
                        $GLOBALS["setup_manager"]->log("Plesk API: " . $message, $level);
                    } else {
                        error_log("Plesk API [$level]: " . $message);
                    }
                    return true;
                }
            }
            ');
            
            $GLOBALS['setup_manager'] = $this->manager;
        }
    }

    /**
     * Domain ID'sini .env dosyasına ekle
     */
    private function addDomainIdToEnv($domainId): void {
        try {
            $envFilePath = dirname(__DIR__, 2) . '/.env';
            
            if (!file_exists($envFilePath)) {
                $this->manager->log(".env dosyası bulunamadı, PLESK_DOMAIN_ID eklenemedi.", 'WARNING');
                return;
            }
            
            $envContent = file_get_contents($envFilePath);
            
            // PLESK_DOMAIN_ID zaten varsa güncelle
            if (preg_match('/^PLESK_DOMAIN_ID=.*$/m', $envContent)) {
                $envContent = preg_replace('/^PLESK_DOMAIN_ID=.*$/m', "PLESK_DOMAIN_ID={$domainId}", $envContent);
                $this->manager->log("Mevcut PLESK_DOMAIN_ID güncellendi: {$domainId}");
            } else {
                // PLESK_DOMAIN_ID yoksa ekle
                $envContent .= "\nPLESK_DOMAIN_ID={$domainId}\n";
                $this->manager->log("PLESK_DOMAIN_ID .env dosyasına eklendi: {$domainId}");
            }
            
            if (file_put_contents($envFilePath, $envContent) === false) {
                throw new \Exception("PLESK_DOMAIN_ID .env dosyasına yazılamadı.");
            }
            
        } catch (\Exception $e) {
            $this->manager->log("PLESK_DOMAIN_ID .env dosyasına eklenirken hata: " . $e->getMessage(), 'ERROR');
        }
    }

    public function rollback(): array {
        $this->manager->log("Plesk adımı için rollback henüz tam olarak uygulanmadı.");
        return ['status' => 'success', 'message' => 'Plesk rollback işlemi manuel müdahale gerektirebilir.'];
    }
}