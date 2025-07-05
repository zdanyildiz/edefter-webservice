<?php

/**
 * Banner Manager - Banner render işlemlerini optimize eden merkezi yönetici
 */
class BannerManager
{
    private static ?BannerManager $instance = null;
    private ?Casper $casper = null;
    private array $bannerInfo = [];
    private array $renderedBanners = [];
    private string $globalCss = '';
    private string $globalJs = '';
    private bool $isInitialized = false;

    private function __construct() {}

    public static function getInstance(): BannerManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Banner Manager'ı başlat
     */
    public function initialize(array $bannerInfo, Casper $casper): void
    {
        if ($this->isInitialized) {
            return; // Zaten başlatılmış
        }

        $this->bannerInfo = $bannerInfo;
        $this->casper = $casper;
        $this->isInitialized = true;

        // Cache'den mevcut render sonuçlarını yükle
        $this->loadFromCache();
    }

    /**
     * Belirli tip banner'ı render et
     */
    public function renderBannerType(int $typeId, ?int $pageId = null, ?int $categoryId = null): array
    {
        $cacheKey = "type_{$typeId}_page_{$pageId}_category_{$categoryId}";

        // Zaten render edilmiş mi kontrol et
        if (isset($this->renderedBanners[$cacheKey])) {
            return $this->renderedBanners[$cacheKey];
        }

        // Banner Controller ile render et
        $bannerController = new BannerController($this->bannerInfo);
        $result = $bannerController->renderBannersByType($typeId, $pageId, $categoryId);

        // Sonucu cache'le
        $this->renderedBanners[$cacheKey] = $result;
        $this->saveToCache();

        // Global CSS/JS'e ekle
        $this->globalCss .= $result['css'];
        $this->globalJs .= $result['js'];

        return $result;
    }

    /**
     * Tüm banner tiplerini render et
     */
    public function renderAllBannerTypes(?int $pageId = null, ?int $categoryId = null): array
    {
        $allCacheKey = "all_page_{$pageId}_category_{$categoryId}";

        // Cache kontrolü
        if (isset($this->renderedBanners[$allCacheKey])) {
            return $this->renderedBanners[$allCacheKey];
        }

        // Banner tiplerini belirle
        $bannerTypes = $this->getBannerTypes($pageId, $categoryId);
        $result = ['types' => []];

        foreach ($bannerTypes as $typeId) {
            $result['types'][$typeId] = $this->renderBannerType($typeId, $pageId, $categoryId);
        }

        // Global CSS/JS
        $result['all_css'] = $this->globalCss;
        $result['all_js'] = $this->globalJs;

        // Cache'le
        $this->renderedBanners[$allCacheKey] = $result;
        $this->saveToCache();

        return $result;
    }

    /**
     * Özel tip render metodları
     */
    public function getSliderBanners(?int $pageId = null, ?int $categoryId = null): array
    {
        return $this->renderBannerType(1, $pageId, $categoryId);
    }

    public function getTopBanners(?int $pageId = null, ?int $categoryId = null): array
    {
        return $this->renderBannerType(2, $pageId, $categoryId);
    }

    public function getMiddleBanners(?int $pageId = null, ?int $categoryId = null): array
    {
        return $this->renderBannerType(3, $pageId, $categoryId);
    }

    public function getBottomBanners(?int $pageId = null, ?int $categoryId = null): array
    {
        return $this->renderBannerType(4, $pageId, $categoryId);
    }

    public function getPopupBanners(?int $pageId = null, ?int $categoryId = null): array
    {
        $result = $this->renderBannerType(5, $pageId, $categoryId);
        // Popup için grup ID ekle
        if (!empty($result['banners'])) {
            $result['groupId'] = $result['banners'][0]['group_info']['id'] ?? null;
        }
        $result['groupId'] = null;
        return $result;
    }

    /**
     * Cache'den yükle
     */
    private function loadFromCache(): void
    {
        if ($this->casper) {
            $cached = $this->casper->getBannerCache();
            if (!empty($cached)) {
                $this->renderedBanners = $cached['rendered'] ?? [];
                $this->globalCss = $cached['global_css'] ?? '';
                $this->globalJs = $cached['global_js'] ?? '';
            }
        }
    }

    /**
     * Cache'e kaydet
     */
    private function saveToCache(): void
    {
        if ($this->casper) {
            $cacheData = [
                'rendered' => $this->renderedBanners,
                'global_css' => $this->globalCss,
                'global_js' => $this->globalJs
            ];
            $this->casper->setBannerCache($cacheData);
        }
    }

    /**
     * Banner tiplerini belirle
     */
    private function getBannerTypes(?int $pageId = null, ?int $categoryId = null): array
    {
        $types = [];        foreach ($this->bannerInfo as $banner) {
            // Gösterim kuralları kontrolü - sayfa/kategori kontrolü
            if ($this->matchesPageAndCategory($banner, $pageId, $categoryId)) {
                $types[] = $banner['type_id'];
            }
        }
        
        return array_unique($types);
    }    /**
     * Sayfa ve kategori eşleşme kontrolü
     */
    private function matchesPageAndCategory(array $banner, ?int $pageId, ?int $categoryId): bool
    {
        $bannerPageId = $banner['page_id'] ?? null;
        $bannerCategoryId = $banner['category_id'] ?? null;
        
        // Sayfa kontrolü - banner için sayfa belirtilmişse ve eşleşmiyorsa false
        if (!is_null($bannerPageId) && $bannerPageId != $pageId) {
            return false;
        }
        
        // Kategori kontrolü - banner için kategori belirtilmişse ve eşleşmiyorsa false
        if (!is_null($bannerCategoryId) && $bannerCategoryId != $categoryId) {
            return false;
        }

        return true;
    }

    /**
     * Global CSS/JS al
     */
    public function getGlobalCss(): string
    {
        return $this->globalCss;
    }

    public function getGlobalJs(): string
    {
        return $this->globalJs;
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        $this->renderedBanners = [];
        $this->globalCss = '';
        $this->globalJs = '';

        Log::write("BannerManager: Cache temizlendi.");
        
        if ($this->casper) {
            $this->casper->clearBannerCache();
        }
    }

    /**
     * Site config değiştiğinde cache'i temizle
     */
    public function onSiteConfigChange(): void
    {
        $this->clearCache();
        $this->isInitialized = false;
    }
}
