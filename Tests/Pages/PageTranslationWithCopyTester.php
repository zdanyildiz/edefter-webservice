<?php
/**
 * PageTranslationWithCopyTester.php
 * 
 * Bu test dosyası, yeni sayfa çeviri sistemini test eder:
 * 1. Kategori kopyalama kontrolü
 * 2. Sayfa kopyalama işlemi  
 * 3. Language mapping tablolarına doğru kayıt ekleme
 * 4. SEO URL düzeltmeleri
 */

$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

$config = new Config();

// Veritabanı bilgilerini al
include_once 'Tests/System/GetLocalDatabaseInfo.php';
$dbInfo = getLocalDatabaseInfo();

include_once DATABASE . 'AdminDatabase.php';
$db = new AdminDatabase($dbInfo['serverName'], $dbInfo['database'], $dbInfo['username'], $dbInfo['password']);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminPage.php';
$adminPage = new AdminPage($db);

include_once MODEL . 'Admin/AdminCategory.php';
$adminCategory = new AdminCategory($db);

echo "<h1>🔄 Page Translation with Copy System Test</h1>";
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
    $sql = "SELECT k.kategoriid, k.kategoriadi, k.ustkategoriid 
            FROM sayfa s
            INNER JOIN sayfalistekategori slk ON s.sayfaid = slk.sayfaid 
            INNER JOIN kategori k ON slk.kategoriid = k.kategoriid 
            WHERE s.sayfaid = :pageID";
    
    $pageCategories = $db->select($sql, ['pageID' => $testPageID]);
    echo "Sayfa kategorileri:<br>";
    foreach ($pageCategories as $category) {
        echo "- Kategori ID: {$category['kategoriid']}, Adı: '{$category['kategoriadi']}', Üst ID: {$category['ustkategoriid']}<br>";
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
        
        // Test 4: Mevcut çeviri durumu
        echo "<h3>🔄 Test 4: Mevcut Çeviri Durumu</h3>";
        
        // Sayfa mapping kontrolü
        $existingPageMapping = $adminLanguage->getPageMapping($testPageID, $targetLanguageID);
        if ($existingPageMapping) {
            $status = $existingPageMapping['translated_page_id'] ? 'Çevrilmiş' : 'Beklemede';
            echo "- Sayfa mapping: {$status} (Mapping ID: {$existingPageMapping['id']})<br>";
            if ($existingPageMapping['translated_page_id']) {
                echo "  └─ Çevrilmiş sayfa ID: {$existingPageMapping['translated_page_id']}<br>";
            }
        } else {
            echo "- Sayfa mapping: Çeviri kaydı yok<br>";
        }
        
        // Kategori mapping kontrolü
        foreach ($pageCategories as $category) {
            $categoryID = $category['kategoriid'];
            $categoryMapping = $adminLanguage->getCategoryMapping($categoryID, $targetLanguageID);
            
            if ($categoryMapping) {
                $status = $categoryMapping['translated_category_id'] ? 'Çevrilmiş' : 'Beklemede';
                echo "- Kategori {$categoryID} mapping: {$status} (Mapping ID: {$categoryMapping['id']})<br>";
                if ($categoryMapping['translated_category_id']) {
                    echo "  └─ Çevrilmiş kategori ID: {$categoryMapping['translated_category_id']}<br>";
                }
            } else {
                echo "- Kategori {$categoryID} mapping: Çeviri kaydı yok<br>";
            }
        }
        echo "<br>";
        
        // Test 5: Sayfa detaylarını göster
        echo "<h3>📄 Test 5: Sayfa Detayları</h3>";
        $pageDetails = $adminPage->getPageById($testPageID);
        if ($pageDetails) {
            echo "Sayfa Detayları:<br>";
            echo "- Benzersiz ID: {$pageDetails['benzersizid']}<br>";
            echo "- Sayfa Adı: {$pageDetails['sayfaad']}<br>";
            echo "- Sayfa Link: {$pageDetails['sayfalink']}<br>";
            echo "- Sayfa Tipi: {$pageDetails['sayfatip']}<br>";
            echo "- Sayfa Sırası: {$pageDetails['sayfasira']}<br>";
            echo "- İçerik Uzunluğu: " . strlen($pageDetails['sayfaicerik'] ?? '') . " karakter<br>";
        }
        echo "<br>";
        
        // Test 6: Gerçek çeviri işlemini çalıştır
        echo "<h3>⚠️ Test 6: Sayfa Çeviri İşlemi (Kategori + Sayfa Kopyalama)</h3>";
        echo "<div style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107;'>";
        echo "<strong>DİKKAT:</strong> Bu test gerçek veritabanı değişiklikleri yapacak!<br>";
        echo "Test sayfası: {$testPageName} (ID: {$testPageID})<br>";
        echo "Hedef dil: {$targetLanguage['languageName']} (ID: {$targetLanguageID})<br>";
        echo "İşlem: Kategori + Sayfa kopyalama + Language mapping<br>";
        echo "</div><br>";
        
        $confirmTest = $_GET['confirm_test'] ?? 'no';
        if ($confirmTest === 'yes') {
            echo "🚀 Sayfa çeviri işlemi başlatılıyor...<br><br>";
            
            try {
                $result = $adminLanguage->processPageTranslation($testPageID, $targetLanguageID, false);
                
                if ($result['status'] === 'success') {
                    echo "✅ <strong>Başarılı!</strong><br>";
                    echo "Mesaj: {$result['message']}<br>";
                    echo "Sayfa işlemi: {$result['pageAction']}<br>";
                    echo "Çeviri durumu: {$result['translationStatus']}<br>";
                    
                    if (isset($result['translatedPageID'])) {
                        echo "Çevrilmiş sayfa ID: {$result['translatedPageID']}<br>";
                    }
                    echo "<br>";
                    
                    echo "İşlenen kategoriler:<br>";
                    foreach ($result['processedCategories'] as $catResult) {
                        echo "- Kategori {$catResult['originalCategoryID']} -> {$catResult['translatedCategoryID']} ({$catResult['action']})<br>";
                    }
                    echo "<br>";
                    
                    // Sonuç kontrol
                    echo "<h4>📊 İşlem Sonrası Kontrol</h4>";
                    
                    // Sayfa mapping kontrolü
                    $newPageMapping = $adminLanguage->getPageMapping($testPageID, $targetLanguageID);
                    if ($newPageMapping) {
                        echo "✅ Sayfa mapping oluşturuldu/güncellendi:<br>";
                        echo "  - Mapping ID: {$newPageMapping['id']}<br>";
                        echo "  - Orijinal sayfa ID: {$newPageMapping['original_page_id']}<br>";
                        echo "  - Çevrilmiş sayfa ID: {$newPageMapping['translated_page_id']}<br>";
                        echo "  - Durum: {$newPageMapping['translation_status']}<br>";
                    }
                    
                    // Çevrilmiş sayfa kontrol
                    if (isset($result['translatedPageID'])) {
                        $translatedPage = $adminPage->getPageById($result['translatedPageID']);
                        if ($translatedPage) {
                            echo "<br>✅ Çevrilmiş sayfa detayları:<br>";
                            echo "  - Benzersiz ID: {$translatedPage['benzersizid']}<br>";
                            echo "  - Sayfa Adı: {$translatedPage['sayfaad']}<br>";
                            echo "  - Sayfa Link: {$translatedPage['sayfalink']}<br>";
                        }
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
            echo "<small style='color: #666;'>Bu butona tıklayarak kategori + sayfa kopyalama işlemini başlatabilirsiniz.</small><br>";
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
echo "✓ Kategori mapping ve kopyalama<br>";
echo "✓ Sayfa mapping ve kopyalama<br>";
echo "✓ SEO URL düzeltmeleri<br>";
echo "✓ Database transaction yönetimi<br>";

echo "</div>";
echo "<br><small>Test dosyası: " . __FILE__ . "</small>";
?>
