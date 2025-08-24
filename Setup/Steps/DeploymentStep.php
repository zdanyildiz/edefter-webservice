<?php

namespace Setup\Steps;

use Setup\Core\SetupStep;
use phpseclib3\Net\SSH2;

class DeploymentStep extends SetupStep {
    private $ssh;

    public function getName(): string {
        return 'Uzak Sunucuya Yükleme';
    }

    public function getDescription(): string {
        return 'Projeyi Git üzerinden klonlar, Composer bağımlılıklarını kurar ve gerekli dosyaları FTP ile yükler.';
    }

    private function getSshConnection() {
        if ($this->ssh) {
            return $this->ssh;
        }

        $jsonConfig = $this->manager->getConfig('json_config');
        $sshConfig = $jsonConfig['ssh'] ?? [];
        
        $serverIP = $sshConfig['server_ip'] ?? null;
        $sshUser = $sshConfig['username'] ?? null;
        $sshPassword = $sshConfig['password'] ?? null;

        if (empty($serverIP) || empty($sshUser) || empty($sshPassword)) {
            throw new \Exception("SSH bağlantı bilgileri JSON config'de eksik.");
        }

        $ssh = new SSH2($serverIP);
        if (!$ssh->login($sshUser, $sshPassword)) {
            throw new \Exception('SSH bağlantısı başarısız. Kullanıcı adı/şifreyi kontrol edin.');
        }
        $this->ssh = $ssh;
        return $this->ssh;
    }

    private function loadRequiredClasses(): void {
        // FtpClient sınıfını yükle - namespace olmadan
        $ftpPath = dirname(__DIR__) . '/FtpClient.php';
        if (file_exists($ftpPath) && !class_exists('FtpClient', false)) {
            require_once $ftpPath;
        }
        
        // Eğer FtpClient hala bulunamazsa hata ver
        if (!class_exists('FtpClient', false) && !class_exists('Setup\FtpClient', false)) {
            throw new \Exception("FtpClient sınıfı bulunamadı. Dosya yolu: " . $ftpPath);
        }
    }

    public function run(): array {
        try {
            $this->manager->log("Dağıtım adımı başlatılıyor...");

            $this->loadRequiredClasses();

            $formData = $this->manager->getConfig('form_data');
            $jsonConfig = $this->manager->getConfig('json_config');
            
            $domain = $formData['domain'] ?? null;
            $ftpUser = substr($formData['username'] ?? '', 0, 32);
            $ftpPassword = $formData['password'] ?? '';
            
            $sshConfig = $jsonConfig['ssh'] ?? [];
            $serverIP = $sshConfig['server_ip'] ?? null;
            $repositoryUrl = $jsonConfig['git']['repository_url'] ?? null;

            if (empty($domain) || empty($serverIP) || empty($repositoryUrl)) {
                throw new \Exception("Domain, sunucu IP veya Git repository URL eksik.");
            }

            // Adım 1: FTP ile index.html silme
            $this->manager->log("FTP ile bağlanıp varsayılan index.html siliniyor...");
            
            // FtpClient sınıfını kullan (namespace'siz)
            $ftpClass = class_exists('FtpClient', false) ? 'FtpClient' : 'Setup\FtpClient';
            $ftp = new $ftpClass($serverIP, $ftpUser, $ftpPassword);
            $ftp->deleteFile("/httpdocs/index.html");
            $ftp->close();
            $this->manager->log("index.html silindi.");

            // Adım 2: Plesk Git Repo Kurulumu (SSH üzerinden)
            $this->manager->log("Plesk üzerinden Git deposu ($repositoryUrl) ayarlanıyor...");
            $repoMessage = $this->addGitRepoToDomain($domain, "eticaret", $repositoryUrl);
            $this->manager->log($repoMessage);

            // Adım 3: Composer Kurulumu (SSH üzerinden)
            $this->manager->log("Composer bağımlılıkları kuruluyor...");
            $composerMessage = $this->runComposerCommand($domain);
            $this->manager->log($composerMessage);

            // Adım 4: Dosyaları FTP ile Yükleme
            $this->manager->log("Gerekli konfigürasyon dosyaları FTP ile yükleniyor...");
            $uploadMessage = $this->uploadFilesViaFtp($serverIP, $ftpUser, $ftpPassword);
            $this->manager->log($uploadMessage);

            return ['status' => 'success', 'message' => 'Dağıtım adımları başarıyla tamamlandı.'];

        } catch (\Exception $e) {
            $this->manager->log("DeploymentStep Hatası: " . $e->getMessage(), 'ERROR');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function addGitRepoToDomain($domain, $repoName, $remoteUrl) {
        $ssh = $this->getSshConnection();
        $command = "plesk ext git --create -domain " . escapeshellarg($domain) . " -name " . escapeshellarg($repoName) . " -remote-url " . escapeshellarg($remoteUrl) . " -deployment-path /httpdocs -deployment-mode auto -skip-ssl-verification true";
        $output = $ssh->exec($command);
        if (stripos($output, 'error') !== false || stripos($output, 'failed') !== false) {
            throw new \Exception("Git deposu oluşturulurken hata oluştu: $output");
        }
        return "Git deposu '$repoName' başarıyla '$domain' alan adına eklendi.";
    }

    private function runComposerCommand($domain) {
        $ssh = $this->getSshConnection();
        $targetDirectory = "/var/www/vhosts/$domain/httpdocs";
        $phpPath = "/opt/plesk/php/8.3/bin/php";
        $composerPharPath = "/usr/local/psa/var/modules/composer/composer.phar";
        $composerCommand = "cd $targetDirectory && COMPOSER_ALLOW_SUPERUSER=1 $phpPath $composerPharPath install";
        $output = $ssh->exec($composerCommand);
        if (stripos($output, 'error') !== false || stripos($output, 'Could not') !== false) {
            throw new \Exception("Composer komutu çalıştırılırken hata oluştu: $output");
        }
        return "Composer install komutu başarıyla çalıştı.";
    }

    private function uploadFilesViaFtp($serverIP, $ftpUser, $ftpPassword) {
        // FtpClient sınıfını kullan
        $ftpClass = class_exists('FtpClient', false) ? 'FtpClient' : 'Setup\FtpClient';
        $ftp = new $ftpClass($serverIP, $ftpUser, $ftpPassword);
        
        // Production .env dosyası oluştur ve yükle
        $this->createProductionEnvFile($ftp);
        
        $ftp->close();
        return "Production .env dosyası oluşturuldu ve yüklendi.";
    }

    private function createProductionEnvFile($ftp) {
        $formData = $this->manager->getConfig('form_data');
        $jsonConfig = $this->manager->getConfig('json_config');
        
        $domain = $formData['domain'];
        $dbName = $formData['databaseName'] ?? str_replace(['.', '-'], ['_', '_'], $domain);
        $dbUser = $formData['username'];
        $dbPassword = $formData['password'];
        
        $sshConfig = $jsonConfig['ssh'] ?? [];
        $serverIP = $sshConfig['server_ip'] ?? 'localhost';
        
        // Production .env içeriği
        $envContent = "# Production Environment Configuration
# Generated automatically by setup process

# Application Settings
APP_NAME=\"E-Commerce Application\"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://{$domain}

# Database Configuration
DB_CONNECTION=mysql
DB_HOST={$serverIP}
DB_PORT=3306
DB_DATABASE={$dbName}
DB_USERNAME={$dbUser}
DB_PASSWORD={$dbPassword}

# Security
APP_KEY=" . $this->generateAppKey() . "
ENCRYPTION_KEY=" . $this->generateEncryptionKey() . "

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail Configuration (update with actual values)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls

# Timezone
APP_TIMEZONE=Europe/Istanbul

# Logging
LOG_LEVEL=error
";

        // Geçici dosya oluştur
        $tempEnvPath = sys_get_temp_dir() . '/production_env_' . uniqid();
        file_put_contents($tempEnvPath, $envContent);
        
        // FTP ile yükle
        $ftp->uploadFile($tempEnvPath, "/httpdocs/.env");
        
        // Geçici dosyayı sil
        unlink($tempEnvPath);
        
        $this->manager->log("Production .env dosyası oluşturuldu - Debug: false, Environment: production");
    }

    private function generateAppKey(): string {
        return 'base64:' . base64_encode(random_bytes(32));
    }

    private function generateEncryptionKey(): string {
        return bin2hex(random_bytes(16));
    }

    public function rollback(): array {
        // Rollback: httpdocs dizinini temizle
        try {
            $this->manager->log("Deployment adımı geri alınıyor...");
            $ssh = $this->getSshConnection();
            $config = $this->manager->getConfig('form_data');
            $domain = $config['domain'];
            $targetDirectory = "/var/www/vhosts/$domain/httpdocs";
            
            // Git reposunu Plesk'ten kaldır
            $ssh->exec("plesk ext git --delete -domain $domain -name eticaret");
            $this->manager->log("Plesk Git eklentisi kaldırıldı.");

            // httpdocs içeriğini temizle
            $ssh->exec("rm -rf $targetDirectory/*");
            $ssh->exec("rm -rf $targetDirectory/.*");
            $this->manager->log("httpdocs dizini temizlendi.");

            return ['status' => 'success', 'message' => 'Dağıtım geri alındı (httpdocs temizlendi).'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}