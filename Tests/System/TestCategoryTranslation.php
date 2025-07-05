<?php
/**
 * Kategori Çeviri Sistemi Test Scripti
 * Kategori çeviri durumunu test eder ve AdminCategoryController action'larını doğrular
 */

require_once '../../App/Core/Config.php';
require_once '../../App/Model/Admin/AdminCategory.php';
require_once '../../App/Model/Admin/AdminLanguage.php';

// Config ve Database bağlantısı
$config = new Config();

echo "=== KATEGORİ ÇEVİRİ SİSTEMİ TEST ===\n\n";

try {
    // AdminCategory ve AdminLanguage modellerini oluştur
    $adminCategory = new AdminCategory($config->db);
    $adminLanguage = new AdminLanguage($config->db);

    // Mevcut dilleri listele
    echo "1. Mevcut Diller:\n";
    $languages = $adminLanguage->getAllLanguages();
    foreach ($languages as $lang) {
        echo "   - {$lang['languageName']} (ID: {$lang['languageID']})\n";
    }
    echo "\n";

    // Ana dili tespit et
    $mainLanguage = $adminLanguage->getMainLanguage();
    echo "2. Ana Dil: {$mainLanguage['languageName']} (ID: {$mainLanguage['languageID']})\n\n";

    // Test için kategorileri çek
    echo "3. Kategori Çeviri Durumu Analizi:\n";
    
    foreach ($languages as $lang) {
        echo "\n--- {$lang['languageName']} için kategoriler ---\n";
        
        try {
            $categoriesWithStatus = $adminCategory->getCategoriesWithTranslationStatus($lang['languageID']);
            
            if (empty($categoriesWithStatus)) {
                echo "   Kategori bulunamadı.\n";
                continue;
            }
            
            foreach ($categoriesWithStatus as $category) {
                echo "   Kategori: {$category['categoryName']} (ID: {$category['categoryID']})\n";
                
                if (isset($category['translationStatus'])) {
                    $status = $category['translationStatus'];
                    
                    if (!empty($status['active'])) {
                        echo "     Aktif Çeviriler: " . implode(', ', $status['active']) . "\n";
                    }
                    
                    if (!empty($status['pending'])) {
                        echo "     Bekleyen Çeviriler: " . implode(', ', $status['pending']) . "\n";
                    }
                    
                    if (!empty($status['missing'])) {
                        echo "     Eksik Çeviriler: " . implode(', ', $status['missing']) . "\n";
                    }
                } else {
                    echo "     Çeviri durumu bilgisi yok\n";
                }
            }
            
        } catch (Exception $e) {
            echo "   HATA: " . $e->getMessage() . "\n";
        }
    }

    echo "\n4. Language Category Mapping Tablosu Kontrolü:\n";
    
    // language_category_mapping tablosunu kontrol et
    $sql = "SHOW TABLES LIKE 'language_category_mapping'";
    $stmt = $config->db->prepare($sql);
    $stmt->execute();
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "   ✓ language_category_mapping tablosu mevcut\n";
        
        // Tablo yapısını kontrol et
        $sql = "DESCRIBE language_category_mapping";
        $stmt = $config->db->prepare($sql);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   Sütunlar:\n";
        foreach ($columns as $column) {
            echo "     - {$column['Field']} ({$column['Type']})\n";
        }
        
        // Mevcut kayıtları say
        $sql = "SELECT COUNT(*) as total FROM language_category_mapping";
        $stmt = $config->db->prepare($sql);
        $stmt->execute();
        $count = $stmt->fetch()['total'];
        echo "   Toplam kayıt: $count\n";
        
    } else {
        echo "   ✗ language_category_mapping tablosu bulunamadı!\n";
    }

    echo "\n5. AdminCategoryController Action Test:\n";
    
    // Simüle edilmiş POST request için
    $_POST['action'] = 'getCategoriesWithTranslationStatus';
    $_POST['languageID'] = $mainLanguage['languageID'];
    
    // Output buffering ile controller çıktısını yakala
    ob_start();
    
    try {
        // Controller dosyasını include et
        include '../../App/Controller/Admin/AdminCategoryController.php';
        $output = ob_get_clean();
        
        // JSON response'u parse et
        $response = json_decode($output, true);
        
        if ($response) {
            echo "   Controller Response Status: {$response['status']}\n";
            echo "   Message: {$response['message']}\n";
            
            if (isset($response['categories'])) {
                echo "   Kategori Sayısı: " . count($response['categories']) . "\n";
            }
        } else {
            echo "   Controller Response (Raw): " . substr($output, 0, 200) . "...\n";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "   Controller Test HATASI: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "GENEL HATA: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST TAMAMLANDI ===\n";
?>
