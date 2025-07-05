<?php
/**
 * Tepe Banner Sorun Çözme Aracı
 * 
 * Gerçek tablo yapısına göre tepe banner sorunlarını çözer
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

class TopBannerFixer
{
    private $pdo;
    private $helper;
    
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
            
            echo "✅ Database bağlantısı başarılı\n\n";
            
        } catch (Exception $e) {
            echo "❌ Database bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function fixTopBanner()
    {
        echo "=== TEPE BANNER SORUN ÇÖZME ===\n\n";
        
        // 1. Mevcut tepe banner durumunu kontrol et
        $this->checkCurrentStatus();
        
        // 2. Tepe banner verisi ekle
        $this->addTopBannerData();
        
        // 3. Kontrol et ve sonuçları göster
        $this->verifyFix();
    }
    
    private function checkCurrentStatus()
    {
        echo "1. 🔍 Mevcut tepe banner durumu kontrol ediliyor...\n";
        
        // Banner group'u kontrol et (ID: 2)
        $stmt = $this->pdo->prepare("SELECT * FROM banner_groups WHERE id = 2");
        $stmt->execute();
        $topGroup = $stmt->fetch();
        
        if ($topGroup) {
            echo "   ✅ Tepe Banner Group mevcut:\n";
            echo "      - ID: {$topGroup['id']}\n";
            echo "      - Name: {$topGroup['group_name']}\n";
            echo "      - Layout ID: {$topGroup['layout_id']}\n";
            echo "      - Style Class: {$topGroup['style_class']}\n";
        }
        
        // Bu group'a ait aktif banner'ları kontrol et
        $stmt = $this->pdo->prepare("SELECT * FROM banners WHERE group_id = 2 AND active = 1");
        $stmt->execute();
        $topBanners = $stmt->fetchAll();
        
        echo "   📊 Group ID 2'ye ait aktif banner sayısı: " . count($topBanners) . "\n";
        
        if (empty($topBanners)) {
            echo "   ⚠️  Tepe banner verisi yok - Eklenecek!\n";
        } else {
            echo "   ✅ Mevcut tepe banner verileri:\n";
            foreach ($topBanners as $banner) {
                echo "      - ID: {$banner['id']}, Title: {$banner['title']}\n";
            }
        }
        echo "\n";
    }
    
    private function addTopBannerData()
    {
        echo "2. 🔧 Tepe banner verisi ekleniyor...\n";
        
        // Önce mevcut aktif tepe banner'ı kontrol et
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM banners WHERE group_id = 2 AND active = 1");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            echo "   ℹ️  Aktif tepe banner zaten mevcut, yenisi eklenmiyor.\n\n";
            return;
        }
        
        // Örnek tepe banner verisi ekle
        $stmt = $this->pdo->prepare("
            INSERT INTO banners (group_id, style_id, title, content, image, link, active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $success = $stmt->execute([
            2, // group_id (Tepe Banner)
            3, // style_id (Layout ID 3 ile uyumlu)
            'Global Pozitif\'e Hoş Geldiniz', // title
            'Profesyonel web tasarımı ve dijital pazarlama çözümleri ile işletmenizi dijital dünyada öne çıkarın. Modern, kullanıcı dostu ve SEO uyumlu web siteleri.', // content
            'Banner/tepe-banner-sample.jpg', // image
            '/hakkimizda', // link
            1 // active
        ]);
        
        if ($success) {
            $bannerId = $this->pdo->lastInsertId();
            echo "   ✅ Örnek tepe banner eklendi (ID: {$bannerId})\n";
            echo "      - Title: Global Pozitif'e Hoş Geldiniz\n";
            echo "      - Image: Banner/tepe-banner-sample.jpg\n";
            echo "      - Link: /hakkimizda\n";
        } else {
            echo "   ❌ Tepe banner eklenirken hata oluştu\n";
        }
        echo "\n";
    }
    
    private function verifyFix()
    {
        echo "3. ✅ Düzeltme doğrulaması yapılıyor...\n";
        
        // Tepe banner'ları tekrar kontrol et
        $stmt = $this->pdo->prepare("
            SELECT b.*, bg.group_name, bg.style_class, bl.layout_name 
            FROM banners b
            JOIN banner_groups bg ON b.group_id = bg.id
            JOIN banner_layouts bl ON bg.layout_id = bl.id
            WHERE b.group_id = 2 AND b.active = 1
        ");
        $stmt->execute();
        $topBanners = $stmt->fetchAll();
        
        if (!empty($topBanners)) {
            echo "   🎉 Tepe banner düzeltmesi başarılı!\n";
            echo "   📊 Aktif tepe banner sayısı: " . count($topBanners) . "\n\n";
            
            foreach ($topBanners as $banner) {
                echo "   📄 Banner Detayları:\n";
                echo "      - ID: {$banner['id']}\n";
                echo "      - Title: {$banner['title']}\n";
                echo "      - Content: " . substr($banner['content'], 0, 100) . "...\n";
                echo "      - Image: {$banner['image']}\n";
                echo "      - Link: {$banner['link']}\n";
                echo "      - Group: {$banner['group_name']}\n";
                echo "      - Style Class: {$banner['style_class']}\n";
                echo "      - Layout: {$banner['layout_name']}\n";
                echo "      ---\n";
            }
        } else {
            echo "   ❌ Tepe banner düzeltmesi başarısız\n";
        }
    }
    
    public function generateTestHTML()
    {
        echo "4. 📄 Test HTML oluşturuluyor...\n";
        
        // Tepe banner verilerini al
        $stmt = $this->pdo->prepare("
            SELECT b.*, bg.style_class 
            FROM banners b
            JOIN banner_groups bg ON b.group_id = bg.id
            WHERE b.group_id = 2 AND b.active = 1
            LIMIT 1
        ");
        $stmt->execute();
        $banner = $stmt->fetch();
        
        if ($banner) {
            $html = '
<!-- Tepe Banner Test HTML -->
<div class="' . $banner['style_class'] . '">
    <div class="banner-container">
        <div class="banner-item">
            <div class="banner-image">
                <img src="/Public/Image/' . $banner['image'] . '" alt="' . htmlspecialchars($banner['title']) . '">
            </div>
            <div class="content-box">
                <h2 class="title">' . htmlspecialchars($banner['title']) . '</h2>
                <p class="content">' . htmlspecialchars($banner['content']) . '</p>
                <div class="button-container">
                    <a href="' . $banner['link'] . '" class="banner-button">Daha Fazla Bilgi</a>
                </div>
            </div>
        </div>
    </div>
</div>';
            
            file_put_contents($basePath . '/Tests/Temp/tepe-banner-test.html', $html);
            echo "   ✅ Test HTML dosyası oluşturuldu: Tests/Temp/tepe-banner-test.html\n";
        }
        echo "\n";
    }
}

// Script çalıştırma
if (basename($_SERVER['PHP_SELF']) === 'TopBannerFixer.php') {
    $fixer = new TopBannerFixer();
    $fixer->fixTopBanner();
    $fixer->generateTestHTML();
    echo "=== TEPE BANNER SORUN ÇÖZME TAMAMLANDI ===\n";
}
