<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/_y/s/global.php");

$formhata = 0;
$formhataaciklama = "";
$formtablo = "uyesiparis";

//düzenle
$sayfabaslik = "Siparişleri Düzenle";
$formbaslik = "Sipariş Liste";

$firmabilgiler_s="SELECT ayarfirmaad,ayarfirmatelefon,ayarfirmavergidairesi,ayarfirmavergino FROM ayarfirma LIMIT 1";
if($data->query($firmabilgiler_s))
{
    $firmabilgiler_v=$data->query($firmabilgiler_s);unset($firmabilgiler_s);
    if($firmabilgiler_v->num_rows>0)
    {
        while($firmabilgiler_t=$firmabilgiler_v->fetch_assoc())
        {
            $ayarfirmaad=$firmabilgiler_t["ayarfirmaad"];
            $ayarfirmatelefon=$firmabilgiler_t["ayarfirmatelefon"];
            $ayarfirmavergidairesi=$firmabilgiler_t["ayarfirmavergidairesi"];
            $ayarfirmavergino=$firmabilgiler_t["ayarfirmavergino"];
        }unset($firmabilgiler_t);
    }else{hatalogisle("hata",$data->error);}
    unset($firmabilgiler_v);
}
function get_column_names($con, $table) {
    $sql = 'DESCRIBE '.$table;
    $result = mysqli_query($con, $sql);

    $rows = array();
    while($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row['Field'];
    }

    return $rows;
}

$sutunlar = get_column_names($data, 'uyesiparis');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Pozitif Panel - Sipariş Liste</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet"
          href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/wizard/wizard.css?1425466601"/>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once($anadizin . "/_y/s/b/header.php"); ?>
<div id="base">
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li><a href="#">Siparişler</a></li>
                    <li class="active"><?= $sayfabaslik ?></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary">
                                <header><?= $formbaslik ?></header>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <div class="form-group">
                                    <input type="text" name="q" id="q" class="form-control" placeholder="Arama:ID yazın" value="">
                                </div>
                                <div class="form-group">
                                    <a href="/_y/s/s/siparisler/OrderList.php?tip=<?=q("tip")?>">Sıfırla</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <section class="siparistablo">
                            <?php
                            foreach($sutunlar as $sutun)
                            {
                                echo '<div class="form-group>">
                                <input 
                                    name="'.$sutun.'" id="'.$sutun.'" 
                                    class="form-control" placeholder="'.$sutun.'">
                                </div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </section>
</div>
<?php require_once($anadizin . "/_y/s/b/menu.php"); ?>
</div>


<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <form name="formsiparis" id="formsiparis" target="_islem" class="form form-validation form-validate" role="form"
              method="post" action="/_y/s/f/siparisisle.php">
            <input type="hidden" name="gsiparisid" id="gsiparisid">
            <input type="hidden" name="odemeyontemi" id="odemeyontemi">
            <input type="hidden" name="odemedurum" id="odemedurum">
            <input type="hidden" name="siparisdurum" id="siparisdurum">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                    <h4 class="modal-title" id="simpleModalLabel">Sipariş Güncelle</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group"><label for="siparisdurumid">Sipariş Durum</label>
                                <select id="siparisdurumid" name="siparisdurumid" class="form-control" required=""
                                        aria-required="true" aria-invalid="false">
                                    <option value="-1">Seçiniz</option>
                                    <?php
                                    $siparisdurum_s = "
													Select 
														siparisdurumid,siparisdurumbaslik 
													From 
														uyesiparisdurum 
													order by 
														siparisdurumid asc
												";
                                    if ($data->query($siparisdurum_s)) {
                                        $siparisdurum_v = $data->query($siparisdurum_s);
                                        unset($siparisdurum_s);
                                        if ($siparisdurum_v->num_rows > 0) {
                                            while ($siparisdurum_t = $siparisdurum_v->fetch_assoc()) {
                                                $siparisdurumid = $siparisdurum_t["siparisdurumid"];
                                                $siparisdurumbaslik = $siparisdurum_t["siparisdurumbaslik"];
                                                echo '<option value="' . $siparisdurumid . '">' . $siparisdurumbaslik . '</option>';
                                            }
                                            unset($siparisdurum_t);
                                        }
                                    } else {
                                        die($data->error);
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group"><label for="kargoid">Kargo</label>
                                <select id="kargoid" name="kargoid" class="form-control" required=""
                                        aria-required="true" aria-invalid="false">
                                    <option value="0">Kargo Seç</option>
                                    <?php
                                    $siparisdurum_s = "
													Select 
														kargoid,kargoad 
													From 
														kargo 
													where kargosil='0'
													order by 
														kargoid asc
												";
                                    if ($data->query($siparisdurum_s)) {
                                        $siparisdurum_v = $data->query($siparisdurum_s);
                                        unset($siparisdurum_s);
                                        if ($siparisdurum_v->num_rows > 0) {
                                            while ($siparisdurum_t = $siparisdurum_v->fetch_assoc()) {
                                                $kargoid = $siparisdurum_t["kargoid"];
                                                $kargoad = $siparisdurum_t["kargoad"];
                                                echo '<option value="' . $kargoid . '">' . $kargoad . '</option>';
                                            }
                                            unset($siparisdurum_t);
                                        }
                                    } else {
                                        die($data->error);
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sipariskargoserino">Kargo Seri No</label>
                                <input type="text" class="form-control" name="sipariskargoserino"
                                       id="sipariskargoserino" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sipariskargotakipd">Takip Kodu</label>
                                <input type="text" class="form-control" name="sipariskargotakip" id="sipariskargotakip"
                                       value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="siparisnotalici">Alıcı Sipariş Notu</label>
                                <textarea name="siparisnotalici" id="siparisnotalici" class="form-control" rows="2"
                                          maxlength="255" style="
											background-color:#efefef;
											width:96%;
											padding: 10px 1% 10px 1%;
											margin:10px 0 0 0;
											border:solid 1px #eee"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="siparisnotyonetici">Yönetici Sipariş Notu</label>
                                <textarea name="siparisnotyonetici" id="siparisnotyonetici" class="form-control"
                                          rows="2" maxlength="255" style="
											background-color:#efefef;
											width:96%;
											padding: 10px 1% 10px 1%;
											margin:10px 0 0 0;
											border:solid 1px #eee"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" id="guncellebutton">Güncelle</button>
                </div>
            </div>
        </form>
    </div>
</div>
<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.4.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>

<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
<script src="/_y/assets/js/libs/select2/select2.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

<script src="/_y/assets/js/libs/multi-select/jquery.multi-select.js"></script>
<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="/_y/assets/js/libs/moment/moment.min.js"></script>

<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

<script src="/_y/assets/js/core/source/custom.js"></script>
<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>

<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
<script src="/_y/assets/js/core/demo/Demo.js"></script>

<script>
    $("#<?=$OrderListsayfa?>").addClass("active");
    $(document).on("keypress","#q",function ()
    {
        if ($("#q").val().length >= 3)
        {
            $deger = $("#q").val();$(".siparistablo").html("");
            $.ajax(
                {
                    type: 'GET',
                    url:"siparisbul.php?tip=<?=q("tip")?>&q="+$deger,
                    dataType: "html",
                    success: function(data)
                    {
                        if($.trim(data))
                        {
                            $(".siparistablo").html(data);
                        }
                        else
                        {
                            $(".siparistablo").html("");
                        }
                    }
                });
        }
    });
    $(document).on("click",".btnspr", function ()
    {
        $siparisid = $(this).data("id");
        $kargoteslimatid = $(this).data("kargoteslimatid");
        $kargoserino = $(this).data("kargoserino");
        $notalici = $(this).data("notalici");
        $notyonetici = $(this).data("notyonetici");
        $odemeyontemi=$(this).data("odemeyontemi");
        $odemedurum=$(this).data("odemedurum");
        $siparisdurum=$(this).data("siparisdurum");
        $("#gsiparisid").val($siparisid);
        $("#sipariskargoserino").val($kargoserino);
        $("#sipariskargotakip").val($kargoteslimatid);
        $("#siparisnotalici").val($notalici);
        $("#siparisnotyonetici").val($notyonetici);
        $("#odemeyontemi").val($odemeyontemi);
        $("#odemedurum").val($odemedurum);
        $("#siparisdurum").val($siparisdurum);
        $("#siparisdurumid").val($siparisdurum);
    });
    $(document).on("submit","#formsiparis",function ()
    {
        if ($("#siparisdurumid option:selected").val() == -1)
        {
            alert("Sipariş durumunu seçin");return false;
        }
        if ($("#siparisdurumid option:selected").val() == 0)
        {
            if($("#kargoid").val()==0)
            {
                alert("Kargo seçin");return false;
            }
        }
    });

    $kapat="";
    $(document).on("click",".faturayazdir",function()
    {
        $form=$(this).data("form");
        $("#faturaicerik-"+$form).show();
        $kapat="faturaicerik-"+$form;
    });
    $(document).on("click",'.kapat',function(){
        $("#"+$kapat).hide();
        $("button.yazdir").show();
        $(".kapat").show();
    });
    function printData()
    {
        var divToPrint=document.getElementById($kapat);
        newWin= window.open("");
        newWin.document.write(divToPrint.outerHTML);
        newWin.print();
        newWin.close();
        $(".kapat").show();
        $("button.yazdir").show();
    }
    $('button.yazdir').on('click',function(){
        $(".kapat").hide();
        $("button.yazdir").hide();
        printData();
    });
    $(document).on("click",".siparisyazdir",function()
    {
        $siparisid=$(this).data("siparisid");
        $kapat="siparisyazdir-"+$siparisid;
        $("#siparisyazdir-"+$siparisid).show();
        /*$("#siparisyazdir-"+$siparisid+" .modal-dialog").css({"width":"100%","height":"100%","padding":"0","margin":"0","background-color":"#fff"});
        $("#siparisyazdir-"+$siparisid+" .modal-content").css({"width":"100%","height":"100%","background-color":"#fff"});
        $("#siparisyazdir-"+$siparisid).css({"visibility":"visible","height":"100%"});*/
        //$("#siparisyazdir-"+$siparisid).addClass( "printSection" );
    });
</script>
<style type="text/css">
    .siparistablo{padding:0}
    button#faturayazdir{float:left;border:1px solid #767775;margin:0 0 2% 2%;}.model {display:none;position:fixed;z-index:5;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:white;background-color: rgba(0,0,0,0.4);}.model-icerik{width:35%;position:relative;background-color:#fefefe;margin:5% auto;padding:20px;border:1px solid #ccc}.kapat {color:#aaa;float:right;font-size:28px;font-weight:bold;position:absolute;right:15px;top:0;z-index:15}.kapat:hover,.kapat:focus{color:black;text-decoration:none;cursor:pointer}button.btn.ink-reaction.btn-raised.btn-primary.btnspr{width:112px}
    @media(max-width:1368px){.model-icerik{width:40%;margin:6% auto}}@media(max-width:1024px){.model-icerik{width:70%;margin:20% 0 0 20%;padding:0px;}table.fatura td{padding:2px}table#yazdir{white-space:initial;}table#yazdir{width:99%}section.faturabilgiler{padding:24px}}@media(max-width:800px){.model-icerik{width:90%;margin:30% 0 0 5%}.card-head{line-height:unset;min-height:auto}}
</style>
</body>
</html>
