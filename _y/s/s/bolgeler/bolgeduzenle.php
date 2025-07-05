<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Bölgeleri Düzenle";
$formbaslik="EK BÖLGE LİSTE";

Veri(true);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>EK Bölge Liste</title>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/modules/materialadmin/css/theme-default/libs/toastr/toastr.css?1422823374" />
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">

		<!-- BEGIN HEADER-->
		<?php require_once($anadizin."/_y/s/b/header.php");?>
		<!-- END HEADER-->

		<!-- BEGIN BASE-->
		<div id="base">

			<!-- BEGIN CONTENT-->
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li><?=$sayfabaslik?></li>
                            <li>Bölge Liste</li>
						</ol>
					</div>
					
					
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
										<div class="tools">
											<a class="btn btn-floating-action btn-default-light" href="/_y/s/s/diller/dilekle.php"><i class="fa fa-plus"></i></a>
										</div>
									</div>
								</div><!--end .card -->
							</div><!--end .col -->
							<!-- END ADD CONTACTS FORM -->
						</div><!--end .row -->
						<!-- BEGIN VALIDATION FORM WIZARD -->
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>#</th>
													<th>Bölge Ad</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
											$ilceler="SELECT CountyID,CountyName FROM yerilce Where CountyID>973";
											if($data->query($ilceler))
											{
											    $ilceler_sonuc=$data->query($ilceler);
											    if($ilceler_sonuc->num_rows>0)
                                                {
                                                    while ($ilceler_yazdir=$ilceler_sonuc->fetch_assoc())
                                                    {
                                                        $CountyID=$ilceler_yazdir["CountyID"];
                                                        $CountyName=$ilceler_yazdir["CountyName"];
                                                        ?>
                                                        <tr id="tr<?=$CountyID?>">
                                                            <td><?=$CountyID?></td>
                                                            <td><?=$CountyName?></td>
                                                            <td>
                                                                <a href="/_y/s/s/bolgeler/ilceekle.php?ilceid=<?=$CountyID?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                                                                <a
                                                                        id="bolgesil"
                                                                        href="#textModal"
                                                                        class="btn btn-icon-toggle"
                                                                        data-id="<?=$CountyID?>"
                                                                        data-tur="yerilce"
                                                                        data-toggle="modal"
                                                                        data-placement="top"
                                                                        data-original-title="Sil"
                                                                        data-target="#simpleModal"
                                                                        data-backdrop="true">
                                                                    <i class="fa fa-trash-o"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
												
											}
											else{hatalogisle("İlçe Düzenleme Liste",$data->error);}

                                            $semtler="SELECT AreaID,AreaName FROM yersemt Where AreaID>2469";
                                            if($data->query($semtler))
                                            {
                                                $semtler_sonuc=$data->query($semtler);
                                                if($semtler_sonuc->num_rows>0)
                                                {
                                                    while ($semtler_yazdir=$semtler_sonuc->fetch_assoc())
                                                    {
                                                        $AreaID=$semtler_yazdir["AreaID"];
                                                        $AreaName=$semtler_yazdir["AreaName"];
                                                        ?>
                                                        <tr id="tr<?=$AreaID?>">
                                                            <td><?=$AreaID?></td>
                                                            <td><?=$AreaName?></td>
                                                            <td>
                                                                <a href="/_y/s/s/bolgeler/semtekle.php?semtid=<?=$AreaID?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                                                                <a
                                                                        id="bolgesil"
                                                                        href="#textModal"
                                                                        class="btn btn-icon-toggle"
                                                                        data-id="<?=$AreaID?>"
                                                                        data-tur="yersemt"
                                                                        data-toggle="modal"
                                                                        data-placement="top"
                                                                        data-original-title="Sil"
                                                                        data-target="#simpleModal"
                                                                        data-backdrop="true">
                                                                    <i class="fa fa-trash-o"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }

                                            }
                                            else{hatalogisle("Semt Düzenleme Liste",$data->error);}

                                            $mahalleler="SELECT NeighborhoodID,NeighborhoodName FROM yermahalle Where AreaID>2469";
                                            if($data->query($mahalleler))
                                            {
                                                $mahalleler_sonuc=$data->query($mahalleler);
                                                if($mahalleler_sonuc->num_rows>0)
                                                {
                                                    while ($mahalleler_yazdir=$mahalleler_sonuc->fetch_assoc())
                                                    {
                                                        $NeighborhoodID=$mahalleler_yazdir["NeighborhoodID"];
                                                        $NeighborhoodName=$mahalleler_yazdir["NeighborhoodName"];
                                                        ?>
                                                        <tr id="tr<?=$NeighborhoodID?>">
                                                            <td><?=$NeighborhoodID?></td>
                                                            <td><?=$NeighborhoodName?></td>
                                                            <td>
                                                                <a href="/_y/s/s/bolgeler/mahalleekle.php?mahalleid=<?=$NeighborhoodID?>" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Düzenle"><i class="fa fa-pencil"></i></a>
                                                                <a
                                                                        id="bolgesil"
                                                                        href="#textModal"
                                                                        class="btn btn-icon-toggle"
                                                                        data-id="<?=$NeighborhoodID?>"
                                                                        data-tur="yermahalle"
                                                                        data-toggle="modal"
                                                                        data-placement="top"
                                                                        data-original-title="Sil"
                                                                        data-target="#simpleModal"
                                                                        data-backdrop="true">
                                                                    <i class="fa fa-trash-o"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }

                                            }
                                            else{hatalogisle("Mahalle Düzenleme Liste",$data->error);}
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
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>
		<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">Bölge Sil</h4>
					</div>
					<div class="modal-body">
						<p>Bu silmek istediğinize emin misiniz?</p>
                        <p>Bu bölgeye bağlı alt bölgeler de silinicektir?</p>
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
				$('a#bolgesil').click(function ()
				{
					$silid=$(this).data("id");
                    $siltur=$(this).data("tur");
				});
				$('#silbutton').click(function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=bolge&id="+$silid+"&tur="+$siltur);
				});
			 });
		</script>
		
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script>
			$("#bolgeduzenlephp").addClass("active");
		</script>
	</body>
</html>
