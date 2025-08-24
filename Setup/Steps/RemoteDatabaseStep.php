<?php

namespace Setup\Steps;

use Setup\Core\SetupStep;
use phpseclib3\Net\SSH2;

class RemoteDatabaseStep extends SetupStep {
    private $ssh;

    public function getName(): string {
        return 'Uzak Veritabanı Kurulumu';
    }

    public function getDescription(): string {
        return 'Uzak sunucuda migration ve seed işlemlerini SSH üzerinden çalıştırır.';
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

    public function run(): array {
        try {
            $this->manager->log("Uzak veritabanı adımı başlatılıyor...");

            $formData = $this->manager->getConfig('form_data');
            $domain = $formData['domain'] ?? null;
            
            if (empty($domain)) {
                throw new \Exception("Domain bilgisi eksik.");
            }

            $ssh = $this->getSshConnection();
            $projectPath = "/var/www/vhosts/$domain/httpdocs";
            $phpPath = "/opt/plesk/php/8.3/bin/php";

            // Adım 1: Phinx Migration çalıştır
            $this->manager->log("Uzak sunucuda Phinx migration'ları çalıştırılıyor...");
            $migrationCommand = "cd $projectPath && $phpPath vendor/bin/phinx migrate -c App/Database/phinx.php -e production";
            $migrationOutput = $ssh->exec($migrationCommand);
            
            if (stripos($migrationOutput, 'error') !== false || stripos($migrationOutput, 'failed') !== false) {
                throw new \Exception("Migration çalıştırılırken hata oluştu: $migrationOutput");
            }
            $this->manager->log("Migration işlemi tamamlandı.");

            // Adım 2: Kontrollü Phinx Seed çalıştır (LocalDatabaseStep gibi)
            $this->manager->log("Uzak sunucuda kontrollü Phinx seed'ler çalıştırılıyor...");
            
            // Önce seed dosyalarının varlığını kontrol et
            $seedCheckCommand = "ls -la $projectPath/App/Database/seeds/";
            $seedCheckOutput = $ssh->exec($seedCheckCommand);
            $this->manager->log("Seed dosyaları kontrol edildi: " . substr($seedCheckOutput, 0, 200) . "...");
            
            // Phinx konfigürasyon dosyasını kontrol et
            $configCheckCommand = "ls -la $projectPath/App/Database/phinx.php";
            $configCheckOutput = $ssh->exec($configCheckCommand);
            $this->manager->log("Phinx config kontrol: " . $configCheckOutput);
            
            // 1. Foreign key kontrollerini devre dışı bırak
            $disableKeysCommand = "cd $projectPath && $phpPath vendor/bin/phinx seed:run -c App/Database/phinx.php -e production -s AAA_DisableKeys";
            $disableOutput = $ssh->exec($disableKeysCommand);
            $this->manager->log("Foreign key kontrollerini devre dışı bırakma: " . $disableOutput);
            
            // 2. Tüm seed'leri çalıştır (AAA_ ve ZZZ_ hariç)
            $allSeedsCommand = "cd $projectPath && find App/Database/seeds -name '*.php' -type f ! -name 'AAA_*' ! -name 'ZZZ_*' | sort";
            $seedFiles = $ssh->exec($allSeedsCommand);
            $seedFilesList = array_filter(explode("\n", trim($seedFiles)));
            
            foreach ($seedFilesList as $seedFile) {
                if (empty($seedFile)) continue;
                
                $seedClassName = basename($seedFile, '.php');
                $this->manager->log("Uzak sunucuda çalıştırılıyor: $seedClassName");
                
                $individualSeedCommand = "cd $projectPath && $phpPath vendor/bin/phinx seed:run -c App/Database/phinx.php -e production -s $seedClassName";
                $individualOutput = $ssh->exec($individualSeedCommand);
                
                if (stripos($individualOutput, 'error') !== false || stripos($individualOutput, 'failed') !== false) {
                    $this->manager->log("$seedClassName hatası: " . substr($individualOutput, 0, 500), 'WARNING');
                    // Devam et, diğer seed'leri dene
                } else {
                    $this->manager->log("$seedClassName başarılı");
                }
            }
            
            // 3. Foreign key kontrollerini yeniden etkinleştir
            $enableKeysCommand = "cd $projectPath && $phpPath vendor/bin/phinx seed:run -c App/Database/phinx.php -e production -s ZZZ_EnableKeys";
            $enableOutput = $ssh->exec($enableKeysCommand);
            $this->manager->log("Foreign key kontrollerini yeniden etkinleştirme: " . $enableOutput);
            
            $this->manager->log("Kontrollü seed işlemi tamamlandı.");

            // Adım 2.5: Yönetici kullanıcıları ekleme
            $this->manager->log("Uzak sunucuda yönetici kullanıcıları ekleniyor...");
            $this->addAdminUsersRemote($ssh, $projectPath, $phpPath);

            // Adım 3: Dosya izinlerini düzelt
            $this->manager->log("Dosya izinleri düzenleniyor...");
            $chmodCommands = [
                "chmod -R 755 $projectPath",
                "chmod -R 777 $projectPath/storage",
                "chmod -R 777 $projectPath/logs",
                "chmod 644 $projectPath/.env"
            ];
            
            foreach ($chmodCommands as $cmd) {
                $ssh->exec($cmd);
            }
            $this->manager->log("Dosya izinleri düzenlendi.");

            // Adım 4: Setup dizinini güvenlik için sil
            $this->manager->log("Güvenlik için Setup dizini uzak sunucudan siliniyor...");
            $this->cleanupSetupDirectory($ssh, $projectPath);

            return ['status' => 'success', 'message' => 'Uzak veritabanı kurulumu tamamlandı ve Setup dizini temizlendi.'];

        } catch (\Exception $e) {
            $this->manager->log("RemoteDatabaseStep Hatası: " . $e->getMessage(), 'ERROR');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Uzak sunucudaki Setup dizinini güvenlik için siler
     */
    private function cleanupSetupDirectory($ssh, $projectPath): void {
        try {
            $setupPath = "$projectPath/Setup";
            
            // Önce Setup dizininin varlığını kontrol et
            $checkCommand = "[ -d '$setupPath' ] && echo 'exists' || echo 'not_exists'";
            $checkResult = trim($ssh->exec($checkCommand));
            
            if ($checkResult === 'exists') {
                $this->manager->log("Setup dizini bulundu, siliniyor: $setupPath");
                
                // Setup dizini içeriğini listele (log için)
                $listCommand = "find '$setupPath' -type f | head -10";
                $fileList = $ssh->exec($listCommand);
                $this->manager->log("Silinecek dosyalar (ilk 10): " . trim($fileList));
                
                // Setup dizinini tamamen sil
                $deleteCommand = "rm -rf '$setupPath'";
                $deleteResult = $ssh->exec($deleteCommand);
                
                // Silme işlemini doğrula
                $verifyCommand = "[ ! -d '$setupPath' ] && echo 'deleted' || echo 'still_exists'";
                $verifyResult = trim($ssh->exec($verifyCommand));
                
                if ($verifyResult === 'deleted') {
                    $this->manager->log("✅ Setup dizini başarıyla silindi: $setupPath");
                } else {
                    $this->manager->log("⚠️ Setup dizini silinemedi, manuel kontrol gerekli: $setupPath", 'WARNING');
                }
                
                // .env dosyasından PLESK_DOMAIN_ID'yi de temizle (opsiyonel)
                $envCleanCommand = "sed -i '/^PLESK_DOMAIN_ID=/d' '$projectPath/.env'";
                $ssh->exec($envCleanCommand);
                $this->manager->log("PLESK_DOMAIN_ID .env dosyasından temizlendi");
                
            } else {
                $this->manager->log("Setup dizini zaten mevcut değil: $setupPath");
            }
            
        } catch (\Exception $e) {
            $this->manager->log("Setup dizini silinirken hata oluştu: " . $e->getMessage(), 'WARNING');
            // Bu hata kritik değil, kurulum devam etsin
        }
    }

    public function rollback(): array {
        try {
            $this->manager->log("Uzak veritabanı adımı geri alınıyor...");
            
            $formData = $this->manager->getConfig('form_data');
            $domain = $formData['domain'] ?? null;
            
            if (empty($domain)) {
                throw new \Exception("Domain bilgisi eksik.");
            }

            $ssh = $this->getSshConnection();
            $projectPath = "/var/www/vhosts/$domain/httpdocs";
            $phpPath = "/opt/plesk/php/8.3/bin/php";

            // Phinx rollback çalıştır (son migration'ı geri al)
            $rollbackCommand = "cd $projectPath && $phpPath vendor/bin/phinx rollback -c App/Database/phinx.php -e production -t 0";
            $rollbackOutput = $ssh->exec($rollbackCommand);
            
            $this->manager->log("Database rollback çıktısı: " . $rollbackOutput);

            return ['status' => 'success', 'message' => 'Uzak veritabanı geri alındı.'];

        } catch (\Exception $e) {
            $this->manager->log("RemoteDatabaseStep Rollback Hatası: " . $e->getMessage(), 'ERROR');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function addAdminUsersRemote($ssh, $projectPath, $phpPath) {
        // Geçici PHP scripti oluştur
        $formData = $this->manager->getConfig('form_data');
        $key = $formData['keyCode'] ?? '';
        $domain = $formData['domain'] ?? '';
        
        if (empty($key)) {
            throw new \Exception("Şifreleme anahtarı bulunamadı.");
        }

        $adminScript = "<?php
        // .env dosyasından veritabanı bilgilerini al
        function loadEnvFile(\$path) {
            \$env = [];
            if (file_exists(\$path)) {
                \$lines = file(\$path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach (\$lines as \$line) {
                    if (strpos(\$line, '=') !== false && substr(\$line, 0, 1) !== '#') {
                        list(\$key, \$value) = explode('=', \$line, 2);
                        \$env[trim(\$key)] = trim(\$value, '\"');
                    }
                }
            }
            return \$env;
        }

        \$env = loadEnvFile('$projectPath/.env');
        \$dbHost = \$env['DB_HOST'] ?? 'localhost';
        \$dbName = \$env['DB_DATABASE'] ?? '';
        \$dbUser = \$env['DB_USERNAME'] ?? '';
        \$dbPass = \$env['DB_PASSWORD'] ?? '';

        if (empty(\$dbName) || empty(\$dbUser)) {
            throw new Exception('.env dosyasından veritabanı bilgileri alınamadı');
        }

        // Basit PDO bağlantısı
        try {
            \$pdo = new PDO(\"mysql:host=\$dbHost;dbname=\$dbName\", \$dbUser, \$dbPass);
            \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException \$e) {
            throw new Exception('Veritabanı bağlantısı başarısız: ' . \$e->getMessage());
        }

        function createPassword(\$length, \$type) {
            if(\$type == 0) \$chars = '0123456789';
            if(\$type == 1) \$chars = 'ABCDEFGHJKMNPRSTUVYZQWX';
            if(\$type == 2) \$chars = 'ABCDEFGHJKMNPRSTUVYZQWX23456789';
            if(\$type == 3) \$chars = 'abcdefghjklmnoprstuvyzqxABCDEFGHJKLMNOPRSTUVYZQWX0123456789%=*';
            return substr(str_shuffle(\$chars), 0, \$length);
        }

        function encrypt(\$data, \$key) {
            if (empty(\$data)) return '';
            \$method = 'AES-256-CBC';
            \$key = hash('sha256', \$key);
            \$iv = substr(hash('sha256', \$key), 0, 16);
            \$encrypted = openssl_encrypt(\$data, \$method, \$key, OPENSSL_RAW_DATA, \$iv);
            return base64_encode(\$iv . \$encrypted);
        }

        \$key = '$key';
        \$domain = '$domain';

        try {
            // Tek yönetici - Pozitif Eticaret
            \$adminKey = createPassword(20, 2);
            \$adminCreateDate = date('Y-m-d H:i:s');
            \$adminFullName = encrypt('Pozitif Eticaret', \$key);
            \$adminEmail = encrypt('info@pozitifeticaret.com', \$key);
            \$adminPhone = encrypt('5312631827', \$key);
            \$adminPassword = createPassword(5, 3);
            
            \$sql = 'INSERT INTO yoneticiler (yoneticianahtar, olusturmatarihi, guncellemetarihi, yoneticiyetki, yoneticiadsoyad, yoneticieposta, yoneticiceptelefon, yoneticisifre, yoneticipin, yoneticiaktif, yoneticisil) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            
            \$stmt = \$pdo->prepare(\$sql);
            \$stmt->execute([\$adminKey, \$adminCreateDate, \$adminCreateDate, 0, \$adminFullName, \$adminEmail, \$adminPhone, \$adminPassword, '1234', 1, 0]);
            
            // Genel ayarlar
            \$generalSql = 'INSERT INTO ayargenel (domain, ssldurum, sitetip, cokludil, uyelik, dilid) VALUES (?, ?, ?, ?, ?, ?)';
            \$stmt2 = \$pdo->prepare(\$generalSql);
            \$stmt2->execute([\$domain, 1, 1, 1, 1, 1]);
            
            echo 'SUCCESS: Yönetici kullanıcısı ve genel ayarlar eklendi';
            
        } catch (Exception \$e) {
            echo 'ERROR: ' . \$e->getMessage();
        }
    ?>";

        // Script'i uzak sunucuya yükle
        $tempScriptPath = "/tmp/add_admins_" . uniqid() . ".php";
        $ssh->exec("cat > $tempScriptPath << 'EOFSCRIPT'\n$adminScript\nEOFSCRIPT");
        
        // Script'i çalıştır
        $output = $ssh->exec("$phpPath $tempScriptPath");
        
        // Geçici dosyayı sil
        $ssh->exec("rm -f $tempScriptPath");
        
        if (stripos($output, 'SUCCESS') === false) {
            throw new \Exception("Yönetici ekleme hatası: $output");
        }
        
        $this->manager->log("Yönetici kullanıcısı uzak sunucuda başarıyla eklendi.");
    }
}