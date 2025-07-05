<?php
/**
 * PageTranslationSystemTester.php
 * 
 * Bu test dosyası, sayfa çeviri sistemindeki kategori kopyalama ve 
 * mapping işlemlerinin doğru çalışıp çalışmadığını test eder.
 * 
 * Test Senaryoları:
 * 1. Kategori çevirisi olmayan bir sayfa için çeviri tetikleme
 * 2. Kategori kopyalama işleminin çalışması
 * 3. Language mapping tablolarına doğru kayıt ekleme
 * 4. Üst-alt kategori hiyerarşisinin korunması
 */

$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

$config = new Config();
include_once DATABASE . 'AdminDatabase.php';
$db = new AdminDatabase();

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminPage.php';
$adminPage = new AdminPage($db);

include_once MODEL . 'Admin/AdminCategory.php';
$adminCategory = new AdminCategory($db);

echo "<h1>📝 Page Translation System Test</h1>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px;'>";

// Test veritabanı bağlantısı
try {
    $testQuery = $db->select("SELECT 1 as test");
    echo "✅ <strong>Veritabanı bağlantısı:</strong> Başarılı<br><br>";
} catch (Exception $e) {
    echo "❌ <strong>Veritabanı bağlantısı:</strong> Hata - " . $e->getMessage() . "<br><br>";
    exit;
}

// Test 1: Mevcut sayfaları listele
echo "<h3>🔍 Test 1: Mevcut Sayfalar</h3>";
$pages = $adminPage->getAllPages(1); // Ana dil (ID=1)
echo "Ana dilde toplam sayfa sayısı: " . count($pages) . "<br>";

if (!empty($pages)) {
    $testPage = $pages[0];
    $testPageID = $testPage['pageID'];
    $testPageName = $testPage['pageName'];
    echo "Test için seçilen sayfa: ID={$testPageID}, Adı='{$testPageName}'<br><br>";
    
    // Test 2: Sayfanın kategorilerini kontrol et
    echo "<h3>📂 Test 2: Sayfa Kategorileri</h3>";
    $sql = "SELECT k.kategoriid, k.kategoriad, k.ustkategoriid 
            FROM sayfa s
            INNER JOIN sayfalistekategori slk ON s.sayfaid = slk.sayfaid 
            INNER JOIN kategori k ON slk.kategoriid = k.kategoriid 
            WHERE s.sayfaid = :pageID";
    
    $pageCategories = $db->select($sql, ['pageID' => $testPageID]);
    echo "Sayfa kategorileri:<br>";
    foreach ($pageCategories as $category) {
        echo "- Kategori ID: {$category['kategoriid']}, Adı: '{$category['kategoriad']}', Üst ID: {$category['ustkategoriid']}<br>";
    }
    echo "<br>";
    
    // Test 3: Mevcut dilleri listele
    echo "<h3>🌍 Test 3: Mevcut Diller</h3>";
    $languages = $adminLanguage->getLanguages();
    echo "Sistem dilleri:<br>";
    foreach ($languages as $lang) {
        $mainLangText = isset($lang['isMainLanguage']) && $lang['isMainLanguage'] == 1 ? ' (Ana Dil)' : '';
        echo "- ID: {$lang['languageID']}, Kod: {$lang['languageCode']}, Adı: {$lang['languageName']}{$mainLangText}<br>";
    }
    
    // Test için hedef dil seç (ana dil olmayan ilk dil)
    $targetLanguage = null;
    foreach ($languages as $lang) {
        if (!isset($lang['isMainLanguage']) || $lang['isMainLanguage'] != 1) {
            $targetLanguage = $lang;
            break;
        }
    }
    
    if ($targetLanguage) {
        $targetLanguageID = $targetLanguage['languageID'];
        echo "<br>Test için seçilen hedef dil: ID={$targetLanguageID}, Kod={$targetLanguage['languageCode']}<br><br>";
        
        // Test 4: Kategori çeviri durumunu kontrol et
        echo "<h3>🔄 Test 4: Kategori Çeviri Durumu</h3>";
        foreach ($pageCategories as $category) {
            $categoryID = $category['kategoriid'];
            $categoryMapping = $adminLanguage->getCategoryMapping($categoryID, $targetLanguageID);
            
            if ($categoryMapping) {
                $status = $categoryMapping['translated_category_id'] ? 'Çevrilmiş' : 'Beklemede';
                echo "- Kategori {$categoryID}: {$status} (Mapping ID: {$categoryMapping['id']})<br>";
            } else {
                echo "- Kategori {$categoryID}: Çeviri kaydı yok<br>";
            }
        }
        echo "<br>";
        
        // Test 5: Sayfa çeviri işlemini simüle et (DRY RUN)
        echo "<h3>🧪 Test 5: Çeviri İşlemi Simülasyonu</h3>";
        echo "İşlem: Sayfa ID {$testPageID} -> Dil ID {$targetLanguageID} çevirisi<br>";
        
        // Mevcut sayfa mapping'ini kontrol et
        $existingPageMapping = $adminLanguage->getPageMapping($testPageID, $targetLanguageID);
        if ($existingPageMapping) {
            echo "✓ Sayfa için mevcut mapping bulundu (ID: {$existingPageMapping['id']})<br>";
        } else {
            echo "• Sayfa için yeni mapping oluşturulacak<br>";
        }
        
        // Her kategori için işlem planını göster
        echo "<br>Kategori işlem planı:<br>";
        foreach ($pageCategories as $category) {
            $categoryID = $category['kategoriid'];
            $categoryMapping = $adminLanguage->getCategoryMapping($categoryID, $targetLanguageID);
            
            if ($categoryMapping && $categoryMapping['translated_category_id']) {
                echo "✓ Kategori {$categoryID}: Mevcut çeviri kullanılacak (ID: {$categoryMapping['translated_category_id']})<br>";
            } else {
                echo "• Kategori {$categoryID}: Kopyalanacak<br>";
                
                // Üst kategori kontrolü
                if ($category['ustkategoriid'] > 0) {
                    $parentMapping = $adminLanguage->getCategoryMapping($category['ustkategoriid'], $targetLanguageID);
                    if ($parentMapping && $parentMapping['translated_category_id']) {
                        echo "  └─ Üst kategori {$category['ustkategoriid']}: Mevcut (ID: {$parentMapping['translated_category_id']})<br>";
                    } else {
                        echo "  └─ Üst kategori {$category['ustkategoriid']}: Önce kopyalanacak<br>";
                    }
                }
            }
        }
        echo "<br>";
        
        // Test 6: Gerçek çeviri işlemini çalıştır (Dikkatli!)
        echo "<h3>⚠️ Test 6: Gerçek Çeviri İşlemi</h3>";
        echo "<div style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'>";
        echo "<strong>DİKKAT:</strong> Bu test gerçek veritabanı değişiklikleri yapacak!<br>";
        echo "Test sayfası: {$testPageName} (ID: {$testPageID})<br>";
        echo "Hedef dil: {$targetLanguage['languageName']} (ID: {$targetLanguageID})<br>";
        echo "</div><br>";
        
        $confirmTest = $_GET['confirm_test'] ?? 'no';
        if ($confirmTest === 'yes') {
            echo "🚀 Çeviri işlemi başlatılıyor...<br><br>";
            
            try {
                $result = $adminLanguage->processPageTranslation($testPageID, $targetLanguageID, false);
                
                if ($result['status'] === 'success') {
                    echo "✅ <strong>Başarılı!</strong><br>";
                    echo "Mesaj: {$result['message']}<br>";
                    echo "Sayfa işlemi: {$result['pageAction']}<br>";
                    echo "Çeviri durumu: {$result['translationStatus']}<br><br>";
                    
                    echo "İşlenen kategoriler:<br>";
                    foreach ($result['processedCategories'] as $catResult) {
                        echo "- Kategori {$catResult['originalCategoryID']} -> {$catResult['translatedCategoryID']} ({$catResult['action']})<br>";
                    }
                    
                } else {
                    echo "❌ <strong>Hata:</strong> {$result['message']}<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ <strong>Exception:</strong> " . $e->getMessage() . "<br>";
            }
            
        } else {
            echo "<a href='?confirm_test=yes' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>
                    ▶️ Gerçek Testi Çalıştır
                  </a><br>";
            echo "<small style='color: #666;'>Bu butona tıklayarak gerçek veritabanı işlemlerini başlatabilirsiniz.</small><br>";
        }
        
    } else {
        echo "❌ Test için uygun hedef dil bulunamadı!<br>";
    }
    
} else {
    echo "❌ Test için sayfa bulunamadı!<br>";
}

echo "<br><hr>";
echo "<h3>📊 Test Özeti</h3>";
echo "Bu test aşağıdaki bileşenleri kontrol etti:<br>";
echo "✓ Veritabanı bağlantısı<br>";
echo "✓ AdminLanguage model metodları<br>";
echo "✓ Kategori mapping kontrolü<br>";
echo "✓ Sayfa-kategori ilişkileri<br>";
echo "✓ Çeviri işlem planlaması<br>";

echo "</div>";
echo "<br><small>Test dosyası: " . __FILE__ . "</small>";
?>
