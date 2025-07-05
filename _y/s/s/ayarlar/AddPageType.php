<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var AdminDatabase $db
 */
$buttonName = "Ekle";

include_once MODEL . "Admin/AdminPageType.php";
$pageTypeModel = new AdminPageType($db);

$pageTypeID = $_GET["pageTypeID"] ?? 0;
$pageTypeID = intval($pageTypeID);

if ($pageTypeID > 0) {
    $pageType = $pageTypeModel->getPageTypeById($pageTypeID);

    if (!empty($pageType)) {
        $pageTypeID = $pageType[0]["pageTypeID"];
        $pageTypeName = $pageType[0]["pageTypeName"];
        $pageTypePermission = $pageType[0]["pageTypePermission"];
        $pageTypeView = $pageType[0]["pageTypeView"];
        $pageTypeDeleted = $pageType[0]["pageTypeDeleted"];

        $buttonName = "Güncelle";
    }
}
$pageTypeID = $pageTypeID ?? 0;
$pageTypeName = $pageTypeName ?? "";
$pageTypePermission = $pageTypePermission ?? 0;
$pageTypeView = $pageTypeView ?? 0;
$pageTypeDeleted = $pageTypeDeleted ?? 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Sayfa Tipi Ekle / Düzenle Pozitif Eticaret</title>
    
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
                        <li class="active">Sayfa Tip Ekle / Düzenle</li>
                    </ol>
                </div>
                <div class="section-body contain-lg">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">

                                <form name="addPageTypeForm" id="addPageTypeForm" class="form form-validation form-validate" role="form" method="post">
                                    <input type="hidden" name="pageTypeID" id="pageTypeID" value="<?=$pageTypeID?>">

                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-sm-4">
                                                <div class="form-group floating-label">
                                                    <input
                                                    type="text"
                                                    class="form-control"
                                                    name="pageTypeName"
                                                    id="pageTypeName"
                                                    value="<?=$pageTypeName?>"
                                                    placeholder="Sayfa Tip Adını Yazınız" required aria-required="true" >
                                                    <label for="pageTypeName">Sayfa Tip Adını Yazınız</label>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline checkbox-styled">
                                                    <label>
                                                        <input type="checkbox" name="pageTypePermission"  id="pageTypePermission" value="1"
                                                        <?php if($pageTypePermission==1) echo "checked"; ?>>

                                                        <span>Yetki</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="checkbox checkbox-inline checkbox-styled">
                                                    <label>
                                                        <input type="checkbox" name="pageTypeView" id="pageTypeView" value="1"
                                                        <?php if($pageTypeView==1) echo "checked"; ?>>
                                                        <span>Görünüm</span>
                                                    </label>
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
                </div>
            </section>
        </div>
        <?php require_once(ROOT."/_y/s/b/menu.php");?>

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
        $("#addPageTypephp").addClass("active");

        //formu dinleyelim
        $(document).on("submit", "#addPageTypeForm", function (e) {
            e.preventDefault();
            //pageTypeName boş olamaz
            if ($("#pageTypeName").val() == "") {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Sayfa tip adı alanı boş olamaz.");
                $("#alertModal").modal("show");
                return;
            }

            let pageTypeID = $("#pageTypeID").val();
            let pageTypeName = $("#pageTypeName").val();
            let pageTypePermission;
            if ($("#pageTypePermission").is(":checked")) {
                pageTypePermission = 1;
            }
            else {
                pageTypePermission = 0;
            }

            let pageTypeView;
            if ($("#pageTypeView").is(":checked")) {
                pageTypeView = 1;
            }
            else {
                pageTypeView = 0;
            }

            let action;
            if (pageTypeID > 0) {
                action = "updatePageType";
            } else {
                action = "addPageType";
            }
            var form = $(this);
            var formData;
            formData = "pageTypeID=" + pageTypeID;
            formData += "&pageTypeName=" + pageTypeName;
            formData += "&pageTypePermission=" + pageTypePermission;
            formData += "&pageTypeView=" + pageTypeView;
            formData += "&action=" + action;
            $.ajax({
                url: "/App/Controller/Admin/AdminPageTypeController.php",
                type: "POST",
                data: formData,
                success: function (data) {
                    console.log(data);
                    var response = JSON.parse(data);
                    if (response.status == "success") {
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        $("#alertMessage").text(response.message);
                        $("#alertModal").modal("show");
                        setTimeout(function () {
                            window.location.href = "/_y/s/s/ayarlar/PageTypeList.php";
                        }, 1500);
                    } else {
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        $("#alertMessage").text(response.message);
                        $("#alertModal").modal("show");
                    }
                }
            });
        });

    </script>

</body>
</html>