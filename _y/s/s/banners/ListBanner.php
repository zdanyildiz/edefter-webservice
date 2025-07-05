<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 * @var Helper $helper
 */

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

include_once MODEL . 'Admin/AdminBannerModel.php';
$adminBannerModel = new AdminBannerCreateModel($db);

$bannerTypeModel = new AdminBannerTypeModel($db);
$bannerTypes = $bannerTypeModel->getAllTypes();

$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;
$languageID = intval($languageId);

$languages = $languageModel->getLanguages();

$bannerGroupName = $_GET["bannerGroupName"] ?? $helper->createPassword(8,2);
$buttonName = "Kaydet";
$bannerBaseImage = "/_y/assets/img/header.jpg";

?><!DOCTYPE html>
<html lang="en">
<head>
    <title>Banner Liste Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />
    <!--toaster-->
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/toastr/toastr.min.css?1424887854" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once(ROOT."/_y/s/b/header.php");?>
<div id="base">
    <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">BANNER LİSTELE / DÜZENLE </li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <form class="form" method="post" id="listBannerForm">
                    <div class="row">
                        <div class="card">
                            <div id="languageContainer" class="card-body style-accent-bright">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="languageID">Banner Grubu için gösterim dili seçin</label>
                                        <select name="languageID" id="languageID" class="form-control">
                                            <?php foreach ($languages as $lang) { ?>
                                                <option value="<?=$lang["languageID"]?>" <?php if($lang["languageID"] == $languageID) echo "selected"; ?>><?=$lang["languageName"]?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- banner-types -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bannerTypeID">Banner Tipini seçin:</label>
                                        <select name="bannerTypeID" id="bannerTypeID" class="form-control">
                                            <option value="0">Banner Tipi Seçin</option>
                                            <?php foreach ($bannerTypes as $bannerType) { ?>
                                                <option value="<?=$bannerType["id"]?>"><?=$bannerType["type_name"]?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- banner-container -->
                            <div id="bannerContainer" class="card-body table-responsive">
                                <table id="bannerGroupTable" class="table table-striped table-hover dataTable no-footer">
                                    <thead>
                                        <tr>
                                            <th>Banner Grubu Adı</th>
                                            <th>Banner Tipi</th>
                                            <th>Banner Düzeni</th>
                                            <th>Gösterim Başlangıç</th>
                                            <th>Gösterim Bitiş</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
    <?php require_once(ROOT."/_y/s/b/rightCanvas.php");?>

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
    <!-- banner silme onayı için confirm modal oluşturalım -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-danger">
                    <header class="modal-title" id="confirmModalLabel">Onay</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p id="confirmMessage">Bu banner grubunu silmek istediğinizden emin misiniz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-danger deleteButtonConfirm" data-id="">Sil</button>
                </div>
            </div>
        </div>
    </div>


</div>

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/select2/select2.js"></script>
<script src="/_y/assets/js/libs/toastr/toastr.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>
    $("#listBannerphp").addClass("active");

    let imgRoot = "<?=imgRoot?>";

    function changeAlertModalHeaderColor(color){
        let alertModalCardHead = $("#alertModal .card-head");
        alertModalCardHead.removeClass("style-danger").removeClass("style-success").removeClass("style-warning");
        alertModalCardHead.addClass("style-"+color);
    }

    //sayfa yüklendikten sonra bannerStyle radio kontrol edelim.
    $(document).ready(function(){

        $('select').select2();
        
        $(document).on("change", "#languageID", function(){
            let bannerContainer = $("#bannerContainer");
            bannerContainer.find("tbody").html("");
            let bannerTypeID = $("#bannerTypeID");
            bannerTypeID.val("0");
            bannerTypeID.trigger("change");
        });
        //#bannerTypeID değiştiğinde bannerLayoutları getirelim
        $(document).on("change", "#bannerTypeID", function(){
            let bannerTypeID = $(this).val();
            let languageID = $("#languageID").val();

            if(bannerTypeID === "0"){
                return;
            }

            $.ajax({
                type: 'POST',
                url: "/App/Controller/Admin/AdminBannerModelController.php",
                data: {action: "getBannerGroupsByLanguageIDAndBannerTypeID", bannerTypeID: bannerTypeID, languageID: languageID},
                dataType: 'json',
                success: function (data) {
                    $data = data;
                    if ($data.status === "success") {
                        let bannerGroups = $data.bannerGroups;
                        let bannerContainer = $("#bannerContainer");
                        bannerContainer.find("tbody").html("");
                        console.log(bannerGroups);

                        for (let i = 0; i < bannerGroups.length; i++) {
                            let bannerGroup = bannerGroups[i];
                            let bannerGroupID = bannerGroup.id;
                            let bannerGroupName = bannerGroup.group_name;
                            let layoutName = bannerGroup.layout_name;
                            let layoutDescription = bannerGroup.layout_description;
                            let columns = bannerGroup.columns;
                            let contentAlignment = bannerGroup.content_alignment;
                            let customCss = bannerGroup.custom_css;
                            let orderNum = bannerGroup.order_num;
                            let visibilityStart = bannerGroup.visibility_start;
                            let visibilityEnd = bannerGroup.visibility_end;
                            let bannerDuration = bannerGroup.bannerDuration;
                            let createdAt = bannerGroup.created_at;
                            let updatedAt = bannerGroup.updated_at;

                            let bannerGroupRow = "<tr id='bannerGroupRow_" + bannerGroupID + "'>";
                            bannerGroupRow += "<td>" + bannerGroupName + "</td>";
                            bannerGroupRow += "<td>" + layoutName + "</td>";
                            bannerGroupRow += "<td>" + layoutDescription + "</td>";
                            bannerGroupRow += "<td>" + visibilityStart + "</td>";
                            bannerGroupRow += "<td>" + visibilityEnd + "</td>";
                            bannerGroupRow += "<td><button type='button' class='btn btn-primary btn-sm editButton' data-id='" + bannerGroupID + "'>Düzenle</button><button type='button' class='btn btn-danger btn-sm deleteButton' data-id='" + bannerGroupID + "'>Sil</button></td>";
                            bannerGroupRow += "</tr>";
                            bannerContainer.find("tbody").append(bannerGroupRow);

                        }
                    }
                    else{
                        console.log($data);
                        toastr.options = {
                            "closeButton": true,
                            "debug": false,
                            "newestOnTop": false,
                            "progressBar": true,
                            "positionClass": "toast-top-right",
                            "preventDuplicates": false,
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "5000",
                            "extendedTimeOut": "1000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "fadeIn",
                            "hideMethod": "fadeOut"
                        };
                        toastr.warning("Bu banner tipinde henüz banner eklenmemiş");
                    }
                }
            });
        });

        //düzenle butonuna tıklandığında
        $(document).on("click", ".editButton", function(){
            let bannerGroupID = $(this).data("id");
            window.location.href = "/_y/s/s/banners/AddBanner.php?bannerGroupID="+bannerGroupID;
        });

        //sil onay butonuna tıklandığında
        $(document).on("click", ".deleteButtonConfirm", function(){
            $("#confirmModal").modal("hide")
            let bannerGroupID = $(this).data("id");
            $.ajax({
                type: 'POST',
                url: "/App/Controller/Admin/AdminBannerModelController.php",
                data: {action: "deleteBannerGroup", bannerGroupID: bannerGroupID},
                dataType: 'json',
                success: function (data) {
                    $data = data;
                    if ($data.status === "success") {
                        changeAlertModalHeaderColor("success");
                        $("#alertMessage").html($data.message);
                        $("#alertModal").modal("show");
                        $("#bannerGroupRow_" + bannerGroupID).remove();

                        setTimeout(function(){
                            $("#alertModal").modal("hide");
                        }, 1500);
                    }
                    else{
                        changeAlertModalHeaderColor("danger");
                        $("#alertMessage").html($data.message);
                        $("#alertModal").modal("show");
                    }
                }
            });
        });

        //sil butonuna tıklandığında
        $(document).on("click", ".deleteButton", function(){
            let bannerGroupID = $(this).data("id");
            $("#confirmMessage").html("Bu banner grubunu silmek istediğinizden emin misiniz?");
            $("#confirmModal").modal("show");
            $(".deleteButtonConfirm").data("id", bannerGroupID);
        });

    });
</script>

</body>
</html>
