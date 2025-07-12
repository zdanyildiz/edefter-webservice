<?php
/**
 * Legacy Tracking Bridge
 * Eski tracking sisteminden yeni platform tracking sistemine köprü
 * Geriye uyumluluk için
 */

/**
 * Eski getSalesConversionCode fonksiyonu için köprü
 * @deprecated Artık PlatformTrackingManager kullanın
 */
function getLegacySalesConversionCode($db, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, new stdClass());
    
    return $trackingManager->generateConversionCode('google_ads', 'purchase', [
        'value' => 0,
        'currency' => 'TRY'
    ], $languageID);
}

/**
 * Eski getCartConversionCode fonksiyonu için köprü
 * @deprecated Artık PlatformTrackingManager kullanın
 */
function getLegacyCartConversionCode($db, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, new stdClass());
    
    return $trackingManager->generateConversionCode('facebook_pixel', 'add_to_cart', [
        'value' => 0,
        'currency' => 'TRY'
    ], $languageID);
}

/**
 * Eski getTagManager fonksiyonu için köprü
 * @deprecated Artık PlatformTrackingManager kullanın
 */
function getLegacyTagManager($db, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, new stdClass());
    
    return $trackingManager->generateHeadCodes($languageID);
}

/**
 * Eski getAnalysisCode fonksiyonu için köprü
 * @deprecated Artık PlatformTrackingManager kullanın
 */
function getLegacyAnalysisCode($db, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, new stdClass());
    
    $gaConfig = $trackingManager->getPlatformConfig('google_analytics', $languageID);
    
    if ($gaConfig) {
        $config = json_decode($gaConfig['config'], true);
        $trackingId = $config['tracking_id'] ?? '';
        
        if ($trackingId) {
            return "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$trackingId}\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$trackingId}');
</script>";
        }
    }
    
    return '';
}

/**
 * Head bölümü için tüm tracking kodlarını al
 * Yeni sistem için ana fonksiyon
 */
function getAllTrackingCodes($db, $config, $languageID = 1, $pageType = 'home', $pageData = []) {
    include_once ROOT . '/App/Helpers/HeadTrackingInjector.php';
    return HeadTrackingInjector::inject($db, $config, $languageID, $pageType, $pageData);
}

/**
 * Hızlı tracking kod enjeksiyonu için global fonksiyon
 */
function injectTrackingCodes($db, $config, $options = []) {
    $languageID = $options['languageID'] ?? 1;
    $pageType = $options['pageType'] ?? 'home';
    $pageData = $options['pageData'] ?? [];
    
    echo getAllTrackingCodes($db, $config, $languageID, $pageType, $pageData);
}

/**
 * Sadece head kodları için (platform initialization)
 */
function getHeadTrackingCodes($db, $config, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, $config);
    return $trackingManager->generateHeadCodes($languageID);
}

/**
 * Sadece dönüşüm kodları için
 */
function getConversionCodes($db, $config, $platform, $eventType, $eventData, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, $config);
    return $trackingManager->generateConversionCode($platform, $eventType, $eventData, $languageID);
}

/**
 * Belirli platform aktif mi kontrol et
 */
function isPlatformActive($db, $platform, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, new stdClass());
    $config = $trackingManager->getPlatformConfig($platform, $languageID);
    
    return $config && $config['status'] == 1;
}

/**
 * Platform konfigürasyonunu al
 */
function getPlatformConfig($db, $platform, $languageID = 1) {
    include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
    $trackingManager = new PlatformTrackingManager($db, new stdClass());
    $config = $trackingManager->getPlatformConfig($platform, $languageID);
    
    return $config ? json_decode($config['config'], true) : [];
}
