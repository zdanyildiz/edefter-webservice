<?php
/**
 * Tepe Banner Canlı Test Scripti
 * Gerçek site üzerinde tepe banner görüntülemesini test eder
 * Config sistemi ile doğru DB bağlantısı kurar
 */

// Dosya yolu ve Config sistemi
$projectRoot = 'c:\Users\zdany\PhpstormProjects\yeni.globalpozitif.com.tr';
require_once $projectRoot . '/App/Core/Config.php';

// Test başlangıcı
echo "<h1>🎯 Tepe Banner Canlı Test Sonuçları</h1>";
echo "<p><strong>Test Tarihi:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Yerel Site Adresi:</strong> <a href='http://l.globalpozitif/' target='_blank'>http://l.globalpozitif/</a></p>";
echo "<hr>";

try {
    // Config sistemi ile DB bağlantısı
    $config = new Config();
    
    echo "<h2>🔧 Sistem Konfigürasyonu</h2>";
    echo "<div style='background: #f0f8ff; padding: 10px; margin: 10px 0;'>";
    echo "<strong>Database Host:</strong> " . $config->dbServerName . "<br>";
    echo "<strong>Database Name:</strong> " . $config->dbName . "<br>";
    echo "<strong>Database User:</strong> " . $config->dbUsername . "<br>";
    echo "<strong>Localhost:</strong> " . ($config->localhost ? 'Evet' : 'Hayır') . "<br>";
    echo "<strong>HTTP Protocol:</strong> " . $config->http . "<br>";
    echo "</div>";

    // PDO bağlantısı
    $pdo = new PDO(
        "mysql:host={$config->dbServerName};dbname={$config->dbName};charset=utf8",
        $config->dbUsername,
        $config->dbPassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "<h2>📊 Tepe Banner Veritabanı Durumu</h2>";
    
    // Tepe banner verilerini çek
    $query = "
        SELECT 
            bg.id as group_id,
            bg.name as group_name,
            bg.style_class,
            bg.group_full_size,
            bg.banner_full_size,
            bg.layout_group,
            COUNT(b.id) as banner_count,
            GROUP_CONCAT(b.id) as banner_ids,
            GROUP_CONCAT(b.title SEPARATOR ' | ') as banner_titles
        FROM banner_groups bg
        LEFT JOIN banners b ON bg.id = b.group_id AND b.is_active = 1
        WHERE bg.type_id = 3 AND bg.is_active = 1
        GROUP BY bg.id
        ORDER BY bg.order_index
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $bannerGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($bannerGroups)) {
        echo "<div style='color: red;'>❌ Aktif tepe banner grubu bulunamadı!</div>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 12px;'>";
        echo "<tr style='background: #f5f5f5;'>
                <th>Grup ID</th>
                <th>Grup Adı</th>
                <th>Style Class</th>
                <th>Group Full Size</th>
                <th>Banner Full Size</th>
                <th>Layout Group</th>
                <th>Banner Sayısı</th>
                <th>Banner IDs</th>
                <th>Banner Başlıkları</th>
              </tr>";
        
        foreach ($bannerGroups as $group) {
            echo "<tr>";
            echo "<td>{$group['group_id']}</td>";
            echo "<td>{$group['group_name']}</td>";
            echo "<td>{$group['style_class']}</td>";
            echo "<td>" . ($group['group_full_size'] ? 'Evet' : 'Hayır') . "</td>";
            echo "<td>" . ($group['banner_full_size'] ? 'Evet' : 'Hayır') . "</td>";
            echo "<td>{$group['layout_group']}</td>";
            echo "<td>{$group['banner_count']}</td>";
            echo "<td>{$group['banner_ids']}</td>";
            echo "<td>" . (strlen($group['banner_titles']) > 50 ? substr($group['banner_titles'], 0, 50) . '...' : $group['banner_titles']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<h2>🎨 CSS Sınıf Analizi</h2>";
    
    // CSS sınıflarını analiz et
    foreach ($bannerGroups as $group) {
        echo "<h3>Grup {$group['group_id']}: {$group['group_name']}</h3>";
        echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0;'>";
        
        // BannerController mantığını simüle et
        $containerClass = '';
        if ($group['group_full_size'] == 0) {
            $containerClass .= ' banner-centered';
        }
        if ($group['banner_full_size'] == 0) {
            $containerClass .= ' banner-content-centered';
        }
        
        echo "<strong>Otomatik Eklenen CSS Sınıfları:</strong><br>";
        echo "<code>banner-group-{$group['group_id']} banner-type-tepe-banner {$group['style_class']}</code><br>";
        echo "<strong>Container Sınıfları:</strong><br>";
        echo "<code>banner-container{$containerClass}</code><br>";
        
        // Ortalama durumu
        if ($group['group_full_size'] == 0 || $group['banner_full_size'] == 0) {
            echo "<div style='color: green;'>✅ Bu banner ortalanacak</div>";
        } else {
            echo "<div style='color: blue;'>📏 Bu banner tam genişlikte olacak</div>";
        }
        
        echo "</div>";
    }

} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Hata: " . $e->getMessage() . "</div>";
}

echo "<h2>🔗 Test Linkleri ve Sonraki Adımlar</h2>";
echo "<div style='background: #e8f4f8; padding: 15px; margin: 10px 0;'>";
echo "<h3>Gerçek Site Test:</h3>";
echo "<ol>";
echo "<li><strong>Ana sayfayı açın:</strong> <a href='http://l.globalpozitif/' target='_blank'>http://l.globalpozitif/</a></li>";
echo "<li><strong>Tepe banner kontrolü:</strong> Sayfanın üst kısmında banner görüntülenip görüntülenmediğini kontrol edin</li>";  
echo "<li><strong>Görsel bileşen kontrolü:</strong> Banner'ın görsel, başlık, içerik ve buton bileşenlerinin tamamının görünür olduğunu doğrulayın</li>";
echo "<li><strong>Ortalama/genişlik kontrolü:</strong> Banner'ın ortalama/tam genişlik ayarının doğru çalıştığını kontrol edin</li>";
echo "<li><strong>Responsive test:</strong> Farklı ekran boyutlarında (mobil, tablet, desktop) test edin</li>";
echo "</ol>";

echo "<h3>Geliştirici Notları:</h3>";
echo "<ul>";
echo "<li>Bu test Config sistemi kullanarak doğru DB bağlantısı kuruyor</li>";
echo "<li>Banner verileri doğrudan canlı veritabanından çekiliyor</li>";
echo "<li>BannerController mantığı simüle edilerek CSS sınıfları hesaplanıyor</li>";
echo "<li>Tüm sistem bilgileri PROJECT_PROMPT.md ve banner_prompt.md dosyalarında dokümante edildi</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Test Tamamlandı:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Sonraki Adım:</strong> Gerçek site üzerinde görsel test yapın: <a href='http://l.globalpozitif/' target='_blank'>http://l.globalpozitif/</a></p>";
?>
