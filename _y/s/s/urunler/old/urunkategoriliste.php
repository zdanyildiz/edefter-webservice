<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="ayarfirma";

//düzenle
$sayfabaslik="Kategorileri Düzenle";
$formbaslik="KATEGORİ LİSTE";

if(s(q("dilid"))!=0) $dilid=q("dilid");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Sistem Panel - Kategori Ekle</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/modules/materialadmin/css/theme-default/libs/toastr/toastr.css?1422823374" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once($anadizin."/_y/s/b/header.php");?>
<div id="base">
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li><a href="#">KATEGORİ BİLGİLERİ</a></li>
                    <li class="active"><?=$sayfabaslik?></li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-head style-primary">
                                <header><?=$formbaslik?></header>
                            </div>
                        </div>
                    </div>
                </div>
                <div  class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <div class="form-group floating-label">
                                    <select id="dilid" name="dilid" class="form-control">
                                        <?php
                                        $dil_s="SELECT dilid,dilad,dilkisa FROM dil Where dilsil='0' and dilaktif='1'";
                                        if($data->query($dil_s))
                                        {
                                            $dil_v=$data->query($dil_s);unset($dil_s);
                                            if($dil_v->num_rows>0)
                                            {
                                                while($dil_t=$dil_v->fetch_assoc())
                                                {
                                                    $l_dilid=$dil_t["dilid"];
                                                    $l_dAd=$dil_t["dilad"];
                                                    $l_dKisa=$dil_t["dilkisa"];
                                                    ?>
                                                    <option value="<?=$l_dilid?>" <?php if($l_dilid==S(q("dilid")))echo "selected"; ?> ><?=$l_dAd?> (<?=$l_dKisa?>)</option>
                                                    <?php
                                                }unset($dil_t);
                                            }unset($dil_v);
                                        }
                                        else{die($data->error);}?>
                                    </select>
                                    <p class="help-block">KATEGORİ LİSTELEME İÇİN DİL SEÇİN. Aradığınız kategoriyi kolayca bulmanızı sağlar!</p>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="q" id="q" class="form-control" placeholder="Arama Örn:Beyaz" value="">
                                </div>
                                <div class="form-group">
                                    <a href="/_y/s/s/urunler/urunkategoriliste.php">Sıfırla</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body aramasonuc">
                                <?php
                                $kategoriliste_sorgu="
													SELECT 
														kategoriid,kategoriad,dilid 
													FROM 
														kategori 
													WHERE 
														kategorisil='0' and kategoriaktif='1' and kategorigrup='7' and ustkategoriid='0'and dilid='".$dilid."' 
													Order By 
														Kategoriid ASC
												";
                                if($data->query($kategoriliste_sorgu))
                                {
                                    $kategoriliste_sonuc=$data->query($kategoriliste_sorgu);unset($kategoriliste_sorgu);
                                    if($kategoriliste_sonuc->num_rows>0)
                                    {
                                        while ($kategoriliste_veri=$kategoriliste_sonuc->fetch_assoc())
                                        {
                                            $kategoriliste_id=$kategoriliste_veri["kategoriid"];
                                            $kategoriliste_ad=$kategoriliste_veri["kategoriad"];
                                            $altkategorivarmi=teksatir("SELECT ustkategoriid From kategori															 Where kategorisil='0' and ustkategoriid='".$kategoriliste_id."'","ustkategoriid");
                                            ?>
                                            <details
                                                    class="row form-group kategori"
                                                    id="tr<?=$kategoriliste_id?>"
                                                    data-id="<?=$kategoriliste_id?>">
                                                <summary style="outline:none;"><?=$kategoriliste_ad?></summary>
                                                <a href="/_y/s/s/urunler/urunkategoriekle.php?kategoriid=<?=$kategoriliste_id?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                                                <a id="kategorisil"
                                                   href="#textModal"
                                                   class="btn btn-icon-toggle"
                                                   data-id="<?=$kategoriliste_id?>"
                                                   data-toggle="modal"
                                                   data-placement="top"
                                                   data-original-title="Sil"
                                                   data-target="#simpleModal"
                                                   data-backdrop="true">
                                                    <i class="fa fa-trash-o"></i></a>
                                            </details>
                                            <div class="row a<?=$kategoriliste_id?>"></div>
                                            <?php
                                        }
                                    }
                                }else{hatalogisle("Kategori Liste",$data->error);}
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once($anadizin."/_y/s/b/menu.php");?>
</div>
<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="simpleModalLabel">Kategori Sil</h4>
            </div>
            <div class="modal-body">
                <p>Kategori silmek istediğinize emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="silbutton">Sil</button>
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
<script src="/_y/assets/js/core/demo/DemoUIMessages.js"></script>
<script>
    $silid=0;
    $(document).ready(function()
    {
        $('a#kategorisil').click(function ()
        {
            $silid=$(this).data("id");
        });
        $('#silbutton').click(function ()
        {
            $('#_islem').attr('src', "/_y/s/f/sil.php?sil=kategori&id="+$silid);
        });
        $(document).on("click",'.kategori',function ()
        {
            $id=$(this).data("id");
            $satir=$("div.a"+$id+"");
            if ($("#tr"+$id)[0].hasAttribute("open"))
            {
                $satir.html("");
            }
            else
            {
                $.ajax({
                    type: 'GET',
                    url:"kategorilistegetir.php?id="+$id,
                    dataType: "html",
                    success: function(data)
                    {
                        if($.trim(data))
                        {
                            $satir.html(data);
                        }
                    }
                });
            }

        });
    });
    $( "#q" ).keypress(function()
    {
        if($("#q").val().length>=3)
        {
            $('#_islem').attr('src', "/_y/s/f/urunkategoribul.php?q="+$("#q").val());
        }
    });
    $( "#dilid" ).change(function()
    {
        window.location.href = "/_y/s/s/urunler/urunkategoriliste.php?dilid="+$("#dilid").val();
    });
</script>
<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
<script>
    $("#urunkategorilistephp").addClass("active");
</script>
</body>
</html>
