<?php
// C:\Users\zdany\PhpstormProjects\erhanozel.globalpozitif.com.tr\App\Cron\ContentTranslator.php
// Bu script, bir cron job (zamanlanmış görev) olarak çalıştırılmak üzere tasarlanmıştır.
// Veritabanından beklemedeki içerik çevirilerini alır ve bunları çevirmek için bir yapay zeka hizmeti kullanır.

$documentRoot = str_replace("\\","/",realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/CronGlobal.php';

/**
 * @var AdminDatabase $db
 * @var Helper $helper
 */

// --- Model Sınıflarının Başlatılması ---
include_once MODEL."Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

include_once MODEL."Admin/AdminCategory.php";
$adminCategory = new AdminCategory($db);

include_once MODEL."Admin/AdminPage.php";
$adminPage = new AdminPage($db);

include_once MODEL."Admin/AdminSeo.php";
$adminSeo = new AdminSeo($db);

include_once MODEL.'Admin/AdminChatCompletion.php';
// Sistem görevleri için varsayılan olarak ID'si 1 olan admin kullanılıyor
$adminChatCompletion = new AdminChatCompletion($db, 1);

Log::adminWrite("ContentTranslator cron job'u başladı.", "info", "cron");

// --- Bekleyen Dil Sabiti Çevirilerini İşle ---
$mainLanguageId = $adminLanguage->getMainLanguageId();
$targetLanguages = $adminLanguage->getLanguages();
$processedConstantsCount = 0; // Initialize counter

foreach ($targetLanguages as $targetLang) {
    if ($targetLang['isMainLanguage'] == 1) {
        continue; // Ana dili atla
    }

    $targetLanguageID = $targetLang['languageID'];
    $targetLanguageCode = $targetLang['languageCode'];
    $targetLanguageName = $targetLang['languageName'];

    Log::adminWrite("Dil sabiti çeviri işlemi başlatıldı. Hedef Dil ID: {$targetLanguageID}, Kod: {$targetLanguageCode}", "info", "cron");

    $db->beginTransaction("ConstantTranslation-{$targetLanguageID}");
    try {
        $mainLanguageConstants = $adminLanguage->getLanguageConstants(); // Ana dildeki tüm sabitleri al

        foreach ($mainLanguageConstants as $constant) {
            $constantID = $constant['constantID'];
            $constantName = $constant['constantName'];
            $constantValue = $constant['constantValue'];
            $constantGroup = $constant['constantGroup'];

            // Hedef dilde bu sabit için çeviri var mı kontrol et
            $existingTranslation = $adminLanguage->getLanguageConstantTranslations($targetLanguageCode, $constantGroup);
            $translationExists = false;
            $existingTranslationID = null;
            foreach ($existingTranslation as $ext) {
                if ($ext['constantID'] == $constantID) {
                    $translationExists = true;
                    $existingTranslationID = $ext['translationID'];
                    break;
                }
            }

            if (!$translationExists || empty($existingTranslationID)) {
                // Çeviri yoksa veya boşsa, yeni çeviri oluştur
                $translatedValue = getTranslatedValue($adminChatCompletion, $constantValue, $targetLanguageName);

                if ($translatedValue !== null) {
                    $translationData = [
                        'languageCode' => $targetLanguageCode,
                        'constantID' => $constantID,
                        'translationValue' => $translatedValue
                    ];
                    $addResult = $adminLanguage->addLanguageConstantTranslation($translationData);
                    if ($addResult['status'] === 'error') {
                        throw new Exception("Dil sabiti çevirisi eklenemedi: {$constantName} (Hata: {$addResult['message']})");
                    }
                    Log::adminWrite("Dil sabiti başarıyla çevrildi ve eklendi: {$constantName} (Dil: {$targetLanguageCode})", "info", "cron");
                } else {
                    Log::adminWrite("Dil sabiti çevrilemedi (muhtemelen boş veya API hatası): {$constantName} (Dil: {$targetLanguageCode})", "warning", "cron");
                }
            } else {
                //Log::adminWrite("Dil sabiti zaten çevrilmiş: {$constantName} (Dil: {$targetLanguageCode})", "info", "cron");
                continue; // Zaten çevrilmişse bir sonraki sabite geç
            }

            $processedConstantsCount++; // Increment counter after processing each constant
            if ($processedConstantsCount >= 20) {
                $db->commit("ConstantTranslation-{$targetLanguageID}"); // Commit current language's constants
                Log::adminWrite("20 dil sabiti çevirisi tamamlandı, çıkılıyor.", "info", "cron");
                exit(); // Exit the script
            }
        }
        $db->commit("ConstantTranslation-{$targetLanguageID}");
        Log::adminWrite("Dil sabiti çeviri işlemi başarıyla tamamlandı. Hedef Dil ID: {$targetLanguageID}", "info", "cron");

    } catch (Exception $e) {
        $db->rollback("ConstantTranslation-{$targetLanguageID}");
        Log::adminWrite("Dil sabiti çeviri işlemi hata ile sonuçlandı. Hedef Dil ID: {$targetLanguageID}. Hata: {$e->getMessage()}", "error", "cron");
    }
}


// --- Bekleyen Kategori Çevirilerini İşle ---

// --- Çeviri için Yardımcı Fonksiyon ---
function getTranslatedValue(AdminChatCompletion $ai, ?string $text, string $languageName, bool $isHtml = false): ?string {
    try {
        // Null veya boş string kontrolü
        if ($text === null || empty(trim($text))) {
            Log::adminWrite("getTranslatedValue: Boş veya null metin geldi, orijinali döndürülüyor. Metin: " . ($text ?? "NULL"), "info", "cron");
            return $text; // Null veya boş metni çevirmeye çalışma, orijinalini döndür
        }

        // TEST AMAÇLI SAHTE ÇEVİRİ
        //$translated = "TEST_TRANSLATED_" . $text . "_TO_" . $languageName;
        //Log::adminWrite("getTranslatedValue: Sahte çeviri yapıldı. Orijinal: " . substr($text, 0, 50) . ", Çevrilen: " . substr($translated, 0, 50), "info", "cron");

        // Orijinal API çağrıları (test için yorum satırı yapıldı)

        if ($isHtml) {
            $translated = $ai->translateHtmlContent($text, $languageName);
        } else {
            $translated = $ai->translateConstant($text, $languageName);
        }

        // API'den false veya boş bir yanıt gelirse null döndür
        if ($translated === false || trim($translated) === '') {
            throw new Exception("AI service returned an empty or invalid response.");
        }


        return $translated;

    } catch (Exception $e) {
        Log::adminWrite("AI Çeviri İstisnası (Sahte Fonksiyon): " . $e->getMessage() . " | Metin: " . substr($text, 0, 100), "error", "cron");
        return null; // Hata durumunda null döndür
    }
}


// --- Bekleyen Kategori Çevirilerini İşle ---
$pendingCategories = $adminLanguage->getPendingCategoryTranslations(5);
$processedCategoriesCount = 0; // Kategori sayacı

foreach ($pendingCategories as $mapping) {
    $mappingId = $mapping['id'];
    $originalId = $mapping['original_category_id'];
    $translatedId = $mapping['translated_category_id'];
    $languageId = $mapping['dilid'];
    Log::adminWrite("Kategori çeviri işlemi başlatıldı. Eşleme ID: {$mappingId}, Orijinal Kategori ID: {$originalId}, Çevrilmiş Kategori ID: {$translatedId}, Dil ID: {$languageId}", "info", "cron");

    $db->beginTransaction("CategoryTranslation-{$mappingId}");
    try {
        $languageCode = $adminLanguage->getLanguageCode($languageId);
        $languageName = $adminLanguage->getLanguageNameByLanguageCode($languageCode);

        if (empty($languageName)) {
            throw new Exception("Dil adı bulunamadı. Dil ID: {$languageId}");
        }
        Log::adminWrite("Kategori çeviri: Dil kodu ve adı alındı. Kod: {$languageCode}, Ad: {$languageName}", "info", "cron");

        $originalCategory = $adminCategory->getCategory($originalId);
        $translatedCategory = $adminCategory->getCategory($translatedId);

        if (!$originalCategory || !$translatedCategory) {
            throw new Exception("Orijinal veya çevrilmiş kategori bulunamadı. Eşleme ID: {$mappingId}");
        }
        Log::adminWrite("Kategori çeviri: Orijinal ('{$originalCategory['categoryName']}') ve çevrilmiş kategori ('{$translatedCategory['categoryName']}') bulundu.", "info", "cron");

        // Kategori Adını Çevir ve Link Oluştur
        $translatedName = getTranslatedValue($adminChatCompletion, $originalCategory['categoryName'], $languageName);
        $fullSeoLink = '';

        if ($translatedName) {
            // Gelişmiş SEO link oluşturma - Non-Latin dilleri destekler
            $newCategorySlug = '/' . $helper->createAdvancedSeoLink($translatedName, $languageCode, $adminChatCompletion, $translatedId);
            $adminCategory->updateCategoryField($translatedId, 'kategoriad', $translatedName);
            $adminCategory->updateCategoryField($translatedId, 'kategorilink', $newCategorySlug);

            $categoryPath = '';
            $hierarchy = $adminCategory->getCategoryHierarchy($translatedId);
            if ($hierarchy) {
                foreach ($hierarchy as $cat) {
                    if ($cat['categoryID'] == $translatedId) continue;
                    $categoryPath .= $cat['categoryLink'];
                }
            }
            $fullSeoLink = '/' . $languageCode . $categoryPath . $newCategorySlug;
            
            Log::adminWrite("Kategori SEO linki oluşturuldu: {$newCategorySlug} (Dil: {$languageCode})", "info", "cron");
        } else {
            throw new Exception("Kategori adı çevrilemedi.");
        }

        // SEO Bilgilerini Çevir ve Güncelle
        $originalSeo = $adminSeo->getSeoByUniqId($originalCategory['categoryUniqID']);
        if ($originalSeo) {
            $translatedSeoTitle = getTranslatedValue($adminChatCompletion, $originalSeo['seoTitle'], $languageName);
            $translatedSeoDesc = getTranslatedValue($adminChatCompletion, $originalSeo['seoDescription'], $languageName);
            $translatedSeoKeywords = getTranslatedValue($adminChatCompletion, $originalSeo['seoKeywords'], $languageName);

            $seoUpdateData = [
                'seoUniqID' => $translatedCategory['categoryUniqID'],
                'seoTitle' => $translatedSeoTitle ?? $originalSeo['seoTitle'],
                'seoDescription' => $translatedSeoDesc ?? $originalSeo['seoDescription'],
                'seoKeywords' => $translatedSeoKeywords ?? $originalSeo['seoKeywords'],
                'seoLink' => $fullSeoLink,
                'seoImage' => $originalSeo['seoImage']
            ];
            $adminSeo->updateSeo($seoUpdateData);
        }

        $adminLanguage->updateCategoryTranslationStatus($mappingId, 'completed');
        Log::adminWrite("Kategori eşleme ID'si başarıyla çevrildi: {$mappingId}", "info", "cron");
        $db->commit("CategoryTranslation-{$mappingId}");
        $processedCategoriesCount++; // Sayacı artır
        if ($processedCategoriesCount >= 5) {
            Log::adminWrite("5 kategori çevirisi tamamlandı, çıkılıyor.", "info", "cron");
            exit(); // 5 kategori işlendiyse çıkış yap
        }

    } catch (Exception $e) {
        $db->rollback("CategoryTranslation-{$mappingId}");
        $errorMessage = $e->getMessage();
        $adminLanguage->updateCategoryTranslationStatus($mappingId, 'failed', $errorMessage);
        Log::adminWrite("Kategori eşleme ID'si çevrilemedi: {$mappingId}. Hata: {$errorMessage}", "error", "cron");
    }
}


// --- Bekleyen Sayfa Çevirilerini İşle ---
$pendingPages = $adminLanguage->getPendingPageTranslations(5);
$processedPagesCount = 0; // Sayfa sayacı

foreach ($pendingPages as $mapping) {
    $mappingId = $mapping['id'];
    $originalId = $mapping['original_page_id'];
    $translatedId = $mapping['translated_page_id'];
    $languageId = $mapping['dilid'];
    Log::adminWrite("Sayfa çeviri işlemi başlatıldı. Eşleme ID: {$mappingId}, Orijinal Sayfa ID: {$originalId}, Çevrilmiş Sayfa ID: {$translatedId}, Dil ID: {$languageId}", "info", "cron");

    $db->beginTransaction("PageTranslation-{$mappingId}");
    try {
        $languageCode = $adminLanguage->getLanguageCode($languageId);
        $languageName = $adminLanguage->getLanguageNameByLanguageCode($languageCode);

        if (empty($languageName)) {
            throw new Exception("Dil adı bulunamadı. Dil ID: {$languageId}");
        }
        Log::adminWrite("Sayfa çeviri: Dil kodu ve adı alındı. Kod: {$languageCode}, Ad: {$languageName}", "info", "cron");

        if(empty($originalId) || empty($translatedId)) {
            throw new Exception("Orijinal veya çevrilmiş sayfa ID'si boş. Eşleme ID: {$mappingId}");
        }

        $originalPage = $adminPage->getPageById($originalId);
        $translatedPage = $adminPage->getPageById($translatedId);

        if (!$originalPage || !$translatedPage) {
            throw new Exception("Orijinal veya çevrilmiş sayfa bulunamadı. Eşleme ID: {$mappingId}");
        }
        Log::adminWrite("Sayfa çeviri: Orijinal ('{$originalPage['pageName']}') ve çevrilmiş sayfa ('{$translatedPage['pageName']}') bulundu.", "info", "cron");

        // Sayfa Adını Çevir ve Link Oluştur
        $translatedName = getTranslatedValue($adminChatCompletion, $originalPage['pageName'], $languageName);
        $fullSeoLink = '';

        if ($translatedName) {
            // Gelişmiş SEO link oluşturma - Non-Latin dilleri destekler
            $newPageSlug = '/' . $helper->createAdvancedSeoLink($translatedName, $languageCode, $adminChatCompletion, $translatedId);
            $adminPage->updatePageField($translatedId, 'sayfaad', $translatedName);
            $adminPage->updatePageField($translatedId, 'sayfalink', $newPageSlug);

            $categoryPath = '';
            $pageCategoryIds = $adminPage->getPageCategoryID($translatedId);
            if (!empty($pageCategoryIds)) {
                $pageCategoryID = $pageCategoryIds[0]['categoryID'];
                $hierarchy = $adminCategory->getCategoryHierarchy($pageCategoryID);
                if ($hierarchy) {
                    foreach ($hierarchy as $cat) {
                        $categoryPath .= $cat['categoryLink'];
                    }
                }
            }
            $fullSeoLink = '/' . $languageCode . $categoryPath . $newPageSlug;
            
            Log::adminWrite("Sayfa SEO linki oluşturuldu: {$newPageSlug} (Dil: {$languageCode})", "info", "cron");
        } else {
            throw new Exception("Sayfa adı çevrilemedi.");
        }

        // Sayfa İçeriğini Çevir
        $translatedContent = getTranslatedValue($adminChatCompletion, $originalPage['pageContent'], $languageName, true);
        if ($translatedContent !== null) {
            $adminPage->updatePageField($translatedId, 'sayfaicerik', $translatedContent);
            Log::adminWrite("Sayfa içeriği başarıyla çevrildi ve güncellendi. Eşleme ID: {$mappingId}", "info", "cron");
        } else {
            Log::adminWrite("Sayfa içeriği çevrilemedi (muhtemelen boş veya API hatası). Eşleme ID: {$mappingId}", "warning", "cron");
        }

        // SEO Bilgilerini Çevir ve Güncelle
        $originalSeo = $adminSeo->getSeoByUniqId($originalPage['pageUniqID']);
        if ($originalSeo) {
            $translatedSeoTitle = getTranslatedValue($adminChatCompletion, $originalSeo['seoTitle'], $languageName);
            $translatedSeoDesc = getTranslatedValue($adminChatCompletion, $originalSeo['seoDescription'], $languageName);
            $translatedSeoKeywords = getTranslatedValue($adminChatCompletion, $originalSeo['seoKeywords'], $languageName);

            $seoUpdateData = [
                'seoUniqID' => $translatedPage['pageUniqID'],
                'seoTitle' => $translatedSeoTitle ?? $originalSeo['seoTitle'],
                'seoDescription' => $translatedSeoDesc ?? $originalSeo['seoDescription'],
                'seoKeywords' => $translatedSeoKeywords ?? $originalSeo['seoKeywords'],
                'seoLink' => $fullSeoLink,
                'seoImage' => $originalSeo['seoImage']
            ];
            $adminSeo->updateSeo($seoUpdateData);
        }

        $adminLanguage->updatePageTranslationStatus($mappingId, 'completed');
        Log::adminWrite("Sayfa eşleme ID'si başarıyla çevrildi: {$mappingId}", "info", "cron");
        $db->commit("PageTranslation-{$mappingId}");
        $processedPagesCount++; // Sayacı artır
        if ($processedPagesCount >= 5) {
            Log::adminWrite("5 sayfa çevirisi tamamlandı, çıkılıyor.", "info", "cron");
            exit(); // 5 sayfa işlendiyse çıkış yap
        }

    } catch (Exception $e) {
        $db->rollback("PageTranslation-{$mappingId}");
        $errorMessage = $e->getMessage();
        $adminLanguage->updatePageTranslationStatus($mappingId, 'failed', $errorMessage);
        Log::adminWrite("Sayfa eşleme ID'si çevrilemedi: {$mappingId}. Hata: {$errorMessage}", "error", "cron");
    }
}

Log::adminWrite("ContentTranslator cron job'u bitti.", "info", "cron");
?>