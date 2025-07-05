<?php
/**
 * @var Config $config
 * @var Session $session
 * @var Database $db
 * @var string $languageCode
 * @var int $languageID
 */

$casper = $session->getCasper();
$siteConfig = $casper->getSiteConfig();
$helper = $casper->getConfig()->Helper;

$companySettings = $siteConfig["companySettings"];
if(empty($companySettings)) {
    header("Location: /_y/s/s/firmabilgileri/AddCompanySettings.php?languageID=$languageID");
    exit;
}

if($languageCode == "ar" || $languageCode == "fa" || $languageCode == "ur" || $languageCode == "ps" || $languageCode == "sd" || $languageCode == "yi" || $languageCode == "dv"){
    $dir="rtl";
}else{
    $dir="ltr";
}
?>
<!DOCTYPE html>
<html lang="<?=$languageCode?>" dir="<?=$dir?>">
<?php
$headData = [
    "session"=>$session,
    'db' => $db,
    'languageID' => $languageID,
    'languageCode' => $languageCode,
    'helper' => $helper
];
$config->loadView("Layouts/head",$headData);

$bodyData = [
    "config"=>$config,
    "session"=>$session,
    "db"=>$db,
    "languageID"=>$languageID,
    "languageCode"=>$languageCode
];
$config->loadView("Layouts/body",$bodyData); ?>
</html>
