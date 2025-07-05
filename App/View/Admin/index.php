<?php
/**
 * @var AdminSession $adminSession
 * @var Database $db
 * @var Router $router
 */
$seoTitle = $router->seoTitle;
$seoLink = $router->seoLink;
?>
<!doctype html>
<html lang="en" dir="ltr">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sash – Bootstrap 5  Admin & Dashboard Template">
    <meta name="author" content="Spruko Technologies Private Limited">
    <meta name="keywords"
          content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="/Public/Admin/assets/images/brand/favicon.ico" />

    <!-- TITLE -->
    <title><?=$seoTitle?> Pozitif E-Ticaret Yönetim Paneli </title>

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="/Public/Admin/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="/Public/Admin/assets/css/style.css" rel="stylesheet" />
    <link href="/Public/Admin/assets/css/dark-style.css" rel="stylesheet" />
    <link href="/Public/Admin/assets/css/transparent-style.css" rel="stylesheet">
    <link href="/Public/Admin/assets/css/skin-modes.css" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="/Public/Admin/assets/css/icons.css" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="/Public/Admin/assets/colors/color1.css" />

</head>

<body class="app sidebar-mini ltr light-mode">

<!-- GLOBAL-LOADER -->
<div id="global-loader">
    <img src="/Public/Admin/assets/images/loader.svg" class="loader-img" alt="Loader">
</div>
<!-- /GLOBAL-LOADER -->

<!-- PAGE -->
<div class="page">
    <div class="page-main">

        <!-- app-Header -->
        <?php include VIEW.'Admin/Layouts/header.php'; ?>
        <!-- /app-Header -->

        <!--APP-SIDEBAR-->
        <?php include VIEW.'Admin/Layouts/nav_left.php'; ?>

        <!--app-content open-->
        <div class="main-content app-content mt-0">
            <div class="side-app">

                <!-- CONTAINER -->
                <div class="main-container container-fluid">

                    <!-- PAGE-HEADER -->
                    <div class="page-header">
                        <h1 class="page-title"><?=$seoTitle?></h1>
                        <div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/Admin">Ana Ekran</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="<?=$seoLink?>" class="active" aria-current="page"> <?=$seoTitle?></a></li>
                            </ol>
                        </div>
                    </div>
                    <!-- PAGE-HEADER END -->
                    <?php
                    $viewName = $router->contentName;
                    $view = new ViewLoader();

                    $viewData = [
                        "adminSession" => $adminSession,
                        "db" => $db,
                        "router" => $router
                    ];

                    $view->loadView($viewName,$viewData);
                    ?>
                </div>
                <!-- CONTAINER END -->
            </div>
        </div>
        <!--app-content close-->

    </div>

    <!-- Sidebar-right -->
    <?php include VIEW.'Admin/Layouts/sidebar_right.php'; ?>
    <!--/Sidebar-right-->

    <!-- FOOTER -->
    <?php include VIEW.'Admin/Layouts/footer.php'; ?>
    <!-- FOOTER END -->

</div>

<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

<!-- JQUERY JS -->
<script src="/Public/Admin/assets/js/jquery.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="/Public/Admin/assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="/Public/Admin/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<!-- SPARKLINE JS-->
<script src="/Public/Admin/assets/js/jquery.sparkline.min.js"></script>

<!-- Sticky js -->
<script src="/Public/Admin/assets/js/sticky.js"></script>

<!-- CHART-CIRCLE JS-->
<script src="/Public/Admin/assets/js/circle-progress.min.js"></script>

<!-- PIETY CHART JS-->
<script src="/Public/Admin/assets/plugins/peitychart/jquery.peity.min.js"></script>
<script src="/Public/Admin/assets/plugins/peitychart/peitychart.init.js"></script>

<!-- SIDEBAR JS -->
<script src="/Public/Admin/assets/plugins/sidebar/sidebar.js"></script>

<!-- Perfect SCROLLBAR JS-->
<script src="/Public/Admin/assets/plugins/p-scroll/perfect-scrollbar.js"></script>
<script src="/Public/Admin/assets/plugins/p-scroll/pscroll.js"></script>
<script src="/Public/Admin/assets/plugins/p-scroll/pscroll-1.js"></script>


<!-- INTERNAL SELECT2 JS -->
<script src="/Public/Admin/assets/plugins/select2/select2.full.min.js"></script>


<?php
$jsContent = $adminSession->getSession("jsContent") ?? "";
echo $jsContent;
?>

<!-- INTERNAL Flot JS -->
<script src="/Public/Admin/assets/plugins/flot/jquery.flot.js"></script>
<script src="/Public/Admin/assets/plugins/flot/jquery.flot.fillbetween.js"></script>
<script src="/Public/Admin/assets/plugins/flot/chart.flot.sampledata.js"></script>
<script src="/Public/Admin/assets/plugins/flot/dashboard.sampledata.js"></script>

<!-- INTERNAL Vector js -->
<script src="/Public/Admin/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="/Public/Admin/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<!-- SIDE-MENU JS-->
<script src="/Public/Admin/assets/plugins/sidemenu/sidemenu.js"></script>

<!-- INTERNAL INDEX JS -->
<script src="/Public/Admin/assets/js/index1.js"></script>

<!-- Color Theme js -->
<script src="/Public/Admin/assets/js/themeColors.js"></script>

<!-- CUSTOM JS -->
<script src="/Public/Admin/assets/js/custom.js"></script>

</body>

</html>
