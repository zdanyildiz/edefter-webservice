<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';

// Banner Manager sınıfını include et  
include_once $documentRoot . $directorySeparator . 'App/Core/BannerManager.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */


$action = $requestData["action"] ?? null;
if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminSiteSettings.php';

include_once MODEL . 'Admin/AdminBannerModel.php';
$bannerTypeModel = new AdminBannerTypeModel($db);
$bannerLayoutModel = new AdminBannerLayoutModel($db);
$bannerGroupModel = new AdminBannerGroupModel($db);
$bannerStyleModel = new AdminBannerStyleModel($db);
$adminBannerDisplayRulesModel = new AdminBannerDisplayRulesModel($db);
$adminBannerModel = new AdminBannerModel($db);

//action getBannerTypes koşılunu yazalım
if($action == "getBannerTypes") {
    $bannerTypes = $bannerTypeModel->getAllTypes();
    echo json_encode($bannerTypes);
    exit();
}
elseif($action == "getBannerLayouts") {
    $typeID = $requestData["bannerTypeID"] ?? null;
    $bannerLayouts = $bannerLayoutModel->getLayoutsByTypeId($typeID);
    echo json_encode([
        'status' => 'success',
        'bannerLayouts' => $bannerLayouts
    ]);
    exit();
}
elseif ($action == "addBanner" || $action == "updateBanner"){
    Log::adminWrite("Banner Ekleme Data: ".json_encode($requestData));
    //exit;
    //bannerGroupName, bannerTypeID, bannerLayoutID, bannerLayoutColumns,languageID, bannerLocation, bannerStartDate, bannerEndDate boş olamaz
    $bannerGroupID = $requestData["bannerGroupID"] ?? 0;
    $bannerGroupName = $requestData["bannerGroupName"] ?? null;
    $bannerTypeID = $requestData["bannerTypeID"] ?? 0;
    $bannerLayoutID = $requestData["bannerLayoutID"] ?? 0;
    $bannerGroupKind = $requestData["bannerKind"];
    $bannerGroupView = $requestData["bannerView"];
    $bannerLayoutColumns = $requestData["bannerLayoutColumns"] ?? 0;
    $bannerGroupTitle = $requestData["bannerGroupTitle"];
    $bannerGroupDesc = $requestData["bannerGroupDesc"];
    $bannerGroupBgColor = $requestData["banner-group-bg-color"];
    $bannerGroupTitleColor = $requestData["banner-group-title-color"];
    $bannerGroupDescColor = $requestData["banner-group-desc-color"];
    $bannerGroupFullSize = $requestData["banner-group-full-size"];
    $bannerFullSize = $requestData["banner-full-size"];

    $languageID = $requestData["languageID"] ?? 0;
    $bannerLocation = $requestData["bannerLocation"] ?? null;
    $bannerStartDate = $requestData["bannerStartDate"] ?? null;
    $bannerEndDate = $requestData["bannerEndDate"] ?? null;
    $bannerDuration = $requestData["bannerDuration"] ?? 0;
    $custom_css = $requestData["bannerCss"] ?? "";
    $style_class = $requestData["bannerStyleClass"] ?? "";

    //mesajı türkçe verelim
    if (!isset($bannerGroupName) || $bannerTypeID==0 || $bannerLayoutID==0 || $bannerLayoutColumns==0 || $languageID==0 || is_null($bannerLocation) || is_null($bannerStartDate) || is_null($bannerEndDate)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Banner Grup Adı, Banner Tipi, Banner Düzeni, Banner Sütun Sayısı, Dil, Bammer Gösterim Yeri, Başlangıç ve Bitiş Tarihleri boş olamaz'
        ]);
        exit();
    }

    //ilk olarak banner grubunu ekleyelim
    //Log::adminWrite("Banner Grubu Ekleme İşlemi, Banner Grubu Ekleme İşlemi Başlatıldı");
    $bannerGroupModel->beginTransaction("bannerGroup");
    if($bannerGroupID == 0){
        $bannerGroupID = $bannerGroupModel->addGroup($bannerGroupName,$bannerGroupTitle,$bannerGroupDesc,$bannerLayoutID,$bannerGroupKind,$bannerGroupView,$bannerLayoutColumns,"horizontal",$style_class,$bannerGroupBgColor,$bannerGroupTitleColor,$bannerGroupDescColor,$bannerGroupFullSize,$custom_css,1,$bannerStartDate,$bannerEndDate,$bannerDuration, $bannerFullSize);

        //banner grubu eklenemediyse hata döndürelim
        if (!$bannerGroupID) {
            $bannerGroupModel->rollBack("bannerGroup");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Grubu Eklenemedi'
            ]);
            exit();
        }
    }
    else{
        $updateGroupResult = $bannerGroupModel->updateGroup($bannerGroupID,$bannerGroupName,$bannerGroupTitle,$bannerGroupDesc,$bannerLayoutID,$bannerGroupKind,$bannerGroupView,$bannerLayoutColumns,"horizontal",$style_class,$bannerGroupBgColor,$bannerGroupTitleColor,$bannerGroupDescColor,$bannerGroupFullSize,$custom_css,1,$bannerStartDate,$bannerEndDate,$bannerDuration, $bannerFullSize);

        if($updateGroupResult < 0){
            $bannerGroupModel->rollBack("bannerGroup");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Grubu Güncellenemedi'
                ]);
            exit();
        }

        $banners = $adminBannerModel->getBannersByGroupID($bannerGroupID);

        foreach ($banners as $banner) {
            $bannerStylesDeleteResult = $bannerStyleModel->deleteStyle($banner["style_id"]);
            if (!$bannerStylesDeleteResult) {
                $bannerGroupModel->rollBack("deleteBannerStyle");
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Banner Stili Silinemedi'
                ]);
                exit();
            }
        }

        $bannerDeleteResult = $adminBannerModel->deleteBannersByGroupID($bannerGroupID);
        if (!$bannerDeleteResult) {
            $bannerGroupModel->rollBack("deleteBanners");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Silinemedi'
            ]);
            exit();
        }

        $deleteDisplayRules = $adminBannerDisplayRulesModel->deleteDisplayRuleByGroupID($bannerGroupID);
        if (!$deleteDisplayRules) {
            $bannerGroupModel->rollBack("deleteDisplayRules");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner görüntülenme silinemedi'
            ]);
            exit();
        }
    }

    //Log::adminWrite("Banner Grubu Ekleme İşlemi, Banner Grubu Ekleme İşlemi Başarılı");

    $bannerSlogans = $requestData["bannerSlogan"] ?? [];

    //banner_styles tablosuna eklemek için banner bilgilerini döngüye alalım
    for ($i=0; $i < count($bannerSlogans); $i++){
        $bannerHeightSize = $requestData["bannerHeightSize"][$i] ?? "";
        $bannerBgColor = $requestData["bannerBgColor"][$i] ?? "";
        $bannerContentBoxColor = $requestData["bannerContentBoxBgColor"][$i] ?? "";
        $bannerTitleColor = $requestData["titleFontColor"][$i] ?? "";
        $bannerTitleSize = $requestData["titleFontSize"][$i] ?? "";
        $bannerContentColor = $requestData["bannerContentFontColor"][$i] ?? "";
        $bannerContentSize = $requestData["bannerContentFontSize"][$i] ?? "";
        $bannerShowButton = $requestData["showButton"][$i] ?? 0;
        $bannerButton = $requestData["bannerButton"][$i] ?? "";
        $bannerButtonLocation = $requestData["bannerButtonLocation"][$i] ?? 5;
        $bannerButtonBgColor = $requestData["bannerButtonBgColor"][$i] ?? "";
        $bannerButtonTextColor = $requestData["bannerButtonTextColor"][$i] ?? "";
        $bannerButtonHoverBgColor = $requestData["bannerButtonHoverBgColor"][$i] ?? "";
        $bannerButtonTextHoverColor = $requestData["bannerButtonTextHoverColor"][$i] ?? "";
        $bannerButtonTextSize = $requestData["bannerButtonTextSize"][$i] ?? "";

        Log::adminWrite("Banner Stili Ekleme İşlemi, Banner Stili Ekleme İşlemi Başlatıldı");
        $bannerStyleID = $bannerStyleModel->addStyle($bannerHeightSize, $bannerBgColor, $bannerContentBoxColor, $bannerTitleColor, $bannerTitleSize, $bannerContentColor, $bannerContentSize, $bannerShowButton, $bannerButton, $bannerButtonLocation, $bannerButtonBgColor, $bannerButtonTextColor, $bannerButtonHoverBgColor, $bannerButtonTextHoverColor, $bannerButtonTextSize);

        if (!$bannerStyleID) {
            $bannerStyleModel->rollBack("bannerStyle");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Stili Eklenemedi'
            ]);
            exit();
        }

        //Log::adminWrite("Banner Stili Ekleme İşlemi, Banner Stili Ekleme İşlemi Başarılı");

        $bannerSlogan = $requestData["bannerSlogan"][$i] ?? "";
        $bannerText = $requestData["bannerText"][$i] ?? "";
        $bannerLink = $requestData["bannerLink"][$i] ?? "#";
        $bannerImage = $requestData["bannerImage"][$i] ?? "";
        $bannerActive = $requestData["bannerActive"][$i] ?? 0;

        Log::adminWrite("Banner Ekleme İşlemi, Banner Ekleme İşlemi Başlatıldı");

        $bannerID = $adminBannerModel->addBanner($bannerGroupID,$bannerStyleID,$bannerSlogan,$bannerText,$bannerImage,$bannerLink,$bannerActive);
        if (!$bannerID) {
            $adminBannerModel->rollBack("banner");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Eklenemedi'
            ]);
            exit();
        }

        //Log::adminWrite("Banner Ekleme İşlemi, Banner Ekleme İşlemi Başarılı");
    }

    //banner_display_rules tablosuna eklemek için banner bilgilerini alalım
    $bannerDisplayLocation = json_decode($requestData["bannerDisplayLocation"] ?? '[]', true);
    $languageCode = $languageModel->getLanguageCode($languageID);

    Log::adminWrite("Banner Gösterim Kuralı Ekleme İşlemi, Banner Gösterim Kuralı Ekleme İşlemi Başlatıldı");

    if ($bannerLocation == 0) {
        Log::adminWrite("Banner Gösterim Kuralı Ekleme İşlemi, Tüm Site");
        // Tüm site
        $pageID = null;
        $categoryID = null;

        $bannerDisplayRuleID = $adminBannerDisplayRulesModel->addDisplayRule($bannerGroupID, $bannerTypeID, $pageID, $categoryID, $languageCode);
        if (!$bannerDisplayRuleID) {
            $adminBannerDisplayRulesModel->rollBack("bannerDisplayRule");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Gösterim Kuralı Eklenemedi'
            ]);
            exit();
        }
    }
    elseif ($bannerLocation == 1) {
        Log::adminWrite("Banner Gösterim Kuralı Ekleme İşlemi, Kategori");
        // Kategori
        $pageID = null;
        foreach ($bannerDisplayLocation as $categoryID) {
            $bannerDisplayRuleID = $adminBannerDisplayRulesModel->addDisplayRule($bannerGroupID, $bannerTypeID, $pageID, $categoryID, $languageCode);
            if (!$bannerDisplayRuleID) {
                $adminBannerDisplayRulesModel->rollBack("bannerDisplayRule");
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Banner Gösterim Kuralı Eklenemedi'
                ]);
                exit();
            }
        }
    }
    elseif ($bannerLocation == 2) {
        Log::adminWrite("Banner Gösterim Kuralı Ekleme İşlemi, Sayfa");
        // Sayfa
        $categoryID = null;
        foreach ($bannerDisplayLocation as $pageID) {
            $bannerDisplayRuleID = $adminBannerDisplayRulesModel->addDisplayRule($bannerGroupID, $bannerTypeID, $pageID, $categoryID, $languageCode);
            if (!$bannerDisplayRuleID) {
                $adminBannerDisplayRulesModel->rollBack("bannerDisplayRule");
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Banner Gösterim Kuralı Eklenemedi'
                ]);
                exit();
            }
        }
    }
    $siteSettings = new AdminSiteSettings($db, $languageID);
    $checkSiteConfigVersion = $siteSettings->getSiteConfigVersions($languageID);
    if ($checkSiteConfigVersion) {
        Log::adminWrite("Site konfigürasyon güncelleniyor","info");
        $siteSettings->updateSiteConfigVersion($languageID);
    }
    else{
        Log::adminWrite("Site konfigürasyon ekleniyor","info");
        $siteSettings->addSiteConfigVersion($languageID);
    }

    // Banner cache'ini temizle (site config değiştiği için)
    if (class_exists('BannerManager')) {
        $bannerManager = BannerManager::getInstance();
        $bannerManager->onSiteConfigChange();
        Log::adminWrite("Banner cache temizlendi","info");
    }

    $adminBannerModel->commit("banner");

    exit(
        json_encode([
            'status' => 'success',
            'message' => 'Banner Eklendi',
            'bannerGroupID' => $bannerGroupID
        ])
    );
}
elseif($action == "getBannerGroupsByLanguageIDAndBannerTypeID"){
    $languageID = $requestData["languageID"] ?? 0;
    $bannerTypeID = $requestData["bannerTypeID"] ?? 0;

    $languageCode = $languageModel->getLanguageCode($languageID);
    $displayResults = $adminBannerDisplayRulesModel->getDisplayRuleByLanguageId($languageCode,$bannerTypeID);

    if (!$displayResults) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Banner Grupları Getirilemedi'
        ]);
        exit();
    }

    $bannerGroups = [];
    foreach ($displayResults as $displayResult) {
        $bannerGroup = $bannerGroupModel->getGroupById($displayResult["group_id"]);
        $bannerLayouts = $bannerLayoutModel->getLayoutById($bannerGroup[0]["layout_id"]);
        Log::adminWrite("Banner Layout: ".json_encode($bannerLayouts));
        $bannerGroup[0]["layout_name"] = $bannerLayouts[0]["layout_name"];
        $bannerGroup[0]["layout_description"] = $bannerLayouts[0]["description"];
        Log::adminWrite("Banner Grubu: ".json_encode($bannerGroup));
        $bannerGroups[] = $bannerGroup[0];
    }

    echo json_encode([
        'status' => 'success',
        'bannerGroups' => $bannerGroups
    ]);
}
elseif ($action == "deleteBannerGroup"){
    $bannerGroupID = $requestData["bannerGroupID"] ?? 0;
    if($bannerGroupID==0){
        echo json_encode([
            'status' => 'error',
            'message' => 'Banner Grubu ID boş olamaz'
        ]);
        exit();
    }

    $banners = $adminBannerModel->getBannersByGroupID($bannerGroupID);

    $bannerGroupModel->beginTransaction("deleteBannerGroup");

    foreach ($banners as $banner) {
        $bannerStylesDeleteResult = $bannerStyleModel->deleteStyle($banner["style_id"]);
        if (!$bannerStylesDeleteResult) {
            $bannerGroupModel->rollBack("deleteBannerStyle");
            echo json_encode([
                'status' => 'error',
                'message' => 'Banner Stili Silinemedi'
            ]);
            exit();
        }
    }

    $bannerDeleteResult = $adminBannerModel->deleteBannersByGroupID($bannerGroupID);
    if (!$bannerDeleteResult) {
        $bannerGroupModel->rollBack("deleteBanners");
        echo json_encode([
            'status' => 'error',
            'message' => 'Banner Silinemedi'
        ]);
        exit();
    }

    $deleteDisplayRules = $adminBannerDisplayRulesModel->deleteDisplayRuleByGroupID($bannerGroupID);
    if (!$deleteDisplayRules) {
        $bannerGroupModel->rollBack("deleteDisplayRules");
        echo json_encode([
            'status' => 'error',
            'message' => 'Banner görüntülenme silinemedi'
            ]);
        exit();
    }

    $bannerGroupDeleteResult = $bannerGroupModel->deleteGroup($bannerGroupID);
    if (!$bannerGroupDeleteResult) {
        $bannerGroupModel->rollBack("deleteBannerGroup");
        echo json_encode([
            'status' => 'error',
            'message' => 'Banner Grubu Silinemedi'
        ]);
        exit();
    }

    $bannerGroupModel->commit("deleteBannerGroup");

    echo json_encode([
        'status' => 'success',
        'message' => 'Banner Grubu Silindi'
    ]);
    exit;
}