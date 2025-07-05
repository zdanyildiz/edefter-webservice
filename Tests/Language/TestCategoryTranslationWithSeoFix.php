<?php
/**
 * Kategori çeviri işleminde SEO URL düzeltmesi ve transaction commit log testi
 * 
 * Test Senaryoları:
 * 1. Kategori çeviri işlemi başlatma
 * 2. SEO URL'inde dil kısaltması değişimini kontrol etme
 * 3. Transaction commit log'unun yazılmasını kontrol etme
 * 4. Mapping tablolarının doğru doldurulduğunu kontrol etme
 */

// Test ortamını hazırla
$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'Tests/System/GetLocalDatabaseInfo.php';
// Veritabanı bilgilerini al
$dbInfo = getLocalDatabaseInfo();

$keyFile = $documentRoot . $directorySeparator . 'App' . $directorySeparator . 'Config' . $directorySeparator . 'Key.php';
if (!file_exists($keyFile)) {
    die("HATA: Key.php dosyası bulunamadı: {$keyFile}");
}               
include_once $keyFile; // $key değişkenini yükler
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';
$config = new Config();

include_once DATABASE . 'AdminDatabase.php';
$db = new AdminDatabase($dbInfo['serverName'], $dbInfo['database'], $dbInfo['username'], $dbInfo['password']);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminCategory.php';
$adminCategory = new AdminCategory($db);

include_once MODEL . 'Admin/AdminSeo.php';
$adminSeo = new AdminSeo($db);

// Test verileri
$sourceLanguageID = 1; // Türkçe (ana dil olduğunu varsayıyoruz)
$targetLanguageID = 2; // İngilizce (test için)

echo "<h2>🧪 Kategori Çeviri ve SEO URL Düzeltme Testi</h2>\n";
echo "<pre>\n";

// 1. Test için örnek bir kategori al
echo "1️⃣ Test kategorisi aranıyor...\n";
$sql = "SELECT * FROM kategori WHERE dilid = :languageID AND kategorisil = 0 LIMIT 1";
$testCategory = $db->select($sql, ['languageID' => $sourceLanguageID]);

if (empty($testCategory)) {
    echo "❌ Test için uygun kategori bulunamadı!\n";
    exit();
}

$testCategory = $testCategory[0];
$originalCategoryID = $testCategory['kategoriid'];
echo "✅ Test kategorisi bulundu: ID={$originalCategoryID}, Ad='{$testCategory['kategoriad']}'\n\n";

// 2. Mevcut SEO kaydını kontrol et
echo "2️⃣ Orijinal SEO kaydı kontrol ediliyor...\n";
$originalSeo = $adminSeo->getSeoByUniqId($testCategory['kategoriUniqID']);
if ($originalSeo) {
    echo "✅ Orijinal SEO bulundu:\n";
    echo "   - Link: {$originalSeo['seoLink']}\n";
    echo "   - Başlık: {$originalSeo['seoTitle']}\n";
} else {
    echo "⚠️ Orijinal SEO kaydı bulunamadı\n";
}
echo "\n";

// 3. Hedef dil bilgilerini al
echo "3️⃣ Hedef dil bilgileri alınıyor...\n";
$targetLanguage = $adminLanguage->getLanguage($targetLanguageID);
if ($targetLanguage) {
    echo "✅ Hedef dil: {$targetLanguage['languageName']} ({$targetLanguage['languageCode']})\n";
} else {
    echo "❌ Hedef dil bulunamadı! ID: {$targetLanguageID}\n";
    exit();
}
echo "\n";

// 4. Mevcut çeviri var mı kontrol et
echo "4️⃣ Mevcut çeviri kontrolü...\n";
$existingMapping = $adminLanguage->getCategoryMapping($originalCategoryID, $targetLanguageID);
if ($existingMapping) {
    echo "⚠️ Bu kategori için zaten çeviri var (ID: {$existingMapping['translated_category_id']})\n";
    echo "   Test devam ediyor...\n";
} else {
    echo "✅ Yeni çeviri oluşturulacak\n";
}
echo "\n";

// 5. Kategori çeviri işlemini başlat
echo "5️⃣ Kategori çeviri işlemi başlatılıyor...\n";
$startTime = microtime(true);

try {
    $result = $adminLanguage->copyAndTranslateCategory($originalCategoryID, $targetLanguageID, false);
    
    $endTime = microtime(true);
    $executionTime = round(($endTime - $startTime) * 1000, 2); // milisaniye
    
    if ($result['status'] === 'success') {
        echo "✅ Kategori çevirisi başarılı! (Süre: {$executionTime}ms)\n";
        echo "   - Orijinal ID: {$result['originalCategoryID']}\n";
        echo "   - Çeviri ID: {$result['translatedCategoryID']}\n";
        echo "   - Durum: {$result['translationStatus']}\n";
        
        $newCategoryID = $result['translatedCategoryID'];
        
        // 6. Yeni kategoriyi kontrol et
        echo "\n6️⃣ Yeni kategori kontrol ediliyor...\n";
        $newCategory = $adminCategory->getCategory($newCategoryID);
        if ($newCategory) {
            echo "✅ Yeni kategori oluşturuldu:\n";
            echo "   - ID: {$newCategory['categoryID']}\n";
            echo "   - Ad: {$newCategory['categoryName']}\n";
            echo "   - Dil ID: {$newCategory['languageID']}\n";
            echo "   - Unique ID: {$newCategory['categoryUniqID']}\n";
        }
        
        // 7. Yeni SEO kaydını kontrol et
        echo "\n7️⃣ Yeni SEO kaydı kontrol ediliyor...\n";
        $newSeo = $adminSeo->getSeoByUniqId($newCategory['categoryUniqID']);
        if ($newSeo) {
            echo "✅ Yeni SEO kaydı oluşturuldu:\n";
            echo "   - Yeni Link: {$newSeo['seoLink']}\n";
            echo "   - Başlık: {$newSeo['seoTitle']}\n";
            
            // SEO URL düzeltmesi kontrolü
            if ($originalSeo && $newSeo) {
                echo "\n🔍 SEO URL Karşılaştırması:\n";
                echo "   - Orijinal: {$originalSeo['seoLink']}\n";
                echo "   - Yeni: {$newSeo['seoLink']}\n";
                
                // Dil kısaltması değişim kontrolü
                if (strpos($newSeo['seoLink'], "/{$targetLanguage['languageCode']}/") !== false) {
                    echo "✅ SEO URL'inde dil kısaltması doğru güncellendi!\n";
                } else {
                    echo "⚠️ SEO URL'inde dil kısaltması güncellenmemiş olabilir\n";
                }
            }
        } else {
            echo "⚠️ Yeni SEO kaydı bulunamadı\n";
        }
        
        // 8. Mapping tablolarını kontrol et
        echo "\n8️⃣ Mapping tabloları kontrol ediliyor...\n";
        $categoryMapping = $adminLanguage->getCategoryMapping($originalCategoryID, $targetLanguageID);
        if ($categoryMapping) {
            echo "✅ Kategori mapping kaydı bulundu:\n";
            echo "   - Mapping ID: {$categoryMapping['id']}\n";
            echo "   - Orijinal ID: {$categoryMapping['original_category_id']}\n";
            echo "   - Çeviri ID: {$categoryMapping['translated_category_id']}\n";
            echo "   - Dil ID: {$categoryMapping['dilid']}\n";
            echo "   - Durum: {$categoryMapping['translation_status']}\n";
        } else {
            echo "❌ Kategori mapping kaydı bulunamadı!\n";
        }
        
    } else {
        echo "❌ Kategori çevirisi başarısız: {$result['message']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Hata oluştu: " . $e->getMessage() . "\n";
}

echo "\n";

// 9. Son log kayıtlarını kontrol et
echo "9️⃣ Son transaction log kayıtları:\n";
$logFile = PUBL . 'Log/Admin/' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", $logContent);
    $recentLogs = array_slice($logLines, -10); // Son 10 satır
    
    $transactionLogs = array_filter($recentLogs, function($line) {
        return strpos($line, 'transaction') !== false;
    });
    
    if (!empty($transactionLogs)) {
        foreach ($transactionLogs as $log) {
            echo "📋 " . trim($log) . "\n";
        }
    } else {
        echo "⚠️ Son 10 log kaydında transaction ile ilgili kayıt bulunamadı\n";
    }
} else {
    echo "⚠️ Admin log dosyası bulunamadı: {$logFile}\n";
}

echo "\n";
echo "🏁 Test tamamlandı!\n";
echo "</pre>\n";
?>
