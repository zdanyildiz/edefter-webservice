<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

$groupID = $_GET["groupID"] ?? 0;
//sayı değise 0
$groupID = intval($groupID);

$languageCode = $_GET["languageCode"] ?? "tr";
//2 karakter değilse tr yapalım
if(strlen($languageCode) != 2){
    $languageCode = "tr";
}

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

$languageIsMain = $languageModel->isMainLanguage($languageCode);

if($languageIsMain['status'] == "success"){
    $languageIsMain = 1;
}
else{
    $languageIsMain = 0;
}

$variants = [];

if($groupID>0){
    include MODEL . 'Admin/AdminProductVariant.php';
    $variantModel = new AdminProductVariant($db);

    $variantGroupResult = $variantModel->getVariantGroup($groupID,$languageCode);
    if($variantGroupResult["status"] == "success"){
        $variantGroup = $variantGroupResult["data"];
        $variantGorupID = $variantGroup["variantGroupID"];
        $variantGroupUniqID = $variantGroup["variantGroupUniqID"];
        $variantGroupName = $variantGroup["variantGroupName"];

        $variantResult = $variantModel->getVariants($variantGorupID, $languageCode);
        if($variantResult["status"] == "success"){
            $variants = $variantResult["data"];
        }
    }
    else{
        $variantGroup = null;
    }
}

$variantGorupID = $variantGorupID ?? 0;
$variantGroupUniqID = $variantGroupUniqID ?? "";
$variantGroupName = $variantGroupName ?? "";

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Varyant Grup Ekle Pozitif E-Ticaret</title>
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
                    <li class="active">Varyant Liste</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">

                    <div class="col-lg-12">
                        <div class="col-lg-offset-1 col-md-8">
                            <div class="card">
                                <div class="card-body ">
                                    <div class="form-group">
                                        <!-- dil listesi gelecek -->
                                        <select name="languageID" id="languageID" class="form-control">
                                            <option value="0">Dil Seçin</option>
                                            <?php foreach($languages as $language){
                                                $selected = strtolower($language['languageCode']) == $languageCode ? "selected" : "";
                                                ?>
                                                <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
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
                                    Geçiş yapmak için dil seçin
                                </p>
                            </article>
                        </div>
                        <div class="col-lg-offset-1 col-md-8">
                            <div class="card">
                                <div class="card-head">
                                    <header>Varyant Grup Ekle</header>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post">
                                        <input type="hidden" id="languageIsMain" name="languageIsMain" value="<?=$languageIsMain?>">
                                        <input type="hidden" id="variantGroupID" name="variantGroupID" value="<?=$variantGorupID?>">
                                        <input type="hidden" id="languageCode" name="languageCode" value="<?=$languageCode?>">
                                        <div class="form-group">
                                            <label for="variantGroupName">Varyant Grup Adı</label>
                                            <input type="text" class="form-control" id="variantGroupName" name="variantGroupName" value="<?=$variantGroupName?>" placeholder="Örn:Renk">
                                        </div>
                                        <button id="saveVariantGroup" type="submit" class="btn btn-primary btn-sm">Kaydet</button>
                                    </form>
                                </div>

                                <?php if($variantGorupID>0):?>
                                    <?php if(count($variants)<=0 and $languageIsMain==0): ?>
                                    <!-- Bu gruba seçenek ekleyebilmek için önce ana dilde seçenek eklenmesi gerekiyor uyarısı verelim-->
                                    <div class="card-head card-head-sm style-danger">
                                        <header><strong class="text-primary"><?=$variantGroupName?></strong> Grubuna Seçenek Eklemek için önce ana dilde seçenek eklemelisiniz!</header>
                                    </div>
                                    <?php elseif($languageIsMain==0): ?>
                                        <div class="card-head card-head-sm style-accent-light">
                                            <header><strong class="text-bold"><?=$variantGroupName?></strong> Grubuna aşağıdaki listeden seçenek ekleyebilirsiniz!</header>
                                        </div>
                                    <?php else: ?>
                                    <div class="card-head card-head-sm">
                                        <header><strong class="text-danger"><?=$variantGroupName?></strong> Grubu için Seçenekleri Ekle</header>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="variantName">Varyant Adı</label>
                                            <input type="text" class="form-control" id="variantName" name="variantName" value="">
                                        </div>
                                        <button id="saveVariant" type="button" class="btn btn-primary-bright btn-sm">Kaydet</button>

                                    </div>
                                    <?php endif;?>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4"></div>
                    </div>
                    <div class="col-lg-12">
                        <div class="col-lg-offset-1 col-md-8">
                            <div class="card">
                                <div class="card-body table-responsive">
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
                                        if(count($variants) > 0){
                                            foreach($variants as $variant){
                                                $disabled = $languageIsMain == 0 ? "disabled" : "";
                                                ?>
                                                <tr id="tr-<?=$variant["variantID"]?>">
                                                    <td>
                                                        <input name="variantID[]" type="hidden" value="<?=$variant["variantID"]?>">
                                                        <div class="btn-group" data-toggle="buttons">
                                                            <label class="btn ink-reaction btn-sm <?=$disabled?>">
                                                                <input type="checkbox"> <i class="fa fa-arrows"></i>
                                                            </label>
                                                        </div></td>
                                                    <td class="variantName">
                                                        <span><?=$variant["variantName"]?></span>

                                                        <div class="form-group col-md-6 hidden">
                                                            <input type="text" name="variantName" class="form-control" value="<?=$variant["variantName"]?>">
                                                        </div>
                                                        <div class="form-group col-md-6 hidden">
                                                            <button class="btn btn-primary btn-sm saveVariantname" data-id="<?=$variant["variantID"]?>">Kaydet</button>
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <a href="javascript(void:0)"
                                                           class="btn btn-primary btn-sm variantEdit"
                                                           data-id="<?=$variant["variantID"]?>"
                                                           data-languageCode="<?=$languageCode?>"
                                                        >Düzenle</a>
                                                    </td>
                                                    <td><button class="btn btn-danger btn-sm deleteVariant <?=$disabled?>" data-id="<?=$variant["variantID"]?>">Sil</button></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
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
    $("#addVariantGroupphp").addClass("active");

    $(document).on("click","#saveVariantGroup", function(e){
        e.preventDefault();
        var variantGroupName = $("#variantGroupName").val();
        var variantGroupID = $("#variantGroupID").val();
        var languageCode = $("#languageCode").val();
        var languageIsMain = $("#languageIsMain").val();
        var action = "addVariantGroup";
        if(languageIsMain == 0){
            action = "addVariantGroupTranslate";
        }
        console.log(action);
        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantGroupName: variantGroupName, variantGroupID: variantGroupID, action: action, languageCode: languageCode
            },
            success: function(data){
                var response = JSON.parse(data);
                if(response.status == "success"){
                    variantGroupID = response.variantGroupID;
                    $("#variantGroupID").val(variantGroupID);
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyant grubu kaydedildi");
                    $("#alertModal").modal("show");
                    //1 saniye sonra yönlendir
                    setTimeout(function(){
                        window.location.href = "/_y/s/s/varyasyonlar/AddVariantGroup.php?groupID="+variantGroupID+"&languageCode="+languageCode;
                    },1000);
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyant grubu kaydedilemedi");
                    $("#alertModal").modal("show");
                }
            }
        });
    });

    $(document).on("click","#saveVariant", function(e){

        var variantName = $("#variantName").val();
        //boş olamaz
        if(variantName == ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
            $("#alertModal #alertMessage").html("Varyant adı boş olamaz");
            $("#alertModal").modal("show");
            return false;
        }

        var isMainLanguage = $("#languageIsMain").val();

        var action = "addVariant";

        if(isMainLanguage == 0){
            action = "addVariantTranslate";
        }
        console.log(action);
        var variantGroupID = $("#variantGroupID").val();
        var languageCode = $("#languageCode").val();

        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantName: variantName, variantGroupID: variantGroupID, action: action, languageCode: languageCode
            },
            success: function(data){
                console.log(data);
                var response = JSON.parse(data);
                if(response.status == "success"){
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyant kaydedildi");
                    $("#alertModal").modal("show");
                    //1 saniye sonra yönlendir
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyant kaydedilemedi");
                    $("#alertModal").modal("show");
                }
            }
        });
    });

    $(document).on("click",".variantEdit", function(e){
        e.preventDefault();
        var variantID = $(this).data("id");

        var languageCode = $(this).data("languagecode");
        var variantName = $("tr .variantName input[name='variantName']").val();

        $("#tr-"+variantID + " .variantName span").addClass("hidden");
        $("#tr-"+variantID+ " .variantName div").removeClass("hidden");

    });

    $(document).on("click",".saveVariantname", function(e){
        e.preventDefault();
        var variantID = $(this).data("id");
        var variantName = $("#tr-"+variantID + " .variantName input[name='variantName']").val();

        if(variantName == ""){
            $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
            $("#alertModal #alertMessage").html("Varyant adı boş olamaz");
            $("#alertModal").modal("show");
            return false;
        }

        var languageCode = $("#languageCode").val();

        var isMainLanguage = $("#languageIsMain").val();

        var action = "updateVariantName";
        if(isMainLanguage == 0){
            action = "addAndUpdateVariantTranslate";
        }

        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantID: variantID, variantName: variantName, action: "addAndUpdateVariantTranslate", languageCode: languageCode
            },
            success: function(data){
                console.log(data);
                var response = JSON.parse(data);
                if(response.status == "success"){
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyant adı güncellendi");
                    $("#alertModal").modal("show");
                    //1 saniye sonra yönlendir
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyant adı güncellenemedi");
                    $("#alertModal").modal("show");
                }
            }
        });
    });

    $(document).on("click",".deleteVariant", function(e){
        e.preventDefault();
        var variantID = $(this).data("id");
        var languageCode = $("#languageCode").val();
        var action = "deleteVariant";
        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantID: variantID, action: action
            },
            success: function(data){
                console.log(data);
                var response = JSON.parse(data);
                if(response.status == "success"){
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyant silindi");
                    $("#alertModal").modal("show");
                    //1 saniye sonra yönlendir
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyant silinemedi");
                    $("#alertModal").modal("show");
                }
            }
        });
    });

    //dil değiştirildiğinde varyant grubu koru dil kodunu değiştir sayfaya git
    $(document).on("change","#languageID", function(e){
        var languageID = $(this).val();
        var languageCode = $(this).find("option:selected").data("languagecode");
        var variantGroupID = $("#variantGroupID").val();

        window.location.href = "/_y/s/s/varyasyonlar/AddVariantGroup.php?groupID="+variantGroupID+"&languageCode="+languageCode;

    });

    function sortVariants(){
        var variantIDs = [];
        $("tbody tr").each(function(){
            var variantID = $(this).find("input[name='variantID[]']").val();
            variantIDs.push(variantID);
        });

        $.ajax({
            url: "/App/Controller/Admin/AdminProductVariantController.php",
            type: "POST",
            data: {
                variantIDs: variantIDs, action: "sortVariants"
            },
            success: function(data){
                console.log(data);
                var response = JSON.parse(data);
                if(response.status == "success"){
                    $("#alertModal .card-head").removeClass("style-warning").addClass("style-success");
                    $("#alertModal #alertMessage").html("Varyantlar sıralandı");
                    $("#alertModal").modal("show");
                }
                else{
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-warning");
                    $("#alertModal #alertMessage").html("Varyantlar sıralanamadı");
                    $("#alertModal").modal("show");
                }
            }
        });
    }

    $("tbody").sortable({
        handle: 'i.fa.fa-arrows', // Sürükleme işlemi için kullanılacak eleman
        axis: 'y', // Y ekseni boyunca sıralama
        update: function (event, ui) {
            sortVariants();
        }
    });
</script>
</body>
</html>
