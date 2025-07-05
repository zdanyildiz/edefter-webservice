<?php
// Tests/System/ContentCopierAnalyzer.php
// ContentCopier sonuçlarını detaylı analiz eden script

include_once 'GetLocalDatabaseInfo.php';

// Database bağlantısı kur
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== CONTENT COPIER SONUÇ ANALİZİ ===\n\n";

// 1. İş emirlerinin durumunu kontrol et
function analyzeJobs($pdo) {
    echo "1. İŞ EMRİ DURUMU:\n";
    echo "===================\n";
    
    $sql = "SELECT 
                id, 
                source_language_id, 
                target_language_id, 
                status, 
                translate_with_ai,
                created_at,
                updated_at,
                error_message
            FROM language_copy_jobs 
            ORDER BY id DESC";
    
    $jobs = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($jobs as $job) {
        echo "İş #".$job['id'].":\n";
        echo "  Kaynak Dil: {$job['source_language_id']} → Hedef Dil: {$job['target_language_id']}\n";
        echo "  Durum: {$job['status']}\n";
        echo "  AI Çeviri: ".($job['translate_with_ai'] ? 'Aktif' : 'Pasif')."\n";
        echo "  Oluşturma: {$job['created_at']}\n";
        echo "  Güncelleme: {$job['updated_at']}\n";
        if ($job['error_message']) {
            echo "  Hata: {$job['error_message']}\n";
        }
        echo "  ---\n";
    }
    echo "\n";
}

// 2. Kopyalanan kategorileri analiz et
function analyzeCopiedCategories($pdo) {
    echo "2. KOPYALANMIŞ KATEGORİLER:\n";
    echo "===========================\n";
    
    $sql = "SELECT 
                lm.dilid,
                lm.original_category_id,
                lm.translated_category_id,
                lm.translation_status,
                orig.kategoriad as original_name,
                trans.kategoriad as translated_name,
                orig.benzersizid as original_uniq,
                trans.benzersizid as translated_uniq
            FROM language_category_mapping lm
            LEFT JOIN kategori orig ON lm.original_category_id = orig.kategoriid
            LEFT JOIN kategori trans ON lm.translated_category_id = trans.kategoriid
            ORDER BY lm.dilid, lm.original_category_id";
    
    $mappings = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    $currentLang = null;
    foreach ($mappings as $mapping) {
        if ($currentLang !== $mapping['dilid']) {
            $currentLang = $mapping['dilid'];
            echo "\nDil ID {$currentLang}:\n";
            echo "=========\n";
        }
        
        echo "  Orijinal: #{$mapping['original_category_id']} - {$mapping['original_name']} ({$mapping['original_uniq']})\n";
        echo "  Kopya: #{$mapping['translated_category_id']} - {$mapping['translated_name']} ({$mapping['translated_uniq']})\n";
        echo "  Çeviri Durumu: {$mapping['translation_status']}\n";
        echo "  ---\n";
    }
    echo "\n";
}

// 3. SEO kopyalama başarısını kontrol et
function analyzeSEOCopying($pdo) {
    echo "3. SEO KOPYALAMA ANALİZİ:\n";
    echo "==========================\n";
    
    // Son kopyalanan kategorileri bul
    $sql = "SELECT 
                lm.translated_category_id,
                k.kategoriad,
                k.benzersizid as category_uniq,
                s.seoid,
                s.baslik as seo_title
            FROM language_category_mapping lm
            JOIN kategori k ON lm.translated_category_id = k.kategoriid
            LEFT JOIN seo s ON k.benzersizid = s.benzersizid
            WHERE lm.dilid = 2
            ORDER BY lm.translated_category_id";
    
    $categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    $totalCategories = count($categories);
    $categoriesWithSEO = 0;
    
    echo "Kopyalanan kategoriler:\n";
    foreach ($categories as $category) {
        $seoStatus = $category['seoid'] ? '✅ SEO VAR' : '❌ SEO YOK';
        if ($category['seoid']) $categoriesWithSEO++;
        
        echo "  {$category['kategoriad']} (#{$category['translated_category_id']}) - {$seoStatus}\n";
        if ($category['seo_title']) {
            echo "    SEO Başlık: {$category['seo_title']}\n";
        }
    }
    
    echo "\nÖzet:\n";
    echo "Toplam kopyalanan kategori: {$totalCategories}\n";
    echo "SEO'lu kategori: {$categoriesWithSEO}\n";
    echo "SEO kopyalama başarı oranı: ".($totalCategories > 0 ? round(($categoriesWithSEO/$totalCategories)*100, 2) : 0)."%\n";
    echo "\n";
}

// 4. Sayfa kopyalama analizi
function analyzeCopiedPages($pdo) {
    echo "4. KOPYALANMIŞ SAYFALAR:\n";
    echo "========================\n";
    
    $sql = "SELECT 
                COUNT(*) as total_pages,
                SUM(CASE WHEN lm.translation_status = 'completed' THEN 1 ELSE 0 END) as completed_pages,
                SUM(CASE WHEN lm.translation_status = 'pending' THEN 1 ELSE 0 END) as pending_pages
            FROM language_page_mapping lm
            WHERE lm.dilid = 2";
    
    $stats = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    echo "Dil ID 2 (İngilizce) için sayfa istatistikleri:\n";
    echo "Toplam kopyalanan sayfa: {$stats['total_pages']}\n";
    echo "Tamamlanan sayfa: {$stats['completed_pages']}\n";
    echo "Bekleyen sayfa: {$stats['pending_pages']}\n";
      // Sayfa SEO durumu
    $sql = "SELECT 
                COUNT(DISTINCT lm.translated_page_id) as total_pages,
                COUNT(DISTINCT s.seoid) as pages_with_seo
            FROM language_page_mapping lm
            JOIN sayfa p ON lm.translated_page_id = p.sayfaid
            LEFT JOIN seo s ON p.benzersizid = s.benzersizid
            WHERE lm.dilid = 2";
    
    $seoStats = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    echo "\nSayfa SEO durumu:\n";
    echo "Toplam sayfa: {$seoStats['total_pages']}\n";
    echo "SEO'lu sayfa: {$seoStats['pages_with_seo']}\n";
    echo "Sayfa SEO başarı oranı: ".($seoStats['total_pages'] > 0 ? round(($seoStats['pages_with_seo']/$seoStats['total_pages'])*100, 2) : 0)."%\n";
    echo "\n";
}

// 5. Transaction başarısını kontrol et
function analyzeTransactionSuccess($pdo) {
    echo "5. TRANSACTION BAŞARI KONTROLÜ:\n";
    echo "================================\n";
    
    // Kategori sayısı tutarlılığı
    $sql = "SELECT 
                COUNT(*) as mapping_count
            FROM language_category_mapping 
            WHERE dilid = 2";
    $mappingCount = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['mapping_count'];
    
    $sql = "SELECT 
                COUNT(*) as category_count
            FROM kategori 
            WHERE dilid = 2";
    $categoryCount = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['category_count'];
    
    echo "Kategori mapping kayıtları: {$mappingCount}\n";
    echo "Hedef dilde kategori sayısı: {$categoryCount}\n";
    echo "Tutarlılık: ".($mappingCount === $categoryCount ? '✅ BAŞARILI' : '❌ TUTARSIZLIK VAR')."\n";
    
    // Sayfa sayısı tutarlılığı
    $sql = "SELECT 
                COUNT(*) as mapping_count
            FROM language_page_mapping 
            WHERE dilid = 2";
    $pageMappingCount = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['mapping_count'];
    
    $sql = "SELECT 
                COUNT(*) as page_count
            FROM sayfa 
            WHERE sayfadil = 2";
    $pageCount = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['page_count'];
    
    echo "\nSayfa mapping kayıtları: {$pageMappingCount}\n";
    echo "Hedef dilde sayfa sayısı: {$pageCount}\n";
    echo "Tutarlılık: ".($pageMappingCount === $pageCount ? '✅ BAŞARILI' : '❌ TUTARSIZLIK VAR')."\n";
    echo "\n";
}

// Ana analiz fonksiyonlarını çalıştır
analyzeJobs($pdo);
analyzeCopiedCategories($pdo);
analyzeSEOCopying($pdo);
analyzeCopiedPages($pdo);
analyzeTransactionSuccess($pdo);

echo "=== ANALİZ TAMAMLANDI ===\n";
?>
