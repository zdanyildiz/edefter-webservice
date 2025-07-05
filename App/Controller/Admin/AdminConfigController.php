<?php
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$checkLanguageSql = "SELECT * FROM dil Where dilsil=0 AND dilaktif=1";
$checkLanguage = $db->select($checkLanguageSql);

$checkGeneralSettingsSql = "SELECT * FROM ayargenel";
$checkGeneralSettings = $db->select($checkGeneralSettingsSql);

$checkCompanySettingsSql = "SELECT * FROM ayarfirma";
$checkCompanySettings = $db->select($checkCompanySettingsSql);

$checkPriceSettingsSql = "SELECT * FROM ayarfiyat";
$checkPriceSettings = $db->select($checkPriceSettingsSql);

if (empty($checkLanguage)) {

    $redirectAddLanguage = $_GET["redirectAddLanguage"] ?? 1;

    if($redirectAddLanguage == 1){
        header("Location: /_y/s/s/diller/AddLanguage.php?redirectAddLanguage=0");exit();
    }
}
elseif (empty($checkGeneralSettings)) {
    $redirectAddGeneralSettings = $_GET["redirectAddGeneralSettings"] ?? 1;

    if($redirectAddGeneralSettings == 1){
        header("Location: /_y/s/s/genelayarlar/AddGeneralSettings.php?redirectAddGeneralSettings=0");exit();
    }
}
elseif (empty($checkCompanySettings)) {
    $redirectAddCompanySettings = $_GET["redirectAddCompanySettings"] ?? 1;

    if($redirectAddCompanySettings == 1){
        header("Location: /_y/s/s/firmabilgileri/AddCompanySettings.php?redirectAddCompanySettings=0");exit();
    }
}
elseif (empty($checkPriceSettings)) {
    $redirectAddPriceSettings = $_GET["redirectAddPriceSettings"] ?? 1;

    if($redirectAddPriceSettings == 1){
        header("Location: /_y/s/s/fiyatayar/PriceSettings.php?redirectAddPriceSettings=0");exit();
    }
}