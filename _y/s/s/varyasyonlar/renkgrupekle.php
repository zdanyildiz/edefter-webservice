<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Renk Gruplar";
$formbaslik="Ürün Renk Grupları";
$butonisim="EKLE";

$f_urunrenkgrupid=f("urunrenkgrupid");
$f_urunrenkgrupad=f("urunrenkgrupad");

if(S(f("urunrenkgrupekle"))==1 && !BosMu($f_urunrenkgrupad))
{
    $sutunlar="urunrenkgrupad,urunrenkgrupsil";
    $degerler=$f_urunrenkgrupad."|*_"."0";
    $tablo="urunrenkgrup";

    if(dogrula("urunrenkgrup","urunrenkgrupad='". $f_urunrenkgrupad ."' and urunrenkgrupsil='0'") && S($f_urunrenkgrupid)==0)
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu isimde ( $f_urunrenkgrupad ) zaten grup var. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/varyasyonlar/renkgrupliste.php'> > Renk Grup Listesine git <</a><br>";
    }
    if($formhata==0)
    {
        if(S($f_urunrenkgrupid)==0)
        {
            $f_benzersizid=SifreUret(20,2);
            ekle($sutunlar.",benzersizid",$degerler."|*_".$f_benzersizid,$tablo,35);
            $f_urunrenkgrupid = teksatir(" Select urunrenkgrupid from urunrenkgrup Where benzersizid='". $f_benzersizid ."'","urunrenkgrupid");
        }
        else
        {
            guncelle($sutunlar,$degerler,$tablo," urunrenkgrupid='". $f_urunrenkgrupid ."' ",35);
        }
    }
}
if(S(q("urunrenkgrupid"))!=0)
{
    if(dogrula("urunrenkgrup","urunrenkgrupid='". q("urunrenkgrupid") ."'"))
    {
        $butonisim="GÜNCELLE";
        $f_urunrenkgrupid=q("urunrenkgrupid");
        $f_urunrenkgrupad=teksatir("Select urunrenkgrupad From urunrenkgrup Where urunrenkgrupid='". q("urunrenkgrupid") ."'","urunrenkgrupad");
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
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
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
                    <li class="active"><a href="/_y/s/s/varyasyonlar/renkgrupliste.php" class="btn ink-reaction btn-raised btn-primary">Renk Grup Liste</a></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <!-- BEGIN ADD CONTACTS FORM -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary form-inverse">
                                <header><?=$formbaslik?></header>
                            </div>
                            <form name="formanaliz" class="form form-validation form-validate" role="form" method="post">
                                <input type="hidden" name="urunrenkgrupekle" value="1">
                                <input type="hidden" name="urunrenkgrupid" value="<?=$f_urunrenkgrupid?>">
                                <!-- BEGIN DEFAULT FORM ITEMS -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group floating-label">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                name="urunrenkgrupad"
                                                                id="urunrenkgrupad"
                                                                value="<?=$f_urunrenkgrupad?>"
                                                                placeholder="Renk Grup Adını Yazın" required aria-required="true" >
                                                        <label for="urunrenkgrupad">Renk Grup Adını Yazın</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-actionbar">
                                    <div class="card-actionbar-row">
                                        <a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
                                        <button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once($anadizin."/_y/s/b/menu.php");?>
</div>
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>

<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
<script src="/_y/assets/js/libs/select2/select2.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

<script src="/_y/assets/js/libs/multi-select/jquery.multi-select.js"></script>
<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="/_y/assets/js/libs/moment/moment.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>

<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/core/demo/Demo.js"></script>
<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>

<script>
    $("#renkgrupeklephp").addClass("active");
</script>
<!-- END JAVASCRIPT -->
</body>
</html>