<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Renkler";
$formbaslik="Ürün Renkleri";
$butonisim="EKLE";

$f_urunrenkgrupid=f("urunrenkgrupid");
$f_urunrenkid=f("urunrenkid");
$f_urunrenkad=f("urunrenkad");
$f_urunrenkkod=f("urunrenkkod");
$f_urunrenksira=S(f("urunrenksira"));

if(S(f("urunrenkekle"))==1 && !BosMu($f_urunrenkad))
{
    $sutunlar="urunrenkgrupid,urunrenkad,urunrenkkod,urunrenksira,urunrenksil";
    $degerler=$f_urunrenkgrupid."|*_".$f_urunrenkad."|*_".$f_urunrenkkod."|*_".$f_urunrenksira."|*_"."0";
    $tablo="urunrenk";
    if(S($f_urunrenkgrupid)==0)
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bir renk grubu seçmelisiniz<br><br>";
    }
    elseif(dogrula("urunrenk","urunrenkad='". $f_urunrenkad ."' and urunrenkgrupid='". $f_urunrenkgrupid ."' and urunrenksil='0'") && S($f_urunrenkid)==0)
    {
        $formhata=1;
        $formhataaciklama="DİKKAT: Bu isimde ( $f_urunrenkad ) zaten Renk var. Lütfen ilgili kaydı düzenleyiniz.<br><br>
		<a href='/_y/s/s/varyasyonlar/Renkliste.php'> > Renk Listesine git <</a><br>";
    }
    if($formhata==0)
    {
        if(S($f_urunrenkid)==0)
        {
            ekle($sutunlar,$degerler,$tablo,35);
            $f_urunrenkid=teksatir("SELECT urunrenkid FROM urunrenk WHERE urunrenkgrupid='".$f_urunrenkgrupid."' and urunrenkad='".$f_urunrenkad."'","urunrenkid");
        }
        else
        {
            guncelle($sutunlar,$degerler,$tablo," urunrenkid='". $f_urunrenkid ."' ",35);
        }
    }
}
if(S(q("urunrenkid"))!=0)
{
    if(dogrula("urunrenk","urunrenkid='". q("urunrenkid") ."'"))
    {
        $butonisim="GÜNCELLE";
        $f_urunrenkid=q("urunrenkid");
        $f_urunrenkad=teksatir("Select urunrenkad From urunrenk Where urunrenkid='". q("urunrenkid") ."'","urunrenkad");
        $f_urunrenksira=teksatir("Select urunrenksira From urunrenk Where urunrenkid='". q("urunrenkid") ."'","urunrenksira");
        $f_urunrenkgrupid=teksatir("Select urunrenkgrupid From urunrenk Where urunrenkid='". q("urunrenkid") ."'","urunrenkgrupid");
        $f_urunrenkkod=teksatir("Select urunrenkkod From urunrenk Where urunrenkid='". q("urunrenkid") ."'","urunrenkkod");
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
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-colorpicker/bootstrap-colorpicker.css?1422823362" />
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
                    <li class="active"><a href="/_y/s/s/varyasyonlar/renkliste.php" class="btn ink-reaction btn-raised btn-primary">Renk Liste</a></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <!-- BEGIN ADD CONTACTS FORM -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary form-inverse">
                                <header><?=$formbaslik?></header>
                                <div class="tools">
                                    <a href="/_y/s/s/varyasyonlar/renkekle.php?renkgrupid=<?=$f_urunrenkgrupid?>" id="yenikutu" class="btn btn-floating-action btn-default-light"><i class="fa fa-plus"></i></a>
                                </div>
                            </div>
                            <form name="formanaliz" class="form form-validation form-validate" role="form" method="post">
                                <input type="hidden" name="urunrenkekle" value="1">
                                <input type="hidden" name="urunrenkid" value="<?=$f_urunrenkid?>">
                                <!-- BEGIN DEFAULT FORM ITEMS -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <select id="urunrenkgrupid" name="urunrenkgrupid" class="form-control">
                                                        <option value="0">Renk Grubu Seçin</option>
                                                        <?php
                                                        if(!isset($data))Veri(true);
                                                        $urungrup_d=0; $urungrup_v=""; $urungrup_s="";
                                                        $urungrup_s="
																		SELECT 
																			urunrenkgrupid,urunrenkgrupad 
																		FROM 
																			urunrenkgrup 
																		Where 
																			urunrenkgrupsil='0' 
																	";
                                                        $urungrup_v=$data->query($urungrup_s);
                                                        if($urungrup_v -> num_rows > 0) $urungrup_d=1;
                                                        unset($urungrup_s);
                                                        if($urungrup_d==1)
                                                        {
                                                            while($urungrup_t=$urungrup_v->fetch_assoc())
                                                            {
                                                                $l_urunrenkgrupid = $urungrup_t["urunrenkgrupid"];
                                                                $l_urunrenkgrupad   = $urungrup_t["urunrenkgrupad"];
                                                                ?>
                                                                <option value="<?=$l_urunrenkgrupid?>" <?php if(S($l_urunrenkgrupid)==S($f_urunrenkgrupid)||($l_urunrenkgrupid==S(q("renkgrupid"))))echo "selected"; ?> >
                                                                    <?=$l_urunrenkgrupad?>
                                                                </option>
                                                                <?php
                                                            }
                                                            unset($urungrup_t,$urungrup_v);
                                                        }
                                                        unset($urungrup_v);
                                                        ?>
                                                    </select>
                                                    <label for="urunrenkgrupid">Renk Grubu Seçin</label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group floating-label">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                name="urunrenkad"
                                                                id="urunrenkad"
                                                                value="<?=$f_urunrenkad?>"
                                                                placeholder="Ürün Rengi Yazın" required aria-required="true" >
                                                        <label for="urunrenkad">Ürün Rengi Yazın</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div id="cp6"
                                                         class="input-group colorpicker-component"
                                                         data-color="<?=$f_urunrenkkod?>"
                                                         data-colorpicker-guid="6">
                                                        <div class="input-group-content">
                                                            <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="urunrenkkod"
                                                                    id="urunrenkkod"
                                                                    value="<?=$f_urunrenkkod?>"
                                                                    placeholder="Ürün Renk Kodu" required aria-required="true" >
                                                            <label for="urunrenkad">Ürün Renk Kodu Seçin</label>
                                                        </div>
                                                        <div class="input-group-addon"><i style="background-color:<?=$f_urunrenkkod?>;border:solid 1px #000"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group floating-label">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                name="urunrenksira"
                                                                id="urunrenksira"
                                                                value="<?=$f_urunrenksira?>"
                                                                placeholder="Ürün Renk Sırası" required aria-required="true" >
                                                        <label for="urunrenkad">Ürün Renk Sırası Yazın</label>
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

<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>

<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
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
    $("#renkeklephp").addClass("active");
    $(document).ready(function(){
        $(document).on("change","#urunrenkgrupid",function()
        {
            $id=$("#urunrenkgrupid").val();
            $("#yenikutu").attr("href","/_y/s/s/varyasyonlar/renkekle.php?renkgrupid="+$id);
        });
        $('#cp6').colorpicker();
    });
</script>
<!-- END JAVASCRIPT -->
</body>
</html>