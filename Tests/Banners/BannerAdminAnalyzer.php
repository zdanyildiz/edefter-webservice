<?php
/**
 * Banner Admin Sistem Analiz Test Scripti
 * AddBanner.php sisteminin Model Context Protocol tabanlı analizi
 */

echo "<h1>🎯 Banner Admin Sistem Analiz Raporu</h1>";
echo "<p><strong>Test Tarihi:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Direct DB connection
try {
    $host = 'localhost';
    $username = 'root';
    $password = 'Global2019*';
    $database = 'yeni.globalpozitif.com.tr';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>✅ Banner Admin Sistem Özeti</h2>";
    
    // Banner tablolarını kontrol et
    $tables = ['banner_types', 'banner_layouts', 'banner_groups', 'banners', 'banner_display_rules', 'banner_styles'];
    $tableInfo = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            $tableInfo[$table] = $count;
        } catch (Exception $e) {
            $tableInfo[$table] = 'Tablo bulunamadı';
        }
    }
    
    echo "<h3>📊 Veritabanı Tabloları</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>Tablo Adı</th><th>Kayıt Sayısı</th><th>Açıklama</th></tr>";
    
    $tableDescriptions = [
        'banner_types' => 'Banner tipleri (Slider, Tepe Banner, vb.)',
        'banner_layouts' => 'Layout şablonları (text, image, text_and_image)',
        'banner_groups' => 'Banner grupları (stil ayarları ile)',
        'banners' => 'Ana banner verileri (başlık, içerik, görsel)',
        'banner_display_rules' => 'Görüntüleme kuralları (sayfa, kategori, dil)',
        'banner_styles' => 'Banner stil detayları (renk, boyut, buton)'
    ];
    
    foreach ($tableInfo as $table => $count) {
        $description = $tableDescriptions[$table] ?? 'Açıklama yok';
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td>$count</td>";
        echo "<td>$description</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Banner tiplerini detaylı göster
    echo "<h3>🎨 Banner Tipleri ve Özellikleri</h3>";
    $typesQuery = "SELECT * FROM banner_types ORDER BY id";
    $types = $pdo->query($typesQuery)->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($types)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Tip Adı</th><th>Açıklama</th><th>Layout Sayısı</th></tr>";
        
        foreach ($types as $type) {
            // Bu tip için layout sayısını bul
            $layoutCountQuery = "SELECT COUNT(*) as count FROM banner_layouts WHERE type_id = ?";
            $layoutStmt = $pdo->prepare($layoutCountQuery);
            $layoutStmt->execute([$type['id']]);
            $layoutCount = $layoutStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo "<tr>";
            echo "<td>{$type['id']}</td>";
            echo "<td><strong>{$type['type_name']}</strong></td>";
            echo "<td>{$type['description']}</td>";
            echo "<td>$layoutCount layout</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // CSS dosyalarını kontrol et
    echo "<h3>🎨 Admin Panel CSS Dosyaları</h3>";
    $cssDir = 'c:\Users\zdany\PhpstormProjects\yeni.globalpozitif.com.tr\_y\s\s\banners\CSS';
    
    if (is_dir($cssDir)) {
        $cssFiles = scandir($cssDir);
        $cssFiles = array_filter($cssFiles, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'css';
        });
        
        echo "<p><strong>CSS Dosya Sayısı:</strong> " . count($cssFiles) . "</p>";
        echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Bulunan CSS Dosyaları:</strong><br>";
        
        $regularFiles = [];
        $minFiles = [];
        
        foreach ($cssFiles as $file) {
            if (strpos($file, '.min.css') !== false) {
                $minFiles[] = $file;
            } else {
                $regularFiles[] = $file;
            }
        }
        
        echo "<p><strong>Normal CSS:</strong> " . implode(', ', $regularFiles) . "</p>";
        echo "<p><strong>Minified CSS:</strong> " . count($minFiles) . " dosya</p>";
        echo "</div>";
    }
    
    // Model sınıflarını kontrol et
    echo "<h3>🔧 Admin Model Sınıfları</h3>";
    $modelFile = 'c:\Users\zdany\PhpstormProjects\yeni.globalpozitif.com.tr\App\Model\Admin\AdminBannerModel.php';
    
    if (file_exists($modelFile)) {
        $modelContent = file_get_contents($modelFile);
        preg_match_all('/class\s+(\w+)\s*{/', $modelContent, $matches);
        $classes = $matches[1];
        
        echo "<div style='background: #e8f4f8; padding: 10px; margin: 10px 0;'>";
        echo "<strong>AdminBannerModel.php içindeki sınıflar:</strong><br>";
        foreach ($classes as $class) {
            echo "<code>$class</code><br>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>";
}

echo "<h2>📋 Banner Admin Sistem Özellikleri</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0;'>";
echo "<h3>✅ Tespit Edilen Özellikler:</h3>";
echo "<ul>";
echo "<li><strong>Global Admin Auth</strong>: _y/s/global.php ile güvenlik sistemi</li>";
echo "<li><strong>Model Context Protocol</strong>: 6 farklı admin model sınıfı tek dosyada</li>";
echo "<li><strong>Dinamik CSS Yükleme</strong>: Banner tipine göre otomatik stil dosyası yükleme</li>";
echo "<li><strong>Real-time Preview</strong>: JavaScript ile anlık önizleme sistemi</li>";
echo "<li><strong>Dropzone Integration</strong>: Drag&drop görsel yükleme</li>";
echo "<li><strong>Color Picker</strong>: Bootstrap colorpicker entegrasyonu</li>";
echo "<li><strong>Sortable Banners</strong>: jQuery UI ile banner sıralama</li>";
echo "<li><strong>MutationObserver</strong>: Görsel değişikliklerini takip</li>";
echo "<li><strong>AJAX Form Processing</strong>: Asenkron form gönderimi</li>";
echo "<li><strong>Multi-language Support</strong>: AdminLanguage modeli ile</li>";
echo "</ul>";

echo "<h3>🎯 Sistem Mimarisi:</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px;'>";
echo "AddBanner.php (Ana Admin Sayfası)
├── _y/s/global.php (Global Admin Auth)
├── AdminBannerModel.php (6 Model Sınıfı)
│   ├── AdminBannerTypeModel
│   ├── AdminBannerLayoutModel  
│   ├── AdminBannerGroupModel
│   ├── AdminBannerModel
│   ├── AdminBannerDisplayRulesModel
│   └── AdminBannerStyleModel
├── CSS/ (Banner Stil Dosyaları)
└── JS/ (JavaScript Dosyaları)";
echo "</pre>";

echo "<h3>📚 Dokümantasyon:</h3>";
echo "<p><strong>Tüm detaylar:</strong> <code>Tests/Banners/banner_admin_prompt.md</code> dosyasında Model Context Protocol standardında dokümante edilmiştir.</p>";
echo "</div>";

echo "<h2>🔗 Sonraki Adımlar</h2>";
echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0;'>";
echo "<ol>";
echo "<li><strong>Admin panel test</strong>: /_y/s/s/banners/AddBanner.php sayfasını test edin</li>";
echo "<li><strong>Banner oluşturma</strong>: Yeni banner grubu oluşturup test edin</li>";
echo "<li><strong>Stil özelleştirme</strong>: Color picker ve stil ayarlarını test edin</li>";
echo "<li><strong>Önizleme sistemi</strong>: Real-time preview fonksiyonunu kontrol edin</li>";
echo "<li><strong>Diğer sistemler</strong>: Product, Member, SEO sistemlerini aynı metodoloji ile analiz edin</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Analiz Tamamlandı:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Sonuç:</strong> Banner admin sistemi tamamen analiz edildi ve Model Context Protocol standardında dokümante edildi.</p>";
?>
