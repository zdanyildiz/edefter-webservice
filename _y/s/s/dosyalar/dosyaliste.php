<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="sayfa";

//düzenle
$sayfabaslik="Dosyaları Düzenle";
$formbaslik="DOSYA LİSTE";

Veri(true);
$dosyalartoplamsayfa=0;
$dosyalar_bitir=50;
if(S(q("sayfa"))==0 || S(q("sayfa"))==1)
{
	$dosyalar_basla=0;
}
else
{
	$dosyalar_basla=(S(q("sayfa"))-1)*$dosyalar_bitir;
}
$dosyaliste_d=0;
$dosyaliste_s="
	SELECT 
		dosyaid,dosyaad,dosya,dosyauzanti 
	FROM 
		dosya
	LIMIT $dosyalar_basla, $dosyalar_bitir
";
$dosyalartoplamsayfa=0;
if($data->query($dosyaliste_s))
{
	$dosyaliste_v=$data->query($dosyaliste_s);
	if($dosyaliste_v->num_rows>0){$dosyaliste_d=1;$dosyalartoplamsayfa=ceil(teksatir("SELECT count(dosyaid) as toplam FROM dosya","toplam")/$dosyalar_bitir);}
}
else{die($data->error);}

unset($dosyaliste_s);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sistem Panel - Dosya Liste</title>

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
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/wizard/wizard.css?1425466601" />
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
							<li><a href="#">DOSYA BİLGİLERİ</a></li>
							<li class="active"><?=$sayfabaslik?></li>
						</ol>
					</div>
					
					
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
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
													<th>Tür</th>
													<th>Dosya Ad</th>
													<th>Düzenle</th>
													<th>Sil</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($dosyaliste_d==1)
											{
												while ($dosyaliste_t=$dosyaliste_v->fetch_assoc()) 
												{
													$dosyaid=$dosyaliste_t["dosyaid"];
													$dosyaad=$dosyaliste_t["dosyaad"];
													$dosyauzanti=$dosyaliste_t["dosyauzanti"];
													$dosya=$dosyaliste_t["dosya"];
												?>
												<tr id="tr<?=$dosyaid?>">
													<td><?=$dosyaid?></td>
													<td><img src="/tema/img/s/<?=$dosyauzanti?>.png" width="40" height="40"><?=$dosyauzanti?></td>
													<td><a href="/m/<?=$dosya?>" target="_blank"><span id="s<?=$dosyaid?>"><?=$dosyaad?></span>
														</a>
													</td>
													<td>
														<div 
															id="d_r<?=$dosyaid?>" 
																style="display:none" class="form-group">
										                	<span style="line-height:20px; width=100%">
										                    	<a href="javascript:void(0)" onclick="document.getElementById('d_r<?=$dosyaid?>').style.display='none'" class="btn ink-reaction btn-flat btn-xs btn-danger">Kapat (x)</a>
										                    </span>
									                    	<div class="clear"></div>
									                		<input name="dosyaID<?=$dosyaid?>" id="dosyaID<?=$dosyaid?>" type="hidden" value="<?=$dosyaid?>" />
									                		<input name="rAd<?=$dosyaid?>" id="rAd<?=$dosyaid?>" type="text" value="<?=$dosyaad?>" class="form-control" style="float:left" />
									                		<span style="margin-left:10px; float:left">
									                    	<a href="javascript:void(0)" 
									                    		onclick="_islem.location='/_y/s/f/sil.php?degistir=dosyaad&id='+document.getElementById('dosyaID<?=$dosyaid?>').value+'&ad='+document.getElementById('rAd<?=$dosyaid?>').value;" class="btn btn-block ink-reaction btn-primary" style="float:right"> Değiştir</a>
									                     	</span>
									                    </div>
														<a href="javascript:void(0)" onclick="document.getElementById('d_r<?=$dosyaid?>').style.display='block'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Edit row"><i class="fa fa-pencil"></i></a>
													</td>
													<td>
														<a 
															id="dosyasil"
															href="#textModal"
															class="btn btn-icon-toggle"
															data-id="<?=$dosyaid?>" 
															data-dosya="/m/<?=$dosya?>" 
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
											unset($dosyaliste_d,$dosyaliste_v,$sayfaid,$sayfaadl);
											?>
											</tbody>
										</table>
									</div><!--end .card-body -->
									<?php sayfala("dosyaliste.php",$dosyalartoplamsayfa,S(q("sayfa")));?>
								</div><!--end .card -->
							</div><!--end .col -->
						</div><!--end .row -->
						<!-- END VALIDATION FORM WIZARD -->
					</div><!--end .section-body -->
				</section>
			</div><!--end #content-->
			<!-- END CONTENT -->
			<!-- BEGIN MENUBAR-->
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
			<!-- END MENUBAR -->
		</div><!--end #base-->
		<!-- END BASE -->
		<!-- BEGIN JAVASCRIPT -->
		<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">dosya Sil</h4>
					</div>
					<div class="modal-body">
						<p>dosya silmek istediğinize emin misiniz?</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
						<button type="button" class="btn btn-primary" id="silbutton">Sil</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
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
		
		<!-- END JAVASCRIPT -->
		<script src="/_y/assets/js/libs/wizard/jquery.bootstrap.wizard.min.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormWizard.js"></script>
		<script>
			$silid=0;
			$dosya="";
			$(document).ready(function()
			{
				$('a#dosyasil').click(function ()
				{
					$silid=$(this).data("id");
					$dosya=$(this).data("dosya");
				});
				$('#silbutton').click(function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=dosya&id="+$silid+"&dosya="+$dosya);
				});
			 });
		</script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script>
			$("#dosyalistephp").addClass("active");
		</script>
	</body>
</html>
