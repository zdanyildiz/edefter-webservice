<?php

namespace Setup\Steps;

use Setup\Core\SetupStep;
use Setup\CloudflareAPI;

class CloudflareStep extends SetupStep {
    private ?\CloudflareAPI $cloudflare = null;

    public function getName(): string {
        return 'Cloudflare Kurulumu';
    }

    public function getDescription(): string {
        return 'Cloudflare üzerinde tüm geçerli domainler için DNS ve Turnstile ayarlarını yapar.';
    }

    public function run(): array {
        try {
            $this->manager->log("Cloudflare adımı başlatılıyor...");

            require_once dirname(__DIR__) . '/CloudflareAPI.php';

            $jsonConfig = $this->manager->getConfig('json_config');
            $formData = $this->manager->getConfig('form_data');

            $apiKey = $jsonConfig['cloudflare']['api_key'] ?? null;
            $email = $jsonConfig['cloudflare']['email'] ?? null;
            $cfAccountID = $jsonConfig['cloudflare']['account_id'] ?? null;
            $serverIP = $jsonConfig['ssh']['server_ip'] ?? null;

            if (empty($apiKey) || empty($email) || empty($cfAccountID)) {
                return ['status' => 'success', 'message' => 'Cloudflare API bilgileri eksik, adım atlandı.'];
            }
            if (empty($serverIP)) {
                return ['status' => 'error', 'message' => 'Sunucu IP adresi (ssh.server_ip) bulunamadı.'];
            }

            $this->cloudflare = new \CloudflareAPI($apiKey, $email);

            list($validDomains, $skippedDomains) = $this->getAndFilterDomains($formData);
            $this->manager->log("İşlenecek domainler: " . implode(', ', $validDomains));
            if (!empty($skippedDomains)) {
                $this->manager->log("Atlanan lokal domainler: " . implode(', ', $skippedDomains));
            }

            $dnsResults = $this->manageDNSForAllDomains($validDomains, $serverIP);
            $turnstileResults = $this->manageTurnstileForAllDomains($validDomains, $cfAccountID);

            $finalMessage = "Cloudflare işlemleri tamamlandı. " . count($validDomains) . " domain işlendi, " . count($skippedDomains) . " domain atlandı.";
            $returnData = $this->prepareReturnData($turnstileResults);

            return [
                'status' => 'success',
                'message' => $finalMessage,
                'data' => $returnData,
                'details' => ['dns_results' => $dnsResults, 'turnstile_results' => $turnstileResults]
            ];

        } catch (\Exception $e) {
            $this->manager->log("CloudflareStep içinde kritik hata: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getAndFilterDomains(array $formData): array {
        $mainDomain = $formData['domain'] ?? null;
        $additionalDomainsRaw = $formData['domains'] ?? '';
        $additionalDomains = explode(',', $additionalDomainsRaw);
        $allDomains = array_merge([$mainDomain], $additionalDomains);
        
        $validDomains = [];
        $skippedDomains = [];
        foreach ($allDomains as $domain) {
            $domain = trim($domain);
            if (empty($domain)) continue;

            if (preg_match('/^l\./', $domain)) {
                $skippedDomains[] = $domain;
            } else {
                $validDomains[] = $domain;
            }
        }
        return [array_unique($validDomains), array_unique($skippedDomains)];
    }

    private function manageDNSForAllDomains(array $domains, string $serverIP): array {
        $results = [];
        foreach ($domains as $domain) {
            $this->manager->log("DNS yönetimi başlatılıyor: {$domain}");
            $domainParts = $this->parseDomain($domain);
            if (isset($domainParts['error'])) {
                $results[$domain] = ['status' => 'error', 'message' => $domainParts['error']];
                continue;
            }

            $zoneId = $this->getOrCreateZone($domainParts['mainDomain']);
            if (!$zoneId) {
                $results[$domain] = ['status' => 'error', 'message' => 'Zone ID alınamadı veya oluşturulamadı.'];
                continue;
            }

            $results[$domain] = $this->manageDNSRecord($zoneId, $domain, $serverIP);
        }
        return $results;
    }

    private function manageTurnstileForAllDomains(array $domains, string $cfAccountID): array {
        $results = [];
        foreach ($domains as $domain) {
            $this->manager->log("Turnstile yönetimi başlatılıyor: {$domain}");
            $widgetName = $this->generateWidgetName($domain);
            $turnstileInfo = $this->cloudflare->getOrCreateTurnstileWidget($cfAccountID, $domain, $widgetName);
            $results[$domain] = $turnstileInfo;
        }
        return $results;
    }

    private function getOrCreateZone(string $mainDomain): ?string {
        $zoneId = $this->cloudflare->getZoneIdByDomain($mainDomain);
        if ($zoneId) {
            $this->manager->log("Zone bulundu: {$mainDomain} (ID: {$zoneId})");
            return $zoneId;
        }

        $this->manager->log("Zone bulunamadı, yeni site ekleniyor: {$mainDomain}");
        $response = $this->cloudflare->addSite($mainDomain);
        if (isset($response['result']['id'])) {
            $this->manager->log("Zone başarıyla oluşturuldu: " . $response['result']['id']);
            return $response['result']['id'];
        }
        
        $this->manager->log("HATA: Cloudflare'e site eklenemedi: " . ($response['errors'][0]['message'] ?? 'Bilinmeyen hata'), 'ERROR');
        return null;
    }

    private function manageDNSRecord(string $zoneId, string $recordName, string $serverIP): array {
        $recordsResponse = $this->cloudflare->listDNSRecords($zoneId, $recordName);
        $existingRecord = null;
        if (isset($recordsResponse['result']) && is_array($recordsResponse['result'])) {
            foreach ($recordsResponse['result'] as $record) {
                if ($record['type'] === 'A' && $record['name'] === $recordName) {
                    $existingRecord = $record;
                    break;
                }
            }
        }

        if ($existingRecord) {
            if ($existingRecord['content'] === $serverIP) {
                $this->manager->log("DNS kaydı zaten güncel: {$recordName}");
                return ['status' => 'success', 'message' => 'Kayıt zaten güncel.'];
            }
            $this->manager->log("Mevcut kayıt bulundu, güncelleniyor: {$recordName}");
            $response = $this->cloudflare->updateDNSRecord($zoneId, $existingRecord['id'], 'A', $recordName, $serverIP, 1, true);
            $action = 'güncellendi';
        } else {
            $this->manager->log("Mevcut kayıt bulunamadı, yeni kayıt ekleniyor: {$recordName}");
            $response = $this->cloudflare->addDNSRecord($zoneId, 'A', $recordName, $serverIP, 1, true);
            $action = 'eklendi';
        }

        if (isset($response['success']) && $response['success'] === true) {
            return ['status' => 'success', 'message' => "DNS kaydı başarıyla {$action}. "];
        } else {
            $errorMessage = $response['errors'][0]['message'] ?? 'Bilinmeyen hata';
            $this->manager->log("HATA: DNS kaydı {$action}: {$errorMessage}", 'ERROR');
            return ['status' => 'error', 'message' => $errorMessage];
        }
    }

    private function prepareReturnData(array $turnstileResults): array {
        $returnData = [];
        foreach ($turnstileResults as $domain => $result) {
            if (isset($result['success']) && $result['success']) {
                $siteKey = $result['sitekey'];
                $secretKey = $result['secret'] ?? null; // Secret sadece oluşturmada gelir
                // Anahtar isimlerini domain ile özelleştir
                $envSiteKey = 'CLOUDFLARE_TURNSTILE_SITE_KEY_' . strtoupper(str_replace('.', '_', $domain));
                $envSecretKey = 'CLOUDFLARE_TURNSTILE_SECRET_KEY_' . strtoupper(str_replace('.', '_', $domain));
                $returnData[$envSiteKey] = $siteKey;
                if ($secretKey) {
                    $returnData[$envSecretKey] = $secretKey;
                }
            }
        }
        return $returnData;
    }

    private function generateWidgetName(string $domain): string {
        if (empty($domain)) {
            return 'default-widget';
        }
        
        // Domain extension'ları ve subdomain'leri kaldırarak temiz bir isim elde et
        $cleanDomain = preg_replace('/\.(com|tr|net|org).*$/', '', $domain);
        $cleanDomain = preg_replace('/^(.*?)\.(.*)$/', '$1', $cleanDomain); // Subdomain'i kaldır

        // Boş kalırsa veya çok kısaysa, domain'i güvenli karakterlerle temizle
        if (empty($cleanDomain) || strlen($cleanDomain) < 3) {
            $cleanDomain = str_replace(['.', '/', ':', '-'], '_', $domain);
        }
        
        return $cleanDomain . '-widget';
    }

    private function parseDomain(string $domain): array {
        $domain = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $domain);
        $trSuffix = "";
        if (substr($domain, -3) === ".tr") {
            $trSuffix = ".tr";
            $domain = substr($domain, 0, -3);
        }
        $domainParts = explode('.', $domain);
        if (count($domainParts) < 2) {
            return ['error' => 'Geçersiz domain formatı'];
        }
        $subDomain = count($domainParts) > 2 ? implode('.', array_slice($domainParts, 0, -2)) : '';
        $mainDomain = implode('.', array_slice($domainParts, -2)) . $trSuffix;
        return [
            'subdomain' => $subDomain,
            'mainDomain' => $mainDomain
        ];
    }

    public function rollback(): array {
        $this->manager->log("Cloudflare adımı için rollback henüz tam olarak uygulanmadı.");
        return ['status' => 'success', 'message' => 'Cloudflare rollback işlemi manuel müdahale gerektirebilir.'];
    }
}