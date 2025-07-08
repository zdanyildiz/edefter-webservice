<?php
// App/Cron/ContentCopier.php

$documentRoot = str_replace("\\", "/", realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/CronGlobal.php';
Log::adminWrite("CronGlobal.php yüklendi, betik devam ediyor.", "info", "cron-copier");

/**
 * @var AdminDatabase $db
 * @var Helper $helper
 */

Log::adminWrite("Model sınıfları yükleniyor...", "info", "cron-copier");

// --- Model Sınıflarının Başlatılması ---
include_once MODEL . "Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

include_once MODEL . "Admin/AdminCategory.php";
$adminCategory = new AdminCategory($db);

include_once MODEL . "Admin/AdminPage.php";
$adminPage = new AdminPage($db);

include_once MODEL . "Admin/AdminSeo.php";
$adminSeo = new AdminSeo($db);

include_once MODEL . "Admin/AdminMenu.php";
$adminMenu = new AdminMenu($db);

// Banner Modelleri
include_once MODEL . "Admin/AdminBannerModel.php";
$adminBannerDisplayRulesModel = new AdminBannerDisplayRulesModel($db);
$adminBannerGroupModel = new AdminBannerGroupModel($db);
$adminBannerModel = new AdminBannerModel($db);
$adminBannerStyleModel = new AdminBannerStyleModel($db);

Log::adminWrite("Model sınıfları yüklendi. Loglama başlıyor.", "info", "cron-copier");

Log::adminWrite("ContentCopier cron job'u başladı.", "info", "cron-copier");

Log::adminWrite("Bekleyen kopyalama işi aranıyor...", "info", "cron-copier");

$pendingJob = $adminLanguage->getPendingCopyJob();

if (!$pendingJob) {
    Log::adminWrite("Bekleyen kopyalama iş emri bulunamadı. Çıkılıyor.", "info", "cron-copier");
    exit();
}

Log::adminWrite("İş emri bulundu: ID #{$pendingJob['id']}. Try bloğuna giriliyor.", "info", "cron-copier");


$jobId = $pendingJob['id'];

// Transaction başlat
$db->beginTransaction("ContentCopier-Job#{$jobId}");

try {
    $adminLanguage->updateCopyJobStatus($jobId, 'processing');
    Log::adminWrite("İş emri #{$jobId} işlenmeye başlandı.", "info", "cron-copier");

    $sourceLangId = $pendingJob['source_language_id'];
    $targetLangId = $pendingJob['target_language_id'];
    
    // Çeviri durumunu 'beklemede' olarak ayarlıyoruz. Çeviri işini ContentTranslator yapacak.
    $translationStatus = 'pending';

    // Kopyalama için kullanılacak modelleri bir dizide toplayalım
    $models = [
        'category' => $adminCategory,
        'page' => $adminPage,
        'seo' => $adminSeo,
        'language' => $adminLanguage,
        'helper' => $helper
    ];

    // 1. ADIM: KATEGORİLERİ VE SAYFALARI KOPYALA
    Log::adminWrite("Adım 1: Kategori ve Sayfa kopyalama başlıyor.", "info", "cron-copier");
    copyCategoryAndChildren(0, 0, $sourceLangId, $targetLangId, $translationStatus, $models);
    Log::adminWrite("Adım 1: Kategori ve Sayfa kopyalama tamamlandı.", "info", "cron-copier");

    // 2. ADIM: MENÜLERİ KOPYALA
    Log::adminWrite("Adım 2: Menü kopyalama başlıyor.", "info", "cron-copier");
    copyMenus($sourceLangId, $targetLangId, $adminMenu);
    Log::adminWrite("Adım 2: Menü kopyalama tamamlandı.", "info", "cron-copier");

    // 3. ADIM: BANNER'LARI KOPYALA
    Log::adminWrite("Adım 3: Banner kopyalama başlıyor.", "info", "cron-copier");
    copyBanners($sourceLangId, $targetLangId, $adminLanguage, $adminBannerDisplayRulesModel, $adminBannerGroupModel, $adminBannerModel, $adminBannerStyleModel);
    Log::adminWrite("Adım 3: Banner kopyalama tamamlandı.", "info", "cron-copier");


    // Gerçek modda bu satırlar aktif olacak:
    $db->commit("ContentCopier-Job#{$jobId}");
    $adminLanguage->updateCopyJobStatus($jobId, 'completed');
    Log::adminWrite("İş emri #{$jobId} başarıyla tamamlandı.", "info", "cron-copier");

} catch (Exception $e) {
    Log::adminWrite("Hata yakalandı: " . $e->getMessage(), "error", "cron-copier");
    // Hata durumunda rollback yap
    $db->rollback("ContentCopier-Job#{$jobId}");
    
    $errorMessage = $e->getMessage();
    $adminLanguage->updateCopyJobStatus($jobId, 'failed', $errorMessage);
    Log::adminWrite("İş emri #{$jobId} işlenirken hata oluştu: {$errorMessage}", "error", "cron-copier");
    Log::adminWrite("Transaction rollback yapıldı.", "warning", "cron-copier");
}

Log::adminWrite("ContentCopier cron job'u bitti.", "info", "cron-copier");

/**
 * Kategori ve altındaki tüm içeriği (alt kategoriler, sayfalar, SEO vb.) özyinelemeli olarak kopyalar.
 */
function copyCategoryAndChildren($parentId, $newParentId, $sourceLangId, $targetLangId, $translationStatus, $models) {
    $categories = $models['category']->getSubcategory($parentId, $sourceLangId);

    foreach ($categories as $category) {
        $originalCategoryId = $category['categoryID'];
        $newCategoryUniqID = $models['helper']->generateUniqID();
        $originalUniqID = $category['categoryUniqueID'] ?? null;

        Log::adminWrite("Kategori kopyalanıyor - ID: {$originalCategoryId} UniqID: {$originalUniqID}", "info", "cron-copier");

        $categoryData = $category;
        unset($categoryData['categoryID'], $categoryData['subcategories'], $categoryData['categoryLanguageID'], $categoryData['categoryCreationDate'], $categoryData['categoryUpdateDate'], $categoryData['categoryParentID'], $categoryData['categoryUniqID'], $categoryData['categoryDelete'], $categoryData['categoryGroup'], $categoryData['categoryUniqueID']);
        $categoryData['languageID'] = $targetLangId;
        $categoryData['createDate'] = date("Y-m-d H:i:s");
        $categoryData['updateDate'] = date("Y-m-d H:i:s");
        $categoryData['topCategoryID'] = $newParentId;
        $categoryData['categoryUniqID'] = $newCategoryUniqID;

        $addCategory = $models['category']->insertCategory($categoryData);
        if ($addCategory['status'] === 'error') {
            throw new Exception("Kategori kopyalanamadı: " . $addCategory['message']);
        }
        $newCategoryId = $addCategory['categoryID'];

        $models['language']->addLanguageCategoryMapping([
            'originalCategoryID' => $originalCategoryId,
            'translatedCategoryID' => $newCategoryId,
            'languageID' => $targetLangId,
            'translationStatus' => $translationStatus
        ]);

        if ($originalUniqID) {
            Log::adminWrite("Kategori SEO kopyalama - Orijinal UniqID: {$originalUniqID}", "info", "cron-copier");
            $originalSeo = $models['seo']->getSeoByUniqId($originalUniqID);
            Log::adminWrite("Kategori SEO kopyalama - getSeoByUniqId sonucu: " . json_encode($originalSeo), "info", "cron-copier");
            if ($originalSeo) {
                $newSeoData = $originalSeo;
                // unset ile gereksiz alanları temizle
                unset($newSeoData['seoID'], $newSeoData['seoCreationDate'], $newSeoData['seoUpdateDate']);
                $newSeoData['seoUniqID'] = $newCategoryUniqID;
                $newSeoData['seoOriginalLink'] = '';
                $newSeoData['seoImage'] = $newSeoData['seoImage'] ?? '';
                
                Log::adminWrite("Kategori SEO kopyalama - Yeni SEO verisi (insert öncesi): " . json_encode($newSeoData), "info", "cron-copier");
                $seoResult = $models['seo']->insertSeo($newSeoData);
                Log::adminWrite("Kategori SEO kopyalama - insertSeo sonucu: " . json_encode($seoResult), "info", "cron-copier");
                if ($seoResult['status'] === 'error') {
                    throw new Exception("Kategori SEO bilgisi kopyalanamadı: " . $seoResult['message']);
                } else {
                    Log::adminWrite("Kategori SEO bilgisi başarıyla kopyalandı. Yeni SEO ID: " . ($seoResult['seoID'] ?? 'N/A'), "info", "cron-copier");
                }
            } else {
                Log::adminWrite("Kategori SEO kopyalama - Orijinal UniqID '{$originalUniqID}' için SEO bilgisi bulunamadı.", "warning", "cron-copier");
            }
        } else {
            Log::adminWrite("Kategori SEO kopyalama - Kategori için orijinal benzersiz ID bulunamadı, SEO kopyalama atlandı.", "warning", "cron-copier");
        }

        $pages = $models['page']->getCategoryPages($originalCategoryId, $sourceLangId);
        foreach ($pages as $page) {
            $originalPageId = $page['pageID'];
            $originalPageUniqID = $page['pageUniqID'] ?? null;
            $newPageUniqID = $models['helper']->generateUniqID();

            $pageData = $page;
            unset($pageData['pageID'], $pageData['pageTypePermission']);
            $pageData['pageUniqID'] = $newPageUniqID;
            $pageData['pageCreateDate'] = date("Y-m-d H:i:s");
            $pageData['pageUpdateDate'] = date("Y-m-d H:i:s");

            $addPage = $models['page']->insertPage($pageData);
            if ($addPage['status'] === 'error') {
                throw new Exception("Sayfa kopyalanamadı: " . $addPage['message']);
            }
            $newPageId = $addPage['pageID'];
            Log::adminWrite("Sayfa kopyalandı. Orijinal Sayfa ID: {$originalPageId}, Yeni Sayfa ID: {$newPageId}, Yeni Kategori ID: {$newCategoryId}", "info", "cron-copier");

            // Yeni sayfayı yeni kategori ile ilişkilendir
            $addPageCategoryResult = $models['page']->insertPageCategory([
                'pageID' => $newPageId,
                'categoryID' => $newCategoryId
            ]);
            if ($addPageCategoryResult['status'] === 'error') {
                Log::adminWrite("Sayfa kategoriye eklenemedi: " . $addPageCategoryResult['message'], "error", "cron-copier");
                // Hata fırlatmak yerine loglayıp devam edebiliriz, kritik bir hata değilse
            } else {
                Log::adminWrite("Sayfa kategoriye başarıyla eklendi. Sayfa ID: {$newPageId}, Kategori ID: {$newCategoryId}", "info", "cron-copier");
            }

            $models['language']->addLanguagePageMapping([
                'originalPageID' => $originalPageId,
                'translatedPageID' => $newPageId,
                'languageID' => $targetLangId,
                'translationStatus' => $translationStatus
            ]);
            Log::adminWrite("Sayfa dil eşleşmesi eklendi. Orijinal Sayfa ID: {$originalPageId}, Yeni Sayfa ID: {$newPageId}, Hedef Dil ID: {$targetLangId}", "info", "cron-copier");

            // --- Medya İlişkilerini Kopyala ---
            // Resimleri kopyala
            $originalPageImages = $models['page']->getPageImages($originalPageId);
            if (!empty($originalPageImages)) {
                // getPageImages'dan dönen string'i parse etmeliyiz
                // Format: imageName:imageName|imageID:imageID|imageUrl:imageUrl||imageName:imageName...
                $imageDetailsArray = explode('||', $originalPageImages);
                $imageIDsToCopy = [];
                foreach ($imageDetailsArray as $imageDetailString) {
                    $parts = explode('|', $imageDetailString);
                    $imageID = null;
                    foreach ($parts as $part) {
                        if (strpos($part, 'imageID:') === 0) {
                            $imageID = (int)str_replace('imageID:', '', $part);
                            break;
                        }
                    }
                    if ($imageID !== null) {
                        $imageIDsToCopy[] = $imageID;
                    }
                }

                if (!empty($imageIDsToCopy)) {
                    $insertPageImageResult = $models['page']->insertPageImages([
                        'pageID' => $newPageId,
                        'imageIDs' => $imageIDsToCopy,
                    ]);
                    if ($insertPageImageResult['status'] === 'error') {
                        Log::adminWrite("Sayfa resimleri kopyalanamadı: " . $insertPageImageResult['message'], "error", "cron-copier");
                        // Hata fırlatmak yerine loglayıp devam edebiliriz, kritik bir hata değilse
                    } else {
                        Log::adminWrite("Sayfa resimleri başarıyla kopyalandı. Yeni Sayfa ID: {$newPageId}, Kopyalanan Resim ID'leri: " . implode(', ', $imageIDsToCopy), "info", "cron-copier");
                    }
                } else {
                    Log::adminWrite("Sayfa resimleri bulunamadı veya parse edilemedi. Orijinal Sayfa ID: {$originalPageId}", "info", "cron-copier");
                }
            }

            // Dosyaları kopyala
            $originalPageFiles = $models['page']->getPageFiles($originalPageId);
            if (!empty($originalPageFiles)) {
                // getPageFiles'dan dönen string'i parse etmeliyiz
                // Format: fileName:fileName, fileID:fileID, file:file, fileExtension:fileExtension; fileName:fileName...
                $fileDetailsArray = explode('; ', $originalPageFiles);
                $fileIDsToCopy = [];
                foreach ($fileDetailsArray as $fileDetailString) {
                    $parts = explode(', ', $fileDetailString);
                    $fileID = null;
                    foreach ($parts as $part) {
                        if (strpos($part, 'fileID:') === 0) {
                            $fileID = (int)str_replace('fileID:', '', $part);
                            break;
                        }
                    }
                    if ($fileID !== null) {
                        $fileIDsToCopy[] = $fileID;
                    }
                }

                if (!empty($fileIDsToCopy)) {
                    $insertPageFileResult = $models['page']->insertPageFiles([
                        'pageID' => $newPageId,
                        'fileIDs' => $fileIDsToCopy,
                    ]);
                    if ($insertPageFileResult['status'] === 'error') {
                        Log::adminWrite("Sayfa dosyaları kopyalanamadı: " . $insertPageFileResult['message'], "error", "cron-copier");
                    } else {
                        Log::adminWrite("Sayfa dosyaları başarıyla kopyalandı. Yeni Sayfa ID: {$newPageId}, Kopyalanan Dosya ID'leri: " . implode(', ', $fileIDsToCopy), "info", "cron-copier");
                    }
                } else {
                    Log::adminWrite("Sayfa dosyaları bulunamadı veya parse edilemedi. Orijinal Sayfa ID: {$originalPageId}", "info", "cron-copier");
                }
            }

            // Videoları kopyala
            $originalPageVideos = $models['page']->getPageVideos($originalPageId);
            if (!empty($originalPageVideos)) {
                foreach ($originalPageVideos as $video) {
                    $insertPageVideoResult = $models['page']->insertPageVideos([
                        'pageID' => $newPageId,
                        'videoID' => $video['videoID'],
                    ]);
                    if (!$insertPageVideoResult) {
                        Log::adminWrite("Sayfa videoları kopyalanamadı. Video ID: " . $video['videoID'], "error", "cron-copier");
                    } else {
                        Log::adminWrite("Sayfa videosu başarıyla kopyalandı. Yeni Sayfa ID: {$newPageId}, Video ID: {$video['videoID']}", "info", "cron-copier");
                    }
                }
            } else {
                Log::adminWrite("Sayfa videoları bulunamadı. Orijinal Sayfa ID: {$originalPageId}", "info", "cron-copier");
            }

            // Galeriyi kopyala
            $originalPageGallery = $models['page']->getPageGallery($originalPageId);
            if (!empty($originalPageGallery)) {
                foreach ($originalPageGallery as $gallery) {
                    $addPageGalleryResult = $models['page']->addPageGallery($newPageId, $gallery['galleryID']);
                    if (!$addPageGalleryResult) {
                        Log::adminWrite("Sayfa galerisi kopyalanamadı. Galeri ID: " . $gallery['galleryID'], "error", "cron-copier");
                    } else {
                        Log::adminWrite("Sayfa galerisi başarıyla kopyalandı. Yeni Sayfa ID: {$newPageId}, Galeri ID: {$gallery['galleryID']}", "info", "cron-copier");
                    }
                }
            } else {
                Log::adminWrite("Sayfa galerisi bulunamadı. Orijinal Sayfa ID: {$originalPageId}", "info", "cron-copier");
            }
            // --- Medya İlişkilerini Kopyala Bitti ---

            if ($originalPageUniqID) {
                $originalPageSeo = $models['seo']->getSeoByUniqId($originalPageUniqID);
                if ($originalPageSeo) {
                    $newPageSeoData = $originalPageSeo;
                    $newPageSeoData['seoUniqID'] = $newPageUniqID;
                    $newPageSeoData['seoOriginalLink'] = '';
                    $newPageSeoData['seoImage'] = $newPageSeoData['seoImage'] ?? '';
                    $pageSeoResult = $models['seo']->insertSeo($newPageSeoData);
                    if ($pageSeoResult['status'] === 'error') {
                        throw new Exception("Sayfa SEO bilgisi kopyalanamadı: " . $pageSeoResult['message']);
                    }
                }
            }
        }
        copyCategoryAndChildren($originalCategoryId, $newCategoryId, $sourceLangId, $targetLangId, $translationStatus, $models);
    }
}

/**
 * Menüleri kaynak dilden hedef dile kopyalar.
 */
function copyMenus($sourceLangId, $targetLangId, $adminMenu) {
    if ($adminMenu->checkMenuByLanguage($targetLangId)) {
        Log::adminWrite("Hedef dilde zaten menü mevcut, kopyalama atlanıyor.", "info", "cron-copier");
        return;
    }

    $originalLanguageMenus = $adminMenu->getMenuByLanguage($sourceLangId);
    if ($originalLanguageMenus) {
        foreach ($originalLanguageMenus as $menu) {
            $newMenuData = [
                "languageID" => $targetLangId,
                "menuLocation" => $menu["menukategori"],
                "menuParent" => $menu["ustmenuid"], // Mapping gerekebilir, şimdilik direkt kopyalanıyor.
                "menuLayer" => $menu["menukatman"],
                "menuName" => $menu["menuad"],
                "menuLink" => $menu["menulink"],
                "menuArea" => $menu["menusira"],
                "getSubCategory" => $menu["altkategori"],
                "contentUniqID" => $menu["menubenzersizid"],
                "contentOrjUniqID" => $menu["orjbenzersizid"],
                "menuType" => $menu["menuType"]
            ];

            if (!$adminMenu->saveMenu($newMenuData)) {
                throw new Exception("Menü kopyalanamadı. Veri: " . json_encode($newMenuData));
            }
        }
    }
}

/**
 * Banner'ları ve ilişkili tüm ayarları (kurallar, gruplar, stiller) kopyalar.
 */
function copyBanners($sourceLangId, $targetLangId, $adminLanguage, $adminBannerDisplayRulesModel, $adminBannerGroupModel, $adminBannerModel, $adminBannerStyleModel) {
    $targetLangCode = $adminLanguage->getLanguageCode($targetLangId);
    $sourceLangCode = $adminLanguage->getLanguageCode($sourceLangId);

    if ($adminBannerDisplayRulesModel->getDisplayRuleByLanguageCode($targetLangCode)) {
        Log::adminWrite("Hedef dilde zaten banner kuralları mevcut, kopyalama atlanıyor.", "info", "cron-copier");
        return;
    }

    $bannerDisplayRules = $adminBannerDisplayRulesModel->getDisplayRuleByLanguageCode($sourceLangCode);
    foreach ($bannerDisplayRules as $rule) {
        $originalGroupId = $rule['group_id'];

        // 1. Banner Grubunu Kopyala
        Log::adminWrite("Banner grubu kopyalanıyor. Orijinal Grup ID: {$originalGroupId}", "info", "cron-copier");
        $originalGroup = $adminBannerGroupModel->getGroupById($originalGroupId);
        if (!$originalGroup) {
            Log::adminWrite("Orijinal banner grubu bulunamadı, atlanıyor. Grup ID: {$originalGroupId}", "warning", "cron-copier");
            continue;
        }
        Log::adminWrite("Orijinal grup verisi: " . json_encode($originalGroup), "info", "cron-copier");

        $validLayoutId = $originalGroup[0]['layout_id'];
        // Geçici çözüm: Eğer layout_id 13 ise 1 olarak değiştir (çünkü 13 geçerli değil gibi görünüyor)
        if ($validLayoutId == 13) {
            $validLayoutId = 1; // Varsayılan geçerli bir layout ID
            Log::adminWrite("Geçersiz layout_id (13) tespit edildi, varsayılan 1 kullanılıyor.", "warning", "cron-copier");
        }

        $newGroupId = $adminBannerGroupModel->addGroup(
            $originalGroup[0]['group_name'] . '_lang_' . $targetLangId, $originalGroup[0]['group_title'], $originalGroup[0]['group_desc'], 
            $validLayoutId, $originalGroup[0]['group_kind'], $originalGroup[0]['group_view'], 
            $originalGroup[0]['columns'], $originalGroup[0]['content_alignment'], $originalGroup[0]['style_class'], 
            $originalGroup[0]['background_color'], $originalGroup[0]['group_title_color'], $originalGroup[0]['group_desc_color'], 
            $originalGroup[0]['group_full_size'], $originalGroup[0]['custom_css'], $originalGroup[0]['order_num'], 
            $originalGroup[0]['visibility_start'], $originalGroup[0]['visibility_end'], $originalGroup[0]['banner_duration'], 
            $originalGroup[0]['banner_full_size']
        );
        Log::adminWrite("Yeni banner grubu oluşturuluyor. Kullanılan Layout ID: {$validLayoutId}", "info", "cron-copier");
        if (!$newGroupId) {
            throw new Exception("Banner grubu kopyalanamadı. Grup ID: {$originalGroupId}. addGroup metodu false döndü.");
        }
        Log::adminWrite("Yeni banner grubu oluşturuldu. Yeni Grup ID: {$newGroupId}", "info", "cron-copier");

        // 2. Görüntüleme Kuralını Yeni Grup ID'si ile Kopyala
        $categoryId = $rule['category_id'] ? ($adminLanguage->getTargetCategoryID($targetLangId, $rule['category_id'])[0]['translated_category_id'] ?? null) : null;
        $pageId = $rule['page_id'] ? ($adminLanguage->getTargetPageID($targetLangId, $rule['page_id'])[0]['translated_page_id'] ?? null) : null;

        if (!$adminBannerDisplayRulesModel->addDisplayRule($newGroupId, $rule['type_id'], $pageId, $categoryId, $targetLangCode)) {
            throw new Exception("Banner görüntüleme kuralı kopyalanamadı.");
        }

        // 3. Gruba Ait Banner'ları ve Stillerini Kopyala
        $originalBanners = $adminBannerModel->getBannersByGroupID($originalGroupId);
        foreach ($originalBanners as $banner) {
            $originalStyle = $adminBannerStyleModel->getStyleById($banner['style_id']);
            if (!$originalStyle) continue;

            $newStyleId = $adminBannerStyleModel->addStyle(
                $originalStyle['banner_height_size'] ?? 0, $originalStyle['background_color'], $originalStyle['content_box_bg_color'], 
                $originalStyle['title_color'], $originalStyle['title_size'], $originalStyle['content_color'], 
                $originalStyle['content_size'], 
                $originalStyle['show_button'] ?? 0, // Eksik olan parametre eklendi
                $originalStyle['button_title'], 
                $originalStyle['button_location'], $originalStyle['button_background'], $originalStyle['button_color'], 
                $originalStyle['button_hover_background'], $originalStyle['button_hover_color'], $originalStyle['button_size']
            );
            if (!$newStyleId) {
                throw new Exception("Banner stili kopyalanamadı.");
            }

            if (!$adminBannerModel->addBanner($newGroupId, $newStyleId, $banner['title'], $banner['content'], $banner['image'], $banner['link'], $banner['active'])) {
                throw new Exception("Banner'ın kendisi kopyalanamadı.");
            }
        }
    }
}
?>