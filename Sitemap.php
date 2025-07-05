<?php
/**
 * @var string $domain
 */
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

################# CONFIG ###################################
//ön tanımlı ayarlarımızı yapalım
$config = new Config();

################# DATABASE #################################

$db=new Database($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

define("DOMAIN", $config->domain);

include_once MODEL."Seo.php";
include_once MODEL."SitemapGenerator.php";
include_once MODEL."Language.php";
include_once MODEL."SiteConfig.php";
include_once MODEL. "Product.php";
$productModel = new Product($db,$config->Json);
include_once MODEL."Currency.php";
$currencyModel = new Currency($db);

$language = new Language($db,"",1);
$languages = $language->getLanguages();
$mainLanguage = $language->getMainLanguages();
if(!empty($mainLanguage)){
    $languageCode = $mainLanguage[0]['dilkisa'];
    $languageID = $mainLanguage[0]['dilid'];
}
else{
    exit("Dil bulunamadı");
}
$robots = "User-agent: *\n";
$robots .= "allow:/\n";
$robots .= "allow:/Public/Image\n";
$robots .= "disallow:/App/\n";
$robotsSiteMap = "";
foreach ($languages as $language) {

    $languageCode = $language['dilkisa'];
    $languageID = $language['dilid'];

    $siteConfigInfo = new SiteConfig($db,$languageID);
    $siteConfigInfo->createSiteConfig();
    $siteConfig = $siteConfigInfo->getSiteConfig();
    //echo '<pre>';
    //print_r($siteConfig);exit;
    $generalSettings = $siteConfig['generalSettings'];
    //print_r($generalSettings);exit;
    $siteDomain = "https://".$generalSettings["domain"];
    $siteType = $generalSettings["sitetip"];
    $priceSettings = $siteConfig['priceSettings'];
    $priceUnitID = $priceSettings["parabirim"];
    $currency = $currencyModel->getCurrencySymbolOrCode($priceUnitID);
    $currencyCode = $currency['parabirimkod'];
    $currentRates = $currencyModel->getCurrentRates($priceUnitID);


    $seoSql = "
        SELECT 
            domain,seo.baslik,seo.aciklama,seo.kelime,seo.link,seo.resim,dil.dilkisa as dil
        FROM 
            seo
            INNER JOIN 
                sayfa ON seo.benzersizid = sayfa.benzersizid
                INNER JOIN 
                    sayfalistekategori ON sayfa.sayfaid = sayfalistekategori.sayfaid
                    INNER JOIN
                        kategori ON sayfalistekategori.kategoriid = kategori.kategoriid
                        INNER JOIN 
                            dil ON kategori.dilid = dil.dilid
                            INNER JOIN
                                ayargenel ON dil.dilid = ayargenel.dilid
        WHERE
            sayfasil=0 AND sayfaaktif=1 AND kategoriaktif=1 AND kategorisil=0 AND dil.dilaktif=1 AND dilsil=0 and dil.dilid=:languageID
    ";
    $seos = $db->select($seoSql, ['languageID' => $languageID]);
    if(!empty($seos)){

        $seos = array_map(function($seo) {
            return new Seo($seo['baslik'], $seo['aciklama'], $seo['kelime'], "https://".$seo['domain'] . $seo['link'], $seo['resim'], $seo['dil']);
        }, $seos);

        $sitemapGenerator = new SitemapGenerator($seos);
        $sitemap = $sitemapGenerator->generateSitemap();
        $imageSitemap = $sitemapGenerator->generateImageSitemap();

        if($siteType==1){

            $merchantProducts = $productModel->getAllProductsForMerchantCenter($languageID);
            $merchantCenterSitemap = $sitemapGenerator->generateMerchantCenterSitemap($siteDomain,$merchantProducts,$currentRates,$currencyCode,strtoupper($languageCode));

            $productList = $sitemapGenerator->generateProductList($siteDomain,$merchantProducts);
            file_put_contents(ROOT . 'merchant-center-sitemap-'.$languageCode.'.xml', $merchantCenterSitemap);
            file_put_contents(ROOT . 'product-list-'.$languageCode.'.xml', $productList);

        }

        file_put_contents(ROOT . 'sitemap-'.$languageCode.'.xml', $sitemap);
        file_put_contents(ROOT . 'imagesitemap-'.$languageCode.'.xml', $imageSitemap);


        $robotsSiteMap = "sitemap: ".$siteDomain."/sitemap-".$languageCode.".xml".PHP_EOL;
        $robotsSiteMap .= "sitemap: ".$siteDomain."/imagesitemap-".$languageCode.".xml".PHP_EOL;

    }
}
$robotsSiteMap = $robots."\n".$robotsSiteMap;
file_put_contents(ROOT . 'robots.txt', $robotsSiteMap);