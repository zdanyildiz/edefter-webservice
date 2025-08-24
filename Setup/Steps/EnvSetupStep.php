<?php

namespace Setup\Steps;

use Setup\Core\SetupStep;

class EnvSetupStep extends SetupStep
{
    public function getName(): string
    {
        return '.env Dosyası Oluşturma';
    }

    public function getDescription(): string
    {
        return 'Formdan gelen bilgilere göre projenin temel yapılandırma (.env) dosyasını oluşturur. Bu adım, sonraki tüm adımların çalışması için kritiktir.';
    }

    public function run(): array
    {
        $this->manager->log("'.env' oluşturma adımı başlatılıyor...");

        try {
            $rootPath = dirname(__DIR__, 2);
            $envFilePath = $rootPath . '/.env';
            $formData = $this->manager->getConfig('form_data');
            $services = $this->manager->getConfig('json_config', []);
            $stepData = $this->manager->getStepData(); // Önceki adımlardan gelen verileri al

            // Gerekli form verilerini kontrol et
            $requiredKeys = ['domain', 'keyCode'];
            foreach ($requiredKeys as $key) {
                if (empty($formData[$key])) {
                    throw new \Exception("Formda gerekli olan '{$key}' alanı boş veya eksik.");
                }
            }
            $this->manager->log("Gerekli form verileri mevcut.");

            // Mevcut .env dosyasını yedekle
            if (file_exists($envFilePath)) {
                $backupPath = $envFilePath . '.bak.' . date('YmdHis');
                if (!rename($envFilePath, $backupPath)) {
                    throw new \Exception("Mevcut .env dosyası yedeklenemedi. İzinleri kontrol edin.");
                }
                $this->manager->log("Mevcut .env dosyası '{$backupPath}' olarak yedeklendi.");
            }

            // .env içeriğini oluştur (artık stepData'yı da içeriyor)
            $envContent = $this->generateEnvContent($formData, $services, $stepData);
            $this->manager->log(".env içeriği başarıyla oluşturuldu.");

            // .env dosyasını yaz
            if (file_put_contents($envFilePath, $envContent) === false) {
                throw new \Exception("'.env' dosyası oluşturulamadı. Proje ana dizininde yazma izni olduğundan emin olun.");
            }

            $this->manager->log("'.env' dosyası başarıyla oluşturuldu: {$envFilePath}");
            return ['status' => 'success', 'message' => '.env dosyası başarıyla oluşturuldu.'];

        } catch (\Exception $e) {
            $this->manager->log("HATA: " . $e->getMessage(), 'ERROR');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function generateEnvContent(array $formData, array $services, array $stepData): string
    {
        $allDomains = $formData['domains'] ?? '';
        if (!empty($formData['domain']) && strpos($allDomains, $formData['domain']) === false) {
            $allDomains = $formData['domain'] . ',' . $allDomains;
        }

        $envData = [
            'APP_KEY' => $formData['keyCode'],
            'APP_ENV' => 'local',
            'APP_DEBUG' => 'true',
            'APP_DOMAIN' => $formData['domain'],
            'APP_DOMAINS' => $allDomains,
            'DB_HOST' => $formData['serverUrl'] ?? '',
            'DB_DATABASE' => $formData['databaseName'] ?? '',
            'DB_USERNAME' => $formData['username'] ?? '',
            'DB_PASSWORD' => $formData['password'] ?? '',
            'DB_LOCAL_HOST' => $formData['localServerUrl'] ?? 'localhost',
            'DB_LOCAL_DATABASE' => $formData['localDatabaseName'] ?? '',
            'DB_LOCAL_USERNAME' => $formData['localUsername'] ?? 'root',
            'DB_LOCAL_PASSWORD' => $formData['localPassword'] ?? '',
        ];

        $serviceMap = [
            'openai' => ['api_key' => 'OPENAI_API_KEY', 'assistant_id' => 'OPENAI_ASSISTANT_ID', 'model' => 'OPENAI_MODEL'],
            'email' => ['host' => 'SMTP_HOST', 'username' => 'SMTP_USERNAME', 'password' => 'SMTP_PASSWORD', 'port' => 'SMTP_PORT', 'smtp_secure' => 'SMTP_SECURE', 'from_email' => 'MAIL_FROM_ADDRESS', 'from_name' => 'MAIL_FROM_NAME'],
            'google_oauth' => ['client_id' => 'GOOGLE_CLIENT_ID', 'client_secret' => 'GOOGLE_CLIENT_SECRET', 'project_id' => 'GOOGLE_PROJECT_ID']
        ];

        foreach ($serviceMap as $serviceName => $keyMap) {
            if (isset($services[$serviceName])) {
                foreach ($keyMap as $jsonKey => $envKey) {
                    if (isset($services[$serviceName][$jsonKey])) {
                        $envData[$envKey] = $services[$serviceName][$jsonKey];
                    }
                }
            }
        }

        $content = "";
        foreach ($envData as $key => $value) {
            $content .= "{$key}={$value}\n";
        }
        return $content;
    }

    public function rollback(): array
    {
        $this->manager->log("'.env' rollback işlemi başlatılıyor...");
        $envFilePath = dirname(__DIR__, 2) . '/.env';

        if (file_exists($envFilePath)) {
            if (unlink($envFilePath)) {
                $this->manager->log("'.env' dosyası başarıyla silindi.");
                return ['status' => 'success', 'message' => '.env dosyası silindi.'];
            }
            return ['status' => 'error', 'message' => "'.env' dosyası silinemedi. İzinleri kontrol edin."];
        }

        return ['status' => 'success', 'message' => 'Silinecek .env dosyası bulunamadı.'];
    }
}