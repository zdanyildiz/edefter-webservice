<?php
/**
 * Ana Dil Olmayan Sayfalar İçin UX Testı
 * Bu script ana dilde olmayan sayfalar için yeni UX yaklaşımını test eder
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../App/Core/Config.php';

try {
    $config = new Config();
    $config->loadClass("Database", "App/Database/Database.php");
    $config->loadClass("AdminPage", "App/Model/Admin/AdminPage.php");
    
    $db = new Database($config);
    $adminPageModel = new AdminPage($db);
    
    echo "=== ANA DİL OLMAYAN SAYFALAR UX TESTİ ===\n\n";
    
    // 1. Ana dil ID'sini tespit et
    $sql = "SELECT dilid, dilad FROM dil WHERE anadil = 1 AND dilsil = 0 AND dilaktif=1 LIMIT 1";
    $mainLangResult = $db->select($sql);
    $mainLanguageID = $mainLangResult ? $mainLangResult[0]['dilid'] : 1;
    $mainLanguageName = $mainLangResult ? $mainLangResult[0]['dilad'] : 'Türkçe';
    
    echo "Ana Dil: {$mainLanguageName} (ID: {$mainLanguageID})\n\n";
    
    // 2. Tüm dilleri listele
    $sql = "SELECT dilid, dilad, anadil FROM dil WHERE dilsil = 0 AND dilaktif=1 ORDER BY dilsira ASC";
    $languages = $db->select($sql);
    
    echo "Mevcut Diller:\n";
    foreach ($languages as $lang) {
        $isMain = $lang['anadil'] == 1 ? ' (ANA DİL)' : '';
        echo "- {$lang['dilad']} (ID: {$lang['dilid']}){$isMain}\n";
    }
    echo "\n";
    
    // 3. Her dil için sayfa analizi yap
    foreach ($languages as $language) {
        $languageID = $language['dilid'];
        $languageName = $language['dilad'];
        $isMainLang = $language['anadil'] == 1;
        
        echo "=== {$languageName} DİLİ ANALİZİ ===\n";
        
        $pages = $adminPageModel->getAllPagesWithTranslationStatus($languageID);
        
        if (empty($pages)) {
            echo "Bu dilde sayfa bulunamadı.\n\n";
            continue;
        }
        
        echo "Toplam Sayfa Sayısı: " . count($pages) . "\n";
        
        // İlk 3 sayfayı örnekle
        $samplePages = array_slice($pages, 0, 3);
        
        foreach ($samplePages as $page) {
            echo "\n--- Sayfa: {$page['pageName']} (ID: {$page['pageID']}) ---\n";
            echo "İsMainLanguage: " . ($page['isMainLanguage'] ? 'Evet' : 'Hayır') . "\n";
            
            if ($page['isMainLanguage']) {
                echo "Bu sayfa ana dilde, çeviri durumu gösterilecek.\n";
                
                // Çeviri durumunu kontrol et
                $translationStatus = $adminPageModel->getPageTranslationStatus($page['pageID']);
                echo "Çeviri Durumu:\n";
                foreach ($translationStatus as $status) {
                    echo "  - {$status['languageName']}: {$status['translationStatus']}\n";
                }
            } else {
                echo "Bu sayfa ana dilde değil, ana dil karşılığı kontrol ediliyor...\n";
                
                // Ana dil karşılığını kontrol et
                $mainEquivalent = $adminPageModel->getMainLanguageEquivalent($page['pageID'], $languageID);
                if ($mainEquivalent) {
                    echo "Ana Dil Karşılığı Bulundu:\n";
                    echo "  - Sayfa: {$mainEquivalent['mainPageName']} (ID: {$mainEquivalent['mainPageID']})\n";
                    echo "  - Dil: {$mainEquivalent['mainLanguageName']}\n";
                    echo "UI Mesajı: '{$mainEquivalent['mainLanguageName']} Karşılığı Var'\n";
                } else {
                    echo "Ana Dil Karşılığı Bulunamadı!\n";
                    echo "UI Mesajı: 'Ana Dil Karşılığı Yok'\n";
                }
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n\n";
    }
    
    // 4. UX Özeti
    echo "=== UX YAKLAŞIMI ÖZETİ ===\n";
    echo "1. Ana dildeki sayfalar: Çeviri durumu badge'leri gösterilir\n";
    echo "2. Ana dilde olmayan sayfalar: 'Türkçe Karşılığı Var' veya 'Ana Dil Karşılığı Yok' uyarısı\n";
    echo "3. Çeviri butonu sadece ana dildeki sayfalar için gösterilir\n";
    echo "4. Bu yaklaşım kullanıcı karışıklığını önler ve daha temiz bir UX sağlar\n\n";
    
    echo "Test tamamlandı!\n";
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
