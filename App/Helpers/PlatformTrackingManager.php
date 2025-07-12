<?php
/**
 * Digital Marketing Platform Tracking Manager
 * Google Analytics, Facebook Pixel, TikTok Pixel vb. platformları için merkezi yönetim
 */

class PlatformTrackingManager {
    
    private $db;
    private $config;
    
    public function __construct($database, $config) {
        $this->db = $database;
        $this->config = $config;
    }
    
    /**
     * Desteklenen tracking platformları
     */
    const PLATFORMS = [
        'google_analytics' => [
            'name' => 'Google Analytics',
            'code' => 'GA',
            'fields' => ['tracking_id', 'measurement_id'],
            'head_template' => '<script async src="https://www.googletagmanager.com/gtag/js?id={{tracking_id}}"></script>
            <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag("js", new Date());
            gtag("config", "{{tracking_id}}");
            </script>'
                    ],
                    'google_ads' => [
                        'name' => 'Google Ads',
                        'code' => 'GAD',
                        'fields' => ['conversion_id', 'conversion_label'],
                        'conversion_template' => '<script>
            gtag("event", "conversion", {
                "send_to": "{{conversion_id}}/{{conversion_label}}",
                "value": {{value}},
                "currency": "{{currency}}"
            });
            </script>'
                    ],
                    'facebook_pixel' => [
                        'name' => 'Facebook Pixel',
                        'code' => 'FB',
                        'fields' => ['pixel_id'],
                        'head_template' => '<script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,"script",
            "https://connect.facebook.net/en_US/fbevents.js");
            fbq("init", "{{pixel_id}}");
            fbq("track", "PageView");
            </script>
            <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{pixel_id}}&ev=PageView&noscript=1"
            /></noscript>'
                    ],
                    'tiktok_pixel' => [
                        'name' => 'TikTok Pixel',
                        'code' => 'TT',
                        'fields' => ['pixel_id'],
                        'head_template' => '<script>
            !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
            ttq.load("{{pixel_id}}");
            ttq.page();
            }(window, document, "ttq");
            </script>'
                    ],
                    'linkedin_insight' => [
                        'name' => 'LinkedIn Insight Tag',
                        'code' => 'LI',
                        'fields' => ['partner_id'],
                        'head_template' => '<script type="text/javascript">
            _linkedin_partner_id = "{{partner_id}}";
            window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
            window._linkedin_data_partner_ids.push(_linkedin_partner_id);
            </script><script type="text/javascript">
            (function(l) {
            if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
            window.lintrk.q=[]}
            var s = document.getElementsByTagName("script")[0];
            var b = document.createElement("script");
            b.type = "text/javascript";b.async = true;
            b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
            s.parentNode.insertBefore(b, s);})(window.lintrk);
            </script>
            <noscript>
            <img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid={{partner_id}}&fmt=gif" />
            </noscript>'
        ]
    ];
    
    /**
     * Platform tracking kodunu getir
     */
    public function getPlatformConfig($platform, $languageID = 1) {
        try {
            // Status koşulunu kaldırıyoruz çünkü mevcut kaydı bulmak için kullanılıyor
            $sql = "SELECT * FROM platform_tracking WHERE platform = :platform AND language_id = :languageID";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':platform', $platform);
            $stmt->bindParam(':languageID', $languageID);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("PlatformTrackingManager::getPlatformConfig - Platform: $platform, LanguageID: $languageID, Result: " . ($result ? 'found' : 'not found'));
            
            return $result;
        } catch (Exception $e) {
            error_log('PlatformTrackingManager getPlatformConfig Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Platform yapılandırmasını kaydet
     */
    public function savePlatformConfig($platform, $config, $languageID = 1, $status = null) {
        try {
            error_log("PlatformTrackingManager::savePlatformConfig - Platform: $platform, LanguageID: $languageID, Status: " . ($status !== null ? $status : 'null'));
            error_log("PlatformTrackingManager::savePlatformConfig - Config: " . json_encode($config));
            
            // Önce mevcut kaydı kontrol et
            $existing = $this->getPlatformConfig($platform, $languageID);
            error_log("PlatformTrackingManager::savePlatformConfig - Existing record: " . ($existing ? 'found' : 'not found'));
            
            $configJson = json_encode($config);
            $updatedAt = date('Y-m-d H:i:s');
            
            if ($existing) {
                // Status parametresi verilmişse güncelle, yoksa mevcut durumu koru
                if ($status !== null) {
                    $sql = "UPDATE platform_tracking SET 
                           config = :config, 
                           status = :status,
                           updated_at = :updatedAt 
                           WHERE platform = :platform AND language_id = :languageID";
                    error_log("PlatformTrackingManager::savePlatformConfig - UPDATE with status");
                } else {
                    $sql = "UPDATE platform_tracking SET 
                           config = :config, 
                           updated_at = :updatedAt 
                           WHERE platform = :platform AND language_id = :languageID";
                    error_log("PlatformTrackingManager::savePlatformConfig - UPDATE without status");
                }
            } else {
                $statusValue = $status !== null ? $status : 1;
                $sql = "INSERT INTO platform_tracking 
                       (platform, language_id, config, status, created_at, updated_at) 
                       VALUES (:platform, :languageID, :config, :status, :createdAt, :updatedAt)";
                error_log("PlatformTrackingManager::savePlatformConfig - INSERT with status: $statusValue");
            }
            
            error_log("PlatformTrackingManager::savePlatformConfig - SQL: $sql");
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':platform', $platform);
            $stmt->bindParam(':languageID', $languageID);
            $stmt->bindParam(':config', $configJson);
            $stmt->bindParam(':updatedAt', $updatedAt);
            
            if ($existing && $status !== null) {
                $stmt->bindParam(':status', $status);
                error_log("PlatformTrackingManager::savePlatformConfig - Binding status for UPDATE: $status");
            } elseif (!$existing) {
                $stmt->bindParam(':status', $statusValue);
                $stmt->bindParam(':createdAt', $updatedAt); // Created time aynı olsun
                error_log("PlatformTrackingManager::savePlatformConfig - Binding status for INSERT: $statusValue");
            }
            
            $result = $stmt->execute();
            error_log("PlatformTrackingManager::savePlatformConfig - Execute result: " . ($result ? 'success' : 'failed'));
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("PlatformTrackingManager::savePlatformConfig - SQL Error: " . json_encode($errorInfo));
            }
            
            return $result;
        } catch (Exception $e) {
            error_log('PlatformTrackingManager Save Error: ' . $e->getMessage());
            error_log('PlatformTrackingManager Save Error - Stack: ' . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Head bölümü için tracking kodlarını oluştur
     */
    public function generateHeadCodes($languageID = 1) {
        $headCodes = '';
        
        foreach (self::PLATFORMS as $platformKey => $platformInfo) {
            $config = $this->getPlatformConfig($platformKey, $languageID);
            
            if ($config && !empty($config['config'])) {
                $configData = json_decode($config['config'], true);
                
                if (isset($platformInfo['head_template'])) {
                    $template = $platformInfo['head_template'];
                    
                    // Template değişkenlerini değiştir
                    foreach ($configData as $key => $value) {
                        if (!empty($value)) {
                            $template = str_replace('{{' . $key . '}}', $value, $template);
                        }
                    }
                    
                    $headCodes .= $template . "\n";
                }
            }
        }
        
        return $headCodes;
    }
    
    /**
     * Dönüşüm kodları oluştur
     */
    public function generateConversionCode($platform, $eventType, $eventData, $languageID = 1) {
        $config = $this->getPlatformConfig($platform, $languageID);
        
        if (!$config || empty($config['config'])) {
            return '';
        }
        
        $configData = json_decode($config['config'], true);
        
        switch ($platform) {
            case 'google_ads':
                return $this->generateGoogleAdsConversion($configData, $eventType, $eventData);
            case 'facebook_pixel':
                return $this->generateFacebookConversion($configData, $eventType, $eventData);
            case 'tiktok_pixel':
                return $this->generateTikTokConversion($configData, $eventType, $eventData);
            default:
                return '';
        }
    }
    
    /**
     * Google Ads dönüşüm kodu
     */
    private function generateGoogleAdsConversion($config, $eventType, $eventData) {
        if (empty($config['conversion_id'])) return '';
        
        $conversionLabel = $config['conversion_label'] ?? '';
        $value = $eventData['value'] ?? 0;
        $currency = $eventData['currency'] ?? 'TRY';
        
        return '<script>
            gtag("event", "conversion", {
                "send_to": "' . $config['conversion_id'] . '/' . $conversionLabel . '",
                "value": ' . $value . ',
                "currency": "' . $currency . '"
            });
        </script>';
    }
    
    /**
     * Facebook Pixel dönüşüm kodu
     */
    private function generateFacebookConversion($config, $eventType, $eventData) {
        if (empty($config['pixel_id'])) return '';
        
        $eventName = $eventType === 'purchase' ? 'Purchase' : 'AddToCart';
        $value = $eventData['value'] ?? 0;
        $currency = $eventData['currency'] ?? 'TRY';
        
        return '<script>
            fbq("track", "' . $eventName . '", {
                value: ' . $value . ',
                currency: "' . $currency . '"
            });
        </script>';
    }
    
    /**
     * TikTok Pixel dönüşüm kodu
     */
    private function generateTikTokConversion($config, $eventType, $eventData) {
        if (empty($config['pixel_id'])) return '';
        
        $eventName = $eventType === 'purchase' ? 'CompletePayment' : 'AddToCart';
        $value = $eventData['value'] ?? 0;
        $currency = $eventData['currency'] ?? 'TRY';
        
        return '<script>
            ttq.track("' . $eventName . '", {
            value: ' . $value . ',
            currency: "' . $currency . '"
            });
        </script>';
    }
    
    /**
     * Tüm aktif platformları getir
     */
    public function getActivePlatforms($languageID = 1) {
        try {
            $sql = "SELECT * FROM platform_tracking WHERE language_id = :languageID AND status = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':languageID', $languageID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('PlatformTrackingManager Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Body bölümü için conversion kodlarını oluştur
     */
    public function generateConversionCodes($languageID = 1) {
        $conversionCodes = '';
        
        foreach (self::PLATFORMS as $platformKey => $platformInfo) {
            $config = $this->getPlatformConfig($platformKey, $languageID);
            
            if ($config && !empty($config['config'])) {
                $configData = json_decode($config['config'], true);
                
                if (isset($platformInfo['body_template'])) {
                    $template = $platformInfo['body_template'];
                    
                    // Template değişkenlerini değiştir
                    foreach ($configData as $key => $value) {
                        if (!empty($value)) {
                            $template = str_replace('{{' . $key . '}}', $value, $template);
                        }
                    }
                    
                    $conversionCodes .= $template . "\n";
                }
            }
        }
        
        return $conversionCodes;
    }
}
