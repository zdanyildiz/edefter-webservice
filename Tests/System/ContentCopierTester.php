<?php
// Tests/System/ContentCopierTester.php
// ContentCopier sistemini test eden script

$documentRoot = str_replace("\\","/",realpath(dirname(__FILE__, 3)));
include_once 'GetLocalDatabaseInfo.php';

// Database bağlantısı kur
$dbInfo = getLocalDatabaseInfo();
$dsn = "mysql:host={$dbInfo['serverName']};dbname={$dbInfo['database']};charset=utf8";
$pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== CONTENT COPIER TEST ARAÇLARI ===\n\n";

// 1. Mevcut dilleri listele
function listLanguages($pdo) {
    echo "1. MEVCUT DİLLER:\n";
    echo "================\n";
    
    $sql = "SELECT dilid, dilad, dilkisa, dilaktif FROM dil ORDER BY dilid";
    $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        foreach ($result as $lang) {
            $status = $lang['dilaktif'] ? 'Aktif' : 'Pasif';
            echo "ID: {$lang['dilid']} | Adı: {$lang['dilad']} | Kod: {$lang['dilkisa']} | Durum: {$status}\n";
        }
    } else {
        echo "Dil bulunamadı.\n";
    }
    echo "\n";
}

// 2. Pending copy job'ları listele
function listPendingJobs($pdo) {
    echo "2. BEKLEYİN KOPYALAMA İŞLERİ:\n";
    echo "=============================\n";
    
    $sql = "SELECT * FROM language_copy_jobs WHERE status IN ('pending', 'processing') ORDER BY created_at DESC";
    $result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        foreach ($result as $job) {
            echo "ID: {$job['id']} | Kaynak Dil: {$job['source_language_id']} | Hedef Dil: {$job['target_language_id']}\n";
            echo "Durum: {$job['status']} | AI Çeviri: " . ($job['translate_with_ai'] ? 'Evet' : 'Hayır') . "\n";
            echo "Oluşturulma: {$job['created_at']}\n";
            if ($job['error_message']) {
                echo "Hata: {$job['error_message']}\n";
            }
            echo "---\n";
        }
    } else {
        echo "Bekleyen iş bulunamadı.\n";
    }
    echo "\n";
}

// 3. Tablo yapısını kontrol et
function checkTables($pdo) {
    echo "3. TABLO YAPISINI KONTROL:\n";
    echo "==========================\n";
    
    $tables = [
        'language_copy_jobs' => 'Kopyalama iş tablosu',
        'language_category_mapping' => 'Kategori eşleme tablosu',
        'language_page_mapping' => 'Sayfa eşleme tablosu',
        'kategori' => 'Kategori tablosu',
        'sayfa' => 'Sayfa tablosu',
        'seo' => 'SEO tablosu'
    ];
    
    foreach ($tables as $table => $description) {
        try {
            $sql = "SELECT COUNT(*) as count FROM $table";
            $result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            echo "✅ {$table} ({$description}): {$result['count']} kayıt\n";
        } catch (PDOException $e) {
            echo "❌ {$table} ({$description}): TABLO BULUNAMADI\n";
        }
    }
    echo "\n";
}

// 4. SEO tablosunu kontrol et
function checkSeoTable($pdo) {
    echo "4. SEO TABLOSU ANALİZİ:\n";
    echo "=======================\n";
    
    try {
        // Toplam SEO kaydı
        $sql = "SELECT COUNT(*) as total FROM seo";
        $total = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
        echo "Toplam SEO kaydı: {$total}\n";
        
        // Benzersiz ID'leri kontrol et
        $sql = "SELECT COUNT(DISTINCT benzersizid) as unique_ids FROM seo";
        $uniqueIds = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC)['unique_ids'];
        echo "Benzersiz ID sayısı: {$uniqueIds}\n";
        
        // Boş alanları kontrol et
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN baslik IS NULL OR baslik = '' THEN 1 ELSE 0 END) as empty_title,
                    SUM(CASE WHEN aciklama IS NULL OR aciklama = '' THEN 1 ELSE 0 END) as empty_desc,
                    SUM(CASE WHEN kelime IS NULL OR kelime = '' THEN 1 ELSE 0 END) as empty_keywords
                FROM seo";
        $stats = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        echo "Boş başlık: {$stats['empty_title']}\n";
        echo "Boş açıklama: {$stats['empty_desc']}\n";
        echo "Boş kelimeler: {$stats['empty_keywords']}\n";
        
        // Örnek kayıtlar
        echo "\nÖrnek SEO kayıtları:\n";
        $sql = "SELECT benzersizid, baslik, aciklama FROM seo LIMIT 3";
        $examples = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($examples as $example) {
            echo "- UniqID: {$example['benzersizid']} | Başlık: " . mb_substr($example['baslik'], 0, 50) . "...\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ SEO tablosu kontrol edilemedi: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// 5. Kategori-SEO ilişkisini kontrol et
function checkCategorySeoRelation($pdo) {
    echo "5. KATEGORİ-SEO İLİŞKİSİ:\n";
    echo "==========================\n";
    
    try {
        // Kategoriler ve SEO eşleşmesi
        $sql = "SELECT 
                    COUNT(k.kategoriid) as total_categories,
                    COUNT(s.seoid) as linked_seo_count
                FROM kategori k
                LEFT JOIN seo s ON k.benzersizid = s.benzersizid
                WHERE k.kategorisil = 0";
        $result = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        echo "Toplam kategori: {$result['total_categories']}\n";
        echo "SEO'lu kategori: {$result['linked_seo_count']}\n";
        
        // SEO'suz kategoriler
        $sql = "SELECT k.kategoriid, k.kategoriad, k.benzersizid
                FROM kategori k
                LEFT JOIN seo s ON k.benzersizid = s.benzersizid
                WHERE k.kategorisil = 0 AND s.seoid IS NULL
                LIMIT 5";
        $noSeoCategories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        
        if ($noSeoCategories) {
            echo "\nSEO'suz kategoriler (ilk 5):\n";
            foreach ($noSeoCategories as $cat) {
                echo "- ID: {$cat['kategoriid']} | Adı: {$cat['kategoriad']} | UniqID: {$cat['benzersizid']}\n";
            }
        }
        
    } catch (PDOException $e) {
        echo "❌ Kategori-SEO ilişkisi kontrol edilemedi: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// 6. Test için sample copy job oluştur
function createTestJob($pdo, $sourceLangId, $targetLangId, $translateWithAI = false) {
    echo "6. TEST İŞİ OLUŞTURMA:\n";
    echo "======================\n";
    
    try {
        $sql = "INSERT INTO language_copy_jobs (source_language_id, target_language_id, translate_with_ai, status, created_at) 
                VALUES (:source, :target, :ai, 'pending', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'source' => $sourceLangId,
            'target' => $targetLangId,
            'ai' => $translateWithAI ? 1 : 0
        ]);
        
        if ($result) {
            $jobId = $pdo->lastInsertId();
            echo "✅ Test işi oluşturuldu - ID: {$jobId}\n";
            echo "Kaynak Dil: {$sourceLangId} → Hedef Dil: {$targetLangId}\n";
            echo "AI Çeviri: " . ($translateWithAI ? 'Aktif' : 'Pasif') . "\n";
        } else {
            echo "❌ Test işi oluşturulamadı\n";
        }
        
    } catch (PDOException $e) {
        echo "❌ Test işi oluşturma hatası: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Ana test fonksiyonları çalıştır
listLanguages($pdo);
listPendingJobs($pdo);
checkTables($pdo);
checkSeoTable($pdo);
checkCategorySeoRelation($pdo);

// Komut satırı argümanları kontrol et
if (isset($argv[1]) && $argv[1] === 'create-test') {
    $sourceLangId = $argv[2] ?? 1;
    $targetLangId = $argv[3] ?? 2;
    $translateWithAI = isset($argv[4]) && $argv[4] === 'ai';
    
    createTestJob($pdo, $sourceLangId, $targetLangId, $translateWithAI);
}

echo "=== TEST TAMAMLANDI ===\n";
echo "\nTest işi oluşturmak için:\n";
echo "php Tests\\System\\ContentCopierTester.php create-test [kaynak_dil_id] [hedef_dil_id] [ai]\n";
echo "Örnek: php Tests\\System\\ContentCopierTester.php create-test 1 2 ai\n";
?>
