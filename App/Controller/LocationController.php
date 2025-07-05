<?php
/**
 * @var Session $session
 * @var Database $db
 * @var array $requestData
 */

$casper = $session->getCasper();

if (!$casper instanceof Casper) {
    echo "Casper is not here - LocationController:14";exit();
}

$visitor = $casper->getVisitor();
if(!isset($visitor['visitorUniqID'])){
    header('Location: /?visitorID-None');exit();
}
$siteConfig = $casper->getSiteConfig();
$pageLinks = $siteConfig['specificPageLinks'];

foreach ($pageLinks as $pageLink) {
    switch ($pageLink['sayfatip']) {
        case 1:
            $contactLink = $pageLink['link'];
            break;
        case 2:
            $newsLink = $pageLink['link'];
            break;
        case 3:
            $galleryLink = $pageLink['link'];
            break;
        case 4:
            $videoLink = $pageLink['link'];
            break;
        case 5:
            $fileLink = $pageLink['link'];
            break;
        case 6:
            $announcementLink = $pageLink['link'];
            break;
        case 7:
            $productLink = $pageLink['link'];
            break;
        case 8:
            $cartLink = $pageLink['link'];
            break;
        case 9:
            $checkoutLink = $pageLink['link'];
            break;
        case 10:
            $membershipAgreementLink = $pageLink['link'];
            break;
        case 11:
            $dealerLoginLink = $pageLink['link'];
            break;
        case 12:
            $distanceSalesLink = $pageLink['link'];
            break;
        case 13:
            $cookiePolicyLink = $pageLink['link'];
            break;
        case 14:
            $termsAndConditionsLink = $pageLink['link'];
            break;
        case 15:
            $privacyPolicyLink = $pageLink['link'];
            break;
        case 16:
            $brandsLink = $pageLink['link'];
            break;
        case 17:
            $memberLink = $pageLink['link'];
            break;
        case 18:
            $cancelReturnFormLink = $pageLink['link'];
            break;
        case 19:
            $favoriteLink = $pageLink['link'];
            break;
        case 20:
            $catalogsLink = $pageLink['link'];
            break;
        case 21:
            $aboutUsLink = $pageLink['link'];
            break;
        case 22:
            $paymentLink = $pageLink['link'];
            break;
        case 23:
            $generalLink = $pageLink['link'];
            break;
        case 24:
            $blogLink = $pageLink['link'];
            break;
        case 25:
            $kvkkLink = $pageLink['link'];
            break;
    }
}

//$requestData = $session->getSession('postData');

if (!empty($requestData)){
    if (!isset($requestData['action'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Lütfen gerekli alanları doldurunuz.',
            'locationData' => $requestData
        ]);
        exit();
    }
    $action = $requestData['action'] ?? null;

    $referrer = $requestData['referrer'];

    $languageCode = $requestData['languageCode'];
    $languageModel = new Language($db, $languageCode);
    $languageModel->getTranslations($languageCode);

    if ($action == "getLocation")
    {
        $locationName = $requestData['locationName'];
        $parentID = $requestData['parentID'];
        //değerlerden herhangi biri boşsa json yazdıralım
        if (empty($locationName) || empty($parentID)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Lütfen gerekli alanları doldurunuz.',
                'memberData' => []
            ]);
            exit();
        }
        // switch case ile locationName'e göre sorgu yapalım
        // şehir (addressCity), ilçe (addressDistrict), semt (addressArea), mahalle (addressNeighborhood)

        $location = new Location($db);
        $session->removeSession('postData');
        switch ($locationName) {
            case "addressCity":
                $result = $location->getCity($parentID);
                break;
            case "addressCounty":
                $result = $location->getCounty($parentID);
                break;
            case "addressArea":
                $result = $location->getArea($parentID);
                break;
            case "addressNeighborhood":
                $result = $location->getNeighborhood($parentID);
                break;
            case "addressPostalCode":
                $result = $location->getPostalCode($parentID);
                break;
            default:
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Böyle bir alan bulunamadı.',
                    'memberData' => []
                ]);
                exit();
        }
        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Veri başarıyla getirildi.',
                'LocationData' => $result
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Veri getirilemedi.',
                'locationData' => []
            ]);
            exit();
        }
    }
}