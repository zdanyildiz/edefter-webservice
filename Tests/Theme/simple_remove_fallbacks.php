<?php
/**
 * Theme.php ve tab dosyalarından fallback değerleri kaldırma scripti - Basit versiyon
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Fallback değerleri kaldırma - Basit versiyon');

try {
    // 1. Theme.php dosyasını işle
    $themeFile = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\_y\\s\\s\\tasarim\\Theme.php';
    
    TestLogger::info('Theme.php dosyası okunuyor...');
    $content = file_get_contents($themeFile);
    
    if ($content === false) {
        throw new Exception('Theme.php dosyası okunamadı');
    }
    
    $originalContent = $content;
    $changes = 0;
    
    // Basit fallback pattern'i: ?? 'değer'
    $patterns = [
        '/\?\?\s*\'[^\']*\'/',
        '/\?\?\s*"[^"]*"/',
        '/\?\?\s*\$[^;]*/',
    ];
    
    foreach ($patterns as $pattern) {
        $content = preg_replace($pattern, '', $content);
        $matches = preg_match_all($pattern, $originalContent);
        if ($matches) {
            $changes += $matches;
        }
    }
    
    TestLogger::info("Theme.php'de {$changes} fallback değeri temizlendi");
    
    // Dosyayı kaydet
    file_put_contents($themeFile, $content);
    TestLogger::success('Theme.php başarıyla güncellendi');
    
    // 2. Tab dosyalarını işle
    $tabsDir = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\_y\\s\\s\\tasarim\\Theme\\tabs\\';
    $tabFiles = glob($tabsDir . '*.php');
    
    $totalTabChanges = 0;
    
    foreach ($tabFiles as $tabFile) {
        TestLogger::info('Tab dosyası işleniyor: ' . basename($tabFile));
        
        $tabContent = file_get_contents($tabFile);
        if ($tabContent === false) {
            TestLogger::error('Tab dosyası okunamadı: ' . $tabFile);
            continue;
        }
        
        $originalTabContent = $tabContent;
        $tabChanges = 0;
        
        // Tab dosyalarında fallback temizleme
        foreach ($patterns as $pattern) {
            $matches = preg_match_all($pattern, $tabContent);
            if ($matches) {
                $tabContent = preg_replace($pattern, '', $tabContent);
                $tabChanges += $matches;
            }
        }
        
        if ($tabChanges > 0) {
            file_put_contents($tabFile, $tabContent);
            $totalTabChanges += $tabChanges;
            TestLogger::success(basename($tabFile) . " dosyasında {$tabChanges} fallback temizlendi");
        } else {
            TestLogger::info(basename($tabFile) . " dosyasında fallback bulunamadı");
        }
    }
    
    TestLogger::success("Toplam {$totalTabChanges} tab dosyası fallback'i temizlendi");
    TestLogger::success('Tüm fallback temizleme işlemi tamamlandı');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

TestHelper::endTest();
?>
