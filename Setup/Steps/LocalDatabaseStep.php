<?php

namespace Setup\Steps;

use Setup\Core\SetupStep;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class LocalDatabaseStep extends SetupStep {

    public function getName(): string {
        return 'Yerel Veritabanı Kurulumu';
    }

    public function getDescription(): string {
        return 'Yerel geliştirme veritabanını oluşturur, migration ve seed işlemlerini çalıştırır.';
    }

    public function run(): array {
        try {
            // Memory limit'i artır
            ini_set('memory_limit', '512M');
            
            $this->manager->log("Yerel veritabanı adımı başlatılıyor...");
            $config = $this->manager->getConfig('form_data');
            $localServerUrl = $config['localServerUrl'];
            $localUsername = $config['localUsername'];
            $localPassword = $config['localPassword'];
            $localDatabaseName = $config['localDatabaseName'];

            // 1. Veritabanını oluştur veya seç
            $conn = new \mysqli($localServerUrl, $localUsername, $localPassword);
            if ($conn->connect_error) {
                return ["status" => "error", "message" => "MySQL sunucusuna bağlanılamadı: " . $conn->connect_error];
            }
            $sql = "CREATE DATABASE IF NOT EXISTS `$localDatabaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if ($conn->query($sql) !== TRUE) {
                return ["status" => "error", "message" => "Veritabanı oluşturulamadı: " . $conn->error];
            }
            $conn->select_db($localDatabaseName);
            $this->manager->log("Veritabanı '$localDatabaseName' hazır veya oluşturuldu.");

            // 2. Phinx Migrations Çalıştır
            $this->manager->log("Phinx migration'ları çalıştırılıyor...");
            $phinxOutput = $this->runPhinxCommand('migrate', 'development');
            $this->manager->log("Phinx migrate çıktısı:
" . $phinxOutput);
            if (strpos($phinxOutput, 'ERROR') !== false) {
                throw new \Exception('Phinx migration sırasında hata oluştu: ' . $phinxOutput);
            }

            // 3. Kontrollü Seeder Çalıştırma
            $this->manager->log("Kontrollü seeder süreci başlatılıyor...");
            $seedPath = dirname(__DIR__, 2) . '/App/Database/seeds/';
            $disableKeysFile = $seedPath . 'AAA_DisableKeys.php';
            $enableKeysFile = $seedPath . 'ZZZ_EnableKeys.php';

            try {
                // Phinx'in kendi bağlantısı üzerinden FK kontrolünü yönet
                $this->runPhinxCommand('seed:run', 'development', 'AAA_DisableKeys');
                $this->manager->log("Yabancı anahtar kontrolleri devre dışı bırakıldı.");

                $seedFiles = glob($seedPath . '*.php');
                foreach ($seedFiles as $file) {
                    $className = basename($file, '.php');
                    // Geçici seederları tekrar çalıştırma
                    if ($className === 'AAA_DisableKeys' || $className === 'ZZZ_EnableKeys') {
                        continue;
                    }
                    $this->manager->log("Çalıştırılıyor: {$className}...");
                    $seedOutput = $this->runPhinxCommand('seed:run', 'development', $className);
                    if (strpos($seedOutput, 'ERROR') !== false) {
                        throw new \Exception("{$className} seeder çalıştırılırken hata oluştu: " . $seedOutput);
                    }
                    
                    // Memory temizliği
                    unset($seedOutput);
                    if (function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                    }
                }
                $this->manager->log("Tüm seeder'lar başarıyla çalıştırıldı.");

                // 4. Yönetici kullanıcıları ekleme
                $this->manager->log("Yönetici kullanıcıları ekleniyor...");
                $this->addAdminUsers($conn, $localDatabaseName);

            } finally {
                // Her durumda FK kontrollerini yeniden etkinleştir ve geçici dosyaları sil
                $this->runPhinxCommand('seed:run', 'development', 'ZZZ_EnableKeys');
                $this->manager->log("Yabancı anahtar kontrolleri yeniden etkinleştirildi.");
                if (file_exists($disableKeysFile)) unlink($disableKeysFile);
                if (file_exists($enableKeysFile)) unlink($enableKeysFile);
                $this->manager->log("Geçici seeder dosyaları silindi.");
                $conn->close();
            }

            return ['status' => 'success', 'message' => "Veritabanı hazır, migration, seed işlemleri ve yönetici kullanıcıları tamamlandı."];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function runPhinxCommand(string $command, string $environment, ?string $seederClass = null): string {
        // Memory temizliği
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        $phinx = new PhinxApplication();
        $phinx->setAutoExit(false);

        $configPath = dirname(__DIR__, 2) . '/App/Database/phinx.php';
        if (!file_exists($configPath)) {
            throw new \Exception("Phinx konfigürasyon dosyası bulunamadı: {$configPath}");
        }

        $params = [
            'command' => $command,
            '--environment' => $environment,
            '--configuration' => $configPath
        ];

        if ($seederClass !== null) {
            $params['-s'] = [$seederClass];
        }

        $input = new ArrayInput($params);
        $output = new BufferedOutput();
        
        try {
            $phinx->run($input, $output);
            $result = $output->fetch();
        } finally {
            // Memory temizliği
            unset($phinx, $input, $output);
        }

        return $result;
    }

    public function rollback(): array {
        try {
            $this->manager->log("Yerel veritabanı adımı geri alınıyor (veritabanı siliniyor)...");
            $config = $this->manager->getConfig('form_data');
            $localServerUrl = $config['localServerUrl'];
            $localUsername = $config['localUsername'];
            $localPassword = $config['localPassword'];
            $localDatabaseName = $config['localDatabaseName'];

            $conn = new \mysqli($localServerUrl, $localUsername, $localPassword);
            if ($conn->connect_error) {
                return ["status" => "error", "message" => "Local DB bağlantı hatası: " . $conn->connect_error];
            }

            $sql = "DROP DATABASE IF EXISTS `$localDatabaseName`";
            if ($conn->query($sql) === TRUE) {
                $conn->close();
                $this->manager->log("Veritabanı '$localDatabaseName' başarıyla silindi.");
                return ['status' => 'success', 'message' => "Veritabanı '$localDatabaseName' silindi."];
            } else {
                $error = $conn->error;
                $conn->close();
                return ['status' => 'error', 'message' => "Veritabanı '$localDatabaseName' silinemedi: " . $error];
            }
        }
        catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function addAdminUsers($conn, $databaseName) {
        // Key dosyasından şifreleme anahtarını al
        $formData = $this->manager->getConfig('form_data');
        $key = $formData['keyCode'] ?? '';
        
        if (empty($key)) {
            throw new \Exception("Şifreleme anahtarı bulunamadı.");
        }

        $conn->select_db($databaseName);

        // İlk yönetici - Pozitif Eticaret
        $adminKey = $this->createPassword(20, 2);
        $adminCreateDate = date("Y-m-d H:i:s");
        $adminFullName = $this->encrypt("Pozitif Eticaret", $key);
        $adminEmail = $this->encrypt("info@pozitifeticaret.com", $key);
        $adminPhone = $this->encrypt("5312631827", $key);
        $adminPassword = $this->createPassword(5, 3);

        $sql = "INSERT INTO yoneticiler 
                (yoneticianahtar, olusturmatarihi, guncellemetarihi, yoneticiyetki, yoneticiadsoyad, yoneticieposta, yoneticiceptelefon, yoneticisifre, yoneticipin, yoneticiaktif, yoneticisil) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        // Değişkenleri ayrı ayrı tanımla
        $adminAuth = 0;
        $adminPin = "1234";
        $adminIsActive = 1;
        $adminIsDeleted = 0;
        
        $stmt->bind_param("sssississsi", 
            $adminKey, $adminCreateDate, $adminCreateDate, 
            $adminAuth, $adminFullName, $adminEmail, $adminPhone, 
            $adminPassword, $adminPin, $adminIsActive, $adminIsDeleted
        );

        if (!$stmt->execute()) {
            throw new \Exception("İlk yönetici eklenemedi: " . $stmt->error);
        }

        // Genel ayarları ekle
        $formData = $this->manager->getConfig('form_data');
        $domain = $formData['domain'] ?? '';
        
        $generalSql = "INSERT INTO ayargenel (domain, ssldurum, sitetip, cokludil, uyelik, dilid) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($generalSql);
        
        // Değişkenleri ayrı ayrı tanımla
        $sslDurum = 1;
        $siteTip = 1;
        $cokluDil = 1;
        $uyelik = 1;
        $dilId = 1;
        
        $stmt3->bind_param("siiiii", $domain, $sslDurum, $siteTip, $cokluDil, $uyelik, $dilId);
        
        if (!$stmt3->execute()) {
            throw new \Exception("Genel ayarlar eklenemedi: " . $stmt3->error);
        }

        $this->manager->log("Yönetici kullanıcısı ve genel ayarlar başarıyla eklendi.");
    }

    private function createPassword($length, $type): string {
        if($type == 0) $chars = "0123456789";
        if($type == 1) $chars = "ABCDEFGHJKMNPRSTUVYZQWX";
        if($type == 2) $chars = "ABCDEFGHJKMNPRSTUVYZQWX23456789";
        if($type == 3) $chars = "abcdefghjklmnoprstuvyzqxABCDEFGHJKLMNOPRSTUVYZQWX0123456789%=*";
        
        return substr(str_shuffle($chars), 0, $length);
    }

    private function encrypt(string $data, string $key): string {
        if (empty($data)) return "";
        if (empty($key)) {
            throw new \InvalidArgumentException('Data and key must not be null or empty.');
        }

        $method = 'AES-256-CBC';
        $key = hash('sha256', $key);
        $iv = substr(hash('sha256', $key), 0, 16);
        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }
}
