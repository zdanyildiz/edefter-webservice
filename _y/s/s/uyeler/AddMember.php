<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 */

include_once MODEL . 'Admin/AdminMember.php';
$memberModel = new AdminMember($db);

include_once MODEL . 'Admin/AdminLocation.php';
$location = new AdminLocation($db);

$memberID = $_GET["memberID"] ?? 0;
$memberID = intval($memberID);

$memberAddresses = [];
if ($memberID > 0) {
    $member = $memberModel->getMemberInfo($memberID);
    if(!empty($member)){
        $memberCreateDate = $member["memberCreateDate"];
        $memberID = $member["memberID"];
        $memberUniqID = $member["memberUniqID"];
        $memberIdentityNo = $member["memberIdentityNo"];
        $memberIdentityNo = $helper->decrypt($memberIdentityNo, $config->key);
        $memberName = $member["memberName"];
        $memberName = $helper->decrypt($memberName, $config->key);
        $memberSurname = $member["memberSurname"];
        $memberSurname = $helper->decrypt($memberSurname, $config->key);
        $memberEmail = $member["memberEmail"];
        $memberEmail = $helper->decrypt($memberEmail, $config->key);
        $memberPassword = $member["memberPassword"];
        $memberPassword = $helper->decrypt($memberPassword, $config->key);
        $memberPhone = $member["memberPhone"];
        $memberPhone = $helper->decrypt($memberPhone, $config->key);
        $memberDescription = $member["memberDescription"];
        $memberInvoiceName = $member["memberInvoiceName"] ?? "";
        $memberInvoiceName = $helper->decrypt($memberInvoiceName, $config->key);
        $memberInvoiceTaxOffice = $member["memberInvoiceTaxOffice"] ?? "";
        $memberInvoiceTaxOffice = $helper->decrypt($memberInvoiceTaxOffice, $config->key);
        $memberInvoiceTaxNumber = $member["memberInvoiceTaxNumber"] ?? "";
        $memberInvoiceTaxNumber = $helper->decrypt($memberInvoiceTaxNumber, $config->key);
        $memberActive = $member["memberActive"];

        $memberAddresses = $memberModel->getAddresses($memberID);

        $addressID = $_GET['addressID'] ?? 0;
        $addressID = intval($addressID);
        if($addressID > 0) {
            $address = $memberModel->getAddressByID($memberID,$addressID);
            if (!empty($address)) {
                $address = $address[0];
                $addressID = $address["addressID"];
                $addressTitle = $address["addressTitle"];
                $addressContactName = $address["addressContactName"];
                $addressContactName = $helper->decrypt($addressContactName, $config->key);
                $addressContactSurname = $address["addressContactSurname"];
                $addressContactSurname = $helper->decrypt($addressContactSurname, $config->key);
                $addressContactIdentityNumber = $address["addressContactIdentityNumber"];
                $addressContactIdentityNumber = $helper->decrypt($addressContactIdentityNumber, $config->key);
                $addressContactPhone = $address["addressContactPhone"];
                $addressContactPhone = $helper->decrypt($addressContactPhone, $config->key);

                $addressDeliveryCountryID = $address["addressDeliveryCountryID"];
                $addressDeliveryCityID = $address["addressDeliveryCityID"];
                $addressDeliveryDistrictID = $address["addressDeliveryDistrictID"];
                $addressDeliveryAreaID = $address["addressDeliveryAreaID"];
                $addressDeliveryNeighborhoodID = $address["addressDeliveryNeighborhoodID"];

                $addressDeliveryPostalCode = $address["addressDeliveryPostalCode"];
                $addressDeliveryStreet = $address["addressDeliveryStreet"];
                $addressDeliveryStreet = $helper->decrypt($addressDeliveryStreet, $config->key);

            }
        }

        include_once MODEL . 'Admin/AdminLocation.php';
        $location = new AdminLocation($db);
        $countries = $location->getAllCountries();

        $cities = $location->getCity(212);
    }
}

$memberID = $memberID ?? 0;
$memberCreateDate = $memberCreateDate ?? "";
$memberUniqID = $memberUniqID ?? "";
$memberIdentityNo = $memberIdentityNo ?? "";
$memberName = $memberName ?? "";
$memberSurname = $memberSurname ?? "";
$memberEmail = $memberEmail ?? "";
$memberPassword = $memberPassword ?? "";
$memberPhone = $memberPhone ?? "";
$memberDescription = $memberDescription ?? "";
$memberInvoiceName = $memberInvoiceName ?? "";
$memberInvoiceTaxOffice = $memberInvoiceTaxOffice ?? "";
$memberInvoiceTaxNumber = $memberInvoiceTaxNumber ?? "";
$memberActive = $memberActive ?? 0;

$addressID = $addressID ?? 0;
$addressTitle = $addressTitle ?? "";
$addressContactName = $addressContactName ?? "";
$addressContactSurname = $addressContactSurname ?? "";
$addressContactIdentityNumber = $addressContactIdentityNumber ?? "";
$addressContactPhone = $addressContactPhone ?? "";

$addressDeliveryCountryID = $addressDeliveryCountryID ?? 212;

$addressDeliveryCityID = $addressDeliveryCityID ?? 0;

$addressDeliveryDistrictID = $addressDeliveryDistrictID ?? 0;

$addressDeliveryAreaID = $addressDeliveryAreaID ?? 0;

$addressDeliveryNeighborhoodID = $addressDeliveryNeighborhoodID ?? 0;

$addressDeliveryPostalCode = $addressDeliveryPostalCode ?? "";
$addressDeliveryStreet = $addressDeliveryStreet ?? "";

$countries = $location->getAllCountries();

$cities = isset($addressDeliveryCountryID) ? $location->getCity($addressDeliveryCountryID) : [];

$counties = isset($addressDeliveryCityID) ? $location->getCounty($addressDeliveryCityID) : [];

$areas = isset($addressDeliveryDistrictID) ? $location->getArea($addressDeliveryDistrictID) : [];

$neighborhoods = isset($addressDeliveryAreaID) ? $location->getNeighborhood($addressDeliveryAreaID) : [];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Üye Ekle / Düzenle Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<!-- BEGIN HEADER-->
<?php require_once(ROOT."/_y/s/b/header.php");?>
<!-- END HEADER-->
<!-- BEGIN BASE-->
<div id="base">
    <!-- BEGIN CONTENT-->
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Üye Ekle / Düzenle</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-md-12">
                        <form name="addMemberForm" id="addMemberForm" class="form form-validation form-validate" role="form" method="post">
                            <input type="hidden" name="memberID" id="memberID" value="<?=$memberID?>">
                            <div class="card">
                                <div class="card-head">
                                    <header class="text-s">KİŞİSEL BİLGİLER</header>
                                    <?php if($memberID>0):?>
                                    <div class="tools">
                                        <a class="btn btn-primary-bright" data-toggle="modal" data-target="#addAddressModal"> Adres Ekle <i class="fa fa-plus"></i></a>
                                    </div>
                                    <?php endif;?>
                                </div>
                                <div class="card-body">
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="memberIdentityNo"
                                                    id="memberIdentityNo"
                                                    value="<?=$memberIdentityNo?>"
                                                    pattern="\d{11}"
                                                    data-rule-digits="true"
                                                    data-rule-minlength="11"
                                                    maxlength="11">
                                            <label for="memberIdentityNo">TC No</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12"> </div>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="memberName"
                                                id="memberName"
                                                value="<?=$memberName?>"
                                                required aria-required="true" >
                                            <label for="memberName">Üye Adını Yazın</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="memberSurname"
                                                id="memberSurname"
                                                value="<?=$memberSurname?>"
                                                required aria-required="true" >
                                            <label for="memberName">Üye Soyadını Yazın</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12"> </div>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="memberPhone"
                                                id="memberPhone"
                                                value="<?=$memberPhone?>"
                                                data-rule-minlength="10"
                                                maxlength="10"
                                                data-rule-digits="true">
                                            <label for="memberPhone">Üye Cep Telefonu Yazın (5321234567)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input
                                                type="email"
                                                class="form-control"
                                                name="memberEmail"
                                                id="memberEmail"
                                                value="<?=$memberEmail?>"
                                                required aria-required="true" >
                                            <label for="memberEmail">Üye Eposta Yazın</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12"> </div>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input type="password" name="memberPassword" class="form-control" value="<?=$memberPassword?>">
                                            <label for="memberPassword">Şifre</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <input type="password" name="memberPassword2" class="form-control" value="<?=$memberPassword?>">
                                            <label for="memberPassword2">Şifre Tekrar</label>
                                        </div>
                                    </div>
                                    <?php if($memberID>0):?>
                                    <div class="col-sm-4">
                                        <div class="form-group floating-label">
                                            <button id="memberPasswordResetButton" type="button" class="btn btn-primary btn-sm">Şifre Sıfırla</button>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                </div>

                                <div class="card-head">
                                    <header class="text-s">KURUMSAL FATURA BİLGİLERİ</header>
                                </div>
                                <div class="card-body">
                                    <div class="col-sm-6">
                                        <div class="form-group floating-label">
                                            <textarea
                                                name="memberInvoiceName"
                                                id="memberInvoiceName"
                                                class="form-control"
                                                rows="2"
                                                maxlength="255"
                                                style="
                                                background-color:#efefef;
                                                width:96%;
                                                padding: 10px 1% 10px 1%;
                                                margin:10px 0 0 0;
                                                border:solid 1px #eee"
                                            ><?=$memberInvoiceName?></textarea>
                                            <label for="memberInvoiceName">Üye Fatura Ünvan</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group floating-label">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="memberInvoiceTaxOffice"
                                                id="uyesifre"
                                                value="<?=$memberInvoiceTaxOffice?>"
                                                data-rule-minlength="2"
                                                maxlength="255">
                                            <label for="memberInvoiceTaxOffice">Üye Vergi Dairesi </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group floating-label">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="memberInvoiceTaxNumber"
                                                id="memberInvoiceTaxNumber"
                                                value="<?=$memberInvoiceTaxNumber?>"
                                                data-rule-digits="true"
                                                data-rule-minlength="10"
                                                maxlength="11">
                                            <label for="memberInvoiceTaxNumber">Üye Vergi/TC No</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="col-sm-6">
                                        <div class="form-group floating-label">
                                            <textarea
                                                    name="memberDescription"
                                                    id="memberDescription"
                                                    class="form-control"
                                                    rows="2"
                                                    maxlength="255"
                                                    style="
                                                    background-color:#efefef;
                                                    width:96%;
                                                    padding: 10px 1% 10px 1%;
                                                    margin:10px 0 0 0;
                                                    border:solid 1px #eee"
                                            ><?=ltrim($memberDescription)?></textarea>
                                            <label for="memberDescription">Üye Not</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group floating-label">
                                            <label class="col-sm-3 control-label">Aktif mi</label>
                                            <div class="col-sm-12">
                                                <label class="radio-inline radio-styled">
                                                    <input type="radio" name="memberActive" value="1" <?php if($memberActive==1)echo'checked'; ?>><span>Aktif</span>
                                                </label>
                                                <label class="radio-inline radio-styled">
                                                    <input type="radio" name="memberActive" value="0"><span>Pasif</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="card-actionbar-row">
                                        <button type="submit" class="btn btn-primary">Kaydet</button>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>
                <!-- adres ekle -->
                <div class="modal fade" id="addAddressModal" tabindex="-1" role="dialog" aria-labelledby="addAddressModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form name="addAddressForm" id="addAddressForm" class="form form-validation form-validate" role="form" method="post">
                            <input type="hidden" name="addressMemberID" id="addressMemberID" value="<?=$memberID?>">
                            <input type="hidden" name="addressID" id="addressID" value="<?=$addressID?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h4 class="modal-title" id="simpleModalLabel">Adres Ekle/Düzenle</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="addressTitle"
                                                            id="addressTitle"
                                                            value="<?=$addressTitle?>"
                                                            data-rule-minlength="2"
                                                            maxlength="100"
                                                            required aria-required="true" >
                                                        <label for="addressTitle">Adres Başlığı (Firma, Depo)</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="addressContactName"
                                                            id="addressContactName"
                                                            value="<?=$addressContactName?>"
                                                            required aria-required="true" >
                                                        <label for="addressContactName">Adres Kişi Adını Yazın</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="addressContactSurname"
                                                            id="addressContactSurname"
                                                            value="<?=$addressContactSurname?>"
                                                            required aria-required="true" >
                                                        <label for="addressContactSurname">Adres Kişi Soyadını Yazın</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="addressContactIdentityNumber"
                                                            id="addressContactIdentityNumber"
                                                            value="<?=$addressContactIdentityNumber?>"
                                                            data-rule-digits="true"
                                                            data-rule-minlength="11"
                                                            maxlength="11"
                                                            required aria-required="true" >
                                                        <label for="yeniadrestcno">Adres Kişi TC No</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group floating-label">
                                                        <input type="tel"
                                                               name="addressContactPhone"
                                                               id="addressContactPhone"
                                                               class="form-control"
                                                               data-rule-minlength="10"
                                                               maxlength="10"
                                                               data-rule-digits="true"
                                                               value="<?=$addressContactPhone?>"
                                                        >
                                                        <label for="addressContactPhone">Adres için Telefon</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <select id="addressDeliveryCountryID" name="addressDeliveryCountryID" class="form-control" required>
                                                            <?php
                                                            if(!empty($countries)){
                                                                foreach ($countries as $country){
                                                                ?>
                                                                <option
                                                                        value="<?=$country['CountryID']?>"
                                                                        data-contrycode="<?=$country['PhoneCode']?>"
                                                                    <?php if($country['CountryID'] == $addressDeliveryCountryID) echo "selected"; ?>>
                                                                    <?=$country['CountryName']?></option>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <label for="addressDeliveryCountryID" class="control-label">Ülke *</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <?php if($addressDeliveryCountryID == 212): ?>
                                                        <select
                                                            id="addressDeliveryCityID"
                                                            name="addressDeliveryCityID"
                                                            class="form-control"
                                                        >
                                                            <option value="0">Şehir Seçin</option>
                                                            <?php
                                                            if(!empty($cities)){
                                                                foreach ($cities as $city){
                                                                ?>
                                                                <option value="<?=$city['CityID']?>" <?php if($city['CityID'] == $addressDeliveryCityID) echo "selected"; ?>><?=$city['CityName']?></option>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php else: ?>
                                                        <input
                                                            type="text"
                                                            name="addressDeliveryCityID"
                                                            id="addressDeliveryCityID"
                                                            value="<?=$addressDeliveryCityID?>"
                                                            class="form-control"
                                                            placeholder="Şehir adı girin"
                                                        >
                                                        <?php endif; ?>
                                                        <label for="addressDeliveryCityID" class="control-label">Şehir *</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <?php if($addressDeliveryCountryID == 212): ?>
                                                        <select
                                                            id="addressDeliveryDistrictID"
                                                            name="addressDeliveryDistrictID"
                                                            class="form-control"
                                                            required
                                                        >
                                                            <option value="0">İlçe Seçin</option>
                                                            <?php
                                                            if(!empty($counties)){
                                                                foreach ($counties as $county){
                                                                    $districtID = $county['CountyID'];
                                                                    $districtName = $county['CountyName'];
                                                                    ?>
                                                                    <option value="<?=$districtID?>" <?php if($districtID==$addressDeliveryDistrictID)echo "selected"; ?>><?=$districtName?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php else: ?>
                                                        <input
                                                            type="text"
                                                            name="addressDeliveryDistrictID"
                                                            id="addressDeliveryDistrictID"
                                                            value="<?=$addressDeliveryDistrictID?>"
                                                            class="form-control"
                                                            placeholder="İlçe adı girin"
                                                        >
                                                        <?php endif; ?>
                                                        <label for="addressDeliveryDistrictID" class="control-label">İlçe *</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <?php if($addressDeliveryCountryID == 212): ?>
                                                        <select
                                                            id="addressDeliveryAreaID"
                                                            name="addressDeliveryAreaID"
                                                            class="form-control"
                                                            required
                                                        >
                                                            <option value="0">Semt Seçin</option>
                                                            <?php
                                                            if(!empty($areas)){
                                                                foreach($areas as $area){
                                                                ?>
                                                                    <option value="<?=$area['AreaID']?>" <?php if($area['AreaID']==$addressDeliveryAreaID)echo "selected"; ?>><?=$area['AreaName']?></option>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php else: ?>
                                                        <input
                                                            type="text"
                                                            name="addressDeliveryAreaID"
                                                            id="addressDeliveryAreaID"
                                                            value="<?=$addressDeliveryAreaID?>"
                                                            class="form-control"
                                                            placeholder="Semt adı girin"
                                                        >
                                                        <?php endif; ?>
                                                        <label for="addressDeliveryAreaID" class="control-label">Semt *</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <?php if($addressDeliveryCountryID == 212): ?>
                                                        <select
                                                            id="addressDeliveryNeighborhoodID"
                                                            name="addressDeliveryNeighborhoodID"
                                                            class="form-control"
                                                            required
                                                        >
                                                            <option value="0">Mahalle Seçin</option>
                                                            <?php
                                                            if(!empty($neighborhoods)){
                                                                foreach($neighborhoods as $neighborhood){
                                                                ?>  <option value="<?=$neighborhood['NeighborhoodID']?>" <?php if($neighborhood['NeighborhoodID']==$addressDeliveryNeighborhoodID)echo "selected"; ?>><?=$neighborhood['NeighborhoodName']?></option>
                                                                <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php else: ?>
                                                        <input
                                                            type="text"
                                                            name="addressDeliveryNeighborhoodID"
                                                            id="addressDeliveryNeighborhoodID"
                                                            value="<?=$addressDeliveryNeighborhoodID?>"
                                                            class="form-control"
                                                            placeholder="Mahalle adı girin"
                                                        >
                                                        <?php endif; ?>
                                                        <label for="addressDeliveryNeighborhoodID" class="control-label">Mahalle *</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <input
                                                            type="text"
                                                            name="addressDeliveryPostalCode"
                                                            id="addressDeliveryPostalCode"
                                                            class="form-control"
                                                            placeholder="Posta Kodu girin"
                                                            value="<?=$addressDeliveryPostalCode?>"
                                                        <label
                                                            for="addressDeliveryPostalCode"
                                                            class="control-label">Posta Kodu *</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="form-group floating-label">
                                                        <textarea
                                                            name="addressDeliveryStreet"
                                                            id="addressDeliveryStreet"
                                                            class="form-control"
                                                            rows="2"
                                                            maxlength="255"
                                                            style="
                                                            background-color:#efefef;
                                                            width:96%;
                                                            padding: 10px 1% 10px 1%;
                                                            margin:10px 0 0 0;
                                                            border:solid 1px #eee"
                                                        ><?=$addressDeliveryStreet?></textarea>
                                                        <label for="addressDeliveryStreet">Açık Adres</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary" id="addAddressButton">Kaydet</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- diğer adresler -->
                <?php
                if(!empty($memberAddresses)){
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-head style-primary">
                                    <header>Üye Adresleri</header>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body ">
                                    <table class="table no-margin">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Başlık</th>
                                            <th>Adres</th>
                                            <th>İşlem</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($memberAddresses as $address){
                                                $addressID = $address['addressID'];
                                                $addressTitle = $address['addressTitle'];
                                                $addressContactName = $address['addressContactName'];
                                                $addressContactName = $helper->decrypt($addressContactName, $config->key);
                                                $addressContactSurname = $address['addressContactSurname'];
                                                $addressContactSurname = $helper->decrypt($addressContactSurname, $config->key);
                                                $addressContactIdentityNumber = $address['addressContactIdentityNumber'];
                                                $addressContactIdentityNumber = $helper->decrypt($addressContactIdentityNumber, $config->key);
                                                $addressContactPhone = $address['addressContactPhone'];
                                                $addressContactPhone = $helper->decrypt($addressContactPhone, $config->key);
                                                $addressDeliveryCountryID = $address['addressDeliveryCountryID'];
                                                $addressDeliveryCountryName = is_numeric($addressDeliveryCountryID) ? $location->getCountryNameById($addressDeliveryCountryID) :$addressDeliveryCountryID;
                                                $addressDeliveryCityID = $address['addressDeliveryCityID'];
                                                $addressDeliveryCityName = is_numeric($addressDeliveryCityID) ? $location->getCityNameById($addressDeliveryCityID) :$addressDeliveryCityID;
                                                $addressDeliveryDistrictID = $address['addressDeliveryDistrictID'];
                                                $addressDeliveryDistrictName = is_numeric($addressDeliveryDistrictID) ? $location->getCountyNameById($addressDeliveryDistrictID) :$addressDeliveryDistrictID;
                                                $addressDeliveryAreaID = $address['addressDeliveryAreaID'];
                                                $addressDeliveryAreaName = is_numeric($addressDeliveryAreaID) ? $location->getAreaNameById($addressDeliveryAreaID) :$addressDeliveryAreaID;
                                                $addressDeliveryNeighborhoodID = $address['addressDeliveryNeighborhoodID'];
                                                $addressDeliveryNeighborhoodName = is_numeric($addressDeliveryNeighborhoodID) ? $location->getNeighborhoodNameById($addressDeliveryNeighborhoodID) :$addressDeliveryNeighborhoodID;
                                                $addressDeliveryPostalCode = $address['addressDeliveryPostalCode'];
                                                $addressDeliveryStreet = $address['addressDeliveryStreet'];
                                                $addressDeliveryStreet = $helper->decrypt($addressDeliveryStreet, $config->key);
                                                ?>
                                                <tr id="tr<?=$addressID?>">
                                                    <td><?=$addressID?></td>
                                                    <td><?=$addressTitle?></td>
                                                    <td><?=$addressContactIdentityNumber?> - <?=$addressContactName?> <?=$addressContactSurname?> - <?=$addressContactPhone?><br>
                                                        <?=$addressDeliveryCityName?> <?=$addressDeliveryDistrictName?> <?=$addressDeliveryAreaName?> <?=$addressDeliveryNeighborhoodName?>
                                                        <?=$addressDeliveryPostalCode?> - <?=$addressDeliveryCountryName?><br>
                                                        <?=$addressDeliveryStreet?>
                                                    </td>
                                                    <td>
                                                        <a
                                                                id="adresduzenle"
                                                                href="/_y/s/s/uyeler/AddMember.php?memberID=<?=$memberID?>&addressID=<?=$addressID?>"
                                                                class="btn btn-icon-toggle"
                                                                data-placement="top"
                                                                data-original-title="Düzenle">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a
                                                                id="adressil"
                                                                href="#textModaladressil"
                                                                class="btn btn-icon-toggle"
                                                                data-id="<?=$addressID?>"
                                                                data-toggle="modal"
                                                                data-placement="top"
                                                                data-original-title="Sil"
                                                                data-target="#simpleModal"
                                                                data-backdrop="true">
                                                            <i class="fa fa-trash-o"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>

    <div class="modal fade" id="memberPasswordResetModal" tabindex="-1" role="dialog" aria-labelledby="memberPasswordResetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-danger">
                    <header class="modal-title" id="memberPasswordResetModalLabel">Şifre Sıfırlama</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>Üye Şifresini sıfırlamak istediğinize emin misiniz?</p>
                    <p>Şifre sıfırlama işleminden sonra üyeye bir e-posta gönderilecektir.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="memberPasswordResetConfirmButton">Şifre Sıfırla</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-danger">
                    <header class="modal-title" id="alertModalLabel">Uyarı</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p id="alertMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>
<script src="/_y/assets/js/panel/getLocation.js"></script>
<script>
    $("#addMemberphp").addClass("active");

    function isDigits(str) {
        return /^\d+$/.test(str);
    }

    function getUrlParameter(name) {
        const urlParams = new URLSearchParams(window.location.search);
        const value = urlParams.get(name);
        if (value) {
            return value;
        }
        return 0;
    }


    $(document).on("submit", "#addMemberForm", function (e) {
        e.preventDefault();
        // kimlik numarası boş olabilir fakat boş değilse 11 haneli ve rakam olmalı
        if ($("#memberIdentityNo").val() != "") {
            if (!isDigits($("#memberIdentityNo").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("TC Kimlik numarası geçersiz");
                $("#alertModal").modal("show");
                return;
            }

            if ($("#memberIdentityNo").val().length != 11) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("TC Kimlik numarası  11 haneli olmalı");
                $("#alertModal").modal("show");
                return;
            }
        }
        //ad, soyad, eposta boş olamaz
        if ($("#memberName").val() == "" || $("#memberSurname").val() == "" || $("#memberEmail").val() == "") {
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").text("Ad, Soyad, E-Posta alanları boş olamaz");
            $("#alertModal").modal("show");
            return;
        }

        //cep telefonu boş olabilir fakat boş değilse 10 haneli ve rakam olmalı
        if ($("#memberPhone").val() != "") {
            if (!isDigits($("#memberPhone").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Cep Telefonu geçersiz");
                $("#alertModal").modal("show");
                return;
            }

            if ($("#memberPhone").val().length != 10) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Cep Telefonu  10 haneli olmalı");
                $("#alertModal").modal("show");
                return;
            }
        }

        // Şifre ve şifre tekrarı aynı olmalı
        if ($("#memberPassword").val() != "" && $("#memberPassword2").val() != "") {
            if ($("#memberPassword").val() != $("#memberPassword2").val()) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Şifre ve Şifre tekrarı aynı olmalı");
                $("#alertModal").modal("show");
                return;
            }
        }

        //vergi numarası boş olabilir fakat boş değilse 10 ya da 11haneli ve rakam olmalı
        if ($("#memberInvoiceTaxNumber").val() != "") {
            if (!isDigits($("#memberInvoiceTaxNumber").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Vergi numarası geçersiz");
                $("#alertModal").modal("show");
                return;
            }

            if ($("#memberInvoiceTaxNumber").val().length != 10 && $("#memberInvoiceTaxNumber").val().length != 11) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Vergi numarası  10 ya da 11 haneli olmalı");
                $("#alertModal").modal("show");
                return;
            }
        }

        var action = "addMember";
        var memberID = $("#memberID").val();
        if (memberID > 0) {
            action = "updateMember";
        }
        var form = $(this);
        var formData = form.serialize();
        formData += "&action=" + action;
        $.ajax({
            url: "/App/Controller/Admin/AdminMemberController.php",
            type: "POST",
            data: formData,
            success: function (data) {
                console.log(data);
                var response = JSON.parse(data);
                if (response.status == "success") {
                    $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                    $("#alertMessage").text(response.message);
                    $("#alertModal").modal("show");
                    setTimeout(function () {
                        window.location.href = "/_y/s/s/uyeler/MemberList.php";
                    }, 1500);
                } else {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text(response.message);
                    $("#alertModal").modal("show");
                }
            }
        });

    });

    $(document).on("change","#addAddressForm select",function(){

        var thisContainer = $(this).closest(".modal-body");
        var containerPrefix = thisContainer.hasClass("modal-body") ? "Delivery" : "Delivery";
        //addressDeliveryCountryID
        var selectNames = [
            "address" + containerPrefix + "CountryID",
            "address" + containerPrefix + "CityID",
            "address" + containerPrefix + "DistrictID",
            "address" + containerPrefix + "AreaID",
            "address" + containerPrefix + "NeighborhoodID"
        ];

        var selectedSelectName = $(this).attr("name");
        var selectedSelectIndex = selectNames.indexOf(selectedSelectName);

        var action = "";

        if (selectedSelectIndex === 0) {
            // Ülke seçildi
            var countryID = $(this).val();
            action = "getCity";
            convertInputSelect(thisContainer, selectNames, countryID);
        } else if (selectedSelectIndex > 0) {
            // Şehir, ilçe, semt veya mahalle seçildi
            action = ["getCounty", "getArea", "getNeighborhood", "getPostalCode"][selectedSelectIndex - 1];
        }

        if (action === "") {
            return;
        }

        var locationID = $(this).val();

        if(selectedSelectIndex != 4){

            var targetSelectSelector = selectNames[selectedSelectIndex + 1];
            var targetSelect = thisContainer.find("select[name='" + targetSelectSelector + "']");
            getLocationData(action, locationID, targetSelect);
        }
        else{
            var target = "address"+ containerPrefix + "Street";
            getPostalCode(locationID, target,  function(postalCode) {
                if (postalCode) {
                    console.log("Posta Kodu: " + postalCode);
                    var postalCodeTarget = "address"+ containerPrefix + "PostalCode"
                    $("#"+postalCodeTarget).val(postalCode);
                } else {
                    console.log("Posta kodu alınamadı");
                }
            });
        }
    });

    //adres kaydedelim
    $(document).on("submit", "#addAddressForm", function (e) {
        e.preventDefault();

        // Tekrarlanan işlemler için kontrol fonksiyonu
        function validateField(selector, errorMessage) {
            if ($(selector).val() === "" || $(selector).val() === "0") {
                showError(errorMessage);
                return false;
            }
            return true;
        }

        // Hata mesajını gösteren fonksiyon
        function showError(message) {
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").text(message);
            $("#alertModal").modal("show");
        }

        if (
            !validateField("#addressName", "Adres adı alanı boş olamaz!") ||
            !validateField("#addressContactName", "Kişi adı alanı boş olamaz!") ||
            !validateField("#addressContactSurname", "Kişi soyadı alanı boş olamaz!") ||
            !validateField("#addressContactIdentityNumber", "Kişi tc no alanı boş olamaz!") ||
            !validateField("#addressContactPhone", "Kişi telefon alanı boş olamaz!") ||
            !validateField("#addressDeliveryStreet", "Açık Adres alanı boş olamaz!")
        ) {
            return;
        }

        var countryID = $("#addressDeliveryCountryID").val();
        if (countryID == 212) {
            // Türkiye için şehir, ilçe, semt, mahalle kontrolü
            if (
                !validateField("#addressDeliveryCityID", "Türkiye için şehir seçmelisiniz!") ||
                !validateField("#addressDeliveryDistrictID", "Türkiye için ilçe seçmelisiniz!") ||
                !validateField("#addressDeliveryAreaID", "Türkiye için semt seçmelisiniz!") ||
                !validateField("#addressDeliveryNeighborhoodID", "Türkiye için mahalle seçmelisiniz!")
            ) {
                return;
            }
        } else {
            // Yurt dışı için şehir kontrolü
            if (!validateField("#addressDeliveryCityID", "Şehir alanı boş olamaz!")) {
                return;
            }
        }

        var action = "addAddress";
        var addressID = $("#addressID").val();
        if (addressID > 0) {
            action = "updateAddress";
        }
        var memberID = $("#addressMemberID").val();
        if (memberID <= 0) {
            console.log("memberID alanı boş olamaz");
            return;
        }

        var form = $(this);
        var formData = form.serialize();
        formData += "&action=" + action;

        $.ajax({
            url: "/App/Controller/Admin/AdminMemberController.php",
            type: "POST",
            data: formData,
            success: function (data) {
                console.log(data);
                var response = JSON.parse(data);
                if (response.status == "success") {
                    $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                    $("#alertMessage").text(response.message);
                    $("#alertModal").modal("show");
                    setTimeout(function () {
                        window.location.href = "/_y/s/s/uyeler/MemberList.php";
                    }, 1500);
                } else {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text(response.message);
                    $("#alertModal").modal("show");
                }
            }
        });
    });

    $(document).on("click","#memberPasswordResetButton",function(){
        var memberID = $("#memberID").val();
        if(memberID>0){
            $("#memberPasswordResetModal").modal("show");
        }
    });

    $(document).on("click","#memberPasswordResetConfirmButton",function(){
        var memberID = $("#memberID").val();
        var action = "updateMemberPassword";
        $.ajax({
            url: "/App/Controller/Admin/AdminMemberController.php",
            type: "POST",
            data: {
                action: action,
                memberID: memberID
            },
            success: function(data){
                console.log(data);
                var response = JSON.parse(data);
                if(response.status == "success"){
                    $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                    $("#alertMessage").text(response.message);
                    $("#alertModal").modal("show");
                    setTimeout(function(){
                        window.location.reload();
                    },1500);
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text(response.message);
                    $("#alertModal").modal("show");
                }
            }
        });
    });

    //sayfa yüklendikten sonra url'den gelen addressID sıfırdan büyükse adres modalını gösterelim
    $(document).ready(function(){
        var addressID = getUrlParameter("addressID");
        if(addressID>0){
            $("#addressID").val(addressID);
            $("#addAddressModal").modal("show");
        }
    });
</script>

</body>
</html>
