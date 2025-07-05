<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="sayfa";

//düzenle
$sayfabaslik="videoları Düzenle";
$formbaslik="VİDEO LİSTE";

Veri(true);
$videolartoplamsayfa=0;
$videolar_bitir=50;
if(S(q("sayfa"))==0 || S(q("sayfa"))==1)
{
	$videolar_basla=0;
}
else
{
	$videolar_basla=(S(q("sayfa"))-1)*$videolar_bitir;
}
$videoliste_d=0;
$videoliste_s="
	SELECT 
		videoid,videoad,video,videouzanti 
	FROM 
		video 
	LIMIT $videolar_basla, $videolar_bitir
";
if($data->query($videoliste_s))
{
	$videoliste_v=$data->query($videoliste_s);
	if($videoliste_v->num_rows>0){$videoliste_d=1;$videolartoplamsayfa=ceil(teksatir("SELECT count(videoid) as toplam FROM video","toplam")/$videolar_bitir);}
	unset($videoliste_s);
}else{die($data->error);}

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sistem Panel - video Liste</title>

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
							<li><a href="#">video BİLGİLERİ</a></li>
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
													<th>video Ad</th>
													<th>Düzenle</th>
													<th>Sil</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($videoliste_d==1)
											{
												while ($videoliste_t=$videoliste_v->fetch_assoc()) 
												{
													$videoid=$videoliste_t["videoid"];
													$videoad=$videoliste_t["videoad"];
													$videouzanti=$videoliste_t["videouzanti"];
													if(BosMu($videouzanti))$videouzanti="youtube";
													$video=$videoliste_t["video"];
												?>
												<tr id="tr<?=$videoid?>">
													<td><?=$videoid?></td>
													<td><img src="/tema/img/s/<?=$videouzanti?>.png" width="40" height="40"><?=$videouzanti?></td>
													<td><a href="/m/<?=$video?>" target="_blank"><span id="s<?=$videoid?>"><?=$videoad?></span>
														</a>
													</td>
													<td>
														<div 
															id="d_r<?=$videoid?>" 
																style="display:none" class="form-group">
										                	<span style="line-height:20px; width=100%">
										                    	<a href="javascript:void(0)" onclick="document.getElementById('d_r<?=$videoid?>').style.display='none'" class="btn ink-reaction btn-flat btn-xs btn-danger">Kapat (x)</a>
										                    </span>
									                    	<div class="clear"></div>
									                		<input name="videoID<?=$videoid?>" id="videoID<?=$videoid?>" type="hidden" value="<?=$videoid?>" />
									                		<input name="rAd<?=$videoid?>" id="rAd<?=$videoid?>" type="text" value="<?=$videoad?>" class="form-control" style="float:left" />
									                		<span style="margin-left:10px; float:left">
									                    	<a href="javascript:void(0)" 
									                    		onclick="_islem.location='/_y/s/f/sil.php?degistir=videoad&id='+document.getElementById('videoID<?=$videoid?>').value+'&ad='+document.getElementById('rAd<?=$videoid?>').value;" class="btn btn-block ink-reaction btn-primary" style="float:right"> Değiştir</a>
									                     	</span>
									                    </div>
														<a href="javascript:void(0)" onclick="document.getElementById('d_r<?=$videoid?>').style.display='block'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Edit row"><i class="fa fa-pencil"></i></a>
													</td>
													<td>
														<a 
															id="videosil"
															href="#textModal"
															class="btn btn-icon-toggle"
															data-id="<?=$videoid?>" 
															data-video="/m/<?=$video?>" 
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
											unset($videoliste_d,$videoliste_v,$sayfaid,$sayfaadl);
											?>
											</tbody>
										</table>
									</div><!--end .card-body -->
									<?php sayfala("videoliste.php",$videolartoplamsayfa,S(q("sayfa")));?>
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
						<h4 class="modal-title" id="simpleModalLabel">video Sil</h4>
					</div>
					<div class="modal-body">
						<p>video silmek istediğinize emin misiniz?</p>
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
			$video="";
			$(document).ready(function()
			{
				$('a#videosil').click(function ()
				{
					$silid=$(this).data("id");
					$video=$(this).data("video");
				});
				$('#silbutton').click(function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=video&id="+$silid+"&video="+$video);
				});
			 });
		</script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script>
			$("#videolistephp").addClass("active");
		</script>
	</body>
</html>
