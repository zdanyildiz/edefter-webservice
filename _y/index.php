<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");?>
<!doctype html>
<html lang="tr">
	<head>
        <title>Anasayfa Pozitif Eticaret</title>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/rickshaw/rickshaw.css?1422792967" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/morris/morris.core.css?1420463396" />
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body id="site" class="menubar-hoverable header-fixed ">
		<!-- BEGIN HEADER-->
		<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/b/header.php");?>
		<!-- END HEADER-->
		<!-- BEGIN BASE-->
		<div id="base">
			<div class="offcanvas"></div>
			<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/b/main.php");?>
			<!-- END CONTENT -->
			<!-- BEGIN MENUBAR-->
			<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/b/menu.php");?>
		</div>

        <script src="/_y/assets/js/libs/jquery/jquery-3.7.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-3.3.2.min.js"></script>

		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>

        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>




        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
		<script>
			$("#home").addClass("active");
		</script>
	</body>
</html>
