<?php
/**
 * Tepe Banner Veri Kontrol ve Düzeltme Aracı
 * 
 * Bu script veritabanındaki tepe banner verilerini kontrol eder ve eksikleri düzeltir.
 */

// Gerekli dosyaları dahil et
$basePath = dirname(__DIR__, 2);
require_once $basePath . '/App/Helpers/Helper.php';
require_once $basePath . '/App/Config/Key.php';
require_once $basePath . '/App/Config/Sql.php';

class TopBannerDataFixer
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
    
    public function checkAndFixTopBanner()
    {
        echo "=== TEPE BANNER VERİ KONTROLÜ VE DÜZELTMESİ ===\n\n";
        
        // 1. Mevcut tabloları kontrol et
        $this->checkTables();
        
        // 2. Banner layout'larını kontrol et
        $this->checkBannerLayouts();
        
        // 3. Banner group'larını kontrol et
        $this->checkBannerGroups();
        
        // 4. Tepe banner verilerini kontrol et
        $this->checkTopBannerData();
        
        // 5. Eksik tepe banner verilerini ekle
        $this->fixTopBannerData();
    }
    
    private function checkTables()
    {
        echo "1. 📋 Veritabanı tabloları kontrol ediliyor...\n";
        
        $tables = ['banners', 'banner_layouts', 'banner_groups'];
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                echo "   ✅ {$table} tablosu mevcut\n";
            } else {
                echo "   ❌ {$table} tablosu bulunamadı\n";
            }
        }
        echo "\n";
    }
    
    private function checkBannerLayouts()
    {
        echo "2. 🎨 Banner layout'ları kontrol ediliyor...\n";
        
        $stmt = $this->pdo->query("SELECT * FROM banner_layouts ORDER BY id");
        $layouts = $stmt->fetchAll();
        
        echo "   Toplam layout sayısı: " . count($layouts) . "\n";
        foreach ($layouts as $layout) {
            echo "   📄 ID: {$layout['id']}, Name: {$layout['name']}, Type: {$layout['type']}\n";
        }
        
        // Tepe banner layout'u var mı kontrol et
        $stmt = $this->pdo->prepare("SELECT * FROM banner_layouts WHERE type LIKE '%tepe%' OR type LIKE '%top%'");
        $stmt->execute();
        $topLayouts = $stmt->fetchAll();
        
        if (empty($topLayouts)) {
            echo "   ⚠️  Tepe banner layout'u bulunamadı!\n";
        } else {
            echo "   ✅ Tepe banner layout'u mevcut:\n";
            foreach ($topLayouts as $layout) {
                echo "      - ID: {$layout['id']}, Name: {$layout['name']}\n";
            }
        }
        echo "\n";
    }
    
    private function checkBannerGroups()
    {
        echo "3. 📍 Banner group'ları kontrol ediliyor...\n";
        
        $stmt = $this->pdo->query("SELECT * FROM banner_groups ORDER BY id");
        $groups = $stmt->fetchAll();
        
        echo "   Toplam group sayısı: " . count($groups) . "\n";
        foreach ($groups as $group) {
            echo "   📁 ID: {$group['id']}, Name: {$group['name']}, Position: {$group['position']}\n";
        }
        
        // Tepe banner group'u var mı kontrol et
        $stmt = $this->pdo->prepare("SELECT * FROM banner_groups WHERE position LIKE '%tepe%' OR position LIKE '%top%'");
        $stmt->execute();
        $topGroups = $stmt->fetchAll();
        
        if (empty($topGroups)) {
            echo "   ⚠️  Tepe banner group'u bulunamadı!\n";
        } else {
            echo "   ✅ Tepe banner group'u mevcut:\n";
            foreach ($topGroups as $group) {
                echo "      - ID: {$group['id']}, Name: {$group['name']}\n";
            }
        }
        echo "\n";
    }
    
    private function checkTopBannerData()
    {
        echo "4. 🖼️  Tepe banner verileri kontrol ediliyor...\n";
        
        // Aktif banner'ları bul
        $stmt = $this->pdo->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY id");
        $activeBanners = $stmt->fetchAll();
        
        echo "   Toplam aktif banner sayısı: " . count($activeBanners) . "\n";
        
        foreach ($activeBanners as $banner) {
            echo "   🎯 Banner ID: {$banner['id']}\n";
            echo "      Title: " . ($banner['title'] ?? 'YOK') . "\n";
            echo "      Content: " . ($banner['content'] ?? 'YOK') . "\n";
            echo "      Image: " . ($banner['image_path'] ?? 'YOK') . "\n";
            echo "      Link: " . ($banner['link_url'] ?? 'YOK') . "\n";
            echo "      Type ID: " . ($banner['type_id'] ?? 'YOK') . "\n";
            echo "      Group ID: " . ($banner['group_id'] ?? 'YOK') . "\n";
            echo "      ---\n";
        }
        echo "\n";
    }
    
    private function fixTopBannerData()
    {
        echo "5. 🔧 Tepe banner eksiklikleri düzeltiliyor...\n";
        
        // Tepe banner layout'u ekle (yoksa)
        $stmt = $this->pdo->prepare("SELECT id FROM banner_layouts WHERE type LIKE '%tepe%' OR name LIKE '%tepe%'");
        $stmt->execute();
        $topLayout = $stmt->fetch();
        
        if (!$topLayout) {
            echo "   📄 Tepe banner layout'u ekleniyor...\n";
            $stmt = $this->pdo->prepare("
                INSERT INTO banner_layouts (name, type, html_template, css_class, is_active) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                'Tepe Banner Layout',
                'tepe-banner',
                '<div class=\"banner-type-tepe-banner\"><div class=\"banner-container\"><div class=\"banner-item\"><div class=\"banner-image\"><img src=\"{image_url}\" alt=\"{title}\"></div><div class=\"content-box\"><h2 class=\"title\">{title}</h2><p class=\"content\">{content}</p><div class=\"button-container\"><a href=\"{link_url}\" class=\"banner-button\">{button_text}</a></div></div></div></div></div>',
                'banner-type-tepe-banner',
                1
            ]);
            $layoutId = $this->pdo->lastInsertId();
            echo "      ✅ Layout eklendi (ID: {$layoutId})\n";
        } else {
            $layoutId = $topLayout['id'];
            echo "   ✅ Tepe banner layout'u zaten mevcut (ID: {$layoutId})\n";
        }
        
        // Tepe banner group'u ekle (yoksa)
        $stmt = $this->pdo->prepare("SELECT id FROM banner_groups WHERE position LIKE '%tepe%' OR name LIKE '%tepe%'");
        $stmt->execute();
        $topGroup = $stmt->fetch();
        
        if (!$topGroup) {
            echo "   📁 Tepe banner group'u ekleniyor...\n";
            $stmt = $this->pdo->prepare("
                INSERT INTO banner_groups (name, position, order_index, is_active) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                'Tepe Banner',
                'tepe',
                1,
                1
            ]);
            $groupId = $this->pdo->lastInsertId();
            echo "      ✅ Group eklendi (ID: {$groupId})\n";
        } else {
            $groupId = $topGroup['id'];
            echo "   ✅ Tepe banner group'u zaten mevcut (ID: {$groupId})\n";
        }
        
        // Örnek tepe banner verisi ekle (yoksa)
        $stmt = $this->pdo->prepare("SELECT id FROM banners WHERE type_id = ? AND group_id = ? LIMIT 1");
        $stmt->execute([$layoutId, $groupId]);
        $existingBanner = $stmt->fetch();
        
        if (!$existingBanner) {
            echo "   🖼️  Örnek tepe banner verisi ekleniyor...\n";
            $stmt = $this->pdo->prepare("
                INSERT INTO banners (title, content, image_path, link_url, button_text, type_id, group_id, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                'Global Pozitif\'e Hoş Geldiniz',
                'Profesyonel web çözümleri ve dijital pazarlama hizmetleri ile işletmenizi dijital dünyada öne çıkarın.',
                '/Public/Image/banners/tepe-banner-sample.jpg',
                '/hakkimizda',
                'Daha Fazla Bilgi',
                $layoutId,
                $groupId,
                1
            ]);
            $bannerId = $this->pdo->lastInsertId();
            echo "      ✅ Örnek banner eklendi (ID: {$bannerId})\n";
        } else {
            echo "   ✅ Tepe banner verisi zaten mevcut\n";
        }
        
        echo "\n";
    }
}

// Script çalıştırma
if (basename($_SERVER['PHP_SELF']) === 'TopBannerDataFixer.php') {
    $fixer = new TopBannerDataFixer();
    $fixer->checkAndFixTopBanner();
    echo "=== TEPE BANNER VERİ DÜZELTMESİ TAMAMLANDI ===\n";
}
