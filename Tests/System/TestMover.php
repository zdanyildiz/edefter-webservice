<?php
/**
 * Test Dosya Taşıma Yardımcısı (TestMover)
 * 
 * Test ortamındaki dosyaları güvenli bir şekilde taşır ve organize eder.
 * Dosya bağımlılıklarını otomatik günceller ve backup oluşturur.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// Test framework'ünü her durumda yükle
include_once __DIR__ . '/../index.php';

class TestMover
{
    /**
     * @var string Tests klasörünün tam yolu
     */
    private static $testsPath;
    
    /**
     * @var array Taşınabilir dosya uzantıları
     */
    private static $allowedExtensions = [
        'php', 'txt', 'log', 'json', 'xml', 'csv', 'html', 'md'
    ];
    
    /**
     * @var array Hassas dosyalar (özel kontrol gerekli)
     */
    private static $sensitiveFiles = [
        'index.php',
        'README.md',
        'example_test.php'
    ];
    
    /**
     * @var array Taşıma işlem logları
     */
    private static $moveLog = [];
      /**
     * Sınıf başlatma
     */
    public static function init()
    {
        if (self::$testsPath === null) {
            self::$testsPath = realpath(__DIR__ . '/../');
        }
        
        // CLI modunda test logger kullan
        if (!isset($_SERVER['HTTP_HOST'])) {
            TestLogger::info('TestMover başlatıldı: ' . self::$testsPath);
        }
    }
    
    /**
     * Belirtilen dosyaları hedef dizine taşır
     * 
     * @param array $moveMap Taşıma haritası ['source' => 'target'] formatında
     * @param bool $dryRun Sadece kontrol et, gerçekten taşıma (default: false)
     * @param bool $updateReferences Include/require referanslarını güncelle (default: true)
     * @return array Sonuç raporu
     */
    public static function moveFiles($moveMap, $dryRun = false, $updateReferences = true)
    {
        self::init();
        
        $results = [
            'moved' => [],
            'skipped' => [],
            'errors' => [],
            'backup' => [],
            'updated_refs' => []
        ];
        
        TestLogger::info('Dosya taşıma işlemi başlatıldı' . ($dryRun ? ' (DRY RUN)' : ''));
        TestLogger::info('Toplam dosya sayısı: ' . count($moveMap));
        
        // Önce tüm dosyaları kontrol et
        foreach ($moveMap as $source => $target) {
            $validation = self::validateMove($source, $target);
            if (!$validation['valid']) {
                $results['errors'][] = [
                    'source' => $source,
                    'target' => $target,
                    'message' => $validation['message']
                ];
                continue;
            }
        }
        
        // Hata varsa işlemi durdur
        if (!empty($results['errors'])) {
            TestLogger::error('Taşıma işlemi durduruldu. Hatalar düzeltilmeli.');
            self::printSummary($results, $dryRun);
            return $results;
        }
        
        // Taşıma işlemlerini gerçekleştir
        foreach ($moveMap as $source => $target) {
            $result = self::moveFile($source, $target, $dryRun, $updateReferences);
            $results[$result['status']][] = $result;
            
            // Referans güncellemelerini kaydet
            if (isset($result['references']) && !empty($result['references'])) {
                $results['updated_refs'] = array_merge($results['updated_refs'], $result['references']);
            }
        }
        
        self::printSummary($results, $dryRun);
        return $results;
    }
    
    /**
     * Tek dosya taşıma işlemi
     * 
     * @param string $source Kaynak dosya yolu (Tests klasörüne göre relative)
     * @param string $target Hedef dosya yolu (Tests klasörüne göre relative)
     * @param bool $dryRun Sadece kontrol et
     * @param bool $updateReferences Referansları güncelle
     * @return array Dosya işlem sonucu
     */
    private static function moveFile($source, $target, $dryRun = false, $updateReferences = true)
    {
        // Dosya yollarını normalleştir
        $source = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $source);
        $target = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $target);
        
        $sourcePath = self::$testsPath . DIRECTORY_SEPARATOR . $source;
        $targetPath = self::$testsPath . DIRECTORY_SEPARATOR . $target;
        
        // Hedef dizinini oluştur
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            if ($dryRun) {
                TestLogger::info("DRY RUN: Dizin oluşturulacak -> " . str_replace(self::$testsPath . DIRECTORY_SEPARATOR, '', $targetDir));
            } else {
                if (!mkdir($targetDir, 0755, true)) {
                    TestLogger::error("Hedef dizin oluşturulamadı: $targetDir");
                    return [
                        'source' => $source,
                        'target' => $target,
                        'status' => 'errors',
                        'message' => 'Hedef dizin oluşturulamadı'
                    ];
                }
            }
        }
        
        if ($dryRun) {
            TestLogger::info("DRY RUN: Taşınacak -> $source → $target");
            
            $references = [];
            if ($updateReferences) {
                $references = self::findReferences($source, true);
            }
            
            return [
                'source' => $source,
                'target' => $target,
                'status' => 'moved',
                'message' => 'DRY RUN - Taşınabilir',
                'references' => $references
            ];
        }
        
        // Backup oluştur (hassas dosyalar için)
        $filename = basename($source);
        if (in_array($filename, self::$sensitiveFiles)) {
            $backupPath = self::createBackup($sourcePath);
            if ($backupPath) {
                TestLogger::info("Backup oluşturuldu: $backupPath");
            }
        }
        
        try {
            // Dosyayı taşı
            if (rename($sourcePath, $targetPath)) {
                TestLogger::success("Taşındı: $source → $target");
                
                $references = [];
                // Referansları güncelle
                if ($updateReferences) {
                    $references = self::updateReferences($source, $target);
                }
                
                return [
                    'source' => $source,
                    'target' => $target,
                    'status' => 'moved',
                    'message' => 'Başarıyla taşındı',
                    'references' => $references
                ];
            } else {
                TestLogger::error("Taşınamadı: $source");
                return [
                    'source' => $source,
                    'target' => $target,
                    'status' => 'errors',
                    'message' => 'Dosya taşınamadı'
                ];
            }
        } catch (Exception $e) {
            TestLogger::error("Hata: $source - " . $e->getMessage());
            return [
                'source' => $source,
                'target' => $target,
                'status' => 'errors',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Taşıma işlemini doğrula
     * 
     * @param string $source Kaynak dosya
     * @param string $target Hedef dosya
     * @return array Doğrulama sonucu
     */
    private static function validateMove($source, $target)
    {
        $source = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $source);
        $target = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $target);
        
        $sourcePath = self::$testsPath . DIRECTORY_SEPARATOR . $source;
        $targetPath = self::$testsPath . DIRECTORY_SEPARATOR . $target;
        
        // Kaynak dosya mevcut mu?
        if (!file_exists($sourcePath)) {
            return [
                'valid' => false,
                'message' => 'Kaynak dosya bulunamadı'
            ];
        }
        
        // Hedef dosya zaten var mı?
        if (file_exists($targetPath)) {
            return [
                'valid' => false,
                'message' => 'Hedef dosya zaten mevcut'
            ];
        }
        
        // Tests klasörü içinde mi?
        if (!self::isInTestsFolder($sourcePath) || !self::isInTestsFolder(dirname($targetPath))) {
            return [
                'valid' => false,
                'message' => 'Tests klasörü dışına taşıma yasak'
            ];
        }
        
        // Dosya uzantısı uygun mu?
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        if (!in_array($extension, self::$allowedExtensions)) {
            return [
                'valid' => false,
                'message' => "İzin verilmeyen uzantı: $extension"
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Geçerli'
        ];
    }
    
    /**
     * Dosyanın Tests klasörü içinde olup olmadığını kontrol eder
     * 
     * @param string $fullPath Tam dosya yolu
     * @return bool
     */
    private static function isInTestsFolder($fullPath)
    {
        $realPath = realpath(dirname($fullPath));
        return $realPath !== false && strpos($realPath, self::$testsPath) === 0;
    }
    
    /**
     * Dosya backup'ı oluştur
     * 
     * @param string $filePath Dosya yolu
     * @return string|false Backup dosya yolu veya false
     */
    private static function createBackup($filePath)
    {
        $backupDir = self::$testsPath . DIRECTORY_SEPARATOR . 'Backup';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filename = basename($filePath);
        $timestamp = date('Y-m-d_H-i-s');
        $backupPath = $backupDir . DIRECTORY_SEPARATOR . $timestamp . '_' . $filename;
        
        if (copy($filePath, $backupPath)) {
            return $backupPath;
        }
        
        return false;
    }
    
    /**
     * Dosya referanslarını bul
     * 
     * @param string $filePath Aranan dosya yolu
     * @param bool $dryRun Sadece kontrol et
     * @return array Referans listesi
     */
    private static function findReferences($filePath, $dryRun = false)
    {
        $references = [];
        $filename = basename($filePath);
        $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        
        // Tests klasöründeki tüm PHP dosyalarını tara
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::$testsPath)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                
                // Include/require pattern'leri ara
                $patterns = [
                    "/include_once\s+['\"]([^'\"]*{$filename})['\"];?/",
                    "/include\s+['\"]([^'\"]*{$filename})['\"];?/",
                    "/require_once\s+['\"]([^'\"]*{$filename})['\"];?/",
                    "/require\s+['\"]([^'\"]*{$filename})['\"];?/",
                    "/['\"]([^'\"]*{$filenameWithoutExt}\.php)['\"]/",
                ];
                
                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches)) {
                        $relativePath = str_replace(self::$testsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                        $references[] = [
                            'file' => $relativePath,
                            'matches' => $matches[0],
                            'dry_run' => $dryRun
                        ];
                        break;
                    }
                }
            }
        }
        
        return $references;
    }
    
    /**
     * Dosya referanslarını güncelle
     * 
     * @param string $oldPath Eski dosya yolu
     * @param string $newPath Yeni dosya yolu
     * @return array Güncellenen referanslar
     */
    private static function updateReferences($oldPath, $newPath)
    {
        $references = self::findReferences($oldPath);
        $updated = [];
        
        $oldFilename = basename($oldPath);
        $newFilename = basename($newPath);
        
        // Relative path hesaplama için
        $oldRelative = str_replace('\\', '/', $oldPath);
        $newRelative = str_replace('\\', '/', $newPath);
        
        foreach ($references as $ref) {
            $filePath = self::$testsPath . DIRECTORY_SEPARATOR . $ref['file'];
            $content = file_get_contents($filePath);
            $originalContent = $content;
            
            // İncelikli replacement (sadece doğru referansları değiştir)
            foreach ($ref['matches'] as $match) {
                // Eski yolu yeni yol ile değiştir
                if (strpos($match, $oldFilename) !== false) {
                    $newMatch = str_replace($oldFilename, $newFilename, $match);
                    
                    // Relative path düzeltmesi
                    if (strpos($match, $oldRelative) !== false) {
                        $newMatch = str_replace($oldRelative, $newRelative, $match);
                    }
                    
                    $content = str_replace($match, $newMatch, $content);
                }
            }
            
            // Değişiklik varsa dosyayı güncelle
            if ($content !== $originalContent) {
                file_put_contents($filePath, $content);
                TestLogger::success("Referans güncellendi: " . $ref['file']);
                $updated[] = [
                    'file' => $ref['file'],
                    'changes' => count($ref['matches'])
                ];
            }
        }
        
        return $updated;
    }
    
    /**
     * Taşıma özeti yazdır
     * 
     * @param array $results Sonuçlar
     * @param bool $dryRun Dry run modu
     */
    private static function printSummary($results, $dryRun)
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📋 TEST DOSYA TAŞIMA ÖZETİ" . ($dryRun ? " (DRY RUN)" : "") . "\n";
        echo str_repeat("=", 60) . "\n";
        
        echo "✅ Taşınan dosyalar: " . count($results['moved']) . "\n";
        foreach ($results['moved'] as $item) {
            echo "   - " . $item['source'] . " → " . $item['target'] . "\n";
        }
        
        if (count($results['updated_refs']) > 0) {
            echo "\n🔗 Güncellenen referanslar: " . count($results['updated_refs']) . "\n";
            foreach ($results['updated_refs'] as $item) {
                echo "   - " . $item['file'] . " (" . $item['changes'] . " değişiklik)\n";
            }
        }
        
        if (count($results['skipped']) > 0) {
            echo "\n⏭️  Atlanan dosyalar: " . count($results['skipped']) . "\n";
            foreach ($results['skipped'] as $item) {
                echo "   - " . $item['source'] . " (" . $item['message'] . ")\n";
            }
        }
        
        if (count($results['errors']) > 0) {
            echo "\n❌ Hatalı dosyalar: " . count($results['errors']) . "\n";
            foreach ($results['errors'] as $item) {
                echo "   - " . $item['source'] . " (" . $item['message'] . ")\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        TestLogger::info('TestMover özet tamamlandı');
    }
    
    /**
     * Test framework sınıflarını TestModel dizinine taşı
     * 
     * @param bool $dryRun Sadece kontrol et
     * @return array Sonuç raporu
     */
    public static function organizeTestFramework($dryRun = false)
    {
        self::init();
        
        // Taşınacak dosyalar listesi
        $frameworkFiles = [
            'TestModel/TestModel/TestAssert.php' => 'TestModel/TestModel/TestAssert.php',
            'TestModel/TestModel/TestDataGenerator.php' => 'TestModel/TestModel/TestDataGenerator.php',
            'TestModel/TestModel/TestLogger.php' => 'TestModel/TestModel/TestLogger.php',
            'TestModel/TestModel/TestRunner.php' => 'TestModel/TestModel/TestRunner.php',
            'TestModel/TestModel/TestValidator.php' => 'TestModel/TestModel/TestValidator.php'
        ];
        
        // Varolan dosyaları kontrol et
        $existingFiles = [];
        foreach ($frameworkFiles as $source => $target) {
            $sourcePath = self::$testsPath . DIRECTORY_SEPARATOR . $source;
            if (file_exists($sourcePath)) {
                $existingFiles[$source] = $target;
            }
        }
        
        if (empty($existingFiles)) {
            TestLogger::info('Taşınacak framework dosyası bulunamadı');
            return ['moved' => [], 'skipped' => [], 'errors' => [], 'updated_refs' => []];
        }
        
        TestLogger::info('Test Framework dosyaları TestModel dizinine taşınıyor...');
        return self::moveFiles($existingFiles, $dryRun, true);
    }    /**
     * Web isteklerini işle
     * 
     * @param string $action İşlem türü
     * @param array $params Parametreler
     * @return array Sonuç
     */
    public static function handleWebRequest($action, $params)
    {
        // İlk olarak init çalıştır
        self::init();
        
        // Web modunda çıktıları engelle
        $originalLoggerState = null;
        if (class_exists('TestLogger')) {
            // TestLogger'ı silent moda al
            $originalLoggerState = TestLogger::$silentMode ?? false;
            TestLogger::$silentMode = true;
        }
        
        // Output buffering başlat
        ob_start();
        
        try {
            switch ($action) {                case 'organize':
                    $dryRun = isset($params['dry']) && $params['dry'] === 'true';
                    $results = self::organizeTestFramework($dryRun);
                    
                    // Buffered output'u temizle
                    ob_get_clean();
                    
                    return [
                        'success' => true,
                        'action' => $action,
                        'dry_run' => $dryRun,
                        'results' => $results,
                        'timestamp' => date('Y-m-d H:i:s')
                    ];
                    
                case 'move':
                    $sourceFiles = isset($params['files']) ? explode(',', $params['files']) : [];
                    $targetDir = isset($params['target']) ? $params['target'] : '';
                    $dryRun = isset($params['dry']) && $params['dry'] === 'true';
                    
                    if (empty($sourceFiles) || empty($targetDir)) {
                        ob_get_clean();
                        return ['success' => false, 'error' => 'Kaynak dosyalar ve hedef dizin gerekli'];
                    }
                    
                    $results = self::moveFiles($sourceFiles, $targetDir, $dryRun);
                    
                    // Buffered output'u temizle
                    ob_get_clean();
                    
                    return [
                        'success' => true,
                        'action' => $action,
                        'dry_run' => $dryRun,
                        'results' => $results,
                        'timestamp' => date('Y-m-d H:i:s')
                    ];
                      case 'list':
                    $files = self::listTestFiles();
                    
                    // Buffered output'u temizle
                    ob_get_clean();
                    
                    return [
                        'success' => true,
                        'action' => $action,
                        'files' => $files,
                        'count' => count($files),
                        'timestamp' => date('Y-m-d H:i:s')
                    ];
                    
                case 'status':
                    // Buffered output'u temizle
                    ob_get_clean();
                    
                    return [
                        'success' => true,
                        'action' => $action,
                        'system' => [
                            'tests_path' => self::$testsPath,
                            'testmodel_exists' => is_dir(self::$testsPath . DIRECTORY_SEPARATOR . 'TestModel'),
                            'framework_files' => self::getFrameworkFiles(),
                            'temp_files' => count(glob(self::$testsPath . DIRECTORY_SEPARATOR . 'Temp' . DIRECTORY_SEPARATOR . '*'))
                        ],
                        'timestamp' => date('Y-m-d H:i:s')
                    ];
                    
                default:
                    ob_get_clean();
                    return ['success' => false, 'error' => 'Geçersiz action: ' . $action];
            }        } catch (Exception $e) {
            // Buffered output'u temizle
            ob_get_clean();
            
            // Logger state'i geri yükle
            if ($originalLoggerState !== null && class_exists('TestLogger')) {
                TestLogger::$silentMode = $originalLoggerState;
            }
            
            return ['success' => false, 'error' => $e->getMessage()];
        } finally {
            // Logger state'i geri yükle
            if ($originalLoggerState !== null && class_exists('TestLogger')) {
                TestLogger::$silentMode = $originalLoggerState;
            }
        }
    }

    /**
     * Test dosyalarını listele
     * 
     * @return array Dosya listesi
     */
    private static function listTestFiles()
    {
        $files = [];
        $testFiles = ['TestModel/TestModel/TestAssert.php', 'TestModel/TestModel/TestDataGenerator.php', 'TestModel/TestModel/TestLogger.php', 'TestModel/TestModel/TestRunner.php', 'TestModel/TestModel/TestValidator.php', 'TestHelper.php'];
        
        foreach ($testFiles as $file) {
            $fullPath = self::$testsPath . DIRECTORY_SEPARATOR . $file;
            $files[$file] = [
                'exists' => file_exists($fullPath),
                'size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                'modified' => file_exists($fullPath) ? date('Y-m-d H:i:s', filemtime($fullPath)) : null
            ];
        }
        
        return $files;
    }
    
    /**
     * Test framework dosyalarını listele
     * 
     * @return array Framework dosya listesi
     */
    private static function getFrameworkFiles()
    {        $frameworkFiles = [
            'TestModel/TestAssert.php',
            'TestModel/TestDataGenerator.php', 
            'TestDatabase.php',
            'TestModel/TestLogger.php',
            'TestModel/TestValidator.php',
            'TestModel/TestRunner.php'
        ];
        
        $files = [];
        foreach ($frameworkFiles as $file) {
            $path = self::$testsPath . DIRECTORY_SEPARATOR . $file;
            $files[$file] = [
                'exists' => file_exists($path),
                'path' => $path,
                'size' => file_exists($path) ? filesize($path) : 0
            ];
        }
        
        return $files;
    }
}

// Komut satırından çalıştırılıyorsa
if (isset($argv) && basename(__FILE__) == basename($argv[0])) {
    echo "📦 Test Dosya Taşıma Yardımcısı\n";
    echo "===============================\n\n";
    
    if (isset($argv[1])) {
        switch ($argv[1]) {
            case 'framework':
                echo "🔧 Test Framework dosyaları organize ediliyor...\n";
                TestMover::organizeTestFramework();
                break;
                
            case 'dry-framework':
                echo "🔍 DRY RUN - Test Framework kontrolü:\n";
                TestMover::organizeTestFramework(true);
                break;
                
            default:
                echo "Kullanım:\n";
                echo "  php TestMover.php framework        # Test framework dosyalarını organize et\n";
                echo "  php TestMover.php dry-framework    # Framework kontrolü (dry run)\n";
        }
    } else {
        echo "Kullanım:\n";
        echo "  php TestMover.php framework        # Test framework dosyalarını organize et\n";
        echo "  php TestMover.php dry-framework    # Framework kontrolü (dry run)\n";
    }
}

// Web tabanlı çalışma için HTTP kontrolü
if (isset($_SERVER['HTTP_HOST']) && (isset($_GET['action']) || isset($_POST['action']))) {
    // Output buffering başlat - test framework çıktılarını yakala
    ob_start();
    
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header('Access-Control-Allow-Headers: Content-Type');
    
    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';
    $params = array_merge($_GET, $_POST);
    unset($params['action']);
    
    try {
        $response = TestMover::handleWebRequest($action, $params);
        
        // Test framework çıktılarını temizle
        ob_clean();
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        // Hata durumunda da çıktıları temizle
        ob_clean();
        
        $errorResponse = [
            'success' => false,
            'error' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($errorResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    exit;
}

// Web istekleri için basit TestLogger sınıfı
if (!class_exists('TestLogger')) {
    class TestLogger
    {
        public static function info($message) {
            // Web istekleri için sessiz log
        }
        
        public static function success($message) {
            // Web istekleri için sessiz log
        }
        
        public static function error($message) {
            error_log($message);
        }
        
        public static function warning($message) {
            // Web istekleri için sessiz log
        }
    }
}
