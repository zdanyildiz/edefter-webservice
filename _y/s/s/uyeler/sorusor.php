<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="sorusor";

//düzenle
$sayfabaslik="Üye Mesajları";
$formbaslik="Üye Mesajları";

Veri(true);
$sorusor_d=0;
$sorusor_s="SELECT siparisno, mesajkonusu, mesajicerik, mesajtarih, mesajid, uye.uyeid, uye.uyeadsoyad FROM sorusor
INNER JOIN uye ON uye.uyeid=sorusor.uyeid WHERE mesajsil='0' AND cevapid='0' ORDER BY mesajtarih DESC";
if($data->query($sorusor_s))
{
	$sorusor_v=$data->query($sorusor_s);
	if($sorusor_v->num_rows>0)$sorusor_d=1;
	unset($sorusor_s);
}else
{
	hatalogisle("mesajgetir",$data->error);
}


?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title><?=$formbaslik?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/bootstrap.css?1422792965" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/materialadmin.css?1425466319" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/font-awesome.min.css?1422529194" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/material-design-iconic-font.min.css?1421434286" />
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/wizard/wizard.css?1425466601" />

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
							<li class="btn ink-reaction btn-raised btn-primary disabled">Ayarlar</li>
							<li class="btn ink-reaction btn-raised btn-primary disabled">Üyeler</li>
							<li class="active"><a href="/_y/s/s/uyeler/AddMember.php" class="btn ink-reaction btn-raised btn-primary"><?=$sayfabaslik?> Ekle (+)</a></li>
						</ol>
					</div>			
					<div class="section-body contain-lg">
						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
										<div class="tools">
											<a class="btn btn-floating-action btn-default-light" href="/_y/s/s/uyeler/AddMember.php"><i class="fa fa-plus"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<thead>
												<tr>
													<th>No</th>
													<th>Üye Ad Soyad</th>
													<th>Mesaj Konusu</th>
													<th>Mesaj Tarihi</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($sorusor_d==1)
											{
												while ($sorusor_t=$sorusor_v->fetch_assoc()) 
												{
													$mesajid=$sorusor_t["mesajid"];
													$mesajkonusu=$sorusor_t["mesajkonusu"];
													$uyeid=$sorusor_t["uyeid"];
													$uyeadsoyad=$sorusor_t["uyeadsoyad"];
													$mesajtarih=$sorusor_t["mesajtarih"];											
												?>
												<tr id="tr<?=$mesajid?>">
													<td><?=$mesajid?></td>
													<td><?=$uyeadsoyad?></td>
													<td><?=$mesajkonusu?></td>
													<td><?=$mesajtarih?></td>
													<td>
														<a 
															href="/_y/s/s/uyeler/cevap.php?mesajid=<?=$mesajid?>" 
															class="btn btn-icon-toggle" 
															data-toggle="tooltip" 
															data-placement="top" 
															data-original-title="Cevapla">
															<i class="fa fa-pencil"></i>
														</a>
														<a 
															id="sorusil"
															href="#textModal"
															class="btn btn-icon-toggle"
															data-id="<?=$mesajid?>" 
															data-toggle="modal"
															data-placement="top"
															data-original-title="Sil" 
															data-target="#simpleModal"
															data-backdrop="true">
															<i class="fa fa-trash-o"></i></a>
														</a>
													</td>
												</tr>
											<?php
												}
											}
											unset($sorusor_d,$sorusor_v,$mesajid);
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
						<h4 class="modal-title" id="simpleModalLabel">Soru Sil</h4>
					</div>
					<div class="modal-body">
						<p>Soruyu silmek istediğinize emin misiniz?</p>
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
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>

		<script src="/_y/assets/js/libs/wizard/jquery.bootstrap.wizard.min.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormWizard.js"></script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script>
			$silid=0;
			$(document).ready(function()
			{
				$(document).on("click",'a#sorusil',function ()
				{
					$silid=$(this).data("id");
				});
				$(document).on("click",'#silbutton',function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=soru&id="+$silid);
				});
			 });
		</script>
		<script>
			$("#sorusorphp").addClass("active");
		</script>
	</body>
</html>
