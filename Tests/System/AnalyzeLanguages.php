<?php
// Sistem dillerini ve SEO link problemini analiz et
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';

try {
    $dbInfo = getLocalDatabaseInfo();
    $dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
    $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== SİSTEM DİLLERİ ANALİZİ ===\n\n";
      // Mevcut dilleri listele
    $stmt = $pdo->query("SELECT dilid, dilad, dilkisa, dilaktif FROM dil WHERE dilsil != 1 ORDER BY dilid");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($languages)) {
        echo "Veritabanında dil bulunamadı!\n";
        exit;
    }
    
    echo "Mevcut Diller:\n";
    echo str_repeat("-", 70) . "\n";
    printf("%-5s | %-20s | %-10s | %-10s\n", "ID", "Dil Adı", "Kısa Kod", "Aktif");
    echo str_repeat("-", 70) . "\n";
    
    foreach ($languages as $lang) {
        printf("%-5s | %-20s | %-10s | %-10s\n", 
            $lang['dilid'], 
            $lang['dilad'] ?: '[Boş]', 
            $lang['dilkisa'] ?: '[Boş]', 
            $lang['dilaktif'] ? 'Evet' : 'Hayır'
        );
    }
    
    echo "\n=== SEO LINK PROBLEM TESTİ ===\n\n";
    
    // Helper sınıfını dahil et
    include_once $documentRoot . $directorySeparator . 'App/Helpers/Helper.php';
    $helper = new Helper();
    
    // Test metinleri - farklı alfabeler
    $testTexts = [
        'Turkish' => 'Türkçe Başlık Şöyle Güzel',
        'English' => 'English Title Example',
        'Arabic' => 'العنوان العربي الجميل',
        'Chinese' => '中文标题示例',
        'Japanese' => '日本語のタイトル例',
        'Korean' => '한국어 제목 예제',
        'Russian' => 'Русский заголовок пример',
        'Hindi' => 'हिंदी शीर्षक उदाहरण',
        'Thai' => 'ตัวอย่างหัวข้อภาษาไทย',
        'Hebrew' => 'כותרת עברית לדוגמה'
    ];
    
    echo "Test Metinleri ve createSeoLink Sonuçları:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-15s | %-30s | %-30s\n", "Dil", "Orijinal Metin", "SEO Link");
    echo str_repeat("-", 80) . "\n";
    
    $problematicLanguages = [];
    
    foreach ($testTexts as $langName => $text) {
        $seoLink = $helper->createSeoLink($text);
        $seoLink = trim($seoLink, '-'); // Başta ve sonda ki - işaretlerini temizle
        
        printf("%-15s | %-30s | %-30s\n", 
            $langName, 
            mb_substr($text, 0, 25) . (mb_strlen($text) > 25 ? '...' : ''), 
            $seoLink ?: '[BOŞ/PROBLEM]'
        );
        
        // Problematik dilleri kaydet
        if (empty($seoLink) || strlen($seoLink) < 3) {
            $problematicLanguages[] = $langName;
        }
    }
    
    echo "\n=== PROBLEMATİK DİLLER ===\n\n";
    if (!empty($problematicLanguages)) {
        echo "SEO link oluşturamayan diller:\n";
        foreach ($problematicLanguages as $lang) {
            echo "- $lang\n";
        }
    } else {
        echo "Tüm diller için SEO link oluşturulabildi.\n";
    }
    
    echo "\n=== ÖNERİLEN ÇÖZÜMLER ===\n\n";
    echo "1. Transliteration (Çeviri Yazı) Sistemi:\n";
    echo "   - Arapça: العربية -> al-arabiya\n";
    echo "   - Çince: 中文 -> zhongwen\n";
    echo "   - Japonca: 日本語 -> nihongo\n\n";
    
    echo "2. Fallback Sistemleri:\n";
    echo "   - UUID tabanlı linkler: /category/uuid-12345\n";
    echo "   - ID tabanlı linkler: /category/id-123\n";
    echo "   - İngilizce çeviri: AI ile İngilizce'ye çevir\n\n";
    
    echo "3. Hibrit Sistem:\n";
    echo "   - Önce transliteration dene\n";
    echo "   - Başarısızsa AI ile İngilizce çevir\n";
    echo "   - Son çare olarak ID kullan\n";

} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
}
?>
