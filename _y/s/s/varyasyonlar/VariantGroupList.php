<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

include_once MODEL . "/Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Varyant Grup Liste Pozitif E-Ticaret</title>
    <!-- BEGIN META -->
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
                    <li class="active">Varyant Grubu Liste</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">

                    <div class="col-lg-offset-1 col-md-8">
                        <div class="card">
                            <div class="card-body ">
                                <div class="form-group">
                                    <!-- dil listesi gelecek -->
                                    <select name="languageID" id="languageID" class="form-control">
                                        <option value="0">Dil Seçin</option>
                                        <?php foreach($languages as $language){
                                            ?>
                                            <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>"><?php echo $language['languageName']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <p class="help-block">Grup Listelemek için Dil Seçin!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <article class="margin-bottom-xxl">
                            <h4>Dil Seçimi</h4><p></p>
                            <p>
                                Varyant Gruplarını Yüklemek için dil seçin
                            </p>
                        </article>
                    </div>


                        <div class="col-lg-offset-1 col-md-8">
                            <div class="card">
                                <div class="card-body ">
                                    <table class="table no-margin">
                                        <thead>
                                        <tr>
                                            <th>Sırala</th>
                                            <th>Ad</th>
                                            <th>Düzenle</th>
                                            <th>Sil</th>
                                        </tr>
                                        </thead>
                                        <tbody class="ui-sortable" data-sortable="true">
                                        <?php

                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4"></div>

                </div>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
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
                    <p id="alertMessage">Grubu silmek istediğinize emin misiniz?</p>
                    <p>Gruba ait tüm seçenekler, grubun diğer dillerdeki karşılıkları ve diğer dillerdeki seçenekleri silinecektir.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-danger" id="deleteVariantGroup">Sil</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-warning">
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

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>
    $("#variantGroupListphp").addClass("active");

    $("#languageID").change(function(){
        var languageID = $(this).val();
        var languageCode = $("#languageID option:selected").data("languagecode");
        if(languageID != 0){
            $.ajax({
                url: "/App/Controller/Admin/AdminProductVariantController.php",
                type: "POST",
                data: {
                    languageCode: languageCode, action: "getVariantGroups"
                },
                success: function(data){
                    var response = JSON.parse(data);
                    if(response.status == "success"){
                        var variantGroups = response.data;
                        var html = "";
                        for(var i = 0; i < variantGroups.length; i++){
                            html += "<tr>";
                            html += '<td><input name="variantID[]" type="hidden" value="'+variantGroups[i].variantGroupID+'"><div class="btn-group" data-toggle="buttons"><label class="btn ink-reaction btn-sm"><input type="checkbox"> <i class="fa fa-arrows"></i></label></div></td>'
                            html += "<td>"+variantGroups[i].variantGroupName+"</td>";
                            html += "<td><a href='/_y/s/s/varyasyonlar/AddVariantGroup.php?groupID="+variantGroups[i].variantGroupID+"&languageCode="+languageCode+"' class='btn btn-primary btn-sm'>Seçenek Düzenle</a> </td>";
                            html += "<td><button class='btn btn-danger btn-sm deleteVariantGroupConfirm' data-id='"+variantGroups[i].variantGroupID+"'>Sil</button></td>";
                            html += "</tr>";
                        }
                        $("tbody").html(html);
                    }
                    else{
                        //alert("Varyant grupları yüklenemedi");
                    }
                }
            });
        }
    });

    $(document).on("click", ".deleteVariantGroupConfirm", function(){
        var variantGroupID = $(this).data("id");
        $("#deleteModal").modal("show");
        $("#deleteVariantGroup").data("id", variantGroupID);
    });

    $("#deleteVariantGroup").click(function(){
        var variantGroupID = $(this).data("id");
        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantGroupID: variantGroupID, action: "deleteVariantGroup"
            },
            success: function(data){
                var response = JSON.parse(data);
                if(response.status == "success"){
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyant grubu silindi");
                    $("#alertModal").modal("show");
                    $("#deleteModal").modal("hide");
                    $("#languageID").trigger("change");
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyant grubu silinemedi");
                    $("#alertModal").modal("show");
                    $("#deleteModal").modal("hide");
                }
            }
        });
    });

    function sortVariantGroups(){
        var variantGroups = [];
        $("tbody tr").each(function(){
            var variantGroupID = $(this).find("input[name='variantID[]']").val();
            variantGroups.push(variantGroupID);
        });
        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantGroupIDs: variantGroups, action: "sortVariantGroups"
            },
            success: function(data){
                var response = JSON.parse(data);
                if(response.status == "success"){
                    //card-header-sm style-success
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyant grupları sıralandı");
                    $("#alertModal").modal("show");
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyant grupları sıralanamadı");
                    $("#alertModal").modal("show");
                }
            }
        });
    }

    $("tbody").sortable({
        handle: 'i.fa.fa-arrows', // Sürükleme işlemi için kullanılacak eleman
        axis: 'y', // Y ekseni boyunca sıralama
        update: function (event, ui) {
            sortVariantGroups();
            console.log("Öğeler sıralandı.");
        }
    });

    //sayfa yüklendikten sonra boş olmayan ilk dili seçili yapalım
    $(document).ready(function(){
        $("#languageID option").each(function(){
            if($(this).val() != 0){
                $("#languageID").val($(this).val());
                $("#languageID").trigger("change");
                return false;
            }
        });
    });
</script>
</body>
</html>
