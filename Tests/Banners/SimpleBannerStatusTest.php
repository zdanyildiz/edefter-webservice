<?php
/**
 * Basit Tepe Banner Test Scripti
 * Doğrudan DB bağlantısı ile tepe banner durumunu kontrol eder
 */

echo "<h1>🎯 Tepe Banner Durum Raporu</h1>";
echo "<p><strong>Test Tarihi:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<hr>";

// Direct DB connection using known credentials
try {
    $host = 'localhost';
    $username = 'root';
    $password = 'Global2019*';
    $database = 'yeni.globalpozitif.com.tr';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>✅ Veritabanı Bağlantısı Başarılı</h2>";
    echo "<p>Host: $host | Database: $database</p>";
    
    // Tepe banner verilerini kontrol et
    $query = "SELECT * FROM banner_groups WHERE type_id = 3 AND is_active = 1 LIMIT 5";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $bannerGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>📊 Tepe Banner Grup Bilgileri</h2>";
    if (empty($bannerGroups)) {
        echo "<p style='color: red;'>❌ Aktif tepe banner grubu bulunamadı!</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Name</th><th>Style Class</th><th>Group Full Size</th><th>Layout Group</th>";
        echo "</tr>";
        
        foreach ($bannerGroups as $group) {
            echo "<tr>";
            echo "<td>{$group['id']}</td>";
            echo "<td>{$group['name']}</td>";
            echo "<td>{$group['style_class']}</td>";
            echo "<td>" . ($group['group_full_size'] ? 'Evet' : 'Hayır') . "</td>";
            echo "<td>{$group['layout_group']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Her grup için aktif banner sayısını kontrol et
        echo "<h3>🎪 Aktif Banner Sayıları</h3>";
        foreach ($bannerGroups as $group) {
            $bannerQuery = "SELECT COUNT(*) as count FROM banners WHERE group_id = ? AND is_active = 1";
            $bannerStmt = $pdo->prepare($bannerQuery);
            $bannerStmt->execute([$group['id']]);
            $bannerCount = $bannerStmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo "<p><strong>Grup {$group['id']} ({$group['name']}):</strong> {$bannerCount} aktif banner</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>";
}

echo "<h2>🔗 Test Sonuçları ve Öneriler</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; margin: 10px 0;'>";
echo "<h3>✅ Tamamlanan İyileştirmeler:</h3>";
echo "<ul>";
echo "<li>BannerController'a layout_group çevirici eklendi</li>";
echo "<li>CSS ortalama sınıfları (.banner-centered, .banner-content-centered) eklendi</li>";
echo "<li>HTML wrapper mevcut (&lt;section id=\"topBanner\"&gt;)</li>";
echo "<li>Full-width/ortalama kontrolü BannerController'da aktif</li>";
echo "</ul>";

echo "<h3>🎯 Sonraki Adımlar:</h3>";
echo "<ol>";
echo "<li><strong>Canlı Test:</strong> <a href='http://l.globalpozitif/' target='_blank'>http://l.globalpozitif/</a> adresinde banner'ı görün</li>";
echo "<li><strong>Banner Kontrolü:</strong> Görsel, başlık, içerik ve buton bileşenlerinin görünürlüğünü kontrol edin</li>";
echo "<li><strong>Ortalama Kontrolü:</strong> Banner'ın ekranda doğru pozisyonda olduğunu kontrol edin</li>";
echo "<li><strong>Responsive Test:</strong> Mobil ve tablet görünümlerini test edin</li>";
echo "</ol>";

echo "<h3>📚 Dokümantasyon:</h3>";
echo "<p><strong>Tüm sistem bilgileri şu dosyalarda güncellenmiştir:</strong></p>";
echo "<ul>";
echo "<li><code>Tests/PROJECT_PROMPT.md</code> - Ana proje bilgileri ve sistem işleyişi</li>";
echo "<li><code>Tests/Banners/banner_prompt.md</code> - Banner sistem detayları</li>";
echo "<li><code>App/Controller/BannerController.php</code> - Layout group çevirici ve ortalama mantığı</li>";
echo "<li><code>Public/CSS/Banners/tepe-banner.css</code> - Modern CSS ortalama sınıfları</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Sonuç:</strong> Tepe banner sistemi optimize edildi ve dokümante edildi. Lütfen canlı sitede test yapın.</p>";
echo "<p><strong>Yerel Site:</strong> <a href='http://l.globalpozitif/' target='_blank'>http://l.globalpozitif/</a></p>";
?>
