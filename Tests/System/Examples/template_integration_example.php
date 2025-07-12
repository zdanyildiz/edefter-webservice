<?php
/**
 * Site Template Head Integration Example
 * Template dosyalarında nasıl kullanılacağını gösteren örnek
 */

// Site header template dosyasında (örn: header.php)
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$siteTitle?></title>
    
    <!-- Meta tags -->
    <meta name="description" content="<?=$siteDescription?>">
    <meta name="keywords" content="<?=$siteKeywords?>">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="/assets/css/main.css">
    
    <?php
    // Platform Tracking Kodlarını Enjekte Et
    if (isset($db) && isset($config)) {
        // Sayfa tipini belirle
        $pageType = 'home';
        $pageData = [];
        
        // URL'ye göre sayfa tipini belirle
        $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
        
        if (strpos($currentUrl, '/product/') !== false || strpos($currentUrl, '/urun/') !== false) {
            $pageType = 'product';
            if (isset($product)) {
                $pageData = [
                    'productID' => $product['productID'] ?? '',
                    'productName' => $product['productName'] ?? '',
                    'categoryName' => $product['categoryName'] ?? '',
                    'price' => $product['productPrice'] ?? 0
                ];
            }
        } elseif (strpos($currentUrl, '/cart') !== false || strpos($currentUrl, '/sepet') !== false) {
            $pageType = 'cart';
            if (isset($cartItems)) {
                $pageData = ['items' => $cartItems];
            }
        } elseif (strpos($currentUrl, '/checkout') !== false || strpos($currentUrl, '/odeme') !== false) {
            $pageType = 'checkout';
        } elseif (strpos($currentUrl, '/thank-you') !== false || strpos($currentUrl, '/tesekkur') !== false) {
            $pageType = 'thankyou';
            if (isset($orderData)) {
                $pageData = $orderData;
            }
        }
        
        // Dil ID'sini belirle
        $languageID = $_SESSION['languageID'] ?? 1;
        
        // Tracking kodlarını enjekte et
        include_once ROOT . '/App/Helpers/LegacyTrackingBridge.php';
        echo getAllTrackingCodes($db, $config, $languageID, $pageType, $pageData);
    }
    ?>
</head>
<body>

<?php
/**
 * Alternatif kullanım: Sadece head kodları
 */
if (false) { // Bu blok örnekleme amaçlı
    echo getHeadTrackingCodes($db, $config, $languageID);
}

/**
 * Ürün sayfası özel kullanımı
 */
if (false) { // Bu blok örnekleme amaçlı
    ?>
    <script>
    // Ürün görüntüleme eventi
    <?php
    if (isset($product) && isPlatformActive($db, 'google_analytics', $languageID)) {
        $gaConfig = getPlatformConfig($db, 'google_analytics', $languageID);
        if (!empty($gaConfig['tracking_id'])) {
            ?>
            gtag('event', 'view_item', {
                'currency': 'TRY',
                'value': <?=$product['productPrice'] ?? 0?>,
                'items': [{
                    'item_id': '<?=$product['productID']?>',
                    'item_name': '<?=addslashes($product['productName'])?>',
                    'category': '<?=addslashes($product['categoryName'] ?? '')?>',
                    'quantity': 1,
                    'price': <?=$product['productPrice'] ?? 0?>
                }]
            });
            <?php
        }
    }
    ?>
    </script>
    <?php
}

/**
 * Sepete ekleme eventi (AJAX ile çağırılabilir)
 */
if (false) { // Bu blok örnekleme amaçlı
    ?>
    <script>
    function trackAddToCart(productData) {
        <?php if (isPlatformActive($db, 'facebook_pixel', $languageID)): ?>
        // Facebook Pixel
        fbq('track', 'AddToCart', {
            value: productData.price,
            currency: 'TRY',
            content_ids: [productData.id],
            content_type: 'product'
        });
        <?php endif; ?>
        
        <?php if (isPlatformActive($db, 'google_analytics', $languageID)): ?>
        // Google Analytics
        gtag('event', 'add_to_cart', {
            currency: 'TRY',
            value: productData.price,
            items: [{
                item_id: productData.id,
                item_name: productData.name,
                quantity: productData.quantity,
                price: productData.price
            }]
        });
        <?php endif; ?>
    }
    </script>
    <?php
}

/**
 * Satın alma sayfası (Thank You Page)
 */
if (false && isset($orderData)) { // Bu blok örnekleme amaçlı
    echo getConversionCodes($db, $config, 'google_ads', 'purchase', [
        'value' => $orderData['total'],
        'currency' => 'TRY',
        'order_id' => $orderData['orderID']
    ], $languageID);
}

/**
 * Manuel platform kontrolü
 */
if (false) { // Bu blok örnekleme amaçlı
    // Google Analytics aktif mi?
    if (isPlatformActive($db, 'google_analytics', $languageID)) {
        $gaConfig = getPlatformConfig($db, 'google_analytics', $languageID);
        echo "<!-- GA Tracking ID: " . ($gaConfig['tracking_id'] ?? 'Bulunamadı') . " -->";
    }
    
    // Facebook Pixel aktif mi?
    if (isPlatformActive($db, 'facebook_pixel', $languageID)) {
        $fbConfig = getPlatformConfig($db, 'facebook_pixel', $languageID);
        echo "<!-- FB Pixel ID: " . ($fbConfig['pixel_id'] ?? 'Bulunamadı') . " -->";
    }
    
    // Google Ads aktif mi?
    if (isPlatformActive($db, 'google_ads', $languageID)) {
        $gadsConfig = getPlatformConfig($db, 'google_ads', $languageID);
        echo "<!-- Google Ads Conversion ID: " . ($gadsConfig['conversion_id'] ?? 'Bulunamadı') . " -->";
    }
}
?>
