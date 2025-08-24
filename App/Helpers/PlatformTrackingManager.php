<?php

/**
 * Digital Marketing Platform Tracking Manager
 * Google Analytics, Facebook Pixel, TikTok Pixel vb. platformları için merkezi yönetim.
 * Bu sınıf, veritabanından aktif izleme platformlarını alır ve ilgili scriptleri
 * web sayfasının <head> ve <body> bölümleri için statik olarak üretir.
 */
class PlatformTrackingManager {

    private static $db;

    /**
     * Desteklenen tracking platformları ve script şablonları.
     */
    const PLATFORMS = [
        "google_tag_manager" => [
            "name" => "Google Tag Manager",
            "code" => "GTM",
            "fields" => ["container_id"],
            "head_template" => <<<EOT
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{container_id}}');</script>
<!-- End Google Tag Manager -->
EOT
,
            "body_template" => <<<EOT
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src='https://www.googletagmanager.com/ns.html?id={{container_id}}'
height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
EOT
        ],
        "meta_pixel" => [
            "name" => "Meta Pixel",
            "code" => "META",
            "fields" => ["pixel_id"],
            "head_template" => <<<EOT
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){
n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{pixel_id}}');
fbq('track', 'PageView');
</script>
<noscript><img height='1' width='1' style='display:none'
src='https://www.facebook.com/tr?id={{pixel_id}}&ev=PageView&noscript=1'
/></noscript>
<!-- End Meta Pixel Code -->
EOT
        ],
        "google_analytics" => [
            "name" => "Google Analytics",
            "code" => "GA",
            "fields" => ["measurement_id"],
            "head_template" => <<<EOT
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{measurement_id}}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{measurement_id}}');
</script>
<!-- End Google Analytics -->
EOT
,
            "body_template" => <<<EOT
<!-- Google Analytics (noscript) -->
<noscript>
  <iframe src="https://www.googletagmanager.com/ns.html?id={{measurement_id}}"
          height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Analytics (noscript) -->
EOT
        ]
    ];

    private static function init() {
        if (self::$db === null) {
            // Veritabanı bağlantısı dışarıdan set edilmeli.
            // Eğer set edilmemişse, burada bir hata fırlatılabilir veya uygun bir şekilde ele alınabilir.
            throw new Exception("Database connection not set for PlatformTrackingManager.");
        }
    }

    /**
     * Veritabanı bağlantısını ayarlar.
     * @param mixed $db Database or mock database object
     */
    public static function setDb($db) {
        self::$db = $db;
    }

    /**
     * Belirtilen dil için tüm aktif platform yapılandırmalarını veritabanından alır.
     * @param int $languageID
     * @return array
     */
    private static function getActivePlatforms($languageID = 1) {
        self::init();
        try {
            $sql = "SELECT platform, config FROM platform_tracking WHERE language_id = :languageID AND status = 1";
            $stmt = self::$db->prepare($sql);
            $stmt->bindParam(':languageID', $languageID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('PlatformTrackingManager Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verilen şablon ve verilerle script kodunu oluşturur.
     * @param string $template
     * @param array $configData
     * @return string
     */
    private static function renderTemplate($template, $configData) {
        foreach ($configData as $key => $value) {
            if (!empty($value)) {
                $template = str_replace('{{' . $key . '}}', $value, $template);
            }
        }
        // Değiştirilmemiş değişkenleri temizle
        $template = preg_replace('/{{\s*\w+\s*}}/', '', $template);
        return $template . "\n";
    }

    /**
     * <head> bölümü için tüm aktif izleme scriptlerini üretir.
     * @param int $languageID
     * @param array $excludePlatforms Hariç tutulacak platformlar
     * @return string
     */
    public static function getHeadScripts($languageID = 1, $excludePlatforms = ['google_analytics']) {
        $activePlatforms = self::getActivePlatforms($languageID);
        Log::write('aktif platformlar: '. json_encode($activePlatforms),"info");
        $headCodes = '';

        foreach ($activePlatforms as $platform) {
            $platformKey = $platform['platform'];
            if (in_array($platformKey, $excludePlatforms)) {
                continue; // Bu platformu atla
            }
            if (isset(self::PLATFORMS[$platformKey]) && !empty(self::PLATFORMS[$platformKey]['head_template'])) {
                $configData = json_decode($platform['config'], true);
                if ($configData) {
                    $headCodes .= self::renderTemplate(self::PLATFORMS[$platformKey]['head_template'], $configData);
                }
            }
        }
        return $headCodes;
    }

    /**
     * <body> bölümünün başlangıcı için tüm aktif izleme scriptlerini (genellikle noscript) üretir.
     * @param int $languageID
     * @param array $excludePlatforms Hariç tutulacak platformlar
     * @return string
     */
    public static function getBodyScripts($languageID = 1, $excludePlatforms = []) {
        $activePlatforms = self::getActivePlatforms($languageID);
        $bodyCodes = '';

        foreach ($activePlatforms as $platform) {
            $platformKey = $platform['platform'];
            if (in_array($platformKey, $excludePlatforms)) {
                continue; // Bu platformu atla
            }
            if (isset(self::PLATFORMS[$platformKey]) && !empty(self::PLATFORMS[$platformKey]['body_template'])) {
                $configData = json_decode($platform['config'], true);
                if ($configData) {
                    $bodyCodes .= self::renderTemplate(self::PLATFORMS[$platformKey]['body_template'], $configData);
                }
            }
        }
        return $bodyCodes;
    }

    /**
     * Google Tag Manager Container ID'sini döndürür.
     * @param int $languageID
     * @return string|null
     */
    public static function getGoogleTagManagerContainerId($languageID = 1) {
        $config = self::getPlatformConfig('google_tag_manager', $languageID);
        if ($config && !empty($config['config'])) {
            $configData = json_decode($config['config'], true);
            return $configData['container_id'] ?? null;
        }
        return null;
    }

    /**
     * Google Analytics Measurement ID'sini döndürür.
     * @param int $languageID
     * @return string|null
     */
    public static function getGoogleAnalyticsId($languageID = 1) {
        $config = self::getPlatformConfig('google_analytics', $languageID);
        if ($config && !empty($config['config'])) {
            $configData = json_decode($config['config'], true);
            return $configData['measurement_id'] ?? null;
        }
        return null;
    }

    /**
     * Kullanıcı kimliği ile özel Google Analytics scriptini üretir.
     * @param int $languageID
     * @param mixed $userId Kullanıcı kimliği
     * @return string
     */
    public static function getGoogleAnalyticsWithUserId($languageID = 1, $userId = null) {
        $gaId = self::getGoogleAnalyticsId($languageID);
        if (!$gaId) {
            return '';
        }

        $script = '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $gaId . '"></script>' . "\n";
        $script .= '<script>' . "\n";
        $script .= '  window.dataLayer = window.dataLayer || [];' . "\n";
        $script .= '  function gtag(){dataLayer.push(arguments);}' . "\n";
        $script .= '  gtag(\'js\', new Date());' . "\n";
        $script .= '  gtag(\'config\', \'' . $gaId . '\');' . "\n";
        
        if ($userId !== null) {
            $script .= '  gtag(\'set\', \'user_properties\', {\'user_id\': \'' . $userId . '\'});' . "\n";
        } else {
            $script .= '  gtag(\'set\', \'user_properties\', {\'user_id\': null});' . "\n";
        }
        
        $script .= '</script>' . "\n";
        return $script;
    }

    /**
     * Belirli bir platformun yapılandırmasını veritabanından alır.
     * Bu metod, getActivePlatforms metodundan farklı olarak tek bir platformu çeker.
     * @param string $platformKey
     * @param int $languageID
     * @return array|false
     */
    public static function getPlatformConfig($platformKey, $languageID = 1) {
        self::init();
        try {
            $sql = "SELECT platform, config, status FROM platform_tracking WHERE platform = :platformKey AND language_id = :languageID";
            $stmt = self::$db->prepare($sql);
            $stmt->bindParam(':platformKey', $platformKey, PDO::PARAM_STR);
            $stmt->bindParam(':languageID', $languageID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('PlatformTrackingManager getPlatformConfig Error: ' . $e->getMessage());
            return false;
        }
    }
}
