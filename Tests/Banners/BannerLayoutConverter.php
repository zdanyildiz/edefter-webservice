<?php
/**
 * Banner Layout Group Çevirici
 * 
 * BannerController'ın anladığı layout_group değerlerine çeviri yapar
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

class BannerLayoutConverter
{
    private $pdo;
    private $helper;
    
    // Layout Group Çeviri Haritası
    private $layoutGroupMap = [
        'fullwidth' => 'text_and_image',
        'carousel' => 'text_and_image', 
        'top-banner' => 'text_and_image',
        'ImageRightBanner' => 'text_and_image',
        'ImageLeftBanner' => 'text_and_image',
        'HoverCardBanner' => 'text_and_image',
        'ProfileCard' => 'text_and_image',
        'IconFeatureCard' => 'text_and_image',
        'FadeFeatureCard' => 'text_and_image',
        'BgImageCenterText' => 'text_and_image',
        'ImageTextOverlayBottom' => 'text_and_image',
        'bottom-banner' => 'text_and_image',
        'popup-banner' => 'text_and_image',
        'header-banner' => 'text_and_image'
    ];
    
    public function __construct()
    {
        $this->helper = new Helper();
        $this->connectDatabase();
    }
    
    private function connectDatabase()
    {
        global $key, $dbLocalServerName, $dbLocalUsername, $dbLocalPassword, $dbLocalName;
        
        try {
            $decryptedHost = $this->helper->decrypt($dbLocalServerName, $key);
            $decryptedUsername = $this->helper->decrypt($dbLocalUsername, $key);
            $decryptedPassword = $this->helper->decrypt($dbLocalPassword, $key);
            $decryptedDatabase = $this->helper->decrypt($dbLocalName, $key);
            
            $this->pdo = new PDO(
                "mysql:host={$decryptedHost};dbname={$decryptedDatabase};charset=utf8mb4",
                $decryptedUsername,
                $decryptedPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            echo "✅ Veritabanı bağlantısı başarılı\n\n";
            
        } catch (Exception $e) {
            echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function convertLayoutGroup($layoutGroup)
    {
        return $this->layoutGroupMap[$layoutGroup] ?? 'text_and_image';
    }
    
    public function analyzeCurrentMappings()
    {
        echo "=== LAYOUT GROUP ÇEVİRİ ANALİZİ ===\n\n";
        
        // Mevcut layout_group değerlerini al
        $stmt = $this->pdo->prepare("SELECT DISTINCT layout_group FROM banner_layouts ORDER BY layout_group");
        $stmt->execute();
        $layoutGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "🔍 Mevcut layout_group değerleri ve çevirileri:\n";
        echo "================================================\n";
        
        foreach ($layoutGroups as $group) {
            $converted = $this->convertLayoutGroup($group);
            $status = isset($this->layoutGroupMap[$group]) ? '✅' : '⚠️';
            echo "  {$status} '{$group}' -> '{$converted}'\n";
        }
        
        echo "\n📊 Çeviri İstatistikleri:\n";
        echo "  - Toplam unique layout_group: " . count($layoutGroups) . "\n";
        echo "  - Tanımlı çeviriler: " . count($this->layoutGroupMap) . "\n";
        echo "  - Eksik çeviriler: " . count(array_diff($layoutGroups, array_keys($this->layoutGroupMap))) . "\n";
        
        return $layoutGroups;
    }
    
    public function testTopBannerConversion()
    {
        echo "\n=== TEPE BANNER ÇEVİRİ TESTİ ===\n";
        echo "================================\n";
        
        // Tepe banner layout bilgisini al
        $stmt = $this->pdo->prepare("
            SELECT bl.layout_group, bg.group_name, bg.id as group_id
            FROM banner_groups bg
            JOIN banner_layouts bl ON bg.layout_id = bl.id
            WHERE bg.id = 2
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $originalGroup = $result['layout_group'];
            $convertedGroup = $this->convertLayoutGroup($originalGroup);
            
            echo "🎯 Tepe Banner ({$result['group_name']}):\n";
            echo "   - Orijinal layout_group: '{$originalGroup}'\n";
            echo "   - Çevrilmiş layout_group: '{$convertedGroup}'\n";
            echo "   - BannerController uyumlu: " . ($convertedGroup === 'text_and_image' ? '✅ Evet' : '❌ Hayır') . "\n";
            
            return $convertedGroup;
        } else {
            echo "❌ Tepe banner bilgisi bulunamadı!\n";
            return null;
        }
    }
    
    public function suggestBannerControllerFix()
    {
        echo "\n=== BANNERCONTROLLER DÜZELTMESİ ÖNERİSİ ===\n";
        echo "==========================================\n";
        
        echo "🔧 YAPILAACK DÜZELTMELER:\n\n";
        
        echo "1. BannerController'da layout_group çevirici eklenmeli:\n";
        echo "   - renderBannerHTML metodunda layout_group değeri çevrilmeli\n";
        echo "   - Çeviri haritası BannerController'a eklenmeli\n\n";
        
        echo "2. Önerilen kod değişikliği:\n";
        echo "   \$layoutGroup = \$this->convertLayoutGroup(\$banner['layout_info']['layout_group']);\n\n";
        
        echo "3. Alternatif çözüm:\n";
        echo "   - SiteConfig'de layout_group'u BannerController uyumlu hale getir\n";
        echo "   - Database'deki layout_group değerlerini standardize et\n\n";
        
        echo "4. Test sonrası doğrulama:\n";
        echo "   - TopBannerHTMLTester tekrar çalıştırılmalı\n";
        echo "   - Görsel test HTML'i kontrol edilmeli\n";
    }
    
    public function generateBannerControllerPatch()
    {
        echo "\n=== BANNERCONTROLLER PATCH DOSYASI ===\n";
        echo "=====================================\n";
        
        $patchCode = '
// BannerController\'a eklenecek layout group çevirici metod
private function convertLayoutGroup($layoutGroup) 
{
    $layoutGroupMap = [
        \'fullwidth\' => \'text_and_image\',
        \'carousel\' => \'text_and_image\', 
        \'top-banner\' => \'text_and_image\',
        \'ImageRightBanner\' => \'text_and_image\',
        \'ImageLeftBanner\' => \'text_and_image\',
        \'HoverCardBanner\' => \'text_and_image\',
        \'ProfileCard\' => \'text_and_image\',
        \'IconFeatureCard\' => \'text_and_image\',
        \'FadeFeatureCard\' => \'text_and_image\',
        \'BgImageCenterText\' => \'text_and_image\',
        \'ImageTextOverlayBottom\' => \'text_and_image\',
        \'bottom-banner\' => \'text_and_image\',
        \'popup-banner\' => \'text_and_image\',
        \'header-banner\' => \'text_and_image\'
    ];
    
    return $layoutGroupMap[$layoutGroup] ?? \'text_and_image\';
}

// renderBannerHTML metodunda şu satır:
// $layoutGroup = $banner[\'layout_info\'][\'layout_group\'];
// Şununla değiştirilmeli:
// $layoutGroup = $this->convertLayoutGroup($banner[\'layout_info\'][\'layout_group\']);
';
        
        $patchFilePath = dirname(__DIR__) . '/Temp/BannerController_LayoutGroup_Patch.php';
        
        // Temp klasörünü oluştur
        if (!is_dir(dirname($patchFilePath))) {
            mkdir(dirname($patchFilePath), 0755, true);
        }
        
        file_put_contents($patchFilePath, "<?php\n// BannerController Layout Group Patch\n" . $patchCode);
        
        echo "📁 Patch dosyası oluşturuldu: Tests/Temp/BannerController_LayoutGroup_Patch.php\n";
        echo "🔧 Bu kod parçalarını BannerController.php'ye ekleyin.\n";
        
        return $patchCode;
    }
}

// Script'i çalıştır
if (basename($_SERVER['PHP_SELF']) === 'BannerLayoutConverter.php') {
    try {
        $converter = new BannerLayoutConverter();
        
        $converter->analyzeCurrentMappings();
        $converter->testTopBannerConversion();
        $converter->suggestBannerControllerFix();
        $converter->generateBannerControllerPatch();
        
        echo "\n=== ÇEVİRİ ANALİZİ TAMAMLANDI ===\n";
        
    } catch (Exception $e) {
        echo "❌ Analiz hatası: " . $e->getMessage() . "\n";
    }
}
