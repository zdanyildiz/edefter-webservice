<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Bayiler";
$formbaslik="Bayiler";
$butonisim="EKLE";

$f_uyeid=S(f("uyeid"));
$f_uyeadsoyad=f("uyeadsoyad");
$f_uyeeposta=f("uyeeposta");
$f_uyesifre=f("uyesifre");
$f_uyetelefon=f("uyetelefon");
$f_uyetcno=f("uyetcno");
$f_uyeaciklama=f("uyeaciklama");
$f_uyefaturaad=f("uyefaturaad");
$f_uyefaturavergidairesi=f("uyefaturavergidairesi");
$f_uyefaturavergino=f("uyefaturavergino");
$f_uyeaktif=f("uyeaktif");

$f_adresid=S(f("adresid"));
$f_adresad=f("adresad");
$f_adressoyad=f("adressoyad");
$f_adrestcno=f("adrestcno");
$f_adresbaslik=f("adresbaslik");
$f_adresulkeid=f("adresulkeid");
$f_adressehirid=f("adressehirid");
$f_adressemtid=f("adressemtid");
$f_adresilceid=f("adresilceid");
$f_adresmahalleid=f("adresmahalleid");
$f_adrespostakod=f("adrespostakod");
$f_adresacik=f("adresacik");
$f_adrestelefon=f("adrestelefon");
if(S(f("yeniadresekle"))==1 && $f_uyeid!=0)
{
    $f_yeniadresid=f("yeniadresid");
    $f_yeniadresad=f("yeniadresad");
    $f_yeniadressoyad=f("yeniadressoyad");
    $f_yeniadrestcno=f("yeniadrestcno");
    $f_adresbaslik=f("yeniadresbaslik");
    $f_adresulkeid=f("yeniadresulkeid");
    $f_adrespostakod=f("yeniadrespostakod");
    $f_adresacik=f("yeniadresacik");
    $f_adrestelefon=f("yeniadrestelefon");
    if(S($f_adresulkeid)!=212)
    {
        $f_adressehirid=f("yeniadressehir");
        $f_adressemtid=f("yeniadressemt");
        $f_adresilceid=f("yeniadresilce");
        $f_adresmahalleid=f("yeniadresmahalle");
    }else{
        $f_adressehirid=f("yeniadressehirid");
        $f_adressemtid=f("yeniadressemtid");
        $f_adresilceid=f("yeniadresilceid");
        $f_adresmahalleid=f("yeniadresmahalleid");
    }
    $f_adresulkekod=teksatir("SELECT * FROM yerulke WHERE CountryID='". $f_adresulkeid ."'","PhoneCode");
    $tablo="uyeadres";
    if(S($f_yeniadresid)==0)
    {
        $sutunlar="uyeid,adresbaslik,adresad,adressoyad,adrestcno,adresulke,adressehir,adresilce,adressemt,adresmahalle,postakod,adresacik,adrestelefon,adresulkekod,adressil";
        $degerler=$f_uyeid."|*_".$f_adresbaslik."|*_".$f_adresad."|*_".$f_adressoyad."|*_".$f_adrestcno."|*_".$f_adresulkeid."|*_".$f_adressehirid."|*_".$f_adresilceid."|*_".$f_adressemtid."|*_".$f_adresmahalleid."|*_".$f_adrespostakod."|*_".$f_adresacik."|*_".$f_adrestelefon."|*_".$f_adresulkekod."|*_0";
        ekle($sutunlar,$degerler,$tablo,62);
    }else{
        $sutunlar="adresbaslik,adresad,adressoyad,adrestcno,adresulke,adressehir,adresilce,adressemt,adresmahalle,postakod,adresacik,adrestelefon,adresulkekod";
        $degerler=$f_adresbaslik."|*_".$f_adresad."|*_".$f_adressoyad."|*_".$f_adrestcno."|*_".$f_adresulkeid."|*_".$f_adressehirid."|*_".$f_adresilceid."|*_".$f_adressemtid."|*_".$f_adresmahalleid."|*_".$f_adrespostakod."|*_".$f_adresacik."|*_".$f_adrestelefon."|*_".$f_adresulkekod;
        guncelle($sutunlar,$degerler,$tablo," adresid='".$f_yeniadresid."' ",62);
    }
}
if(S(f("uyeekle"))==1 && !BosMu($f_uyeadsoyad))
{
    if(S($f_adresulkeid)!=212)
    {
        $f_adressehirid=f("adressehir");
        $f_adressemtid=f("adressemt");
        $f_adresilceid=f("adresilce");
        $f_adresmahalleid=f("adresmahalle");
    }
    $f_adresulkekod=teksatir("SELECT * FROM yerulke WHERE CountryID='". $f_adresulkeid ."'","PhoneCode");
    $simdi=date("Y-m-d H:i:s");
    $sutunlar="uyeguncellemetarih,
		uyetip,
		uyeadsoyad,
		uyeeposta,
		uyesifre,
		uyetelefon,
		uyetcno,
		uyeaciklama,
		uyefaturaad,
		uyefaturavergidairesi,
		uyefaturavergino,
		uyeaktif,
		uyesil";
    $degerler=$simdi."|*_1|*_".
        $f_uyeadsoyad."|*_".
        sifrele($f_uyeeposta,$anahtarkod)."|*_".
        sifrele($f_uyesifre,$anahtarkod)."|*_".
        sifrele($f_uyetelefon,$anahtarkod)."|*_".
        $f_uyetcno."|*_".
        $f_uyeaciklama."|*_".
        $f_uyefaturaad."|*_".
        $f_uyefaturavergidairesi."|*_".
        $f_uyefaturavergino."|*_".
        $f_uyeaktif.
        "|*_0";
    $tablo="uye";

    if(dogrula("uye","uyetelefon='". sifrele($f_uyetelefon,$anahtarkod) ."' and uyetip='1' ") && S($f_uyeid)==0 )
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu telefon ( $f_uyeadsoyad adına $f_uyetelefon ) zaten kayıtlı. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/bayiler/bayiliste.php'> >Bayi Listesine git <</a><br>";
    }
    if(dogrula("uye","uyeeposta='". sifrele($f_uyeeposta,$anahtarkod) ."' and uyetip='1' ") && S($f_uyeid)==0 )
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu eposta ( $f_uyeadsoyad adına $f_uyeeposta ) zaten kayıtlı. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/bayiler/bayiliste.php'> >uye Listesine git <</a><br>";
    }
    if(S($f_uyeid)==0 && $formhata==0)
    {
        $f_benzersizid=SifreUret(20,2);
        ekle($sutunlar.",benzersizid,uyeolusturmatarih",$degerler."|*_".$f_benzersizid."|*_".$simdi,$tablo,59);
        $f_uyeid = teksatir(" Select uyeid from uye Where benzersizid='". $f_benzersizid ."'","uyeid");

        $tablo="uyeadres";
        $sutunlar="uyeid,adresbaslik,adresad,adressoyad,adrestcno,adresulke,adressehir,adresilce,adressemt,adresmahalle,postakod,adresacik,adrestelefon,adresulkekod,adressil";
        $degerler=$f_uyeid."|*_".$f_adresbaslik."|*_".$f_adresad."|*_".$f_adressoyad."|*_".$f_adrestcno."|*_".$f_adresulkeid."|*_".$f_adressehirid."|*_".$f_adresilceid."|*_".$f_adressemtid."|*_".$f_adresmahalleid."|*_".$f_adrespostakod."|*_".$f_adresacik."|*_".$f_adrestelefon."|*_".$f_adresulkekod."|*_0";
        ekle($sutunlar,$degerler,$tablo,62);
        $f_adresid=teksatir("Select adresid From uyeadres Where uyeid='". $f_uyeid ."'","adresid");
    }
    elseif($formhata==0 && dogrula("uye","uyeeposta='". sifrele($f_uyeeposta,$anahtarkod) ."' and uyetip='1' and uyesil='0' and uyeid!='".$f_uyeid."' "))
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu eposta ( $f_uyeeposta ) zaten kayıtlı. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/bayiler/bayiliste.php'> >Bayi Listesine git <</a><br>";
    }
    elseif($formhata==0 && dogrula("uye","uyetelefon='". sifrele($f_uyetelefon,$anahtarkod) ."' and uyetip='1' and uyesil='0' and uyeid!='".$f_uyeid."' "))
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu telefon ( $f_uyetelefon ) zaten kayıtlı. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/bayiler/bayiliste.php'> >Bayi Listesine git <</a><br>";
    }
    elseif($formhata==0)
    {
        guncelle($sutunlar,$degerler,$tablo," uyeid='". $f_uyeid ."' ",59);
        $tablo="uyeadres";
        $sutunlar="adresbaslik,adresad,adressoyad,adrestcno,adresulke,adressehir,adresilce,adressemt,adresmahalle,postakod,adresacik,adrestelefon,adresulkekod";
        $degerler=$f_adresbaslik."|*_".$f_adresad."|*_".$f_adressoyad."|*_".$f_adrestcno."|*_".$f_adresulkeid."|*_".$f_adressehirid."|*_".$f_adresilceid."|*_".$f_adressemtid."|*_".$f_adresmahalleid."|*_".$f_adrespostakod."|*_".$f_adresacik."|*_".$f_adrestelefon."|*_".$f_adresulkekod;
        guncelle($sutunlar,$degerler,$tablo," adresid='".$f_adresid."' ",62);
    }
}
if(S(q("uyeid"))!=0)
{
    if(dogrula("uye","uyeid='". q("uyeid") ."'"))
    {
        $butonisim="GÜNCELLE";
        $f_uyeid=q("uyeid");
        $uye_s="
			SELECT
				uyeadsoyad,uyeeposta,uyesifre,uyetelefon,uyetcno,uyeaciklama,uyefaturaad,uyefaturavergidairesi,uyefaturavergino,uyeaktif,
				uyeadres.adresid,adresbaslik,adresad,adressoyad,adrestcno,adresulke,adressehir,adresilce,adressemt,adresmahalle,postakod,adresacik,adrestelefon
			FROM 
				uye
					left join uyeadres on
						uyeadres.uyeid=uye.uyeid
			WHERE 
				uyetip='1' and uye.uyeid='".$f_uyeid."'
			ORDER BY uyeadres.adresid DESC";
        if($data->query($uye_s))
        {
            $uye_v=$data->query($uye_s);
            unset($uye_s);
            if($uye_v->num_rows>0)
            {
                while ($uye_t=$uye_v->fetch_assoc())
                {
                    $f_uyeadsoyad=$uye_t["uyeadsoyad"];
                    $f_uyeeposta=coz($uye_t["uyeeposta"],$anahtarkod);
                    $f_uyesifre=coz($uye_t["uyesifre"],$anahtarkod);
                    if(!BosMu($uye_t["uyetelefon"]))
                    {
                        $f_uyetelefon=coz($uye_t["uyetelefon"],$anahtarkod);
                    }
                    else{$uye_t["uyetelefon"]="";}
                    $f_uyetcno=$uye_t["uyetcno"];
                    $f_uyeaciklama=$uye_t["uyeaciklama"];
                    $f_uyefaturaad=$uye_t["uyefaturaad"];
                    $f_uyefaturavergidairesi=$uye_t["uyefaturavergidairesi"];
                    $f_uyefaturavergino=$uye_t["uyefaturavergino"];
                    $f_uyeaktif=$uye_t["uyeaktif"];
                    $f_adresid=$uye_t["adresid"];
                    $f_adresbaslik=$uye_t["adresbaslik"];
                    $f_adresad=$uye_t["adresad"];
                    $f_adressoyad=$uye_t["adressoyad"];
                    $f_adrestcno=$uye_t["adrestcno"];
                    $f_adresulkeid=$uye_t["adresulke"];
                    $f_adressehirid=$uye_t["adressehir"];
                    $f_adresilceid=$uye_t["adresilce"];
                    $f_adressemtid=$uye_t["adressemt"];
                    $f_adresmahalleid=$uye_t["adresmahalle"];
                    $f_adrespostakod=$uye_t["postakod"];
                    $f_adresacik=$uye_t["adresacik"];
                    $f_adrestelefon=$uye_t["adrestelefon"];
                }
                unset($uye_t);
            }
            unset($uye_v);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title><?=$sayfabaslik?></title>
		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<!-- BEGIN HEADER-->
		<?php require_once($anadizin."/_y/s/b/header.php");?>
		<!-- END HEADER-->
		<!-- BEGIN BASE-->
		<div id="base">
			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="btn ink-reaction btn-raised btn-primary disabled">Ayarlar</li>
							<li class="btn ink-reaction btn-raised btn-primary disabled"><?=$sayfabaslik?></li>
							<li class="active"><a href="/_y/s/s/bayiler/bayiliste.php" class="btn ink-reaction btn-raised btn-primary">Bayi Liste</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-md-12">
                                <form name="formanaliz" id="formanaliz" class="form form-validation form-validate" role="form" method="post">
                                    <input type="hidden" name="uyeekle" value="1">
                                    <input type="hidden" name="uyeid" value="<?=$f_uyeid?>">
                                    <div class="card">
                                        <div class="card-head style-primary form-inverse">
                                            <header><?=$formbaslik?></header>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
                                                    <div class="row card-outlined margin-bottom-xl border-gray">
                                                        <div class="card-head">
                                                            <header class="text-s">KİŞİSEL BİLGİLER</header>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="uyeadsoyad"
                                                                        id="uyeadsoyad"
                                                                        value="<?=$f_uyeadsoyad?>"
                                                                        placeholder="Bayi Yetkili Adını Soyadını Yazın" required aria-required="true" >
                                                                <label for="uyeadsoyad">Bayi Yetkili Adını Soyadını Yazın</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="uyesifre"
                                                                        id="uyesifre"
                                                                        value="<?=$f_uyesifre?>"
                                                                        data-rule-minlength="6"
                                                                        maxlength="20"
                                                                        placeholder="Bayi Şifresi Yazın" required aria-required="true" >
                                                                <label for="uyesifre">Bayi Şifresi Yazın</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="uyetcno"
                                                                        id="uyetcno"
                                                                        value="<?=$f_uyetcno?>"
                                                                        data-rule-digits="true"
                                                                        data-rule-minlength="11"
                                                                        maxlength="11"
                                                                        placeholder="TC No" required aria-required="true" >
                                                                <label for="uyetcno">TC No</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="uyetelefon"
                                                                        id="uyetelefon"
                                                                        value="<?=$f_uyetelefon?>"
                                                                        data-rule-minlength="10"
                                                                        maxlength="10"
                                                                        data-rule-digits="true"
                                                                        placeholder="Bayi Cep Telefonu Yazın (5601234567)"
                                                                        required aria-required="true" >
                                                                <label for="uyetelefon">Bayi Cep Telefonu Yazın (5601234567)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="email"
                                                                        class="form-control"
                                                                        name="uyeeposta"
                                                                        id="uyeeposta"
                                                                        value="<?=$f_uyeeposta?>"
                                                                        placeholder="Bayi Eposta Yazın" required aria-required="true" >
                                                                <label for="uyeeposta">Bayi Eposta Yazın</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row card-outlined margin-bottom-xl border-gray">
                                                        <div class="card-head">
                                                            <header class="text-s">KURUMSAL FATURA BİLGİLERİ</header>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
																<textarea
                                                                        name="uyefaturaad"
                                                                        id="uyefaturaad"
                                                                        class="form-control"
                                                                        rows="2"
                                                                        maxlength="255"
                                                                        style="
																		background-color:#efefef;
																		width:96%;
																		padding: 10px 1% 10px 1%;
																		margin:10px 0 0 0;
																		border:solid 1px #eee"
                                                                ><?=ltrim($f_uyefaturaad)?></textarea>
                                                                <label for="uyefaturaad">Bayi Fatura Ünvan</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="uyefaturavergidairesi"
                                                                        id="uyesifre"
                                                                        value="<?=$f_uyefaturavergidairesi?>"
                                                                        data-rule-minlength="2"
                                                                        maxlength="255"
                                                                        placeholder="Bayi Vergi Dairesi" required aria-required="true" >
                                                                <label for="uyefaturavergidairesi">Bayi Vergi Dairesi </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="uyefaturavergino"
                                                                        id="uyefaturavergino"
                                                                        value="<?=$f_uyefaturavergino?>"
                                                                        data-rule-digits="true"
                                                                        data-rule-minlength="10"
                                                                        maxlength="11"
                                                                        placeholder="Bayi Vergi/TC No" required aria-required="true" >
                                                                <label for="uyefaturavergino">Bayi Vergi/TC No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
																<textarea
                                                                        name="uyeaciklama"
                                                                        id="uyeaciklama"
                                                                        class="form-control"
                                                                        rows="2"
                                                                        maxlength="255"
                                                                        style="
																		background-color:#efefef;
																		width:96%;
																		padding: 10px 1% 10px 1%;
																		margin:10px 0 0 0;
																		border:solid 1px #eee"
                                                                ><?=ltrim($f_uyeaciklama)?></textarea>
                                                                <label for="uyeaciklama">Bayi Not</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
                                                                <label class="col-sm-3 control-label">Aktif mi</label>
                                                                <div class="col-sm-12">
                                                                    <label class="radio-inline radio-styled">
                                                                        <input type="radio" name="uyeaktif" value="1" <?php if(S($f_uyeaktif)==1)echo'checked'; ?>><span>Aktif</span>
                                                                    </label>
                                                                    <label class="radio-inline radio-styled">
                                                                        <input type="radio" name="uyeaktif" value="0"><span>Pasif</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-head style-primary form-inverse">
                                            <header>Bayi Adres</header>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="row card-outlined margin-bottom-xl border-gray">
                                                        <div class="card-head">
                                                            <header class="text-s">ADRES TEMEL BİLGİLER</header>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input type="hidden" name="adresid" value="<?=$f_adresid?>">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="adresbaslik"
                                                                        id="adresbaslik"
                                                                        value="<?=$f_adresbaslik?>"
                                                                        data-rule-minlength="2"
                                                                        maxlength="100"
                                                                        placeholder="Adres Başlığı (Ev, İş)"
                                                                        required aria-required="true" >
                                                                <label for="adresbaslik">Adres Başlığı (Ev, İş)</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="adresad"
                                                                        id="adresad"
                                                                        value="<?=$f_adresad?>"
                                                                        placeholder="Adres Yetkili Adını Yazın" required aria-required="true" >
                                                                <label for="adresad">Adres Yetkili Adını Yazın</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="adressoyad"
                                                                        id="adressoyad"
                                                                        value="<?=$f_adressoyad?>"
                                                                        placeholder="Adres Yetkili Soyadını Yazın" required aria-required="true" >
                                                                <label for="adressoyad">Adres Yetkili Soyadını Yazın</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group floating-label">
                                                                <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        name="adrestcno"
                                                                        id="adrestcno"
                                                                        value="<?=$f_adrestcno?>"
                                                                        data-rule-digits="true"
                                                                        data-rule-minlength="11"
                                                                        maxlength="11"
                                                                        placeholder="Adres Yetkili TC No" required aria-required="true" >
                                                                <label for="adrestcno">Adres Yetkili TC No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <select id="adresulkeid" name="adresulkeid" class="form-control" required>
                                                                    <?php
                                                                    $ulke_d=0; $ulke_v=""; $ulke_s="";
                                                                    $ulke_s="SELECT CountryID,CountryName,BinaryCode FROM yerulke ORDER BY CountryName ASC";
                                                                    $ulke_v=$data->query($ulke_s);
                                                                    if($ulke_v->num_rows>0)$ulke_d=1;
                                                                    unset($ulke_s);
                                                                    if($ulke_d==1)
                                                                    {
                                                                        while($ulke_t=$ulke_v->fetch_assoc())
                                                                        {
                                                                            $l_ulkeid=$ulke_t["CountryID"];
                                                                            $l_ulkeAd=$ulke_t["CountryName"];
                                                                            $l_ulkeKisa=$ulke_t["BinaryCode"];
                                                                            ?>
                                                                            <option value="<?=$l_ulkeid?>" <?php if($l_ulkeid==$f_adresulkeid)echo "selected";?>><?=$l_ulkeAd?>(<?=$l_ulkeKisa?>)</option><?php
                                                                        }
                                                                        unset($ulke_t,$ulke_v);
                                                                    }
                                                                    unset($ulke_v);
                                                                    ?>
                                                                </select>
                                                                <label for="adresulkeid" class="control-label">Ülke *</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <select
                                                                        id="adressehirid"
                                                                        name="adressehirid"
                                                                        class="form-control"
                                                                    <?php if(S($f_adressehirid)==0) echo 'style="display: none;"'; ?>>
                                                                    <option value="0">Şehir Seçin</option>
                                                                    <?php
                                                                    if(S($f_adressehirid)!=0)
                                                                    {
                                                                        $sehir_d=0; $sehir_v=""; $sehir_s="";
                                                                        $sehir_s="SELECT CityID,CityName FROM yersehir WHERE CountryID='". $f_adresulkeid ."' ORDER BY CityName ASC";
                                                                        $sehir_v=$data->query($sehir_s);
                                                                        if($sehir_v->num_rows>0)$sehir_d=1;
                                                                        unset($sehir_s);
                                                                        if($sehir_d==1)
                                                                        {
                                                                            while($sehir_t=$sehir_v->fetch_assoc())
                                                                            {
                                                                                $l_sehirid=$sehir_t["CityID"];
                                                                                $l_sehirAd=$sehir_t["CityName"];
                                                                                ?><option value="<?=$l_sehirid?>" <?php if($l_sehirid==$f_adressehirid)echo "selected"; ?>><?=$l_sehirAd?></option><?php
                                                                            }
                                                                            unset($sehir_t,$sehir_v);
                                                                        }
                                                                        unset($sehir_v);
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <input
                                                                        type="text"
                                                                        name="adressehir"
                                                                        id="adressehir"
                                                                        class="form-control"
                                                                        value="<?php if(S($f_adressehirid)==0)echo $f_adressehirid;?>"
                                                                        placeholder="Şehir adı girin"
                                                                    <?php if(S($f_adressehirid)!=0)echo 'style="display: none;"'; ?>
                                                                >
                                                                <label for="adressehirid" class="control-label">Şehir *</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <select
                                                                        id="adresilceid"
                                                                        name="adresilceid"
                                                                        class="form-control"
                                                                        required
                                                                    <?php if(S($f_adresilceid)==0) echo 'style="display: none;"'; ?>>
                                                                    <option value="0">İlçe Seçin</option>
                                                                    <?php
                                                                    if(S($f_adresilceid)!=0)
                                                                    {
                                                                        $ilce_d=0; $ilce_v=""; $ilce_s="";
                                                                        $ilce_s="SELECT CountyID,CountyName FROM yerilce WHERE CityID='". $f_adressehirid ."' ORDER BY CountyName ASC";
                                                                        $ilce_v=$data->query($ilce_s);
                                                                        if($ilce_v->num_rows>0)$ilce_d=1;
                                                                        unset($ilce_s);
                                                                        if($ilce_d==1)
                                                                        {
                                                                            while($ilce_t=$ilce_v->fetch_assoc())
                                                                            {
                                                                                $l_ilceid=$ilce_t["CountyID"];
                                                                                $l_ilceAd=$ilce_t["CountyName"];
                                                                                ?><option value="<?=$l_ilceid?>" <?php if($l_ilceid==$f_adresilceid)echo"selected"; ?>><?=$l_ilceAd?></option><?php
                                                                            }
                                                                            unset($ilce_t,$ilce_v);
                                                                        }
                                                                        unset($ilce_v);
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <input
                                                                        type="text"
                                                                        name="adresilce"
                                                                        id="adresilce"
                                                                        class="form-control"
                                                                        value="<?php if(S($f_adresilceid)==0)echo $f_adresilceid;?>"
                                                                        placeholder="İlçe adı girin"
                                                                    <?php if(S($f_adresilceid)!=0)echo 'style="display: none;"';?> >
                                                                <label for="adresilceid" class="control-label">İlçe *</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <select
                                                                        id="adressemtid"
                                                                        name="adressemtid"
                                                                        class="form-control"
                                                                        required
                                                                    <?php if(S($f_adressemtid)==0)echo 'style="display: none;"'; ?>>
                                                                    <option value="0">Semt Seçin</option>
                                                                    <?php
                                                                    if(S($f_adressemtid)!=0)
                                                                    {
                                                                        $semt_d=0; $semt_v=""; $semt_s="";
                                                                        $semt_s="SELECT AreaID,AreaName FROM yersemt WHERE CountyID='". $f_adresilceid ."' ORDER BY AreaName ASC";
                                                                        $semt_v=$data->query($semt_s);
                                                                        if($semt_v->num_rows>0)$semt_d=1;
                                                                        unset($semt_s);
                                                                        if($semt_d==1)
                                                                        {
                                                                            while($semt_t=$semt_v->fetch_assoc())
                                                                            {
                                                                                $l_semtid=$semt_t["AreaID"];
                                                                                $l_semtAd=$semt_t["AreaName"];
                                                                                ?>
                                                                                <option value="<?=$l_semtid?>" <?php if($l_semtid==$f_adressemtid)echo"selected"; ?>><?=$l_semtAd?></option><?php
                                                                            }
                                                                            unset($semt_t,$semt_v);
                                                                        }
                                                                        unset($semt_v);
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <input
                                                                        type="text"
                                                                        name="adressemt"
                                                                        id="adressemt"
                                                                        class="form-control"
                                                                        value="<?php if(S($f_adressemtid)==0)echo $f_adressemtid;?>"
                                                                        placeholder="Semt adı girin"
                                                                    <?php if(S($f_adressemtid)!=0)echo 'style="display: none;"';?> >
                                                                <label for="adressemtid" class="control-label">Semt *</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <select
                                                                        id="adresmahalleid"
                                                                        name="adresmahalleid"
                                                                        class="form-control"
                                                                        required
                                                                    <?php
                                                                    if(S($f_adresmahalleid)==0)echo 'style="display: none;"';
                                                                    ?>>
                                                                    <option value="0">Mahalle Seçin</option>
                                                                    <?php
                                                                    if(S($f_adresmahalleid)!=0)
                                                                    {
                                                                        $mahalle_d=0; $mahalle_v=""; $mahalle_s="";
                                                                        $mahalle_s="SELECT NeighborhoodID,NeighborhoodName FROM yermahalle WHERE AreaID='". $f_adressemtid ."' ORDER BY NeighborhoodName ASC";
                                                                        $mahalle_v=$data->query($mahalle_s);
                                                                        if($mahalle_v->num_rows>0)$mahalle_d=1;
                                                                        unset($mahalle_s);
                                                                        if($mahalle_d==1)
                                                                        {
                                                                            while($mahalle_t=$mahalle_v->fetch_assoc())
                                                                            {
                                                                                $l_mahalleid=$mahalle_t["NeighborhoodID"];
                                                                                $l_mahalleAd=$mahalle_t["NeighborhoodName"];
                                                                                ?>
                                                                                <option value="<?=$l_mahalleid?>" <?php if($l_mahalleid==$f_adresmahalleid)echo"selected"; ?>><?=$l_mahalleAd?></option><?php
                                                                            }
                                                                            unset($mahalle_t,$mahalle_v);
                                                                        }
                                                                        unset($mahalle_v);
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <input
                                                                        type="text"
                                                                        name="adresmahalle"
                                                                        id="adresmahalle"
                                                                        class="form-control"
                                                                        value="<?php if(S($f_adresmahalleid)==0)echo $f_adresmahalleid;?>"
                                                                        placeholder="Mahalle *"
                                                                    <?php
                                                                    if(S($f_adresmahalleid)!=0)echo 'style="display: none;"';?>>
                                                                <label
                                                                        for="adresmahalleid"
                                                                        class="control-label">Mahalle *</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <input
                                                                        type="text"
                                                                        name="adrespostakod"
                                                                        id="adrespostakod"
                                                                        class="form-control"
                                                                        placeholder="Posta Kodu girin"
                                                                        value="<?=$f_adrespostakod?>" >
                                                                <label
                                                                        for="adrespostakod"
                                                                        class="control-label">Posta Kodu *</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
																<textarea
                                                                        name="adresacik"
                                                                        id="adresacik"
                                                                        class="form-control"
                                                                        rows="2"
                                                                        maxlength="255"
                                                                        style="
																		background-color:#efefef;
																		width:96%;
																		padding: 10px 1% 10px 1%;
																		margin:10px 0 0 0;
																		border:solid 1px #eee"
                                                                ><?=ltrim($f_adresacik)?></textarea>
                                                                <label for="adresacik">uye Açık Adres</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="form-group floating-label">
                                                                <input type="tel"
                                                                       name="adrestelefon"
                                                                       id="adrestelefon"
                                                                       class="form-control"
                                                                       data-rule-minlength="10"
                                                                       maxlength="10"
                                                                       data-rule-digits="true"
                                                                       value="<?=$f_adrestelefon?>"
                                                                >
                                                                <label for="adresacik">Adres için Telefon</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-actionbar">
                                            <div class="card-actionbar-row">
                                                <a class="btn btn-primary btn-default-bright" href="/_y/s/s/uyeler/AddMember.php">İPTAL</a>
                                                <button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
                                            </div>
                                        </div>
                                        <?php if(S($f_uyeid)!=0 && S($f_adresid)!=0){ ?>
                                            <div class="card-actionbar">
                                                <div class="card-actionbar-row">
                                                    <a
                                                            id="adresekle"
                                                            class="btn btn-primary btn-default-bright"
                                                            href="#textModal"
                                                            data-toggle="modal"
                                                            data-placement="top"
                                                            data-original-title="Adres Ekle"
                                                            data-target="#simpleModal"
                                                            data-backdrop="true"
                                                    >(+) Adres Ekle</a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </form>
							</div>
						</div>
						<!-- diğer adresler -->
						<?php
							$adresler_d=0;
							$adresler_s="select adresid,adresbaslik from uyeadres where adressil='0' and uyeid='".$f_uyeid."' Order By adresid ASC";
							$adresler_v=$data->query($adresler_s);
							unset($adresler_s);
							if($adresler_v->num_rows>1)$adresler_d=1;
                            if($adresler_d==1)
                        {
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-head style-primary">
                                            <header>Diğer Adresler</header>
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
                                                    <th>Ad</th>
                                                    <th>İşlem</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if($adresler_d==1)
                                                {
                                                    $i=0;
                                                    while ($adresler_t=$adresler_v->fetch_assoc())
                                                    {
                                                        $i++;
                                                        $l_adresid=$adresler_t["adresid"];
                                                        $l_adresad=$adresler_t["adresbaslik"];
                                                        if($i>1){
                                                            ?>
                                                            <tr id="tr<?=$l_adresid?>">
                                                                <td><?=$l_adresid?></td>
                                                                <td><?=$l_adresad?></td>
                                                                <td>
                                                                    <a
                                                                            id="adresduzenle"
                                                                            href="#textModal"
                                                                            class="btn btn-icon-toggle"
                                                                            data-id="<?=$l_adresid?>"
                                                                            data-toggle="modal"
                                                                            data-target="#simpleModal"
                                                                            data-placement="top"
                                                                            data-original-title="Düzenle">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </a>
                                                                    <a
                                                                            id="adressil"
                                                                            href="#textModaladressil"
                                                                            class="btn btn-icon-toggle"
                                                                            data-id="<?=$l_adresid?>"
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
                                                    }
                                                }
                                                unset($adresler_d,$adresler_v,$l_adresid,$l_adresad);
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        ?>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>
		<?php //Bayi Adres Popup?>
		<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
                <form name="yeniadres" id="yeniadres" class="form form-validation form-validate" role="form" method="post">
                    <input type="hidden" name="yeniadresekle" value="1">
                    <input type="hidden" name="uyeid" value="<?=$f_uyeid?>">
                    <input type="hidden" name="yeniadresid" id="yeniadresid" value="0">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="simpleModalLabel">Adres Ekle/Düzenle</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="yeniadresbaslik"
                                                        id="yeniadresbaslik"
                                                        value=""
                                                        data-rule-minlength="2"
                                                        maxlength="100"
                                                        placeholder="Adres Başlığı (Firma, Depo)"
                                                        required aria-required="true" >
                                                <label for="yeniadresbaslik">Adres Başlığı (Firma, Depo)</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="yeniadresad"
                                                        id="yeniadresad"
                                                        value=""
                                                        placeholder="Adres Yetkili Adını Yazın" required aria-required="true" >
                                                <label for="yeniadressoyad">Adres Yetkili Adını Yazın</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="yeniadressoyad"
                                                        id="yeniadressoyad"
                                                        value=""
                                                        placeholder="Adres Yetkili Soyadını Yazın" required aria-required="true" >
                                                <label for="yeniadressoyad">Adres Yetkili Soyadını Yazın</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="yeniadrestcno"
                                                        id="yeniadrestcno"
                                                        value=""
                                                        data-rule-digits="true"
                                                        data-rule-minlength="11"
                                                        maxlength="11"
                                                        placeholder="Adres Yetkili TC No" required aria-required="true" >
                                                <label for="yeniadrestcno">Adres Yetkili TC No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select id="yeniadresulkeid" name="yeniadresulkeid" class="form-control" required>
                                                    <?php
                                                    $ulke_d=0; $ulke_v=""; $ulke_s="";
                                                    $ulke_s="SELECT CountryID,CountryName,BinaryCode FROM yerulke ORDER BY CountryName ASC";
                                                    $ulke_v=$data->query($ulke_s);
                                                    if($ulke_v->num_rows>0)$ulke_d=1;
                                                    unset($ulke_s);
                                                    if($ulke_d==1)
                                                    {
                                                        while($ulke_t=$ulke_v->fetch_assoc())
                                                        {
                                                            $l_ulkeid=$ulke_t["CountryID"];
                                                            $l_ulkeAd=$ulke_t["CountryName"];
                                                            $l_ulkeKisa=$ulke_t["BinaryCode"];
                                                            ?><option value="<?=$l_ulkeid?>"><?=$l_ulkeAd?> (<?=$l_ulkeKisa?>)</option><?php
                                                        }
                                                        unset($ulke_t,$ulke_v);
                                                    }
                                                    unset($ulke_v);
                                                    ?>
                                                </select>
                                                <label for="yeniadresulkeid" class="control-label">Ülke *</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select
                                                        id="yeniadressehirid"
                                                        name="yeniadressehirid"
                                                        class="form-control"
                                                        style="display:none"
                                                ><option value="0">Şehir Seçin</option>
                                                </select>
                                                <input
                                                        type="text"
                                                        name="yeniadressehir"
                                                        id="yeniadressehir"
                                                        class="form-control"
                                                        value=""
                                                        placeholder="Şehir adı girin"
                                                >
                                                <label for="yeniadressehirid" class="control-label">Şehir *</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select
                                                        id="yeniadresilceid"
                                                        name="yeniadresilceid"
                                                        class="form-control"
                                                        required style="display:none"
                                                >
                                                </select>
                                                <input
                                                        type="text"
                                                        name="yeniadresilce"
                                                        id="yeniadresilce"
                                                        class="form-control"
                                                        value=""
                                                        placeholder="İlçe adı girin">
                                                <label for="yeniadresilceid" class="control-label">İlçe *</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select
                                                        id="yeniadressemtid"
                                                        name="yeniadressemtid"
                                                        class="form-control"
                                                        required style="display:none"
                                                >
                                                </select>
                                                <input
                                                        type="text"
                                                        name="yeniadressemt"
                                                        id="yeniadressemt"
                                                        class="form-control"
                                                        value=""
                                                        placeholder="Semt adı girin"
                                                >
                                                <label for="yeniadressemtid" class="control-label">Semt *</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select
                                                        id="yeniadresmahalleid"
                                                        name="yeniadresmahalleid"
                                                        class="form-control"
                                                        required style="display:none"
                                                >
                                                </select>
                                                <input
                                                        type="text"
                                                        name="yeniadresmahalle"
                                                        id="yeniadresmahalle"
                                                        class="form-control"
                                                        value=""
                                                        placeholder="Mahalle *"
                                                >
                                                <label
                                                        for="yeniadresmahalleid"
                                                        class="control-label">Mahalle *</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        name="yeniadrespostakod"
                                                        id="yeniadrespostakod"
                                                        class="form-control"
                                                        placeholder="Posta Kodu girin"
                                                        value="" >
                                                <label
                                                        for="adrespostakod"
                                                        class="control-label">Posta Kodu *</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group floating-label">
												<textarea
                                                        name="yeniadresacik"
                                                        id="yeniadresacik"
                                                        class="form-control"
                                                        rows="2"
                                                        maxlength="255"
                                                        style="
														background-color:#efefef;
														width:96%;
														padding: 10px 1% 10px 1%;
														margin:10px 0 0 0;
														border:solid 1px #eee"
                                                ></textarea>
                                                <label for="yeniadresacik">uye Açık Adres</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group floating-label">
                                                <input type="tel"
                                                       name="yeniadrestelefon"
                                                       id="yeniadrestelefon"
                                                       class="form-control"
                                                       data-rule-minlength="10"
                                                       maxlength="10"
                                                       data-rule-digits="true"
                                                       value=""
                                                >
                                                <label for="yeniadresacik">Adres için Telefon</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary" id="silbutton">Kaydet</button>
                        </div>
                    </div>
                </form>
			</div>
		</div>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
		<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
		<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
		<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
		<script src="/_y/assets/js/libs/moment/moment.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>
		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

		<script src="/_y/assets/js/libs/toastr/toastr.js"></script>
		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<script>
			$("#bayieklephp").addClass("active");
		</script>
		<script>
			$( "#adresulkeid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				if(str==212)
				{
			    	$("#adressehirid").show();
					$("#adresilceid").show();
					$("#adressemtid").show();
					$("#adresmahalleid").show();
					$("#adressehir").hide();
					$("#adressehir").val("");
					$("#adresilce").hide();
					$("#adresilce").val("");
					$("#adressemt").hide();
					$("#adressemt").val("");
					$("#adresmahalle").hide();
					$("#adresmahalle").val("");
			    	$("#_islem").attr("src", "/_y/s/f/sehirgetir.php?ulkeid="+str);
				}
				else
				{
					$("#adressehirid").hide();
					$("#adressehirid").empty();
					$("#adresilceid").hide();
					$("#adresilceid").empty();
					$("#adressemtid").hide();
					$("#adressemtid").empty();
					$("#adresmahalleid").hide();
					$("#adresmahalleid").empty();
					$("#adressehir").show();
					$("#adresilce").show();
					$("#adressemt").show();
					$("#adresmahalle").show();
				}
			});
			$( "#adressehirid" ).click('change',function()
			{
				$("#adressemtid").empty();
				$("#adresmahalleid").empty();
				var str = "";
				str = $("#adressehirid option:selected").val();
			    $("#_islem").attr("src", "/_y/s/f/ilcegetir.php?sehirid="+str);
			});
			$( "#adresilceid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				$("#adresmahalleid").empty();
			    $("#_islem").attr("src", "/_y/s/f/semtgetir.php?ilceid="+str);
			});
			$( "#adressemtid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/mahallegetir.php?semtid="+str);
			});
			$( "#adresmahalleid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/postakodgetir.php?mahalleid="+str);
			});
			$( "#yeniadresulkeid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				if(str==212)
				{
			    	$("#yeniadressehirid").show();
					$("#yeniadresilceid").show();
					$("#yeniadressemtid").show();
					$("#yeniadresmahalleid").show();
					$("#yeniadressehir").hide();
					$("#yeniadressehir").val("");
					$("#yeniadresilce").hide();
					$("#yeniadresilce").val("");
					$("#yeniadressemt").hide();
					$("#yeniadressemt").val("");
					$("#yeniadresmahalle").hide();
					$("#yeniadresmahalle").val("");
			    	$("#_islem").attr("src", "/_y/s/f/sehirgetir.php?yeni=1&ulkeid="+str);
				}
				else
				{
					$("#yeniadressehirid").hide();
					$("#yeniadressehirid").empty();
					$("#yeniadresilceid").hide();
					$("#yeniadresilceid").empty();
					$("#yeniadressemtid").hide();
					$("#yeniadressemtid").empty();
					$("#yeniadresmahalleid").hide();
					$("#yeniadresmahalleid").empty();
					$("#yeniadressehir").show();
					$("#yeniadresilce").show();
					$("#yeniadressemt").show();
					$("#yeniadresmahalle").show();
				}
			});
			$( "#yeniadressehirid" ).click('change',function()
			{
				$("#yeniadressemtid").empty();
				$("#yeniadresmahalleid").empty();
				var str = "";
				str = $("#yeniadressehirid option:selected").val();
			    $("#_islem").attr("src", "/_y/s/f/ilcegetir.php?yeni=1&sehirid="+str);
			});
			$( "#yeniadresilceid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
				$("#yeniadresmahalleid").empty();
			    $("#_islem").attr("src", "/_y/s/f/semtgetir.php?yeni=1&ilceid="+str);
			});
			$( "#yeniadressemtid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/mahallegetir.php?yeni=1&semtid="+str);
			});
			$( "#yeniadresmahalleid" ).on('change',function()
			{
				var str = "";
				str = $(this).val();
			    $("#_islem").attr("src", "/_y/s/f/postakodgetir.php?yeni=1&mahalleid="+str);
			});
			$("#adresekle").on("click",function()
			{
				$("#yeniadresid").val(0);
				$("#yeniadressehirid").hide();
				$("#yeniadressehirid").empty();
				$("#yeniadresilceid").hide();
				$("#yeniadresilceid").empty();
				$("#yeniadressemtid").hide();
				$("#yeniadressemtid").empty();
				$("#yeniadresmahalleid").hide();
				$("#yeniadresmahalleid").empty();
				$("#yeniadressehir").show();
				$("#yeniadresilce").show();
				$("#yeniadressemt").show();
				$("#yeniadresmahalle").show();
				$("#yeniadressehir").val("");
				$("#yeniadresilce").val("");
				$("#yeniadressemt").val("");
				$("#yeniadresmahalle").val("");
				$("#yeniadresbaslik").val("");
				$("#yeniadrespostakod").val("");
				$("#yeniadrestelefon").val("");
				$("#yeniadresacik").val("");
				$("#yeniadresulkeid option:first").attr('selected','selected');

			});
			$(".btn.btn-icon-toggle").on("click", function()
			{
				$adresid=$(this).data("id");
				$("#yeniadresid").val($adresid);
				$("#yeniadressehirid").hide();
				$("#yeniadressehirid").empty();
				$("#yeniadresilceid").hide();
				$("#yeniadresilceid").empty();
				$("#yeniadressemtid").hide();
				$("#yeniadressemtid").empty();
				$("#yeniadresmahalleid").hide();
				$("#yeniadresmahalleid").empty();
				$("#yeniadressehir").show();
				$("#yeniadresilce").show();
				$("#yeniadressemt").show();
				$("#yeniadresmahalle").show();
				$("#yeniadressehir").val("");
				$("#yeniadresilce").val("");
				$("#yeniadressemt").val("");
				$("#yeniadresmahalle").val("");
				$("#yeniadresbaslik").val("");
				$("#yeniadrespostakod").val("");
				$("#yeniadrestelefon").val("");
				$("#yeniadresacik").val("");
				$("#_islem").attr("src", "/_y/s/f/adresgetir.php?adresid="+$adresid);

			});

			$(".modal-dialog").css({"width":"80%"});
		</script>
	</body>
</html>