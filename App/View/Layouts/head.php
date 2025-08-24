<?php
/**
 * @var $session Session
 * @var $casper Casper
 * @var string $languageCode
 * @var Config $config
 * @var Database $db
 * @var int $languageID
 * @var Helper $helper
 */
$casper = $session->getCasper();
$config = $casper->getConfig();
$siteConfig = $casper->getSiteConfig();

$logoSettings = $siteConfig["logoSettings"];
$logoImg = $logoSettings["resim_url"];
$logoAlt = $logoSettings["logoyazi"];
$favIcon = $logoSettings["favIcon"];

$companySettings = $siteConfig["companySettings"];

$routerResult = $session->getSession("routerResult");

$siteSettings = $siteConfig['siteSettings'];
$bodySiteSettings = array_filter($siteSettings, function($siteSetting) {
    return $siteSetting['section'] == "body";
});

foreach ($bodySiteSettings as $siteSetting) {
    if ($siteSetting['element'] == "newsletter") {
        $bodyShowNewsletter = $siteSetting['is_visible'] == 1 ? 1 : 0;
    }
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-type" content="public">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0">
    <meta name="referrer" content="always" />
    <meta name="robots" content="index,follow">
    <meta name="robots" content="all">
    <meta name="revisit-after" content="1 weeks">
    <meta name="revisit" content="1 weeks">
    <meta name="rating" content="General">

    <meta name="publisher" content="<?=$companySettings["ayarfirmakisaad"]?>">
    <meta name="dcterms.Publisher" content="<?=$companySettings["ayarfirmakisaad"]?>" />
    <meta name="author" content="<?=$companySettings["ayarfirmakisaad"]?>" />
    <meta property="article:publisher" content="<?=$companySettings["ayarfirmakisaad"]?>" />
    <meta property="article:author" content="<?=$companySettings["ayarfirmakisaad"]?>" />
    <meta property="og:site_name" content="<?=$companySettings["ayarfirmakisaad"]?>"/>
    <meta name="copyright" content="Copyright © <?=$config->http.$config->hostDomain?> <?=$companySettings["ayarfirmakisaad"]?> Tüm Hakları Saklıdır.">
    <meta name="dcterms.Format" content="text/html" />
    <meta name="dcterms.Relation" content="<?=$companySettings["ayarfirmakisaad"]?>" />
    <meta name="dcterms.Language" content="<?=$routerResult["languageCode"]?>" />
    <meta name="dcterms.Type" content="text/html" />
    <meta name="dcterms.Coverage" content="<?=$config->http.$config->hostDomain?>" />
    <meta name="dcterms.Rights" content="Copyright © <?=$config->http.$config->hostDomain?> <?=$companySettings["ayarfirmakisaad"]?> Tüm Hakları Saklıdır" />
    <meta name="dcterms.Contributor" content="<?=$companySettings["ayarfirmakisaad"]?>" />

    <title><?=$routerResult["seoTitle"]?></title>
    <meta name="dcterms.Title" content="<?=$routerResult["seoTitle"]?>" />
    <meta property="og:title" content="<?=$routerResult["seoTitle"]?>"/>
    <meta name="twitter:title" content="<?=$routerResult["seoTitle"]?>" />
    <meta name="abstract" content="<?=$routerResult["seoDescription"]?>">
    <meta name="dcterms.Subject" content="<?=$routerResult["seoDescription"]?>" />
    <meta name="description" content="<?=$routerResult["seoDescription"]?>" />
    <meta name="dcterms.Description" content="<?=$routerResult["seoDescription"]?>" />
    <meta property="og:description" content="<?=$routerResult["seoDescription"]?>"/>
    <meta name="twitter:description" content="<?=$routerResult["seoDescription"]?>l" />

    <link rel="publisher" href="<?=$config->http.$config->hostDomain?>" />
    <link rel="amphtml" href="<?=$config->http.$config->hostDomain?>/amp/">
    <link rel="canonical" href="<?=$config->http.$config->hostDomain?>/"/>
    <meta name="twitter:url" content="<?=$config->http.$config->hostDomain?>/" />
    <meta property="og:url" content="<?=$config->http.$config->hostDomain?>/"/>
    <meta name="dcterms.Identifier" content="<?=$config->http.$config->hostDomain?>/" />
    <meta name="keywords" content="GLOBAL,POZİTİF,TEKNOLOJİLER,SAN.VE,DIŞ,TİC.,AŞ." />
    <meta property="article:tag" content="GLOBAL"/>
    <meta property="article:tag" content="POZİTİF"/>
    <meta property="article:tag" content="TEKNOLOJİLER"/>
    <meta property="article:tag" content="SAN.VE"/>
    <meta property="article:tag" content="DIŞ"/>
    <meta property="article:tag" content="TİC."/>
    <meta property="article:tag" content="AŞ."/>
    <meta name="dcterms.Date" content="2024-02-20" />
    <link rel="image_src" href="<?=$config->http.$config->hostDomain.imgRoot.$logoImg?>" />
    <meta name="twitter:image" content="<?=$config->http.$config->hostDomain.imgRoot.$logoImg?>" />
    <meta property="og:image" content="<?=$config->http.$config->hostDomain.imgRoot.$logoImg?>"/>
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="100">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="#">
    <meta name="twitter:creator" content="#">
    <meta name="geo.region" content="tr-tr" />
    <meta name="geo.placename" content="<?=$companySettings['ayarfirmasehir'] ?? ""?>" />
    <meta name="geo.position" content="<?=$companySettings['ayarfirmaenlem'] ?? ""?>,<?=$companySettings['ayarfirmaboylam'] ?? ""?>" />
    <meta name="og:email" content="<?=$companySettings['ayarfirmaeposta'] ?? ""?>">
    <meta name="og:phone_number" content="<?=$companySettings['ayarfirmatelefon'] ?? ""?>">
    <meta name="og:fax_number" content="<?=$companySettings['ayarfirmafaks'] ?? ""?>">
    <meta name="og:latitude" content="<?=$companySettings['ayarfirmaenlem'] ?? ""?>">
    <meta name="og:longitude" content="<?=$companySettings['ayarfirmaboylam'] ?? ""?>">
    <meta name="og:street-address" content="<?=$companySettings['ayarfirmasemt'] ?? ""?> <?=$companySettings['ayarfirmamahalle'] ?? ""?> <?=$companySettings['ayarfirmaadres'] ?? ""?>">
    <meta name="og:locality" content="<?=$companySettings['ayarfirmailce'] ?? ""?>">
    <meta name="og:region" content="<?=$companySettings['ayarfirmasehir'] ?? ""?>">
    <meta name="og:postal-code" content="<?=$companySettings['ayarfirmapostakod'] ?? ""?>">
    <meta name="og:country-name" content="<?=$companySettings['ayarfirmaulke'] ?? ""?>">
    <meta name="theme-color" content="#fff">
    <link rel="icon" href="<?=$config->http.$config->hostDomain.imgRoot.$favIcon?>" type="" sizes="32x32">

    <?php
    $shema = $casper->getSchema();
    if (!empty($shema)) {
        echo "<script type='application/ld+json'>$shema</script>";
    }
    $casper = $session->getCasper();
    $cssContents = $casper->getCssContents();
    ?>
    <style><?php if(!empty($cssContents)) echo $cssContents; ?></style>
    <?php
        $casper->setCssContents("");
        $session->updateSession("casper",$casper);
    ?>
    <?php
    require_once Helpers . 'PlatformTrackingManager.php';
    PlatformTrackingManager::setDb($db);
    
    // Kullanıcı kimliği yönetimi
    $analyticsUserId = $session->getSession('analytics_user_id');
    $casper = $session->getCasper();
    $visitor = $casper->getVisitor();
    $memberStatus = $visitor['visitorIsMember']['memberStatus'] ?? false;
    
    // User ID mantığı:
    // - İlk ziyaret (giriş yapılmamış): user_id hiç gönderme
    // - Login olmuş: memberUniqID gönder  
    // - Logout olmuş: null gönder
    
    $gaId = PlatformTrackingManager::getGoogleAnalyticsId($languageID);
    if ($gaId) {
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $gaId . '"></script>' . "\n";
        echo '<script>' . "\n";
        echo '  window.dataLayer = window.dataLayer || [];' . "\n";
        echo '  function gtag(){dataLayer.push(arguments);}' . "\n";
        echo '  gtag(\'js\', new Date());' . "\n";
        echo '  gtag(\'config\', \'' . $gaId . '\');' . "\n";
        
        // User ID kontrolü
        if ($memberStatus && $analyticsUserId && $analyticsUserId !== "null") {
            // Login olmuş kullanıcı - memberUniqID gönder
            echo '  gtag(\'set\', \'user_properties\', {\'user_id\': \'' . $analyticsUserId . '\'});';
        } elseif (isset($_SESSION['analytics_user_id']) && $analyticsUserId === "null") {
            // Logout olmuş kullanıcı - null gönder
            echo '  gtag(\'set\', \'user_properties\', {\'user_id\': null});';
        }
        // İlk ziyaret: user_id hiç gönderilmez
        
        echo '</script>' . "\n";
    }
    
    // Diğer platform script'lerini ekle (GA hariç)
    echo PlatformTrackingManager::getHeadScripts($languageID);
    ?>
</head>
