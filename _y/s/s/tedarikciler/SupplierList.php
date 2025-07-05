<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 */

include_once MODEL . 'Admin/AdminSupplier.php';
$supplierModel = new AdminSupplier($db,$config);

$suppliers = $supplierModel->getAllSuppliers();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Tedarikçi Liste Pozitif ETicaret</title>
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
                    <li class="active">Tedarikçi Listesi</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
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
                                    if ($suppliers) {
                                        foreach ($suppliers as $supplier) {
                                            $supplierTitle = $supplier['supplierTitle'];
                                            $supplierTitle = $helper->decrypt($supplierTitle, $config->key);
                                            ?>
                                            <tr>
                                                <td><?php echo $supplier['supplierID']; ?></td>
                                                <td><?php echo $supplierTitle; ?></td>
                                                <td>
                                                    <a href="/_y/s/s/tedarikciler/AddSupplier.php?supplierID=<?php echo $supplier['supplierID']; ?>" class="btn btn-xs btn-primary">Güncelle</a>
                                                    <button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#deleteSupplierConfirmModal" data-id="<?php echo $supplier['supplierID']; ?>">Sil</button>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="3">Tedarikçi bulunamadı.</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>

    <div class="modal fade" id="deleteSupplierConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteSupplierConfirmModalLabel" aria-hidden="true">
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
                    <p id="alertMessage">Tedarikçiyi silmek istediğinize emin misiniz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-danger" id="deleteBrandConfirmButton">Sil</button>
                </div>
            </div>
        </div>
    </div>

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
    $("#supplierListphp").addClass("active");

    $("#deleteSupplierConfirmModal").on("show.bs.modal", function (e) {
        var id = $(e.relatedTarget).data('id');
        $("#deleteBrandConfirmButton").data('id', id);
    });

    $("#deleteBrandConfirmButton").click(function () {
        var id = $(this).data('id');
        $.ajax({
            url: "/App/Controller/Admin/AdminSupplierController.php",
            type: "POST",
            data: {
                action: "deleteSupplier",
                supplierID: id
            },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status == "success") {
                    window.location.reload();
                } else {
                    $("#alertMessage").text(data.message);
                    $("#alertModal").modal('show');
                }
            }
        });
    });
</script>
</body>
</html>
