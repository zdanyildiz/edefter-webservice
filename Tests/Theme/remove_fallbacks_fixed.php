<?php
/**
 * Theme.php ve tab dosyalarından fallback değerleri kaldırma scripti
 * Bu script tüm ?? 'değer' pattern'lerini bulup kaldırır
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Fallback değerleri kaldırma');

try {
    // 1. Theme.php dosyasını işle
    $themeFile = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\_y\\s\\s\\tasarim\\Theme.php';
    
    TestLogger::info('Theme.php dosyası okunuyor...');
    $content = file_get_contents($themeFile);
    
    if ($content === false) {
        throw new Exception('Theme.php dosyası okunamadı');
    }
    
    $originalContent = $content;
    
    // CSS değişkenleri için fallback kaldırma - Basit pattern
    // Pattern: $customCSS['key'] ?? 'default-value'
    $pattern = '/\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*[\'"][^\']*[\'"](\)?)(\?\>?)/';
    $matches = [];
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    
    TestLogger::info('Theme.php dosyasında ' . count($matches) . ' CSS fallback değeri bulundu');
    
    foreach ($matches as $match) {
        $fullMatch = $match[0];
        // Extract just the variable part
        preg_match('/(\$customCSS\[[\'"][^\'"]+[\'"]\])/', $fullMatch, $varMatch);
        if (isset($varMatch[1])) {
            $cssVar = $varMatch[1];
            $ending = isset($match[1]) ? $match[1] : '';
            $phpEnd = isset($match[2]) ? $match[2] : '';
            
            // Replace with just the variable
            $replacement = $cssVar . $ending . $phpEnd;
            $content = str_replace($fullMatch, $replacement, $content);
            
            TestLogger::info('CSS fallback kaldırıldı: ' . substr($fullMatch, 0, 50) . '...');
        }
    }
    
    // sanitizeColorValue içindeki fallback'leri de kaldır
    $colorPattern = '/sanitizeColorValue\(\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*[\'"][^\']*[\'"]\)/';
    $colorMatches = [];
    preg_match_all($colorPattern, $content, $colorMatches, PREG_SET_ORDER);
    
    TestLogger::info('Theme.php dosyasında ' . count($colorMatches) . ' renk fallback değeri bulundu');
    
    foreach ($colorMatches as $match) {
        $fullMatch = $match[0];
        // Extract variable name
        preg_match('/(\$customCSS\[[\'"][^\'"]+[\'"]\])/', $fullMatch, $varMatch);
        if (isset($varMatch[1])) {
            $cssVar = $varMatch[1];
            // Replace with clean version
            $replacement = 'sanitizeColorValue(' . $cssVar . ')';
            $content = str_replace($fullMatch, $replacement, $content);
            
            TestLogger::info('Renk fallback kaldırıldı: ' . substr($fullMatch, 0, 50) . '...');
        }
    }
    
    // Çoklu fallback'leri kaldır (mobile için desktop fallback kullanan satırlar)
    $multiPattern = '/\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*[\'"][^\']*[\'"]/';
    $multiMatches = [];
    preg_match_all($multiPattern, $content, $multiMatches, PREG_SET_ORDER);
    
    TestLogger::info('Theme.php dosyasında ' . count($multiMatches) . ' çoklu fallback değeri bulundu');
    
    foreach ($multiMatches as $match) {
        $fullMatch = $match[0];
        // Extract first variable
        preg_match('/(\$customCSS\[[\'"][^\'"]+[\'"]\])/', $fullMatch, $varMatch);
        if (isset($varMatch[1])) {
            $firstVar = $varMatch[1];
            // Replace with just the first variable
            $content = str_replace($fullMatch, $firstVar, $content);
            
            TestLogger::info('Çoklu fallback kaldırıldı: ' . substr($fullMatch, 0, 60) . '...');
        }
    }
    
    // Dosyayı kaydet
    $success = file_put_contents($themeFile, $content);
    if ($success === false) {
        throw new Exception('Theme.php dosyası kaydedilemedi');
    }
    
    $totalRemoved = count($matches) + count($colorMatches) + count($multiMatches);
    TestLogger::success('Theme.php dosyasından ' . $totalRemoved . ' fallback değeri kaldırıldı');
    
    // 2. Tab dosyalarını işle
    $tabsDir = 'c:\\Users\\zdany\\PhpstormProjects\\erhanozel.globalpozitif.com.tr\\_y\\s\\s\\tasarim\\Theme\\tabs\\';
    $tabFiles = glob($tabsDir . '*.php');
    
    foreach ($tabFiles as $tabFile) {
        TestLogger::info('Tab dosyası işleniyor: ' . basename($tabFile));
        
        $tabContent = file_get_contents($tabFile);
        if ($tabContent === false) {
            TestLogger::error('Tab dosyası okunamadı: ' . $tabFile);
            continue;
        }
        
        $originalTabContent = $tabContent;
        $totalTabChanges = 0;
        
        // Input value attribute'larında fallback kaldırma
        // Pattern: value="<?=$customCSS['key'] ?? 'default'?>"
        $valuePattern = '/value=[\'"]<\?=\s*\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*[\'"][^\']*[\'"](\s*\?\>)?[\'\"]/';
        $valueMatches = [];
        preg_match_all($valuePattern, $tabContent, $valueMatches, PREG_SET_ORDER);
        
        foreach ($valueMatches as $valueMatch) {
            $fullMatch = $valueMatch[0];
            // Extract variable name
            preg_match('/\$customCSS\[[\'"]([^\'"]+)[\'"]\]/', $fullMatch, $varMatch);
            if (isset($varMatch[1])) {
                $varName = $varMatch[1];
                // Replace with clean version using string replacement
                $replacement = str_replace('VARNAME', $varName, 'value="<?=$customCSS[QVARNAME]?>"');
                $replacement = str_replace('Q', "'", $replacement);
                $tabContent = str_replace($fullMatch, $replacement, $tabContent);
                $totalTabChanges++;
            }
        }
        
        // PHP echo satırlarında fallback kaldır (value dışında)
        $echoPattern = '/<\?=\s*\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*[\'"][^\']*[\'"](\s*\?\>)?/';
        $echoMatches = [];
        preg_match_all($echoPattern, $tabContent, $echoMatches, PREG_SET_ORDER);
        
        foreach ($echoMatches as $echoMatch) {
            $fullMatch = $echoMatch[0];
            // Extract variable name
            preg_match('/\$customCSS\[[\'"]([^\'"]+)[\'"]\]/', $fullMatch, $varMatch);
            if (isset($varMatch[1])) {
                $varName = $varMatch[1];
                // Replace with clean version using string replacement
                $replacement = str_replace('VARNAME', $varName, '<?=$customCSS[QVARNAME]?>');
                $replacement = str_replace('Q', "'", $replacement);
                $tabContent = str_replace($fullMatch, $replacement, $tabContent);
                $totalTabChanges++;
            }
        }
        
        // PHP içinde fallback kaldır
        $phpPattern = '/\$customCSS\[[\'"][^\'"]+[\'"]\]\s*\?\?\s*[\'"][^\']*[\'"]/';
        $phpMatches = [];
        preg_match_all($phpPattern, $tabContent, $phpMatches, PREG_SET_ORDER);
        
        foreach ($phpMatches as $phpMatch) {
            $fullMatch = $phpMatch[0];
            // Extract variable name
            preg_match('/(\$customCSS\[[\'"][^\'"]+[\'"]\])/', $fullMatch, $varMatch);
            if (isset($varMatch[1])) {
                $cssVar = $varMatch[1];
                // Replace with just the variable
                $tabContent = str_replace($fullMatch, $cssVar, $tabContent);
                $totalTabChanges++;
            }
        }
        
        if ($totalTabChanges > 0) {
            // Dosyayı kaydet
            $success = file_put_contents($tabFile, $tabContent);
            if ($success !== false) {
                TestLogger::success('Tab dosyası güncellendi: ' . basename($tabFile) . ' (' . $totalTabChanges . ' fallback kaldırıldı)');
            } else {
                TestLogger::error('Tab dosyası kaydedilemedi: ' . $tabFile);
            }
        } else {
            TestLogger::info('Tab dosyasında fallback bulunamadı: ' . basename($tabFile));
        }
    } // foreach tab files bitişi
    
    
    TestLogger::success('Tüm fallback değerleri başarıyla kaldırıldı');
    
} catch (Exception $e) {
    TestLogger::error('Hata: ' . $e->getMessage());
}

TestHelper::endTest();
?>
