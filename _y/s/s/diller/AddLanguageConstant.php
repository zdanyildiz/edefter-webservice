<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 * @var Helper $helper
 * @var int $adminAuth
 */

$buttonName = "Kaydet";

include_once MODEL."Admin/AdminLanguage.php";
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();

$languageConstantGroup = $adminLanguage->getLanguageConstantGroups();

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Dil Sabitleri Ekle/Düzenle Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
                        <li class="active">Dil Sabiti Ekle</li>
                    </ol>
                </div>

                <div class="section-body contain-lg">
                    <div class="row">
                        <form id="updateConstantForm" class="form form-validation form-validate" role="form" method="post">
                            <div class="card">
                                <?php if($adminAuth == 0): ?>
                                <div class="card-head" id="addConstantBody">
                                    <header class="card-head-title">Yeni Sabit Ekle</header>
                                    <div class="tools">
                                        <div class="btn-group">
                                            <a class="btn btn-icon-toggle btn-close" data-toggle="modal" data-target="#addConstantModal" aria-hidden="true">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select id="languageID" name="languageID" class="form-control">
                                                    <?php
                                                    foreach($languages as $language){
                                                        $selected = "";
                                                        if($languageID == $language["languageID"]){
                                                            $selected = "selected";
                                                        }

                                                        if($language["languageID"] > 1 && $adminAuth > 0){
                                                            echo "<option value='".$language["languageCode"]."' $selected>".$language["languageName"]."</option>";
                                                        }
                                                        else{
                                                            echo "<option value='".$language["languageCode"]."' $selected>".$language["languageName"]."</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <label for="languageID">Ayarların uygulanacağı dili seçin</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <select id="constantGroup" name="constantGroup" class="form-control">
                                                    <option value="">Seçiniz</option>
                                                    <?php
                                                    foreach($languageConstantGroup as $group){
                                                        echo "<option value='".$group["constantGroup"]."'>".$group["constantGroup"]."</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <label for="constantGroup">Düzenlenecek Grubu Seçin</label>
                                            </div>
                                        </div>

                                        <div class="col-xs-12">
                                            <h4 class="text-warning text-md">Seçtiğiniz Dilin Çevirilerini Yazınız</h4>
                                        </div>

                                    </div>
                                </div>

                                <div class="card-body" id="constantBody"></div>

                                <div class="card-actionbar">
                                    <div class="card-actionbar-row">
                                        <button type="submit" class="btn btn-primary btn-default"><?=$buttonName?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <?php require_once(ROOT."/_y/s/b/menu.php");?>
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Kapat"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="formAlertModalLabel">Uyarı</h4>
                </div>
                <div class="modal-body">
                    <p id="alertText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
        </div>

        <!-- yeni dil sabiti eklemek için modal yapalım -->
        <?php if($adminAuth == 0): ?>
        <div class="modal fade" id="addConstantModal" tabindex="-1" role="dialog" aria-labelledby="addConstantModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="card">
                    <form id="addConstantForm" class="form form-validation form-validate" role="form" method="post" novalidate="novalidate">
                        <div class="card-head card-head-sm style-danger">
                            <header class="modal-title" id="addConstantModalLabel">Yeni Sabit Ekle</header>
                            <div class="tools">
                                <div class="btn-group">
                                    <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                        <i class="fa fa-close"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <select id="constantGroupForAdd" name="constantGroup" class="form-control">
                                            <option value="">Seçiniz</option>
                                            <?php
                                            foreach($languageConstantGroup as $group){
                                                echo "<option value='".$group["constantGroup"]."'>".$group["constantGroup"]."</option>";
                                            }
                                            ?>
                                        </select>
                                        <label for="constantGroup">Sabit Eklenecek Grubu Seçin</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-xs-5">
                                    <div class="no-margin">
                                        <textarea type="text" class="form-control" name="constantName" id="constantName"></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-7">
                                    <div class=" no-margin">
                                        <textarea name="translationValue" id="translationValue" type="text" class="form-control text-light"></textarea>
                                        <label for="translationValue" class="text-accent-bright"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-actionbar">
                            <div class="card-actionbar-row">
                                <button type="submit" class="btn btn-primary btn-default"><?=$buttonName?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- deleteConstantConfirmModal -->
        <div class="modal fade" id="deleteConstantConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConstantConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="card">
                    <div class="card-head card-head-sm style-danger">
                        <header class="modal-title" id="deleteConstantConfirmModalLabel">Sabit Silme</header>
                        <div class="tools">
                            <div class="btn-group">
                                <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                    <i class="fa fa-close"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Sabit silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="card-actionbar">
                        <div class="card-actionbar-row">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-primary" id="deleteConstantConfirmButton">Sil</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
    <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

    <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script src="/_y/assets/js/libs/select2/select2.js"></script>
    <script src="/_y/assets/js/core/source/App.js"></script>
    <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
    <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
    <script src="/_y/assets/js/core/source/AppCard.js"></script>
    <script src="/_y/assets/js/core/source/AppForm.js"></script>
    <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
    <script src="/_y/assets/js/core/source/AppVendor.js"></script>

    <script>
        $("#addLanguageConstantphp").addClass("active");

        //html kullanacağımız js sabit tanımı yapalım
        const row = `
            <div class="row">
                <input type="hidden" name="constantID[]" class="constantID" value="[constantID]">
                <input type="hidden" name="translationID[]" class="translationID" value="[translationID]">
                <div class="col-md-4">
                    <div class="no-margin">
                        <textarea type="text" class="form-control constantValue" id="constantTextInput-[constantID]" name="constantValue[]"" readonly>[constantValue]</textarea>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class=" no-margin">
                        <textarea type="text" id="translatedConstantInput-[constantID]" class="form-control translationValue" name="translationValue[]"">[translationValue]</textarea>
                        <label for="translationValue" class="text-accent-bright"></label>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="javascript:void(0)" id="deleteConstantLink" class="btn btn-icon-toggle btn-default-light" data-toggle="modal" data-target="#deleteConstantConfirmModal" data-id="[constantID]" title="Silmek için tıklayın"><i class="fa fa-trash"></i></a>
                </div>
                <div class="col-md-1">
                    <button id="btn-[constantID]" type="button" class="btn btn-primary btn-sm translateConstantButton hidden" data-id="[constantID]">AI Çeviri</button>
                </div>
            </div>
        `;

        //#constantGorup değişimini dinleyelim
        $(document).on("change", "#constantGroup",function(){
            let constantGorup = $(this).val();
            let action = "getConstantWithGroup";
            let languageCode = $("#languageID").val();


            $("#constantBody").html("");
            $.ajax({
                url:"/App/Controller/Admin/AdminLanguageController.php",
                type:"POST",
                data:{
                    action:action,
                    constantGorup:constantGorup,
                    languageCode:languageCode
                },
                success:function(response){
                    //console.log(response);
                    response = JSON.parse(response);
                    if(response.status === "error"){
                        $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                        return;
                    }
                    else{
                        $("#constantBody").html("");
                        let constantRows = response.constantRows;
                        for(let i=0;i<constantRows.length;i++){

                            let constantRow = constantRows[i];
                            let constantID = constantRow.constantID;
                            let constantName = constantRow.constantName;
                            let constantValue = constantRow.constantValue;
                            let translationID = constantRow.translationID;
                            let translationValue = constantRow.translationValue ;

                            let newRow = row;
                            newRow = newRow.replaceAll("[constantID]",constantID);
                            newRow = newRow.replace("[translationID]",translationID);

                            if(languageCode === "tr"){
                                newRow = newRow.replace("[constantValue]",constantName);
                                newRow = newRow.replace("readonly","");
                            }
                            else{
                                newRow = newRow.replace("[constantValue]",constantValue);
                            }
                            newRow = newRow.replace("[translationID]",translationID);
                            newRow = newRow.replace("[translationValue]",translationValue);

                            $("#constantBody").append(newRow);
                        }
                        if(languageCode !== "tr"){
                            $(".translateConstantButton").removeClass("hidden");
                        }
                    }
                }
            });
        });

        //dil değişince constantBody'i temizleyelim
        $(document).on("change","#languageID",function(){
            $("#constantBody").html("");
            //grup seçimini seçiniz yapalım
            $("#constantGroup").val("");
        });

        //updateConstantForm submit
        $("#updateConstantForm").submit(function(e){
            e.preventDefault();

            let languageCode = $("#languageID").val();
            let constantGroup = $("#constantGroup").val();

            let ConstantIds = [];
            $(".constantID").each(function(){
                ConstantIds.push($(this).val());
            });

            let translationValues = [];
            $(".translationValue").each(function(){
                translationValues.push($(this).val());
            });

            let translationIDs = [];
            $(".translationID").each(function(){
                translationIDs.push($(this).val());
            });


            let action = "updateLanguageConstantTranslation";

            let constantData = {
                constantIDs: ConstantIds,
                translationIDs: translationIDs,
                translationValues: translationValues,
                languageCode: languageCode,
                constantGroup: constantGroup
            };

            if(languageCode == "tr") {
                action = "updateLanguageConstant";

                let constantNames = [];
                $(".constantValue").each(function(){
                    constantNames.push($(this).val());
                });

                constantData.constantNames = constantNames; // Add constantNames to constantData
            }

            //constantData'yı konsola basalım
            console.log(constantData);
            $.ajax({
                url:"/App/Controller/Admin/AdminLanguageController.php",
                type:"POST",
                data:{
                    action:action,
                    constantData:constantData
                },
                success:function(response){
                    console.log(response);
                    response = JSON.parse(response);
                    if(response.status == "error"){
                        $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                    }
                    else{
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-succes");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                        setTimeout(function(){
                            $("#alertModal").modal("hide");
                        },1000);
                    }
                }
            });
        });

        $(document).on("submit", "#addConstantForm", function (e){
            e.preventDefault();

            var constantGroup = $("#constantGroupForAdd").val();
            var constantName = $("#constantName").val();
            var translationValue = $("#translationValue").val();
            if(constantName == "" || translationValue == ""){
                $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");
                $("#alertText").html("Eklenecek alanları boş bırakmayınız");
                $("#alertModal").modal("show");
                return false;
            }

            $.ajax({
                url:"/App/Controller/Admin/AdminLanguageController.php",
                type:"POST",
                data:{
                    action:"addLanguageConstant",
                    constantData:{
                        constantGroup: constantGroup,
                        constantName: constantName,
                        translationValue: translationValue
                    }
                },
                success:function(response){
                    console.log(response);
                    response = JSON.parse(response);
                    if(response.status == "error"){
                        $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                    }
                    else{
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-succes");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                        $("#addConstantModal").modal("hide");
                        setTimeout(function(){
                            $("#alertModal").modal("hide");
                        },1000);
                        setTimeout(function(){
                            $("#addConstantModal").modal("show");
                        },1000);

                        //sayfayı yenileyelim
                        //window.location.reload();
                    }
                }
            });
        });

        //deleteConstantConfirmModal'a tıklandığında deleteConstantForm gönderilir
        $(document).on("click","#deleteConstantConfirmButton",function(){
            var constantID = $(this).data("id");
            $.ajax({
                url:"/App/Controller/Admin/AdminLanguageController.php",
                type:"POST",
                data:{
                    action:"deleteLanguageConstant",
                    constantID:constantID
                },
                success:function(response){
                    console.log(response);
                    response = JSON.parse(response);
                    if(response.status == "error"){
                        $("#alertModal .card-head").removeClass("style-succes").addClass("style-danger");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                    }
                    else{
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-succes");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                        $("#deleteConstantConfirmModal").modal("hide");
                        //sayfayı yenileyelim
                        window.location.reload();
                    }
                }
            });
        });

        $(document).on("click","#deleteConstantLink",function(){
            var constantID = $(this).data("id");
            $("#deleteConstantConfirmButton").data("id",constantID);
        });

        $(document).on("click", ".translateConstantButton", function() {
            var translateButton = $(this);
            var constantID = translateButton.data("id");
            var constantText = $("#constantTextInput-"+constantID).val();
            var targetLanguage = $("#languageID").val(); // Hedef dil
            var translatedConstantInput = $("#translatedConstantInput-"+constantID);

            console.log(constantID);
            console.log(constantText);
            console.log(targetLanguage);

            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
            $("#alertText").html("Çeviri yapılıyor...");
            $("#alertModal").modal("show");

            $.ajax({
                url: "/App/Controller/Admin/AdminChatCompletionController.php",
                type: "POST",
                data: {
                    action: "translateConstant",
                    text: constantText,
                    language: targetLanguage
                },
                success: function(response) {
                    console.log(response);
                    response = JSON.parse(response);
                    if(response.status === "error") {
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        $("#alertText").html(response.message);
                        $("#alertModal").modal("show");
                    } else {
                        //$("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        //$("#alertText").html("Çeviri başarılı!");
                        //$("#alertModal").modal("show");
                        translatedConstantInput.val(response.data); // Çeviriyi input alanına yerleştir
                        $("#alertModal").modal("hide");
                    }
                },
                error: function() {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertText").html("Bir hata oluştu.");
                    $("#alertModal").modal("show");
                }
            });
        });


        $('select').select2();

    </script>
</body>
</html>
