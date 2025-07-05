<?php

class BannerController
{
    private $bannerInfo;
    private $cssContent = '';
    private $jsContent = '';
    private Helper $helper;

    public function __construct($bannerInfo = [])
    {
        $this->helper = new Helper();
        $this->bannerInfo = $bannerInfo;
        // JavaScript global initleme fonksiyonunu ekle
        $this->jsContent .= $this->getGlobalInitJS();
    }

    /**
     * Banner tipine göre filtreleme fonksiyonu
     */
    public function getBannersByType($typeId, $pageId = null, $categoryId = null)
    {
        $filteredBanners = [];
        $pageBanners = $this->getBannersByPage($pageId, $categoryId);

        foreach ($pageBanners as $banner) {
            if ($banner['type_id'] == $typeId) {
                $filteredBanners[] = $banner;
            }
        }

        return $filteredBanners;
    }

    /**
     * Belirli tipteki bannerları render et
     */
    public function renderBannersByType($typeId, $pageId = null, $categoryId = null)
    {
        $banners = $this->getBannersByType($typeId, $pageId, $categoryId);

        if (empty($banners)) {
            return [
                'html' => '',
                'css' => '',
                'js' => '',
                'banners' => []
            ];
        }

        // Banner tiplerine göre CSS dosyasını ekle
        $this->loadBannerTypeCSS($banners);

        // Banner stillerine göre dinamik CSS oluştur
        $this->generateBannerCSS($banners);

        // Banner HTML'lerini oluştur
        $html = '';
        foreach ($banners as $banner) {
            $html .= $this->renderBannerHTML($banner);
        }

        return [
            'html' => $html,
            'css' => $this->cssContent,
            'js' => $this->jsContent,
            'banners' => $banners
        ];
    }

    /**
     * Sadece slider bannerları render et
     */
    public function renderSliderBanners($pageId = null, $categoryId = null)
    {
        // Banner tipi 1: Slider
        return $this->renderBannersByType(1, $pageId, $categoryId);
    }

    /**
     * Sadece tepe bannerları render et
     */
    public function renderTopBanners($pageId = null, $categoryId = null)
    {
        // Banner tipi 2: Tepe banner (Üstte gösterilen)
        return $this->renderBannersByType(2, $pageId, $categoryId);
    }

    public function renderBottomBanners($pageId = null, $categoryId = null)
    {
        // Banner tipi 2: Tepe banner (Üstte gösterilen)
        return $this->renderBannersByType(4, $pageId, $categoryId);
    }

    public function renderPopupBanners($pageId = null, $categoryId = null)
    {
        // Banner tipi 5: Popup banner
        $result = $this->renderBannersByType(5, $pageId, $categoryId);
        // include groupId for cookie naming
        $groupId = $this->getPopupGroupId($result['banners']);
        $result['groupId'] = $groupId;

        return $result;
    }
    /**
     * Returns popup group ID from banners array
     */
    private function getPopupGroupId(array $banners)
    {
        return isset($banners[0]['group_info']['id']) ? $banners[0]['group_info']['id'] : null;
    }

    /**
     * Tüm banner tiplerini ayrı ayrı render edip döndür
     */
    public function renderAllBannerTypes($pageId = null, $categoryId = null)
    {
        $banners = $this->getBannersByPage($pageId, $categoryId);

        // Banner tiplerini bul
        $bannerTypes = [];
        foreach ($banners as $banner) {
            $typeId = $banner['type_id'];
            if (!isset($bannerTypes[$typeId])) {
                $bannerTypes[$typeId] = [
                    'type_name' => $banner['type_name'],
                    'banners' => []
                ];
            }
            $bannerTypes[$typeId]['banners'][] = $banner;
        }

        // Her tip için ayrı render işlemi yap
        $result = [];
        foreach ($bannerTypes as $typeId => $typeInfo) {
            $typeResult = $this->renderBannersByType($typeId, $pageId, $categoryId);
            $result[$typeId] = [
                'type_name' => $typeInfo['type_name'],
                'html' => $typeResult['html'],
                'css' => $typeResult['css'],
                'js' => $typeResult['js'],
                'banners' => $typeResult['banners']
            ];
        }

        // Tüm CSS ve JS içeriğini birleştir
        $allCss = '';
        $allJs = '';
        foreach ($result as $typeResult) {
            $allCss .= $typeResult['css'];
            $allJs .= $typeResult['js'];
        }

        return [
            'types' => $result,
            'all_css' => $allCss,
            'all_js' => $allJs
        ];
    }

    /**
     * Sayfa/kategori ID'sine göre banner filtreleme
     */
    public function getBannersByPage($pageId = null, $categoryId = null)
    {
        $filteredBanners = [];

        foreach ($this->bannerInfo as $banner) {
            // Tüm sayfalarda gösterilecek bannerlar
            if (empty($banner['page_id']) && empty($banner['category_id'])) {
                $filteredBanners[] = $banner;
                continue;
            }

            // Belirli sayfaya özel bannerlar
            if (!empty($pageId) && $banner['page_id'] == $pageId) {
                $filteredBanners[] = $banner;
                continue;
            }

            // Belirli kategoriye özel bannerlar
            if (!empty($categoryId) && $banner['category_id'] == $categoryId) {
                $filteredBanners[] = $banner;
                continue;
            }
        }

        return $filteredBanners;
    }

    /**
     * Banner stillerine göre dinamik CSS oluşturma
     */    public function generateBannerCSS($banners)
    {
        $css = '';
        $renderedGroups = []; // Her grup için ayrı kontrol
        //print_r($banners);exit;
        foreach ($banners as $banner) {
            //echo '<pre>'; print_r($banner['group_info']); exit;
            $bannerGroupId = $banner['group_info']['id'];
            $bannerType = $banner['type_id'];
            $bannerTypeName = str_replace(' ', '-', $banner['type_name']);
            $bannerTypeName = str_replace(["(",")"], '', $bannerTypeName);
            $bannerTypeName = $this->helper->turkish_to_lower($bannerTypeName);
            $bannerTypeName = $this->helper->trToEn($bannerTypeName);

            $bannerGroupTitleColor = $banner['group_info']['group_title_color'];
            $bannerGroupDescColor = $banner['group_info']['group_desc_color'];
            $bannerGroupBgColor = $banner['group_info']['background_color'];
            $bannerGroupFullSize = $banner['group_info']['group_full_size'];
            $bannerFullSize = $banner['group_info']['banner_full_size'];
            $customCss = $banner['group_info']['custom_css'] ?? '';
            $styleClass = $banner['group_info']['style_class'] ?? '';

            $layoutGroup = $banner['layout_info']['layout_group'];
            $layoutView = $banner['layout_info']['layout_view'];
            $columns = $banner['layout_info']['columns'];
            
            // Bu grup için daha önce CSS oluşturuldu mu kontrol et
            if(!in_array($bannerGroupId, $renderedGroups)){
                // Banner ana container - SADECE DEĞİŞKEN VERİLER
                $css .= "/* Banner Group {$bannerGroupId} - Dynamic Styles Only */\n";
                
                // Arka plan rengi (değişken veri)
                if (!empty($bannerGroupBgColor)) {
                    $css .= ".banner-group-{$bannerGroupId} {\n";
                    $css .= "  background-color: {$bannerGroupBgColor};\n";
                    $css .= "}\n\n";
                }


                // Genişlik ayarları (değişken veri)
                if ($bannerGroupFullSize != 1 || $bannerFullSize != 1) {
                    $css .= ".banner-group-{$bannerGroupId} {\n";
                    $css .= ($bannerGroupFullSize == 1) ? "  width: 100%;\n" : "  width: var(--content-max-width);\n";
                    $css .= ($bannerGroupFullSize == 1) ? "  max-width: 100%;\n" : "  max-width: var(--content-max-width);\n";
                    $css .= "}\n\n";

                    $css .= ".banner-group-{$bannerGroupId} .banner-container {\n";
                    $css .= ($bannerFullSize == 1) ? "  width: 100%;\n" : "  width: var(--content-max-width);\n";
                    $css .= ($bannerFullSize == 1) ? "  max-width: 100%;\n" : "  max-width: var(--content-max-width);\n";
                    $css .= "}\n\n";
                }

                // Sütun ayarları (değişken veri)
                if ($columns > 1) {
                    $containerType = ($bannerType == 1) ? "slider" : (($bannerType == 6) ? "carousel" : "container");
                    $css .= ".banner-group-{$bannerGroupId} .{$layoutGroup}-{$containerType} {\n";
                    $css .= "  grid-template-columns: repeat({$columns}, 1fr);\n";
                    $css .= "}\n\n";
                }

                // Grup renk ayarları (değişken veri)
                if(!empty($bannerGroupTitleColor)){
                    $css .= ".banner-group-{$bannerGroupId} .banner-group-title {\n";
                    $css .= "  color: {$bannerGroupTitleColor};\n";
                    $css .= "}\n\n";
                }
                if(!empty($bannerGroupDescColor)){
                    $css .= ".banner-group-{$bannerGroupId} .banner-group-desc {\n";
                    $css .= "  color: {$bannerGroupDescColor};\n";
                    $css .= "}\n\n";
                }
            }            // Banner öğeleri için stil tanımlamaları
            foreach ($banner['banners'] as $singleBanner) {
                $bannerItemId = $singleBanner['id'];
                $style = $singleBanner['style'];

                // Debug: Style verilerini kontrol et
                //Log::write("Banner_id: $bannerItemId - Style Debug: " . json_encode($style));
                //Log::write("Banner_id: $bannerItemId - ShowButton type: " . gettype($style['show_button']) . " value: " . var_export($style['show_button'], true));

                $css .= ".banner-{$bannerItemId} {\n";
                // Banner tipine göre yükseklik birimini ayarla
                if ($bannerType ==12) { // Tepe Banner için px kullan
                    $css .= "  height: {$style['banner_height_size']}vh;\n";
                }
                elseif ($bannerType == 2) { // Tepe Banner için px kullan
                    $css .= "  height: {$style['banner_height_size']}px;\n";
                }
                else {
                    $css .= "  min-height: {$style['banner_height_size']}px;\n";
                }

                if(!empty($style['background_color'])){
                    $css .= "  background-color: {$style['background_color']};\n";
                }

                $css .= "}\n\n";

                $css .= ".banner-{$bannerItemId} .content-box {\n";
                if(!empty($style['content_box_bg_color'])){
                    $css .= "  background-color: {$style['content_box_bg_color']};\n";
                }

                $css .= "}\n\n";

                $css .= ".banner-{$bannerItemId} .title,.banner-type-{$bannerTypeName}.{$styleClass} .banner-item.banner-{$bannerItemId} h2.title{\n";
                $css .= "  color: {$style['title_color']};\n";
                $css .= "  font-size: {$style['title_size']}px;\n";
                $css .= "}\n\n";

                $css .= ".banner-{$bannerItemId} .content {\n";
                $css .= "  color: {$style['content_color']};\n";                $css .= "  font-size: {$style['content_size']}px;\n";
                $css .= "}\n\n";

                // Debug: CSS oluşturma sırasında show_button kontrolü
                //Log::write("CSS Debug - Banner_id: $bannerItemId - show_button value: " . var_export($style['show_button'], true));
                
                if ($style['show_button'] == 1) {
                    //Log::write("CSS Debug - Banner_id: $bannerItemId - BUTON CSS'İ OLUŞTURULUYOR!");
                    $css .= ".banner-{$bannerItemId} .banner-button {\n";
                    $css .= "  background-color: {$style['button_background']};\n";
                    $css .= "  color: {$style['button_color']};\n";
                    $css .= "  font-size: {$style['button_size']}px;\n";
                    $css .= "}\n\n";

                    $css .= ".banner-{$bannerItemId} .banner-button:hover {\n";
                    $css .= "  background-color: {$style['button_hover_background']};\n";
                    $css .= "  color: {$style['button_hover_color']};\n";
                    $css .= "}\n\n";
                } else {
                    //Log::write("CSS Debug - Banner_id: $bannerItemId - Buton CSS'i oluşturulmuyor");
                }
            }
            
            // Özel CSS (değişken veri)
            if (!empty($customCss)) {
                $css .= "/* Custom CSS for Group {$bannerGroupId} */\n";
                $css .= $customCss . "\n\n";
            }
            
            // Bu grup için CSS oluşturulduğunu kaydet
            $renderedGroups[] = $bannerGroupId;
        }

        $this->cssContent .= $css;
        return $css;
    }

    /**
     * Banner tipine göre CSS dosyalarını yükle
     */
    public function loadBannerTypeCSS($banners)
    {
        $css = '';
        $loadedTypes = [];
        $loadedLayouts = [];
        $loadClass = [];
        foreach ($banners as $banner) {
            $typeId = $banner['type_id'];
            $layoutGroup = $banner['layout_info']['layout_group'];
            $layoutView = $banner['layout_info']['layout_view'];

            // Banner tipine göre CSS dosyasını yükle
            if (!in_array($typeId, $loadedTypes)) {
                $typeName = $this->helper->turkish_to_lower($banner['type_name']);
                $typeName = $this->helper->trToEn($typeName);
                $typeName = strtolower(str_replace(' ', '-', $typeName));
                $cssPath = CSS . "Banners/{$typeName}.min.css";

                if (file_exists($cssPath)) {
                    //Log::write("Banner Css Path: {$cssPath}");
                    $css .= "/* Banner Type: {$typeName} */\n";
                    $css .= file_get_contents($cssPath) . "\n";
                    $loadedTypes[] = $typeId;
                }
            }            $groupInfo = $banner['group_info'];
            $styleClass = $groupInfo['style_class'];
            if(!in_array($styleClass, $loadClass)){

                // Önce .min.css dosyasını kontrol et
                $cssStylePath = CSS . "Banners/{$styleClass}.min.css";
                
                if(file_exists($cssStylePath)) {
                    //Log::write("Banner Style Path (min): {$cssStylePath}");
                    $css .= "/* Banner Style: {$styleClass} */\n";
                    $css .= file_get_contents($cssStylePath) . "\n";
                    $loadClass[] = $styleClass;
                }
                // .min.css yoksa normal .css dosyasını kontrol et
                else {
                    $cssStylePath = CSS . "Banners/{$styleClass}.css";
                    if(file_exists($cssStylePath)) {
                        //Log::write("Banner Style Path (normal): {$cssStylePath}");
                        $css .= "/* Banner Style: {$styleClass} */\n";
                        $css .= file_get_contents($cssStylePath) . "\n";
                        $loadClass[] = $styleClass;
                    }
                }
            }

            // Layout kombinasyonuna göre CSS dosyasını yükle
            $layoutKey = "{$layoutGroup}-{$layoutView}";
            if (!in_array($layoutKey, $loadedLayouts)) {
                $cssLayoutPath = CSS . "Banners/layouts/{$layoutKey}.min.css";

                if (file_exists($cssLayoutPath)) {
                    $css .= "/* Banner Layout: {$layoutKey} */\n";
                    $css .= file_get_contents($cssLayoutPath) . "\n";
                    $loadedLayouts[] = $layoutKey;
                }
            }
        }

        $this->cssContent .= $css;
        return $css;
    }

    /**
     * Banner HTML yapısını oluştur
     */
    public function renderBannerHTML($banner)
    {
        $bannerId = $banner['group_info']['id'];
        $bannerType = $banner['type_id'];
        $bannerTypeName = str_replace(' ', '-', $banner['type_name']);
        $bannerTypeName = str_replace(["(",")"], '', $bannerTypeName);
        $bannerTypeName = $this->helper->turkish_to_lower($bannerTypeName);
        $bannerTypeName = $this->helper->trToEn($bannerTypeName);
        $layoutGroup = $this->convertLayoutGroup($banner['layout_info']['layout_group']);
        $layoutView = $banner['layout_info']['layout_view'];
        $columns = $banner['layout_info']['columns'];
        $styleClass = $banner['group_info']['style_class'];
        $groupView = $banner['group_info']['group_view'];
        $groupKind = $banner['group_info']['group_kind'];
        
        // Full width kontrolü ve merkezleme sınıfı
        $containerClass = '';
        if (isset($banner['group_info']['group_full_size']) && $banner['group_info']['group_full_size'] == 0) {
            $containerClass .= ' banner-centered';
        }
        if (isset($banner['group_info']['banner_full_size']) && $banner['group_info']['banner_full_size'] == 0) {
            $containerClass .= ' banner-content-centered';
        }        // Ana container - tüm sınıfları ekle
        $html = "<div class='banner-group-{$bannerId} banner-type-{$bannerTypeName} {$styleClass} {$groupView}'  data-layout-group='{$layoutGroup}'>\n";

        // Banner grup başlığı ve açıklaması (eğer varsa)
        $groupTitle = $banner['group_info']['group_title'] ?? '';
        $groupDesc = $banner['group_info']['group_desc'] ?? '';
        
        if (!empty($groupTitle) || !empty($groupDesc)) {
            $html .= "<div class='banner-group-header'>\n";
            
            if (!empty($groupTitle)) {
                $html .= "<h2 class='banner-group-title'>{$groupTitle}</h2>\n";
            }
            
            if (!empty($groupDesc)) {
                $html .= "<div class='banner-group-desc'>{$groupDesc}</div>\n";
            }
            
            $html .= "</div>\n";
        }

        // Banner tipine ve layout görünümüne göre iç container oluştur
        if ($styleClass == "fullwidth") { // Slider
            $html .= "<div class='slider-container {$layoutView}-slider'>\n";
        }
        else if ($styleClass == "Carousel" || $styleClass == "CarouselComments") { // Carousel Slider
            $html .= "<div class='carousel-container {$layoutView}-carousel'>\n";
        }
        else {
            $html .= "<div class='banner-container{$containerClass}'>\n";
        }

        foreach ($banner['banners'] as $singleBanner) {
            $bannerItemId = $singleBanner['id'];
            $title = $singleBanner['title'];
            $content = $singleBanner['content'];
            $image = $singleBanner['image'];
            $link = $singleBanner['link'];
            $style = $singleBanner['style'];

            // Layout türüne göre item class'ı oluştur
            $itemClass = "banner-item banner-{$bannerItemId}";

            if ($columns > 1) {
                $itemClass .= " column-item";
            }

            if ($layoutGroup == 'text_and_image') {
                $itemClass .= " text-image-layout";
            }
            else if ($layoutGroup == 'only_text') {
                $itemClass .= " text-only-layout";
            }
            else if ($layoutGroup == 'only_image') {
                $itemClass .= " image-only-layout";
            }

            $html .= "<div class='{$itemClass}'>\n";

            if (!empty($link)) {
                $html .= "<a href='{$link}'>\n";
            }

            // Text ve image düzeni
            if ($groupKind == 'text_and_image' || $groupKind == 'only_image') {
                $html .= "<div class='banner-image'>\n";
                $html .= "<img src='/Public/Image/?imagePath={$image}' alt='{$title}'>\n";
                $html .= "</div>\n";
            }            if ($groupKind == 'text_and_image' || $groupKind == 'only_text') {
                $html .= "<div class='content-box'>\n";
                $html .= "<h2 class='title'>{$title}</h2>\n";
                $html .= "<div class='content'>{$content}</div>\n";

                // Debug: HTML oluşturma sırasında show_button kontrolü
                //Log::write("HTML Debug - Banner_id: $bannerItemId - show_button value: " . var_export($style['show_button'], true) . " type: " . gettype($style['show_button']));
                //Log::write("HTML Debug - Banner_id: $bannerItemId - show_button == 1: " . var_export($style['show_button'] == 1, true));
                //Log::write("HTML Debug - Banner_id: $bannerItemId - show_button === 1: " . var_export($style['show_button'] === 1, true));
                //Log::write("HTML Debug - Banner_id: $bannerItemId - show_button == '1': " . var_export($style['show_button'] == '1', true));

                if ($style['show_button'] == 1) {
                    //Log::write("HTML Debug - Banner_id: $bannerItemId - BUTON OLUŞTURULUYOR!");
                    $html .= "<div class='button-container'>\n";
                    $html .= "<button class='banner-button' id='bannerButton-{$bannerItemId}'>{$style['button_title']}</button>\n";
                    $html .= "</div>\n";
                } else {
                    //Log::write("HTML Debug - Banner_id: $bannerItemId - Buton oluşturulmuyor");
                }

                $html .= "</div>\n"; // content-box
            }
            else{
                // Debug: Else bloğunda da aynı kontrol
                //Log::write("HTML Debug (ELSE) - Banner_id: $bannerItemId - show_button value: " . var_export($style['show_button'], true));
                
                if ($style['show_button'] == 1) {
                    //Log::write("HTML Debug (ELSE) - Banner_id: $bannerItemId - BUTON OLUŞTURULUYOR!");
                    $html .= "<div class='button-container'>\n";
                    $html .= "<button class='banner-button' id='bannerButton-{$bannerItemId}'>{$style['button_title']}</button>\n";
                    $html .= "</div>\n";
                } else {
                    //Log::write("HTML Debug (ELSE) - Banner_id: $bannerItemId - Buton oluşturulmuyor");
                }
            }

            //Log::write("Banner_id: $bannerItemId ShowButton: {$style['show_button']}");

            if (!empty($link)) {
                $html .= "</a>\n";
            }

            $html .= "</div>\n"; // banner-item
        }

        $html .= "</div>\n"; // container        // Banner tiplerine özel kontrol butonları
        if ($styleClass == "fullwidth") { // Slider
            // Tek banner varsa kontrolleri gösterme
            $bannerCount = count($banner['banners']);
            if ($bannerCount > 1) {
                $html .= "<div class='slider-controls'>\n";
                $html .= "<button class='prev-slide'>&#10094;</button>\n";
                $html .= "<button class='next-slide'>&#10095;</button>\n";
                $html .= "</div>\n";
            }

            $this->jsContent .= $this->getSliderJS($bannerId, $layoutView, $bannerCount);
        }
        else if ($bannerType == 5) { // popup banner
            $this->jsContent .= $this->getPopupJS();
        }
        else if ($styleClass == "Carousel" || $styleClass == "CarouselComments") { // Carousel
            $html .= "<div class='carousel-controls'>\n";
            $html .= "<button class='prev-carousel'>&#10094;</button>\n";
            $html .= "<button class='next-carousel'>&#10095;</button>\n";
            $html .= "</div>\n";

            $this->jsContent .= $this->getCarouselJS($bannerId, $layoutView);
        }

        $html .= "</div>\n"; // banner

        return $html;
    }

    /**
     * Global slider ve carousel initilize fonksiyonu
     */
    private function getGlobalInitJS()
    {
        return "
        (function() {
            // Spesifik slider init kontrolü için global değişken
            window.initializedSliders = window.initializedSliders || {};
            
            // sayfa yükleme durumunu kontrol et
            function waitForPageLoad(callback) {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', callback);
                } else {
                    callback();
                }
            }
            
            // Tüm slider'ları başlat
            waitForPageLoad(function() {
                ////console.log('Global slider init başladı');
                
                // Button işlevlerini düzenle fonksiyonu
                function fixSliderButtons() {
                    // Tüm slider containerları için
                    const sliders = document.querySelectorAll('.slider-container');
                    
                    sliders.forEach((slider, index) => {
                        // Slider kontrolleri
                        const sliderParent = slider.closest('.banner-type-slider');
                        const sliderId = sliderParent ? sliderParent.getAttribute('data-id') || sliderParent.className.match(/banner-group-(\d+)/)?.[1] || index : index;
                        
                        // Bu slider spesifik olarak başlatıldıysa atla
                        if (window.initializedSliders[sliderId]) {
                            ////console.log('Slider #' + sliderId + ' zaten başlatılmış, atlanıyor');
                            return;
                        }
                        
                        // İlk slider öğesini görünür yap ama active sınıfı eklemeden
                        const firstSlide = slider.querySelector('.banner-item');
                        if (firstSlide) {
                            firstSlide.classList.add('initial-visible');
                        }
                        
                        // Eğer spesifik bir slider tanımı varsa, global init'i atlayalım
                        if (sliderParent && sliderParent.classList.contains('banner-initialized')) {
                            return;
                        }
                          const prevBtn = sliderParent ? sliderParent.querySelector('.prev-slide') : null;
                        const nextBtn = sliderParent ? sliderParent.querySelector('.next-slide') : null;
                        
                        // Butonlar yoksa (tek banner durumu) sadece görünüm ayarlarını yap
                        if (!prevBtn || !nextBtn) {
                            // İlk ve tek slide'ı göster
                            const slides = slider.querySelectorAll('.banner-item');
                            if (slides.length === 1) {
                                slides[0].classList.add('active');
                                slides[0].style.display = 'block';
                                slides[0].style.opacity = '1';
                            }
                            return;
                        }
        
                        if (prevBtn && nextBtn) {
                            ////console.log('Slider #' + sliderId + ' için butonlar aktifleştiriliyor');
        
                            // Bu slider'ın başlatıldığını işaretle
                            window.initializedSliders[sliderId] = true;
                            
                            // Slider'ı initialized olarak işaretle
                            if (sliderParent) {
                                sliderParent.classList.add('banner-initialized');
                                // Benzersiz ID ekliyoruz
                                if (!sliderParent.hasAttribute('data-id')) {
                                    sliderParent.setAttribute('data-id', sliderId);
                                }
                            }
        
                            // Butonlara doğrudan tıklama işlevselliği ekleme
                            const slides = slider.querySelectorAll('.banner-item');
                            let currentIndex = 0;
                            let isAnimating = false;
                            let isFirstActivation = true;
                            let animationCounter = 0; // Animasyon sayacı ekledik
                            let autoplayInterval; // Değişkeni tanımla
                            
                            // İlk slayt gösterimi - başlangıçta sadece bir kez çalışacak
                            function initFirstSlide() {
                                // İlk slaytı göster (initial-visible sınıfını kullan, active değil)
                                slides.forEach((slide, i) => {
                                    if (i === 0) {
                                        slide.classList.add('initial-visible');
                                        slide.style.display = 'block';
                                        slide.style.opacity = '1';
                                        slide.style.transform = 'scale(1)';
                                    } else {
                                        slide.classList.remove('initial-visible');
                                        slide.style.display = 'none';
                                        slide.style.opacity = '0';
                                    }
                                });
                            }
                            
                            function showSlide(idx, direction = null) {
                                if (isAnimating) return;
                                isAnimating = true;
                                
                                ////console.log('showSlide çalışıyor, index:', idx, 'animasyon sayacı:', animationCounter);
                                
                                // İlk aktivasyon kontrolü
                                const specialFirstActivation = isFirstActivation;
                                isFirstActivation = false;
                                
                                // Tüm initial-visible sınıflarını kaldır
                                slides.forEach(slide => {
                                    slide.classList.remove('initial-visible');
                                });
                                
                                // Önceki aktif slaytı kaldır
                                const currentActiveSlide = slider.querySelector('.banner-item.active');
                                if (currentActiveSlide) {
                                    // Tüm animasyon sınıflarını temizle
                                    currentActiveSlide.classList.remove('active', 'slide-next', 'slide-prev', 'first-activation', 'with-zoom', 'no-zoom');
                                    
                                    // İlk gösterimde geçiş animasyonu olmadan değişim yap
                                    if (!specialFirstActivation) {
                                        setTimeout(() => {
                                            currentActiveSlide.style.display = 'none';
                                            currentActiveSlide.style.opacity = '0';
                                            currentActiveSlide.style.transform = 'scale(0.95)';
                                        }, 300);
                                    } else {
                                        currentActiveSlide.style.display = 'none';
                                        currentActiveSlide.style.opacity = '0';
                                        currentActiveSlide.style.transform = 'scale(0.95)';
                                    }
                                }
                                
                                // Yeni slaytı göster
                                const newSlide = slides[idx];
                                newSlide.style.display = 'block';
                                
                                // Animasyon kontrolü - ilk aktivasyon ise animasyon olmadan göster
                                if (specialFirstActivation) {
                                    newSlide.classList.add('active', 'first-activation', 'no-zoom');
                                    newSlide.style.opacity = '1'; 
                                    newSlide.style.transform = 'scale(1)'; 
                                    isAnimating = false;
                                } else {
                                    // Animasyon için çok kısa gecikme
                                    requestAnimationFrame(() => {
                                        // Slaytın stil özelliklerini doğrudan ayarla
                                        newSlide.style.opacity = '1'; 
                                        newSlide.style.transform = 'scale(1)'; 
                                        
                                        // Her ikinci animasyon için zoom efekti uygula
                                        const applyZoom = animationCounter % 2 === 0;
                                        
                                        // Geçiş yönüne göre sınıf ekle
                                        if (direction === 'next') {
                                            newSlide.classList.add('active', 'slide-next', applyZoom ? 'with-zoom' : 'no-zoom');
                                        } else if (direction === 'prev') {
                                            newSlide.classList.add('active', 'slide-prev', applyZoom ? 'with-zoom' : 'no-zoom');
                                        } else {
                                            newSlide.classList.add('active', applyZoom ? 'with-zoom' : 'no-zoom');
                                        }
                                        
                                        // Animasyon sayacını artır
                                        animationCounter++;
                                        
                                        // Animasyon bittiğinde isAnimating'i false yap
                                        setTimeout(() => {
                                            isAnimating = false;
                                        }, 800);
                                    });
                                }
                            }
        
                            function goToPrev(e) {
                                if (e) e.preventDefault();
                                ////console.log('Önceki slayta git fonksiyonu çalıştı');
                                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                                showSlide(currentIndex, 'prev');
                                
                                // Butona tıklandığında otomatik geçiş süresini sıfırla
                                clearInterval(autoplayInterval);
                                /*autoplayInterval = setInterval(() => {
                                    if (!isAnimating) {
                                        goToNext();
                                    }
                                }, 5000);*/
                                
                                return false;
                            }
                            
                            function goToNext(e) {
                                if (e) e.preventDefault();
                                ////console.log('Sonraki slayta git fonksiyonu çalıştı');
                                currentIndex = (currentIndex + 1) % slides.length;
                                showSlide(currentIndex, 'next');
                                
                                // Butona tıklandığında otomatik geçiş süresini sıfırla
                                clearInterval(autoplayInterval);
                                /*autoplayInterval = setInterval(() => {
                                    if (!isAnimating) {
                                        goToNext();
                                    }
                                }, 5000);*/
                                
                                return false;
                            }
                            
                            // Önceki tüm event listener'ları temizle
                            prevBtn.replaceWith(prevBtn.cloneNode(true));
                            nextBtn.replaceWith(nextBtn.cloneNode(true));
                            
                            // Yeni butonları al
                            const newPrevBtn = sliderParent.querySelector('.prev-slide');
                            const newNextBtn = sliderParent.querySelector('.next-slide');
                            
                            // Event listener'lar ekle
                            newPrevBtn.addEventListener('click', goToPrev);
                            newNextBtn.addEventListener('click', goToNext);
                            
                            // İkinci bir yöntem olarak onclick ataması
                            newPrevBtn.onclick = goToPrev;
                            newNextBtn.onclick = goToNext;
                            
                            // İlk slaytı başlat
                            initFirstSlide();
                            
                            // İlk aktif slayt gösterimi için kısa gecikme - başlangıç animasyonsuz
                            setTimeout(() => {
                                showSlide(0);
                            }, 50);
                            
                            // Otomatik geçiş
                            autoplayInterval = setTimeout(() => {
                                goToNext();
                                // Sonraki otomatik geçişler için normal aralık kullan
                                autoplayInterval = setInterval(() => {
                                    if (!isAnimating) {
                                        goToNext();
                                    }
                                }, 5000);
                            }, 6000); // İlk otomatik geçiş için 6 saniye bekle
                            
                            // Mouse üzerine geldiğinde otomatik geçişi durdur
                            slider.addEventListener('mouseenter', () => {
                                clearInterval(autoplayInterval);
                            });
                            
                            // Mouse ayrıldığında otomatik geçişi yeniden başlat
                            slider.addEventListener('mouseleave', () => {
                                autoplayInterval = setInterval(() => {
                                    if (!isAnimating) {
                                        goToNext();
                                    }
                                }, 5000);
                            });
                        } else {
                            //console.error('Slider #' + sliderId + ' kontrolleri bulunamadı');
                        }
                    });
                }
                
                // Sayfa yüklendikten sonra butonları düzelt
                setTimeout(fixSliderButtons, 100);
                
                // Sayfa tamamen yüklendikten sonra tekrar düzelt (eğer dinamik içerik yüklendiyse)
                window.addEventListener('load', function() {
                    setTimeout(fixSliderButtons, 500);
                });
            });
        })();
        ";
    }    /**
     * Slider için gerekli JS kodunu al
     */
    private function getSliderJS($bannerId, $layoutView, $bannerCount = 1)
    {
        $autoplaySpeed = 5000;
        $effect = ($layoutView == 'box') ? 'slide' : 'fade';
        
        // Tek banner varsa sadece statik gösterim yap
        if ($bannerCount <= 1) {
            return "
            (function() {
                function waitForPageLoad(callback) {
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', callback);
                    } else {
                        callback();
                    }
                }
                
                waitForPageLoad(function() {
                    const sliderContainer = document.querySelector('.banner-group-{$bannerId} .slider-container');
                    if (sliderContainer) {
                        const slide = sliderContainer.querySelector('.banner-item');
                        if (slide) {
                            slide.classList.add('active');
                            slide.style.display = 'block';
                            slide.style.opacity = '1';
                        }
                    }
                });
            })();
            ";
        }

        return "
        (function() {
            // Spesifik slider init kontrolü için global değişken tanımlama
            window.initializedSliders = window.initializedSliders || {};
            
            // sayfa yükleme durumunu kontrol et
            function waitForPageLoad(callback) {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', callback);
                } else {
                    callback();
                }
            }
            
            // Spesifik slider'ı başlat
            waitForPageLoad(function() {
                const initSlider_{$bannerId} = function() {
                    ////console.log('Spesifik slider {$bannerId} başlatılıyor...');
                    
                    // Bu slider zaten global olarak başlatıldıysa çıkış yap
                    if (window.initializedSliders['{$bannerId}']) {
                        ////console.log('Slider {$bannerId} zaten başka bir script tarafından başlatılmış');
                        return;
                    }
                    
                    // Bu slider'ın başlatıldığını kaydet
                    window.initializedSliders['{$bannerId}'] = true;
                    
                    const sliderContainer = document.querySelector('.banner-group-{$bannerId} .slider-container');
                    if (!sliderContainer) {
                        //console.error('Slider container bulunamadı (.banner-group-{$bannerId} .slider-container)');
                        return;
                    }
    
                    // Slider'ı özelleştirilmiş olarak işaretle
                    const sliderParent = sliderContainer.closest('.banner-type-slider');
                    if (sliderParent) {
                        sliderParent.classList.add('banner-initialized');
                        sliderParent.classList.add('effect-{$effect}');
                        sliderParent.setAttribute('data-id', '{$bannerId}');
                    }
    
                    const slides = sliderContainer.querySelectorAll('.banner-item');
                    if (slides.length === 0) {
                        //console.error('Slider öğeleri bulunamadı');
                        return;
                    }
    
                    const prevButton = sliderParent.querySelector('.prev-slide');
                    const nextButton = sliderParent.querySelector('.next-slide');
                    
                    if (!prevButton || !nextButton) {
                        //console.error('Slider butonları bulunamadı');
                        return;
                    }
                    
                    let currentSlide = 0;
                    let isAnimating = false;
                    let isFirstActivation = true;
                    let animationCounter = 0; // Animasyon sayacı ekledik
                    let interval; // Değişkeni tanımla
                    
                    // İlk slayt gösterimi - başlangıçta sadece bir kez çalışacak
                    function initFirstSlide() {
                        // İlk slaytı göster (initial-visible sınıfını kullan, active değil)
                        slides.forEach((slide, i) => {
                            if (i === 0) {
                                slide.classList.add('initial-visible');
                                slide.style.display = 'block';
                                slide.style.opacity = '1';
                                slide.style.transform = 'scale(1)';
                            } else {
                                slide.classList.remove('initial-visible');
                                slide.style.display = 'none';
                                slide.style.opacity = '0';
                            }
                        });
                    }
                    
                    function showSlide(index, direction = null) {
                        if (isAnimating) return;
                        isAnimating = true;
                        
                        ////console.log('Spesifik slider - showSlide çalışıyor, index:', index, 'animasyon sayacı:', animationCounter);
                        
                        // İlk aktivasyon kontrolü
                        const specialFirstActivation = isFirstActivation;
                        isFirstActivation = false;
                        
                        // Tüm initial-visible sınıflarını kaldır
                        slides.forEach(slide => {
                            slide.classList.remove('initial-visible');
                        });
                        
                        // Önceki aktif slaytı sakla
                        const currentActiveSlide = sliderContainer.querySelector('.banner-item.active');
                        if (currentActiveSlide) {
                            // Tüm animasyon sınıflarını temizle
                            currentActiveSlide.classList.remove('active', 'slide-next', 'slide-prev', 'first-activation', 'with-zoom', 'no-zoom');
                            
                            // İlk gösterimde geçiş animasyonu olmadan değişim yap
                            if (!specialFirstActivation) {
                                setTimeout(() => {
                                    if (currentActiveSlide !== slides[index]) {
                                        currentActiveSlide.style.display = 'none';
                                        currentActiveSlide.style.opacity = '0';
                                        currentActiveSlide.style.transform = 'scale(0.95)';
                                    }
                                }, 300);
                            } else {
                                currentActiveSlide.style.display = 'none';
                                currentActiveSlide.style.opacity = '0';
                                currentActiveSlide.style.transform = 'scale(0.95)';
                            }
                        }
                        
                        // Yeni slaytı göster
                        const newSlide = slides[index];
                        newSlide.style.display = 'block';
                        
                        // İlk aktivasyon ise animasyon olmadan göster
                        if (specialFirstActivation) {
                            newSlide.classList.add('active', 'first-activation', 'no-zoom');
                            newSlide.style.opacity = '1'; 
                            newSlide.style.transform = 'scale(1)'; 
                            isAnimating = false;
                        } else {
                            // Animasyon için çok kısa gecikme
                            requestAnimationFrame(() => {
                                // Slaytın stil özelliklerini doğrudan ayarla
                                newSlide.style.opacity = '1'; 
                                newSlide.style.transform = 'scale(1)'; 
                                
                                // Her ikinci animasyon için zoom efekti uygula
                                const applyZoom = animationCounter % 2 === 0;
                                
                                // Geçiş yönüne göre sınıf ekle
                                if (direction === 'next') {
                                    newSlide.classList.add('active', 'slide-next', applyZoom ? 'with-zoom' : 'no-zoom');
                                } else if (direction === 'prev') {
                                    newSlide.classList.add('active', 'slide-prev', applyZoom ? 'with-zoom' : 'no-zoom');
                                } else {
                                    newSlide.classList.add('active', applyZoom ? 'with-zoom' : 'no-zoom');
                                }
                                
                                // Animasyon sayacını artır
                                animationCounter++;
                                
                                // Animasyon tamamlandıktan sonra isAnimating'i false yap
                                setTimeout(() => {
                                    isAnimating = false;
                                }, 800);
                            });
                        }
                    }
                    
                    function goToNext(e) {
                        if (e) e.preventDefault();
                        ////console.log('Spesifik slider - sonraki slayta git fonksiyonu çalıştı');
                        currentSlide = (currentSlide + 1) % slides.length;
                        showSlide(currentSlide, 'next');
                        
                        // Butona tıklandığında otomatik geçiş süresini sıfırla
                        clearInterval(interval);
                        /*interval = setInterval(function() {
                            if (!isAnimating) {
                                goToNext();
                            }
                        }, {$autoplaySpeed});*/
                        
                        return false;
                    }
                    
                    function goToPrev(e) {
                        if (e) e.preventDefault();
                        ////console.log('Spesifik slider - önceki slayta git fonksiyonu çalıştı');
                        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                        showSlide(currentSlide, 'prev');
                        
                        // Butona tıklandığında otomatik geçiş süresini sıfırla
                        clearInterval(interval);
                        /*interval = setInterval(function() {
                            if (!isAnimating) {
                                goToNext();
                            }
                        }, {$autoplaySpeed});*/
                        
                        return false;
                    }
                    
                    // Önceki tüm event listener'ları temizle
                    prevButton.replaceWith(prevButton.cloneNode(true));
                    nextButton.replaceWith(nextButton.cloneNode(true));
                    
                    // Yeni butonları al
                    const newPrevBtn = sliderParent.querySelector('.prev-slide');
                    const newNextBtn = sliderParent.querySelector('.next-slide');
                    
                    // Event listener'lar ekle
                    newPrevBtn.addEventListener('click', goToPrev);
                    newNextBtn.addEventListener('click', goToNext);
                    
                    // İkinci bir yöntem olarak onclick ataması
                    newPrevBtn.onclick = goToPrev;
                    newNextBtn.onclick = goToNext;
                    
                    // İlk slaytı başlat
                    initFirstSlide();
                    
                    // İlk aktif slayt gösterimi için kısa gecikme - başlangıç animasyonsuz
                    setTimeout(() => {
                        showSlide(0);
                    }, 50);
                    
                    // Otomatik geçiş
                    interval = setTimeout(() => {
                        goToNext();
                        // Sonraki otomatik geçişler için normal aralık kullan
                        interval = setInterval(function() {
                            if (!isAnimating) {
                                goToNext();
                            }
                        }, {$autoplaySpeed});
                    }, 6000); // İlk otomatik geçiş için 6 saniye bekle
                    
                    // Mouse üzerine gelince otomatik geçişi durdur
                    sliderContainer.addEventListener('mouseenter', function() {
                        clearInterval(interval);
                    });
                    
                    sliderContainer.addEventListener('mouseleave', function() {
                        interval = setInterval(function() {
                            if (!isAnimating) {
                                goToNext();
                            }
                        }, {$autoplaySpeed});
                    });
                    
                    // Dokunmatik cihaz desteği ekle
                    let touchStartX = 0;
                    let touchEndX = 0;
                    
                    sliderContainer.addEventListener('touchstart', function(e) {
                        touchStartX = e.changedTouches[0].screenX;
                    }, { passive: true });
                    
                    sliderContainer.addEventListener('touchend', function(e) {
                        touchEndX = e.changedTouches[0].screenX;
                        handleSwipe();
                    }, { passive: true });
                    
                    function handleSwipe() {
                        const swipeThreshold = 50;
                        if (touchEndX < touchStartX - swipeThreshold) {
                            // Sola kaydırma - sonraki slayt
                            goToNext();
                        }
                        if (touchEndX > touchStartX + swipeThreshold) {
                            // Sağa kaydırma - önceki slayt
                            goToPrev();
                        }
                    }
                };
                
                // Slider'ı başlat
                setTimeout(function() {
                    initSlider_{$bannerId}();
                }, 100);
            });
        })();
        ";
    }    /**
     * Carousel için gerekli JS kodunu al
     */
    private function getCarouselJS($bannerId, $layoutView)
    {
        return "
        // Carousel JavaScript - Geliştirilmiş versiyon
        (function() {
            //console.log('[Carousel] Carousel JS yükleniyor - Banner ID: {$bannerId}');
            
            function initCarousel() {
                //console.log('[Carousel] initCarousel çalıştırılıyor...');
                
                try {
                    // Banner grubunu bul - daha esnek selector
                    let bannerGroup = document.querySelector('.banner-group-{$bannerId}');
                    if (!bannerGroup) {
                        // Alternatif selector'ları dene
                        bannerGroup = document.querySelector('[class*=\"banner-group-{$bannerId}\"]');
                        if (!bannerGroup) {
                            throw new Error('Banner Group bulunamadı (ID: {$bannerId})');
                        }
                    }
                    //console.log('[Carousel] Banner grubu bulundu:', bannerGroup.className);
                    
                    // Carousel container'ı bul
                    const carouselContainer = bannerGroup.querySelector('.carousel-container');
                    if (!carouselContainer) {
                        throw new Error('Carousel Container bulunamadı');
                    }
                    //console.log('[Carousel] Carousel container bulundu');
                    
                    // Banner öğelerini al
                    const items = carouselContainer.querySelectorAll('.banner-item');
                    if (items.length === 0) {
                        throw new Error('Banner items bulunamadı');
                    }
                    //console.log('[Carousel] ' + items.length + ' adet öğe bulundu');
                    
                    // Önceki ve sonraki butonları bul
                    const prevButton = bannerGroup.querySelector('.prev-carousel');
                    const nextButton = bannerGroup.querySelector('.next-carousel');
                    
                    if (!prevButton || !nextButton) {
                        throw new Error('Carousel butonları bulunamadı');
                    }
                    //console.log('[Carousel] Butonlar bulundu');
                    
                    // Butonların tıklanabilir olduğundan emin ol
                    const ensureButtonStyles = () => {
                        const buttonStyle = `
                            .banner-group-{$bannerId} .carousel-controls .prev-carousel,
                            .banner-group-{$bannerId} .carousel-controls .next-carousel {
                                position: relative !important;
                                z-index: 1000 !important;
                                cursor: pointer !important;
                                pointer-events: auto !important;
                                background-color: rgba(0, 0, 0, 0.7) !important;
                                border: 2px solid rgba(255, 255, 255, 0.3) !important;
                            }
                            .banner-group-{$bannerId} .carousel-controls .prev-carousel:hover,
                            .banner-group-{$bannerId} .carousel-controls .next-carousel:hover {
                                background-color: rgba(0, 0, 0, 0.9) !important;
                                border-color: rgba(255, 255, 255, 0.6) !important;
                            }
                        `;
                        
                        let styleElement = document.getElementById('carousel-style-{$bannerId}');
                        if (!styleElement) {
                            styleElement = document.createElement('style');
                            styleElement.id = 'carousel-style-{$bannerId}';
                            document.head.appendChild(styleElement);
                        }
                        styleElement.textContent = buttonStyle;
                    };
                    
                    ensureButtonStyles();
                    // Item genişliğini ve kaydırma mesafesini doğru hesapla
                    const calculateItemWidth = () => {
                        const firstItem = items[0];
                        const containerWidth = carouselContainer.clientWidth;
                        const containerComputedStyle = window.getComputedStyle(carouselContainer);
                        
                        // Gerçek item genişliğini ölç
                        const itemRect = firstItem.getBoundingClientRect();
                        const itemStyle = window.getComputedStyle(firstItem);
                        
                        // Item genişliği + margin hesapla
                        const itemWidth = itemRect.width;
                        const itemMarginLeft = parseFloat(itemStyle.marginLeft) || 0;
                        const itemMarginRight = parseFloat(itemStyle.marginRight) || 0;
                        const itemGap = parseFloat(containerComputedStyle.gap) || 0;
                        
                        // Toplam item boyutu (genişlik + margin + gap)
                        const totalItemWidth = itemWidth + itemMarginLeft + itemMarginRight + itemGap;
                        
                        /*console.log('[Carousel] Item ölçüleri:', {
                            itemWidth: itemWidth,
                            marginLeft: itemMarginLeft,
                            marginRight: itemMarginRight,
                            gap: itemGap,
                            totalItemWidth: totalItemWidth,
                            containerWidth: containerWidth
                        });*/
                        
                        // Görünür item sayısını belirle (CSS'deki class'lara göre)
                        let visibleItems = 1;
                        if (bannerGroup.classList.contains('triple')) visibleItems = 3;
                        else if (bannerGroup.classList.contains('double')) visibleItems = 2;
                        else if (bannerGroup.classList.contains('quad')) visibleItems = 4;
                        else if (bannerGroup.classList.contains('quinary')) visibleItems = 5;
                        
                        // Responsive kontrol - daha hassas
                        const currentWidth = window.innerWidth;
                        if (currentWidth <= 480) {
                            visibleItems = 1;
                        } else if (currentWidth <= 768) {
                            visibleItems = Math.min(visibleItems, 2);
                        } else if (currentWidth <= 992) {
                            visibleItems = Math.min(visibleItems, 3);
                        }
                        
                        // Kaydırma miktarını hesapla
                        // Tek item kaydırmak istiyorsak: totalItemWidth
                        // Görünür alanın tamamını kaydırmak istiyorsak: containerWidth
                        
                        // Varsayılan: tek item kaydır
                        let scrollAmount = totalItemWidth;
                        
                        // Eğer item çok büyükse veya tek item görünüyorsa, container genişliği kullan
                        if (visibleItems === 1 || totalItemWidth > containerWidth * 0.8) {
                            scrollAmount = containerWidth;
                        }
                        
                        // Minimum scroll miktarı (çok küçük kaydırmalar için)
                        scrollAmount = Math.max(scrollAmount, 50);
                        
                        /*console.log('[Carousel] Scroll miktarı hesaplandı:', {
                            scrollAmount: scrollAmount,
                            visibleItems: visibleItems,
                            calculationMethod: totalItemWidth > containerWidth * 0.8 ? 'container-width' : 'item-width'
                        });*/
                        
                        return Math.round(scrollAmount);
                    };
                    
                    // Scroll fonksiyonları
                    const scrollPrev = () => {
                        //console.log('[Carousel] Prev butona tıklandı!');
                        const scrollAmount = calculateItemWidth();
                        carouselContainer.scrollBy({
                            left: -scrollAmount,
                            behavior: 'smooth'
                        });
                    };
                    
                    const scrollNext = () => {
                        //console.log('[Carousel] Next butona tıklandı!');
                        const scrollAmount = calculateItemWidth();
                        carouselContainer.scrollBy({
                            left: scrollAmount,
                            behavior: 'smooth'
                        });
                    };
                    
                    // Event listener'ları temizle ve yeniden ekle
                    const resetButtonEvents = () => {
                        // Önceki event'leri temizle
                        const newPrevButton = prevButton.cloneNode(true);
                        const newNextButton = nextButton.cloneNode(true);
                        
                        prevButton.parentNode.replaceChild(newPrevButton, prevButton);
                        nextButton.parentNode.replaceChild(newNextButton, nextButton);
                        
                        // Yeni event'leri ekle
                        newPrevButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            scrollPrev();
                        });
                        
                        newNextButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            scrollNext();
                        });
                        
                        // Onclick da ekle
                        newPrevButton.onclick = function(e) {
                            if (e) e.preventDefault();
                            scrollPrev();
                            return false;
                        };
                        
                        newNextButton.onclick = function(e) {
                            if (e) e.preventDefault();
                            scrollNext();
                            return false;
                        };
                        
                        //console.log('[Carousel] Event listener\'lar yenilendi');
                    };
                    
                    resetButtonEvents();
                    
                    // Scroll pozisyonuna göre buton görünürlüğü
                    const updateButtonVisibility = () => {
                        const scrollPosition = carouselContainer.scrollLeft;
                        const maxScroll = carouselContainer.scrollWidth - carouselContainer.clientWidth;
                        
                        const newPrevButton = bannerGroup.querySelector('.prev-carousel');
                        const newNextButton = bannerGroup.querySelector('.next-carousel');
                        
                        if (newPrevButton && newNextButton) {
                            newPrevButton.style.opacity = scrollPosition <= 5 ? '0.5' : '1';
                            newNextButton.style.opacity = scrollPosition >= maxScroll - 5 ? '0.5' : '1';
                        }
                    };
                    
                    // Scroll event listener
                    carouselContainer.addEventListener('scroll', updateButtonVisibility);
                    
                    // İlk yükleme
                    setTimeout(updateButtonVisibility, 300);
                    
                    // Mobil dokunmatik destek
                    let touchStartX = 0;
                    let touchEndX = 0;
                    
                    carouselContainer.addEventListener('touchstart', function(e) {
                        touchStartX = e.changedTouches[0].screenX;
                    }, { passive: true });
                    
                    carouselContainer.addEventListener('touchend', function(e) {
                        touchEndX = e.changedTouches[0].screenX;
                        const swipeThreshold = 50;
                        
                        if (touchEndX < touchStartX - swipeThreshold) {
                            scrollNext();
                        } else if (touchEndX > touchStartX + swipeThreshold) {
                            scrollPrev();
                        }
                    }, { passive: true });
                    
                    //console.log('[Carousel] Carousel başarıyla başlatıldı');
                    
                } catch (error) {
                    //console.error('[Carousel] HATA:', error.message);
                    //console.error('[Carousel] Stack:', error.stack);
                }
            }
            
            // Sayfa yükleme durumuna göre başlat
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCarousel);
            } else {
                setTimeout(initCarousel, 100);
            }
            
            // Sayfa tamamen yüklendikten sonra tekrar kontrol et
            window.addEventListener('load', function() {
                setTimeout(initCarousel, 200);
            });
        })();";
    }

    public function getPopupJS()
    {
        // JS for popup close
        return '
        (function(){
            let popupArea = document.getElementById("PopupBanner");
            if(!popupArea)            {
                //console.log("Popup banner bulunamadı");
                return;
            }
            
            let btn = document.getElementById("popupBannerClose");
            
            if(!btn) {
                //console.log("Popup banner kapatma butonu bulunamadı");
                return; // Buton yoksa işlemi sonlandır
            }
            
            btn.addEventListener("click", function() {
                popupArea.style.display = "none";
                
               let bannerGroupID = btn.getAttribute("data-banner-group-id");
                //console.log("bannerGroupId: " + bannerGroupID);
                let cookieName = "popupBanner-" + bannerGroupID;
                
                //console.log("Popup çerezi kaydediliyor...");
                var uri = "/?/control/cookie/get/createCookie&name=" + cookieName + "&value=OK";
            
                fetch(uri, {
                    method: "GET",
                    credentials: "same-origin"
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    //console.log(data);
                    if (data.status === "success") {
                        //console.log("Popup Banner Çerez başarıyla kaydedildi.");
                    } 
                    else {
                        //console.error("Popup Banner Çerez kaydedilirken hata oluştu:", data.message);
                    }
                })
                .catch(error => {
                    //console.error("Popup Banner Çerez kaydedilirken hata oluştu:", error);
                });
            
            });
        })();
        ';
    }

    /**
     * Sayfa için tüm banner içeriklerini render et
     */
    public function renderBanners($pageId = null, $categoryId = null)
    {
        $banners = $this->getBannersByPage($pageId, $categoryId);

        if (empty($banners)) {
            return [
                'html' => '',
                'css' => '',
                'js' => '',
                'banners' => []
            ];
        }

        // Banner tiplerine göre CSS dosyalarını ekle
        $this->loadBannerTypeCSS($banners);

        // Banner stillerine göre dinamik CSS oluştur
        $this->generateBannerCSS($banners);

        // Banner HTML'lerini oluştur
        $html = '';
        foreach ($banners as $banner) {
            $html .= $this->renderBannerHTML($banner);
        }

        return [
            'html' => $html,
            'css' => $this->cssContent,
            'js' => $this->jsContent,
            'banners' => $banners
        ];
    }

    /**
     * CSS içeriğini al
     */
    public function getCssContent()
    {
        return $this->cssContent;
    }

    /**
     * JS içeriğini al
     */
    public function getJsContent()
    {
        return $this->jsContent;
    }

    /**
     * Layout group çevirici - Database'deki layout_group değerlerini BannerController'ın anladığı formata çevirir
     */
    private function convertLayoutGroup($layoutGroup) 
    {
        $layoutGroupMap = [
            'fullwidth' => 'text_and_image',
            'carousel' => 'text_and_image', 
            'top-banner' => 'text_and_image',
            'ImageRightBanner' => 'text_and_image',
            'ImageLeftBanner' => 'text_and_image',
            'HoverCardBanner' => 'text_and_image',
            'ProfileCard' => 'text_and_image',
            'IconFeatureCard' => 'text_and_image',
            'FadeFeatureCard' => 'text_and_image',
            'BgImageCenterText' => 'text_and_image',
            'ImageTextOverlayBottom' => 'text_and_image',
            'bottom-banner' => 'text_and_image',
            'popup-banner' => 'text_and_image',
            'header-banner' => 'text_and_image'
        ];
        
        return $layoutGroupMap[$layoutGroup] ?? 'text_and_image';
    }
}

