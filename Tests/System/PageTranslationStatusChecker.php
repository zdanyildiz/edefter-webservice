<?php
// Tests/System/PageTranslationStatusChecker.php
// Sayfa çeviri durumlarını kontrol etmek için

include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

echo "=== SAYFA ÇEVİRİ DURUMU ANALİZİ ===\n";
echo "Tarih: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    // 1. Tüm dilleri listele
    echo "--- MEVCUT DİLLER ---\n";
    $stmt = $pdo->query("SELECT dilid, dilad, dilkisa FROM dil WHERE dilaktif = 1 ORDER BY dilid");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($languages as $lang) {
        $isMain = $lang['dilid'] == 1 ? ' (ANA DİL)' : '';
        echo "• {$lang['dilad']} ({$lang['dilkisa']}) - ID: {$lang['dilid']}{$isMain}\n";
    }
    
    // Ana dil ID'sini belirle (genelde 1)
    $mainLanguageId = 1;
    echo "\nAna Dil ID: {$mainLanguageId}\n";    // 2. Ana dildeki sayfa sayısı
    echo "\n--- ANA DİL SAYFA İSTATİSTİKLERİ ---\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sayfa WHERE sayfasil = 0");
    $mainPageCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Toplam aktif sayfa: {$mainPageCount}\n";

    // 3. Her dil için çeviri durumlarını kontrol et
    echo "\n--- ÇEVİRİ DURUM İSTATİSTİKLERİ ---\n";
    
    foreach ($languages as $lang) {
        if ($lang['dilid'] == $mainLanguageId) continue; // Ana dili atla
        
        $languageId = $lang['dilid'];
        $languageName = $lang['dilad'];
        
        echo "\n🌐 {$languageName} ({$lang['dilkisa']}):\n";
        
        // Çeviri durumlarını say
        $stmt = $pdo->prepare("
            SELECT translation_status, COUNT(*) as count 
            FROM language_page_mapping 
            WHERE dilid = ? 
            GROUP BY translation_status
        ");
        $stmt->execute([$languageId]);
        $statuses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $pending = $statuses['pending'] ?? 0;
        $completed = $statuses['completed'] ?? 0;
        $failed = $statuses['failed'] ?? 0;
        $total = $pending + $completed + $failed;
        
        echo "  ⏳ Bekleyen: {$pending}\n";
        echo "  ✅ Tamamlanan: {$completed}\n";
        echo "  ❌ Başarısız: {$failed}\n";
        echo "  📊 Toplam: {$total}\n";
        
        if ($total > 0) {
            $completionRate = round(($completed / $total) * 100, 1);
            echo "  📈 Tamamlanma Oranı: %{$completionRate}\n";
        }
    }

    // 4. Çevrilmemiş ana dil sayfalarını bul
    echo "\n--- ÇEVİRİLMEMİŞ SAYFALAR ---\n";
    
    foreach ($languages as $lang) {
        if ($lang['dilid'] == $mainLanguageId) continue;
        
        $languageId = $lang['dilid'];
          // Ana dildeki sayfalarda çevirisi olmayan olanları bul
        $stmt = $pdo->prepare("
            SELECT s.sayfaid, s.sayfaad, s.sayfaicerik
            FROM sayfa s
            WHERE s.sayfasil = 0
            AND s.sayfaid NOT IN (
                SELECT lpm.original_page_id 
                FROM language_page_mapping lpm 
                WHERE lpm.dilid = ?
            )
            LIMIT 5
        ");
        $stmt->execute([$languageId]);
        $untranslatedPages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($untranslatedPages)) {
            echo "\n🔍 {$lang['dilad']} diline çevrilmemiş sayfalar:\n";
            foreach ($untranslatedPages as $page) {
                echo "  • {$page['sayfaad']} (ID: {$page['sayfaid']})\n";
            }
        }
    }

    // 5. Önerilen PageList.php geliştirmeleri
    echo "\n--- ÖNERİLEN GELİŞTİRMELER ---\n";
    echo "1. 📊 Çeviri Durumu Sütunu: Her sayfa satırına çeviri durumlarını ekle\n";
    echo "2. 🎯 Filtreleme: 'Çevrilmemiş', 'Bekleyen', 'Tamamlanan' filtresi\n";
    echo "3. 🚀 Toplu Çeviri: Seçilen sayfaları toplu çeviriye gönder\n";
    echo "4. 📈 Progress Bar: Genel çeviri ilerleme göstergesi\n";
    echo "5. 🔄 Otomatik Yenileme: Çeviri durumu değişikliklerini canlı takip\n";

} catch (Exception $e) {
    echo "❌ HATA: " . $e->getMessage() . "\n";
}

echo "\n=== ANALİZ TAMAMLANDI ===\n";
?>
