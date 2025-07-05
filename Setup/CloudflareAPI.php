<?php

class CloudflareAPI
{
    private $apiKey;
    private $email;
    private $apiBaseUrl = "https://api.cloudflare.com/client/v4/";

    public function __construct($apiKey, $email)
    {
        $this->apiKey = $apiKey;
        $this->email = $email;
    }

    // cURL işlemi
    private function request($method, $endpoint, $data = null)
    {
        $url = $this->apiBaseUrl . $endpoint;
        $headers = [
            "X-Auth-Email: " . $this->email,
            "X-Auth-Key: " . $this->apiKey,
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return "cURL Error: $error";
        } else {
            return json_decode($response, true);
        }
    }

    // Yeni site ekleme
    public function addSite($domain)
    {
        $data = [
            "name" => $domain,
            "jump_start" => true
        ];

        return $this->request("POST", "zones", $data);
    }

    // Tüm domainleri (zone'ları) listeleme ve zone ID'lerini alma
    public function listZones()
    {
        $response = $this->request("GET", "zones");
        if (isset($response['result'])) {
            $zones = [];
            foreach ($response['result'] as $zone) {
                $zones[] = [
                    'id' => $zone['id'],
                    'name' => $zone['name'],
                    'status' => $zone['status'],
                    'created_on' => $zone['created_on']
                ];
            }
            return $zones;
        } else {
            //print_r($response);exit;
            return "Error fetching zones.";
        }
    }

    // Zone ID'yi domain adı ile alma
    public function getZoneIdByDomain($domain)
    {
        // Tek bir domain (zone) bilgisi almak için API'ye sorgu yapıyoruz
        $endpoint = "zones?name=" . urlencode($domain);
        $response = $this->request("GET", $endpoint);

        if (isset($response['result']) && count($response['result']) > 0) {
            return $response['result'][0]['id'];
        }

        // Domain bulunamazsa null döndür
        return null;
    }

    // DNS kaydı ekleme
    public function addDNSRecord($zoneId, $type, $name, $content, $ttl = 3600, $proxied = false)
    {
        $data = [
            "type" => $type,
            "name" => $name,
            "content" => $content,
            "ttl" => $ttl,
            "proxied" => $proxied
        ];

        return $this->request("POST", "zones/{$zoneId}/dns_records", $data);
    }

    // Subdomain ekleme (DNS kaydı ekleyerek yapılır)
    public function addSubdomain($zoneId, $subdomain, $rootDomain, $ipAddress, $ttl, $proxied)
    {
        return $this->addDNSRecord($zoneId, "A", "{$subdomain}.{$rootDomain}", $ipAddress, $ttl, $proxied);
    }

    public function getOrCreateTurnstileWidget($accountId, $domainName, $widgetName = null, $mode = "managed")
    {
        if (empty($accountId)) {
            return ["success" => false, "message" => "Account ID is required."];
        }
        if (empty($domainName)) {
            return ["success" => false, "message" => "Domain name is required."];
        }

        $effectiveWidgetName = $widgetName ?: $domainName; // Eğer widgetName belirtilmemişse domainName'i kullan

        // Mevcut widget'ları listele
        $listWidgetsEndpoint = "accounts/{$accountId}/challenges/widgets";
        $existingWidgetsResponse = $this->request("GET", $listWidgetsEndpoint);

        if (isset($existingWidgetsResponse['success']) && $existingWidgetsResponse['success'] === false) {
            // request metodundan gelen hata formatını kullan
            return $existingWidgetsResponse;
        }

        // Yanıtın 'result' anahtarını ve bir dizi olup olmadığını kontrol et
        if (isset($existingWidgetsResponse['result']) && is_array($existingWidgetsResponse['result'])) {
            foreach ($existingWidgetsResponse['result'] as $widget) {
                if (in_array($domainName, $widget['domains'])) {
                    // Widget bulundu. Sitekey döndürülür.
                    // Not: Mevcut widget'ın secret'ı güvenlik nedeniyle API üzerinden tekrar alınamaz.
                    // Eğer secret kaybolduysa, widget'ı silip yeniden oluşturmanız veya secret'ı rotate etmeniz gerekir.
                    return [
                        "success" => true,
                        "action" => "found",
                        "sitekey" => $widget['sitekey'],
                        "secret_status" => "Secret is not retrievable for existing widgets via API. Use previously stored secret or rotate if lost.",
                        "name" => $widget['name'],
                        "mode" => $widget['mode']
                    ];
                }
            }
        } elseif (isset($existingWidgetsResponse['errors'])) { // Hata durumu
            return [
                "success" => false,
                "message" => "Failed to list Turnstile widgets.",
                "details" => $existingWidgetsResponse['errors']
            ];
        }


        // Widget bulunamadı, yenisini oluştur
        $createWidgetEndpoint = "accounts/{$accountId}/challenges/widgets";
        $postData = [
            "name" => $effectiveWidgetName,
            "domains" => [$domainName],
            "mode" => $mode, // "managed", "non-interactive", veya "invisible"
            // "region" => "world" // veya "eu", isteğe bağlı
            // "bot_fight_mode" => false, // isteğe bağlı
            // "offlabel" => false // Turnstile'ı Cloudflare markası olmadan kullanmak için (Enterprise plan gerektirir)
        ];

        $newWidgetResponse = $this->request("POST", $createWidgetEndpoint, $postData);

        if (isset($newWidgetResponse['success']) && $newWidgetResponse['success'] === true && isset($newWidgetResponse['result'])) {
            return [
                "success" => true,
                "action" => "created",
                "sitekey" => $newWidgetResponse['result']['sitekey'],
                "secret" => $newWidgetResponse['result']['secret'], // Secret sadece oluşturma sırasında döndürülür
                "name" => $newWidgetResponse['result']['name'],
                "mode" => $newWidgetResponse['result']['mode']
            ];
        } else {
            // request metodundan gelen hata formatını kullan veya kendi formatınızı oluşturun
            $errorMessage = "Failed to create Turnstile widget.";
            if (isset($newWidgetResponse['message'])) { // request metodundan gelen message
                $errorMessage = $newWidgetResponse['message'];
            } elseif (isset($newWidgetResponse['errors'])) { // API'den doğrudan gelen errors
                $errorMessages = [];
                foreach ($newWidgetResponse['errors'] as $err) {
                    $errorMessages[] = "Error " . $err['code'] . ": " . $err['message'];
                }
                $errorMessage = implode("; ", $errorMessages);
            }
            return [
                "success" => false,
                "message" => $errorMessage,
                "details" => $newWidgetResponse // Hata detaylarını da ekle
            ];
        }
    }

}

// Yeni site eklemek
//$response = $cloudflare->addSite("example.com");
//print_r($response);

// Domain listesi ve Zone ID'lerini almak
//$zones = $cloudflare->listZones();
//print_r($zones);

// Domain adı ile Zone ID almak
//$zoneId = $cloudflare->getZoneIdByDomain("example.com");
//echo "Zone ID for example.com: " . $zoneId;

// Subdomain eklemek
//$response = $cloudflare->addSubdomain($zoneId, "sub", "example.com", "192.168.1.1");
//print_r($response);

?>
