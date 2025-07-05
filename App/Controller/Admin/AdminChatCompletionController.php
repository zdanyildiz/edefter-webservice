<?php
$documentRoot = str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\", "/", DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
include_once MODEL . 'Admin/AdminChatCompletion.php';
include_once MODEL . 'Admin/AdminLanguage.php';

/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 */

$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

$admin = $adminCasper->getAdmin();
$adminID = $admin["yoneticiid"];

// AdminChatCompletion sınıfını başlatıyoruz
$chatCompletion = new AdminChatCompletion($db, $adminID);
$languageModel = new AdminLanguage($db);

$response = [
    'status' => 'success',
    'data' => null,
    'message' => ''
];

try {
    switch ($action) {
        case 'translateConstant':
            $text = $requestData['text'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($text) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Text veya dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // Çeviri işlemini yap
            $translatedText = $chatCompletion->translateConstant($text, $languageName);
            if(!$translatedText){
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Çeviri başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Çeviri başarılı',
                'data' => $translatedText
            ]);
            break;
        //ürün kategorisi içerik üret
        case 'productCategoryContentGenerator':
            $contentDescription = $requestData['contentDescription'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($contentDescription) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kategori örnek cümle ve dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // Kategori içeriği üretme işlemini yap
            $contentResponse = $chatCompletion->productCategoryContentGenerator($contentDescription, $languageName);
            if (!$contentResponse) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kategori içeriği üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Kategori içeriği üretimi başarılı',
                'data' => $contentResponse
            ]);
            break;
        //ürün kategorisi seo içerik üret
        case 'productCategorySeoGenerator':
            $title = $requestData['title'] ?? '';
            $description = $requestData['description'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($title) || empty($description) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kategori adı, başlık, açıklama veya dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // Kategori SEO içeriği üretme işlemini yap
            $seoResponse = $chatCompletion->productCategorySeoGenerator($title, $description, $languageName);
            if (!$seoResponse) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kategori SEO içerik üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Kategori SEO içerik üretimi başarılı',
                'data' => $seoResponse
            ]);
            break;
        //ürün içerik üret
        case 'productContentGenerator':
            $contentDescription = $requestData['contentDescription'] ?? null;
            $language = $requestData['language'] ?? null;

            if (empty($contentDescription) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'İçerik açıklaması ve dil değeri boş olamaz.'
                ]);
                exit();
            }
            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // İçerik üretme işlemini yap
            $contentGeneratorResponse = $chatCompletion->productContentGenerator($contentDescription, $languageName);
            if (!$contentGeneratorResponse) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Ürün içeriği üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Ürün içeriği üretimi başarılı',
                'data' => $contentGeneratorResponse
            ]);
            break;
        //ürün seo içerik üret
        case 'productSeoGenerator':
            $title = $requestData['title'] ?? '';
            $description = $requestData['description'] ?? '';
            $category = $requestData['category'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($title) || empty($description) || empty($category) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kategori, başlık veya dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            //Log::adminWrite("ChatCompletionController: productSeoGenerator - title: $title, description: $description, category: $category, language: $language", "info");
            // Ürün SEO içeriğini oluştur
            $seoContent = $chatCompletion->productSeoGenerator($category, $title, $description, $languageName);
            if (!$seoContent) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'SEO içerik üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'SEO içerik üretimi başarılı',
                'data' => $seoContent
            ]);
            break;
        //ürün olmayan kategori içerik üret
        case 'generalCategoryContentGenerator':
            $categoryDescription = $requestData['categoryDescription'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($categoryDescription) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Örnek açıklama ve dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // İçerik üretme işlemini yap
            $contentResponse = $chatCompletion->generalCategoryContentGenerator($categoryDescription, $languageName);
            if (!$contentResponse) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Ürün olmayan kategori içeriği üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Ürün olmayan kategori içeriği üretimi başarılı',
                'data' => $contentResponse
            ]);
            break;
        // Ürün olmayan kategori SEO içerik üretme
        case 'generalCategorySeoGenerator':
            $title = $requestData['title'] ?? '';
            $description = $requestData['description'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($title) || empty($description) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Başlık, açıklama veya dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // SEO içerik üretme işlemini yap
            $seoResponse = $chatCompletion->generalCategorySeoGenerator($title, $description, $languageName);
            if (!$seoResponse) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Ürün olmayan kategori SEO içerik üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Ürün olmayan kategori SEO içerik üretimi başarılı',
                'data' => $seoResponse
            ]);
            break;
        //Sayfa İçerik üret
        case 'generalPageContentGenerator':
            $contentDescription = $requestData['contentDescription'] ?? null;
            $language = $requestData['language'] ?? null;

            if (empty($contentDescription) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'İçerik açıklaması ve dil değeri boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // İçerik üretme işlemini yap
            $contentResponse = $chatCompletion->generalPageContentGenerator($contentDescription, $languageName);
            if (!$contentResponse) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Bilgilendirme sayfası içeriği üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Bilgilendirme sayfası içeriği üretimi başarılı',
                'data' => $contentResponse
            ]);
            break;
        //Sayfa SEO üret
        case 'generalPageSeoGenerator':
            $title = $requestData['title'] ?? '';
            $description = $requestData['description'] ?? '';
            $category = $requestData['category'] ?? '';
            $language = $requestData['language'] ?? '';

            if (empty($title) || empty($description) || empty($category) || empty($language)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Kategori, başlık veya dil parametresi boş olamaz.'
                ]);
                exit();
            }

            $languageName = $languageModel->getLanguageNameByLanguageCode($language);

            // SEO içerik üretimi işlemini yap
            $seoContent = $chatCompletion->generalPageSeoGenerator($category, $title, $description, $languageName);
            if (!$seoContent) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Bilgilendirme sayfası SEO içerik üretimi başarısız'
                ]);
                exit();
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Bilgilendirme sayfası SEO içerik üretimi başarılı',
                'data' => $seoContent
            ]);
            break;

        default:
            Log::adminWrite("ChatCompletionController: handleAction - Invalid action parameter: $action", "error");

            json_encode([
                'status' => 'error',
                'message' => 'Invalid action parameter.'
            ]);
            throw new Exception("Invalid action parameter.");
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

