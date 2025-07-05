<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Config $config
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

$action = $_GET["action"] ?? null;

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminLocation.php';
$locationModel = new AdminLocation($db);

$countries = $locationModel->getAllCountries();

include_once MODEL . 'Admin/AdminCompany.php';
$companyModel = new AdminCompany($db);

$parentCompanyID = 0;

$companyData = $companyModel->getCompanyByLanguageID($languageID);
if(!empty($companyData)&&$action==null){
    $companyID = $companyData['companyID'];
    $companyName = $companyData['companyName'];
    $companyShortName = $companyData['companyShortName'];
    $companyTaxOffice = $companyData['taxOffice'];
    $companyTaxNumber = $companyData['taxNumber'];
    $companyCountry = $companyData['country'];
    //$companyCountry 212 ise şehirleri getirelim
    if($companyCountry == 212){
        $companyCities = $locationModel->getCity($companyCountry);
    }
    $companyCountryName = $locationModel->getCountryNameById($companyCountry);

    $companyCity = $companyData['city'];

    //companyCity sayı ise ilçeleri getirelim
    if(is_numeric($companyCity)){
        $companyCounties = $locationModel->getCounty($companyCity);
    }
    $companyCityName = $locationModel->getCityNameById($companyCity);

    $companyCounty = $companyData['district'];
    //companyCounty sayı ise semtleri getirelim
    if(is_numeric($companyCounty)){
        $companyAreas = $locationModel->getArea($companyCounty);
    }
    $companyCountyName = $locationModel->getCountyNameById($companyCounty);

    $companyArea = $companyData['area'];
    //companyArea sayı ise mahalleleri getirelim
    if(is_numeric($companyArea)){
        $companyNeighborhoods = $locationModel->getNeighborhood($companyArea);
    }
    $companyAreaName = $locationModel->getAreaNameById($companyArea);


    $companyNeighborhood = $companyData['neighborhood'];
    $companyNeighborhoodName = $locationModel->getNeighborhoodNameById($companyNeighborhood);

    $companyPostalCode = $companyData['postalCode'];
    $companyAddress = $companyData['address'];
    $companyEmail = $companyData['email'];
    $companyGsm = $companyData['gsm'];
    $companyPhone = $companyData['phone'];
    $companyFax = $companyData['fax'];
    $companyCoordinate = !empty($companyData['latitude'])&&!empty($companyData['longitude']) ? $companyData['latitude'] . "," . $companyData['longitude'] : "";
    $companyMap = $companyData['map'];
    $companyCountryCode = $companyData['countryCode'];
    $companyUniqueId = $companyData['uniqueId'];
}
elseif($action=="addBranch") {
    $parentCompanyID = $companyData['companyID'];
    $companyName = $companyData['companyName'];
    $companyTaxOffice = $companyData['taxOffice'];
    $companyTaxNumber = $companyData['taxNumber'];

}
elseif($action=="updateBranch") {
    $companyID = $_GET['companyID'] ?? 0;
    $companyID = intval($companyID);

    $companyData = $companyModel->getCompany($companyID);

    if(!empty($companyData)){
        $companyID = $companyData['companyID'];
        $companyName = $companyData['companyName'];
        $companyShortName = $companyData['companyShortName'];
        $companyTaxOffice = $companyData['taxOffice'];
        $companyTaxNumber = $companyData['taxNumber'];
        $companyCountry = $companyData['country'];
        //$companyCountry 212 ise şehirleri getirelim
        if($companyCountry == 212){
            $companyCities = $locationModel->getCity($companyCountry);
        }
        $companyCountryName = $locationModel->getCountryNameById($companyCountry);

        $companyCity = $companyData['city'];

        //companyCity sayı ise ilçeleri getirelim
        if(is_numeric($companyCity)){
            $companyCounties = $locationModel->getCounty($companyCity);
        }
        $companyCityName = $locationModel->getCityNameById($companyCity);

        $companyCounty = $companyData['district'];
        //companyCounty sayı ise semtleri getirelim
        if(is_numeric($companyCounty)){
            $companyAreas = $locationModel->getArea($companyCounty);
        }
        $companyCountyName = $locationModel->getCountyNameById($companyCounty);

        $companyArea = $companyData['area'];
        //companyArea sayı ise mahalleleri getirelim
        if(is_numeric($companyArea)){
            $companyNeighborhoods = $locationModel->getNeighborhood($companyArea);
        }
        $companyAreaName = $locationModel->getAreaNameById($companyArea);


        $companyNeighborhood = $companyData['neighborhood'];
        $companyNeighborhoodName = $locationModel->getNeighborhoodNameById($companyNeighborhood);

        $companyPostalCode = $companyData['postalCode'];
        $companyAddress = $companyData['address'];
        $companyEmail = $companyData['email'];
        $companyGsm = $companyData['gsm'];
        $companyPhone = $companyData['phone'];
        $companyFax = $companyData['fax'];
        $companyCoordinate = !empty($companyData['latitude'])&&!empty($companyData['longitude']) ? $companyData['latitude'] . "," . $companyData['longitude'] : "";
        $companyMap = $companyData['map'];
        $companyCountryCode = $companyData['countryCode'];
        $companyUniqueId = $companyData['uniqueId'];

    }
}

$companyID = $companyID ?? 0;
$companyName = $companyName ?? "";
$companyShortName = $companyShortName ?? "";
$companyTaxOffice = $companyTaxOffice ?? "";
$companyTaxNumber = $companyTaxNumber ?? "";
$companyCountry = $companyCountry ?? "212";
if(!isset($companyCities)){
    $companyCities = $locationModel->getCity($companyCountry);
}
$companyCity = $companyCity ?? "";
$companyCounty = $companyCounty ?? "";
$companyArea = $companyArea ?? "";
$companyNeighborhood = $companyNeighborhood ?? "";
$companyStreet = $companyStreet ?? "";
$companyPostalCode = $companyPostalCode ?? "";
$companyAddress = $companyAddress ?? "";
$companyEmail = $companyEmail ?? "";
$companyGsm = $companyGsm ?? "";
$companyPhone = $companyPhone ?? "";
$companyFax = $companyFax ?? "";
$companyCoordinate = $companyCoordinate ?? "";
$companyMap = $companyMap ?? "";
$companyCountryCode = $companyCountryCode ?? "";
$companyUniqueId = $companyUniqueId ?? "";

$companyCityName = $companyCityName ?? "";
$companyCountyName = $companyCountyName ?? "";
$companyAreaName = $companyAreaName ?? "";
$companyNeighborhoodName = $companyNeighborhoodName ?? "";

$companyCities = $companyCities ?? [];
$companyCounties = $companyCounties ?? [];
$companyAreas = $companyAreas ?? [];
$companyNeighborhoods = $companyNeighborhoods ?? [];

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Firma Bilgileri Ekle/Düzenle Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/select2/select2.css?1424887856" />

        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">
        
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Firma Bilgileriniz</li>
						</ol>
					</div>
					
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
                                    <div class="card-head">
                                        <?php if(!empty($companyData)&&$action==null):?>
                                            <header>Firma Bilgileriniz</header>
                                            <div class="tools">
                                                <a class="btn btn-primary-bright" href="/_y/s/s/firmabilgileri/AddCompanySettings.php?languageID=<?=$languageID?>&action=addBranch">
                                                    Şube Ekle
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </div>
                                        <?php else:?>
                                            <header>Şube Bilgileriniz</header>
                                        <?php endif;?>
                                    </div>
									<div class="card-body ">
										<div id="rootwizard2" class="form-wizard form-wizard-horizontal">
											<form 
												method="post" 
												id="addCompanySettingsForm"
												class="form form-validation form-validate"
												role="form" 
												novalidate="novalidate">
                                                <input type="hidden" name="companyID" id="companyID" value="<?=$companyID?>">
                                                <input type="hidden" name="parentCompanyID" id="parentCompanyID"value="<?=$parentCompanyID?>">
												<div class="form-wizard-nav">
													<div class="progress"><div class="progress-bar progress-bar-primary"></div></div>
													<ul class="nav nav-justified">
														<li>
															<a href="#step1" id="unvanTab" data-toggle="tab">
																<span class="step">1</span> <span class="title">ÜNVAN</span>
															</a>
														</li>
														<li>
															<a href="#step2" data-toggle="tab">
																<span class="step">2</span> <span class="title">ULAŞIM</span>
															</a>
														</li>
														<li>
															<a href="#step3" data-toggle="tab">
																<span class="step">3</span> <span class="title">İLETİŞİM</span>
															</a>
														</li>
														<li class="active">
															<a href="#step4" data-toggle="tab">
																<span class="step">4</span> <span class="title">HARİTA</span>
															</a>
														</li>
													</ul>
												</div>
												<div class="tab-content clearfix">
													<div class="tab-pane active" id="step1">
														<div class="row">
															<div class="col-sm-12">
																<div class="form-group">
																	<select id="languageID" name="languageID" class="form-control">
																	<?php
                                                                    if(!empty($languages)){
                                                                        foreach ($languages as $lang){
                                                                            ?>
                                                                            <option value="<?=$lang['languageID']?>" <?php if($lang['languageID'] == $languageID) echo "selected"; ?>><?=$lang['languageName']?></option>
                                                                            <?php
                                                                        }
                                                                    }
																	?>
																	</select>
																	<p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
																</div>
															</div>
														</div>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <div class="form-group">
                                                                    <input
                                                                            type="text"
                                                                            name="companyShortName"
                                                                            id="companyShortName"
                                                                            value="<?=$companyShortName?>"
                                                                            class="form-control"
                                                                            maxlength="50">
                                                                    <label for="ayrfirmafirmakisaad" class="control-label">Kısa Ad *</label>
                                                                    <p class="help-block">örnek: Pozitif ETicaret</p>
                                                                </div>
                                                            </div>
                                                        </div>

														<div class="row">
                                                            <div class="col-sm-4">
                                                                <div class="form-group">
                                                                    <input
                                                                            type="text"
                                                                            name="companyName"
                                                                            id="companyName"
                                                                            value="<?=$companyName?>"
                                                                            class="form-control"
                                                                            maxlength="255">
                                                                    <label for="companyName" class="control-label">Ünvan *</label>
                                                                </div>
                                                            </div>
															<div class="col-sm-4">
																<div class="form-group">
																	<input 
																		type="text" 
																		name="companyTaxOffice"
																		id="companyTaxOffice"
																		value="<?=$companyTaxOffice?>"
																		class="form-control">
																	<label 
																		for="companyTaxOffice"
																		class="control-label">Vergi Dairesi</label>
																</div>
															</div>
															<div class="col-sm-4">
																<div class="form-group">
																	<input 
																		type="text" 
																		name="companyTaxNumber"
																		id="companyTaxNumber"
																		value="<?=$companyTaxNumber?>"
																		class="form-control">
																	<label 
																		for="companyTaxNumber"
																		class="control-label">Vergi/TC Kimlik NO</label>
																</div>
															</div>
														</div>
													</div>
													<div class="tab-pane" id="step2">
														<br/><br/>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<select id="companyCountryID" name="companyCountryID" class="form-control">
																	<?php
																	//$countries
                                                                    if(!empty($countries)){
                                                                        foreach ($countries as $country){
                                                                            ?>
                                                                            <option
                                                                                value="<?=$country['CountryID']?>"
                                                                                data-contrycode="<?=$country['PhoneCode']?>"
                                                                                <?php if($country['CountryID'] == $companyCountry) echo "selected"; ?>>
                                                                                <?=$country['CountryName']?></option>
                                                                            <?php
                                                                        }
                                                                    }
																	?>
																	</select>
																	<label for="companyCountryID" class="control-label">Ülke *</label>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																	<select 
																		id="companyCityID" 
																		name="companyCityID" 
																		class="form-control <?=!is_numeric($companyCity) ? "hidden" :""?>">
																		<option value="0">Şehir Seçin</option>
																		<?php
																		if(!empty($companyCities)){
                                                                            foreach ($companyCities as $city){
                                                                                ?>
                                                                                <option
                                                                                        value="<?=$city['CityID']?>"
                                                                                    <?php if($city['CityID'] == $companyCity) echo "selected"; ?>
                                                                                ><?=$city['CityName']?></option>
                                                                                <?php
                                                                            }
                                                                        }
																		?>
																	</select>
																	<input 
																		type="text" 
																		name="companyCity" 
																		id="companyCity" 
																		class="form-control <?=is_numeric($companyCity) ? "hidden" :""?>"
																		value="<?=$companyCityName?>"
																		placeholder="Şehir adı girin">
																	<label for="companyCityID" class="control-label">Şehir *</label>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<select 
																		id="companyCountyID" 
																		name="companyCountyID" 
																		class="form-control <?=!is_numeric($companyCounty) ? "hidden" :""?>"
																		>
																		<option value="0">İlçe Seçin</option>
																		<?php
                                                                        if(!empty($companyCounties)){
                                                                            foreach ($companyCounties as $district){
                                                                                ?>
                                                                                <option value="<?=$district['CountyID']?>" <?php if($district['CountyID'] == $companyCounty) echo "selected"; ?>><?=$district['CountyName']?></option>
                                                                                <?php
                                                                            }
                                                                        }
																		?>
																	</select>
																	<input 
																		type="text" 
																		name="companyCounty" 
																		id="companyCounty" 
																		class="form-control <?=is_numeric($companyCounty) ? "hidden" :""?>"
                                                                        placeholder="İlçe adı girin"
                                                                        value="<?=$companyCountyName?>"
																		 >
																	<label for="companyCountyID" class="control-label">İlçe </label>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																	<select 
																		id="companyAreaID" 
																		name="companyAreaID" 
																		class="form-control <?=!is_numeric($companyArea) ? "hidden" :""?>"
																		>
																		<option value="0">Semt Seçin</option>
																		<?php
                                                                        if(!empty($companyAreas)){
                                                                            foreach ($companyAreas as $area){
                                                                                ?>
                                                                                <option value="<?=$area['AreaID']?>" <?php if($area['AreaID'] == $companyArea) echo "selected"; ?>><?=$area['AreaName']?></option>
                                                                                <?php
                                                                            }
                                                                        }
																		?>
																	</select>
																	<input 
																		type="text" 
																		name="companyArea" 
																		id="companyArea" 
																		class="form-control <?=is_numeric($companyArea) ? "hidden" :""?>"
																		value="<?=$companyNeighborhoodName?>"
																		placeholder="Semt adı girin"  >
																	<label for="companyAreaID" class="control-label">Semt </label>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<select 
																		id="companyNeighbourhoodID" 
																		name="companyNeighbourhoodID" 
																		class="form-control <?=!is_numeric($companyNeighborhood) ? "hidden" :""?>"
																		>
																		<option value="0">Mahalle Seçin</option>
																		<?php
                                                                        if(!empty($companyNeighborhoods)){
                                                                            foreach ($companyNeighborhoods as $neighborhood){
                                                                                ?>
                                                                                <option value="<?=$neighborhood['NeighborhoodID']?>" <?php if($neighborhood['NeighborhoodID'] == $companyNeighborhood) echo "selected"; ?>><?=$neighborhood['NeighborhoodName']?></option>
                                                                                <?php
                                                                            }
                                                                        }
																		?>
																	</select>
																	<input 
																		type="text" 
																		name="companyNeighbourhood" 
																		id="companyNeighbourhood" 
																		class="form-control <?=is_numeric($companyNeighborhood) ? "hidden" :""?>"
																		value="<?=$companyNeighborhoodName?>"
																		placeholder="Mahalle *"
                                                                    >
																	<label 
																		for="companyNeighbourhoodID" 
																		class="control-label">Mahalle </label>
																</div>
																<div>
																	<input 
																		type="text" 
																		name="companyPostalCode" 
																		id="companyPostalCode" 
																		class="form-control" 
																		placeholder="Posta Kodu girin" 
																		value="<?=$companyPostalCode?>" >
																	<label 
																		for="companyPostalCode" 
																		class="control-label">Posta Kodu *</label>
																</div>
															</div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
																	<textarea
                                                                            name="companyAddress"
                                                                            id="companyAddress"
                                                                            class="form-control"
                                                                            rows="3"><?=$companyAddress?></textarea>
                                                                    <label for="companyAddress">Adres *</label>
                                                                </div>
                                                            </div>
														</div>
													</div>
													<div class="tab-pane" id="step3">
														<br/><br/>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<input type="email" name="companyEmail" id="companyEmail" value="<?=$companyEmail?>" class="form-control" data-rule-email="true">
																	<label for="companyEmail" class="control-label">E-Posta *</label>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																	<input 
																		type="tel" 
																		name="companyGsm" 
																		id="companyGsm" 
																		value="<?=$companyGsm?>" 
																		class="form-control">
																	<label for="companyGsm" class="control-label">GSM </label>
																	<p class="help-block"><a href="#" target="_blank">+905321234567</a></p>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-6">
																<div class="form-group">
																	<input 
																		type="tel" 
																		name="companyPhone" 
																		id="companyPhone" 
																		value="<?=$companyPhone?>"
																		class="form-control">
																	<label for="companyPhone" class="control-label">Telefon </label>
																	<p class="help-block"><a href="#" target="_blank">+902121234567</a></p>
																</div>
															</div>
															<div class="col-sm-6">
																<div class="form-group">
																	<input 
																		type="tel" 
																		name="companyFax" 
																		id="companyFax" 
																		value="<?=$companyFax?>"
																		class="form-control" 
																		data-rule-minlength="10" 
																		data-rule-maxlength="10" 
																		data-rule-number="true">
																	<label for="companyFax" class="control-label">Faks</label>
																	<p class="help-block"><a href="#" target="_blank">+902121234567</a></p>
																</div>
															</div>
														</div>
													</div>
													<div class="tab-pane" id="step4">
														<br/><br/>
														<div class="row">
															<div class="col-sm-12">
																<div class="form-group">
																	<input 
																		type="text" 
																		name="companyCoordinate" 
																		id="companyCoordinate" 
																		value="<?=$companyCoordinate?>" 
																		class="form-control"  
																		>
																	<label for="companyCoordinate" class="control-label">Koordinatlarınız *</label>
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-12">
																	<div class="form-group">
																		<p class="help-block">
                                                                            <a href="#locationSelectInfoModal"
                                                                               data-target="#locationSelectInfoModal"
                                                                                 data-toggle="modal"
                                                                               class="text-ultra-bold text-danger"
                                                                                 id="findCoordinate">Koordinatları nasıl alacağınızı öğrenin, haritadan konumunuzu bulun
                                                                            </a>
                                                                        </p>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="form-group">
																<textarea 
																	name="companyMap" 
																	id="companyMap" 
																	class="form-control" 
																	rows="3"><?=$companyMap?></textarea>
																<label for="companyMap">Google Haritalar Kodu (iframe)</label>
																<p class="help-block"><a
                                                                            href="#mapSelectInfoModal"
                                                                            data-target="#mapSelectInfoModal"
                                                                            data-toggle="modal"
                                                                            class=" text-danger text-ultra-bold"
                                                                            >Kodu nasıl alacağınızı öğrenin
                                                                    </a></p>
															</div>
														</div><br>
														<div class="card-actionbar">
															<div class="card-actionbar-row">
																<button id="AddCompanyButton" type="button" class="btn btn-primary ink-reaction">GÖNDER</button>
															</div>
														</div>
													</div>
												</div>
												<ul class="pager wizard">
													<li class="previous first"><a class="btn-raised" href="javascript:void(0);">İlk</a></li>
													<li class="previous"><a class="btn-raised" href="javascript:void(0);">Önceki</a></li>
													<li class="next last"><a class="btn-raised" href="javascript:void(0);">Son</a></li>
													<li class="next"><a class="btn-raised" href="javascript:void(0);">Sonraki</a></li>
												</ul>
											</form>
										</div>
									</div>
								</div>
								<em class="text-caption">Dikkat (*) Yıldızlı Bilgiler GOOGLE, GOOLE HARİTALAR, GOOGLE İŞLETMEM AÇISINDAN ÖNEMLİDİR! Seçilen dil için yayınlanacaktır!</em>
							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <!-- Lokasyon Seçimi Bilgilendirme Modalı -->
            <div class="modal fade" id="locationSelectInfoModal" tabindex="-1" role="dialog" aria-labelledby="locationSelectInfoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title" id="locationSelectInfoModalLabel">Koordina Nasıl Seçilir?</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <p><strong># Google Haritalar'dan Koonumunuzu Bulun</strong><br>
                                <a href="https://maps.google.com" class="text-primary border-lg" target="_blank">maps.google.com</a>'adresine gidin.<br>
                                Arama alanına adresinizi yazın ve aratın.<br>
                                Konumunuzun olduğu noktaya sağ tıklayın ve açılan menüden en üstte yer alan numaraların olduğu alanı tıklayın.<br>
                                Koordinatlarınızı kopyalamış olacaksınız.<br>
                                Bu sayfadaki Koordinat alanına girin.
                            </p>
                            <img src="/_y/assets/img/location_select.jpg" width="100%" style="width: 100%; height: auto">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Harita Kodu Alma Bilgilendirme Modalı -->
            <div class="modal fade" id="mapSelectInfoModal" tabindex="-1" role="dialog" aria-labelledby="mapSelectInfoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title" id="mapSelectInfoModalLabel">Koordina Nasıl Seçilir?</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <p><strong># Google Haritalar'dan Harita Kodunuzu Alın</strong><br>
                                <a href="https://maps.google.com" class="text-primary border-lg" target="_blank">maps.google.com</a>'adresine gidin.<br>
                                Arama alanına adresinizi yazın ve aratın.<br>
                                Konumunuzun olduğu noktaya bir kere tıklayın ve soldaki menüden <strong>Paylaş</strong> butonuna tıklayın.<br>
                                Açılan pencereden <strong>Harita Yerleştirme</strong> sekmesine tıklayın.<br>
                                <strong>HTML'Yİ KOPYALA</strong> bağlantısına tıklayın.<br>
                                Kopyaladığınız kodu bu sayfadaki Google Haritalar Kodu alanına yapıştırın.
                            </p>
                            <img src="/_y/assets/img/map_select_1.jpg" width="100%" style="width: 100%; height: auto">
                            <img src="/_y/assets/img/map_select_2.jpg" width="100%" style="width: 100%; height: auto">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- alert uyarıları için modal oluşturalım -->
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
        <!-- BEGIN JAVASCRIPT -->
        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

        <script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
        <script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>

        <script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>

        <script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
        <script src="/_y/assets/js/libs/wizard/jquery.bootstrap.wizard.min.js"></script>

        <script src="/_y/assets/js/libs/select2/select2.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/core/demo/Demo.js"></script>
        <script src="/_y/assets/js/core/demo/DemoFormWizard.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
        <!-- END JAVASCRIPT -->
		<script>
            $("#addCompanySettingsphp").addClass("active");

            function getLocation(source,target){
                var id = $("#"+source).val();
                var targetSelect = $("#"+target);

                var action = "getCity";

                if(target == "companyCountyID") action = "getCounty";
                if(target == "companyAreaID") action = "getArea";
                if(target == "companyNeighbourhoodID") action = "getNeighborhood";

                $.ajax({
                    url: "/App/Controller/Admin/AdminLocationController.php",
                    type: "POST",
                    data: {id: id, action: action},
                    success: function(response){
                        console.log(response);
                        var data = JSON.parse(response);

                        if(data.status == "success") {
                            targetSelect.empty();
                            var locations = data.location;
                            var option = '<option value="">Seçiniz</option>';
                            targetSelect.append(option);
                            for (var i = 0; i < locations.length; i++) {
                                var location = locations[i];
                                var locationID = location.id;
                                var locationName = location.name;

                                option = '<option value="' + locationID + '">' + locationName + '</option>';
                                targetSelect.append(option);
                            }
                        }
                    }
                });
            }

            function getPostalCode(){
                var neighbourhoodID = $("#companyNeighbourhoodID").val();

                $.ajax({
                    url: "/App/Controller/Admin/AdminLocationController.php",
                    type: "POST",
                    data: {id: neighbourhoodID, action: "getPostalCode"},
                    success: function(response){
                        console.log(response);
                        var data = JSON.parse(response);

                        if(data.status == "success") {
                            var postalCode = data.postalCode;
                            $("#companyPostalCode").val(postalCode);
                        }
                    }
                });
            }

            function selectTurkey(){
                $("#companyCityID").removeClass("hidden");
                $("#companyCity").addClass("hidden");
                $("#companyCountyID").removeClass("hidden");
                $("#companyCounty").addClass("hidden");
                $("#companyAreaID").removeClass("hidden");
                $("#companyArea").addClass("hidden");
                $("#companyNeighbourhoodID").removeClass("hidden");
                $("#companyNeighbourhood").addClass("hidden");

                getLocation("companyCountryID","companyCityID");
            }

            function showAlertModal(message,status="danger"){
                if(status == "danger"){
                    $("#alertModal .card-head").removeClass("style-success");
                    $("#alertModal .card-head").addClass("style-danger");
                }
                else{
                    $("#alertModal .card-head").removeClass("style-danger");
                    $("#alertModal .card-head").addClass("style-success");
                }
                $("#alertMessage").html(message);
                $("#alertModal").modal("show");
            }

            $(document).ready(function(){
                setTimeout(function(){
                    $("#unvanTab").click();
                },1000);

                <?php if($companyCountry == 212 && $companyID == 0): ?>
                selectTurkey();
                <?php endif; ?>

                //ülke seçildiğinde 212 olursa şehirleri getirelim, tüm bölge isimlerini gizleyelim, select boxları gösterelim
                $(document).on("change", "#companyCountryID", function(){
                    var countryID = $(this).val();
                    if(countryID == 212){
                        selectTurkey();
                    }
                    else{
                        $("#companyCityID").addClass("hidden");
                        $("#companyCityID").empty();
                        $("#companyCity").removeClass("hidden");

                        $("#companyCountyID").addClass("hidden");
                        $("#companyCountyID").empty();
                        $("#companyCounty").removeClass("hidden");

                        $("#companyAreaID").addClass("hidden");
                        $("#companyAreaID").empty();
                        $("#companyArea").removeClass("hidden");

                        $("#companyNeighbourhoodID").addClass("hidden");
                        $("#companyNeighbourhoodID").empty();
                        $("#companyNeighbourhood").removeClass("hidden");
                    }
                });

                //şehir seçildiğinde
                $(document).on("change", "#companyCityID", function(){
                    getLocation("companyCityID","companyCountyID");
                });

                //ilçe seçildiğinde
                $(document).on("change", "#companyCountyID", function(){
                    getLocation("companyCountyID","companyAreaID");
                });

                //semt seçildiğinde
                $(document).on("change", "#companyAreaID", function(){
                    getLocation("companyAreaID","companyNeighbourhoodID");
                });

                //mahalle seçildiğinde
                $(document).on("change", "#companyNeighbourhoodID", function(){
                    getPostalCode();
                });

                //dil değişirse sayfayı yeniden yükleyelim
                $(document).on("change", "#languageID", function(){
                    var languageID = $(this).val();
                    window.location.href = "/_y/s/s/firmabilgileri/AddCompanySettings.php?languageID="+languageID;
                });

                $(document).on("click", "#AddCompanyButton", function() {
                    var companyID = $("#companyID").val();
                    var languageID = $("#languageID").val();
                    var companyShortName = $("#companyShortName").val();
                    var companyName = $("#companyName").val();
                    var companyTaxOffice = $("#companyTaxOffice").val();
                    var companyTaxNumber = $("#companyTaxNumber").val();
                    var companyCountryID = $("#companyCountryID").val();
                    var companyCityID = $("#companyCityID").val();
                    var companyCountyID = $("#companyCountyID").val();
                    var companyAreaID = $("#companyAreaID").val();
                    var companyNeighbourhoodID = $("#companyNeighbourhoodID").val();
                    var companyPostalCode = $("#companyPostalCode").val();
                    var companyAddress = $("#companyAddress").val();
                    var companyEmail = $("#companyEmail").val();
                    var companyGsm = $("#companyGsm").val();
                    var companyPhone = $("#companyPhone").val();
                    var companyFax = $("#companyFax").val();
                    var companyCoordinate = $("#companyCoordinate").val();
                    var companyMap = $("#companyMap").val();
                    var companyCountryCode = $("#companyCountryID option:selected").data("contrycode");

                    if (companyShortName == "" || companyName == "") {
                        showAlertModal("Kısa Ad ve Ünvan boş olamaz!");
                        return;
                    }

                    if (companyCountryID == 212) {
                        if (companyCityID == 0 || companyCountyID == 0 || companyAreaID == 0 || companyNeighbourhoodID == 0 || companyAddress == "" || companyPostalCode == "") {
                            showAlertModal("Şehir, İlçe, Semt, Mahalle, Adres, Posta Kodu boş olamaz!");
                            return;
                        }
                    }
                    else {
                        if (companyCity == "" || companyAddress == "" || companyPostalCode == "") {
                            showAlertModal("Şehir, Adres, Posta Kodu boş olamaz!");
                            return;
                        }

                        companyCityID = $("#companyCity").val();
                        companyCountyID = $("#companyCounty").val();
                        companyAreaID = $("#companyArea").val();
                        companyNeighbourhoodID = $("#companyNeighbourhood").val();
                    }

                    if (companyEmail == "" || companyGsm == "") {
                        showAlertModal("E-Posta ve GSM boş olamaz!");
                        return;
                    }

                    var action = "addCompany";
                    if(companyID != 0) action = "updateCompany";

                    var parentCompanyID = $("#parentCompanyID").val();
                    if(parentCompanyID > 0){
                        action = "addBranch";
                        if(companyID > 0){
                            action = "updateBranch";
                        }
                    }

                    $.ajax({
                        url: "/App/Controller/Admin/AdminCompanyController.php",
                        type: "POST",
                        data: {
                            companyID: companyID,
                            parentCompanyID: parentCompanyID,
                            languageID: languageID,
                            companyShortName: companyShortName,
                            companyName: companyName,
                            companyTaxOffice: companyTaxOffice ?? "",
                            companyTaxNumber: companyTaxNumber ?? "",
                            companyCountryID: companyCountryID,
                            companyCityID: companyCityID,
                            companyCountyID: companyCountyID,
                            companyAreaID: companyAreaID,
                            companyNeighbourhoodID: companyNeighbourhoodID,
                            companyPostalCode: companyPostalCode,
                            companyAddress: companyAddress,
                            companyEmail: companyEmail,
                            companyGsm: companyGsm,
                            companyPhone: companyPhone ?? "",
                            companyFax: companyFax ?? "",
                            companyCoordinate: companyCoordinate,
                            companyMap: companyMap,
                            companyCountryCode: companyCountryCode,
                            action: action
                        },
                        success: function(response){
                            console.log(response);
                            var data = JSON.parse(response);
                            var message = data.message;
                            if(data.status === "success") {
                                showAlertModal("Firma bilgileriniz başarıyla güncellendi!","success");
                                //1,5 saniye sonra sayfayı yenileyelim
                                setTimeout(function(){
                                    window.location.href = "/_y/s/s/firmabilgileri/AddCompanySettings.php?languageID="+languageID;
                                },1500);
                            }
                            else{
                                showAlertModal(message,"danger");
                            }
                        }
                    });

                });
            });
		</script>
	</body>
</html>
