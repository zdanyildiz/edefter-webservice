<?php
/**
 * Site Head Tracking Code Injector
 * Head bölümüne platform tracking kodlarını otomatik ekler
 */

class HeadTrackingInjector {
    
    private $db;
    private $config;
    private $trackingManager;
    
    public function __construct($database, $config) {
        $this->db = $database;
        $this->config = $config;
        
        // PlatformTrackingManager'ı yükle
        include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
        $this->trackingManager = new PlatformTrackingManager($database, $config);
    }
    
    /**
     * Head tracking kodlarını oluştur ve döndür
     * 
     * @param int $languageID Dil ID
     * @return string Head bölümü için tracking kodları
     */
    public function generateHeadTrackingCodes($languageID = 1) {
        try {
            return $this->trackingManager->generateHeadCodes($languageID);
        } catch (Exception $e) {
            error_log('HeadTrackingInjector Error: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Sayfa tipine göre ek tracking kodları
     * 
     * @param string $pageType Sayfa tipi (home, product, cart, checkout, thankyou)
     * @param array $pageData Sayfa verisi
     * @param int $languageID Dil ID
     * @return string Sayfa tipi için özel tracking kodları
     */
    public function generatePageTrackingCodes($pageType, $pageData = [], $languageID = 1) {
        $codes = '';
        
        switch ($pageType) {
            case 'product':
                $codes .= $this->generateProductViewTracking($pageData, $languageID);
                break;
                
            case 'cart':
                $codes .= $this->generateCartTracking($pageData, $languageID);
                break;
                
            case 'checkout':
                $codes .= $this->generateCheckoutTracking($pageData, $languageID);
                break;
                
            case 'thankyou':
                $codes .= $this->generatePurchaseTracking($pageData, $languageID);
                break;
        }
        
        return $codes;
    }
    
    /**
     * Ürün görüntüleme tracking
     */
    private function generateProductViewTracking($productData, $languageID) {
        $codes = '';
        
        // Google Analytics - Product View
        if (!empty($productData['productID'])) {
            $codes .= "<script>
                gtag('event', 'view_item', {
                    'currency': 'TRY',
                    'value': " . ($productData['price'] ?? 0) . ",
                    'items': [{
                        'item_id': '" . $productData['productID'] . "',
                        'item_name': '" . addslashes($productData['productName'] ?? '') . "',
                        'category': '" . addslashes($productData['categoryName'] ?? '') . "',
                        'quantity': 1,
                        'price': " . ($productData['price'] ?? 0) . "
                    }]
                });
            </script>\n";
        }
        
        // Facebook Pixel - Product View
        $codes .= "<script>
            fbq('track', 'ViewContent', {
                content_type: 'product',
                content_ids: ['" . ($productData['productID'] ?? '') . "'],
                content_name: '" . addslashes($productData['productName'] ?? '') . "',
                content_category: '" . addslashes($productData['categoryName'] ?? '') . "',
                value: " . ($productData['price'] ?? 0) . ",
                currency: 'TRY'
            });
        </script>\n";
        
        // TikTok Pixel - Product View
        $codes .= "<script>
            ttq.track('ViewContent', {
                content_type: 'product',
                content_id: '" . ($productData['productID'] ?? '') . "',
                content_name: '" . addslashes($productData['productName'] ?? '') . "',
                value: " . ($productData['price'] ?? 0) . ",
                currency: 'TRY'
            });
        </script>\n";
        
        return $codes;
    }
    
    /**
     * Sepet tracking
     */
    private function generateCartTracking($cartData, $languageID) {
        $codes = '';
        
        if (!empty($cartData['items'])) {
            $totalValue = array_sum(array_column($cartData['items'], 'total'));
            
            // Google Analytics - Add to Cart
            $codes .= "<script>
                gtag('event', 'add_to_cart', {
                    'currency': 'TRY',
                    'value': " . $totalValue . ",
                    'items': [";
            
            foreach ($cartData['items'] as $item) {
                $codes .= "{
                    'item_id': '" . $item['productID'] . "',
                    'item_name': '" . addslashes($item['productName']) . "',
                    'quantity': " . $item['quantity'] . ",
                    'price': " . $item['price'] . "
                },";
            }
            $codes = rtrim($codes, ',');
            $codes .= "]});
            </script>\n";
            
            // Facebook Pixel - Add to Cart
            $codes .= "<script>
                fbq('track', 'AddToCart', {
                    value: " . $totalValue . ",
                    currency: 'TRY',
                    content_ids: ['" . implode("','", array_column($cartData['items'], 'productID')) . "'],
                    content_type: 'product'
                });
            </script>\n";
        }
        
        return $codes;
    }
    
    /**
     * Satın alma tracking
     */
    private function generatePurchaseTracking($orderData, $languageID) {
        $codes = '';
        
        if (!empty($orderData['orderID'])) {
            // Tüm aktif platformlar için dönüşüm kodları oluştur
            $activePlatforms = $this->trackingManager->getActivePlatforms($languageID);
            
            foreach ($activePlatforms as $platform) {
                $platformKey = $platform['platform'];
                $eventData = [
                    'value' => $orderData['total'] ?? 0,
                    'currency' => 'TRY',
                    'order_id' => $orderData['orderID'],
                    'items' => $orderData['items'] ?? []
                ];
                
                $conversionCode = $this->trackingManager->generateConversionCode(
                    $platformKey, 
                    'purchase', 
                    $eventData, 
                    $languageID
                );
                
                $codes .= $conversionCode . "\n";
            }
        }
        
        return $codes;
    }
    
    /**
     * Belirli bir platform için dönüşüm kodu oluştur
     */
    public function generateConversionCode($platform, $eventType, $eventData, $languageID = 1) {
        try {
            return $this->trackingManager->generateConversionCode($platform, $eventType, $eventData, $languageID);
        } catch (Exception $e) {
            error_log('HeadTrackingInjector Conversion Error: ' . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Hızlı entegrasyon için static metod
     */
    public static function inject($db, $config, $languageID = 1, $pageType = 'home', $pageData = []) {
        $injector = new self($db, $config);
        
        $headCodes = $injector->generateHeadTrackingCodes($languageID);
        $pageCodes = $injector->generatePageTrackingCodes($pageType, $pageData, $languageID);
        
        return $headCodes . $pageCodes;
    }
}
