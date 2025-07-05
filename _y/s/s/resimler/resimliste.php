<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="Resim";

//düzenle
$sayfabaslik="Resimleri Düzenle";
$formbaslik="RESİM LİSTE";

Veri(true);
if(q("sil")==1)
{
	$sayfaresimayni_s="
		SELECT 
			resimid,sayfaid
		FROM
			sayfalisteresim
		GROUP BY resimid HAVING COUNT(*)>1
	";
	if($data->query($sayfaresimayni_s))
	{
		$sayfaresimayni_v=$data->query($sayfaresimayni_s);unset($sayfaresimayni_s);
		if($sayfaresimayni_v->num_rows>0)
		{
			while ($sayfaresimayni_t=$sayfaresimayni_v->fetch_assoc())
			{
				$sayfaresimayni_sayfaid=$sayfaresimayni_t["sayfaid"];
				$sayfaresimayni_resimid=$sayfaresimayni_t["resimid"];
				$data->query("DELETE FROM sayfalisteresim WHERE resimid='". $sayfaresimayni_resimid ."' and sayfaid='".$sayfaresimayni_sayfaid."'");
				ekle("resimid,sayfaid",$sayfaresimayni_resimid."|*_".$sayfaresimayni_sayfaid,"sayfalisteresim",25);
			}unset($sayfaresimayni_t,$sayfaresimayni_sayfaid,$sayfaresimayni_resimid);
		}unset($sayfaresimayni_v);
	}else{hatalogisle("sayfaresimliste",$data->error);}

	$resimliste_s="
		SELECT 
			resimid,resim.resimklasorid,resimad,resim,resimklasorad 
		FROM 
			resim 
			inner join resimklasor on 
			resimklasor.resimklasorid=resim.resimklasorid 
	";
	if($data->query($resimliste_s))
	{
		$resimliste_v=$data->query($resimliste_s);unset($resimliste_s);
		if($resimliste_v->num_rows>0)
		{
			while ($resimliste_t=$resimliste_v->fetch_assoc()) 
			{
				$resimid=$resimliste_t["resimid"];;
				$klasorad=$resimliste_t["resimklasorad"];
				$resim=$resimliste_t["resim"];
				if(!file_exists($anadizin . "/m/r/".$klasorad."/".$resim))
				{
					$data->query("DELETE FROM sayfalisteresim WHERE resimid='". $resimid ."'");
					$data->query("DELETE FROM resimgaleriliste WHERE resimid='". $resimid ."'");
					$data->query("UPDATE kategori SET resimid='0' WHERE resimid='". $resimid ."'");
					$data->query("DELETE FROM resim WHERE resimid='". $resimid ."'");
				}
			}unset($resimliste_t,$resimid,$klasorad,$resim);
		}unset($resimliste_v);
	}else{hatalogisle("resimliste",$data->error);}
}
$resimler_bitir=50;
if(S(q("sayfa"))==0 || S(q("sayfa"))==1)
{
	$resimler_basla=0;
}
else
{
	$resimler_basla=(S(q("sayfa"))-1)*$resimler_bitir;
}
$resimlertoplamsayfa=0;
$resimliste_d=0;
$resimliste_s="
	SELECT 
		resimid,resim.resimklasorid,resimad,resim,resimklasorad 
	FROM 
		resim 
		inner join resimklasor on 
		resimklasor.resimklasorid=resim.resimklasorid 
	LIMIT $resimler_basla, $resimler_bitir";
if($data->query($resimliste_s))
{
	$resimliste_v=$data->query($resimliste_s);unset($resimliste_s);
	if($resimliste_v->num_rows>0){$resimliste_d=1;$resimlertoplamsayfa=ceil(teksatir("SELECT count(resimid) as toplam FROM resim inner join resimklasor on resimklasor.resimklasorid=resim.resimklasorid ","toplam")/$resimler_bitir);}
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sistem Panel - Sayfa Liste</title>

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
							<li><a href="#">RESİM BİLGİLERİ</a></li>
							<li class="active"><?=$sayfabaslik?></li>
						</ol>
					</div>
					
					
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<div class="tools">Kırık Resim Sil
											<a 
												class="btn btn-floating-action btn-default-light" 
												href="#textModal" 
												data-toggle="modal"
												data-placement="top"
												data-original-title="Sil" 
												data-target="#kirikresimpencere"
												title="Kırık Resim Sil"><i class="fa fa-minus"></i></a>
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
													<th>Klasor</th>
													<th>Resim</th>
													<th>Düzenle</th>
													<th>Sil</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($resimliste_d==1)
											{
												while ($resimliste_t=$resimliste_v->fetch_assoc()) 
												{
													$resimid=$resimliste_t["resimid"];
													$resimad=$resimliste_t["resimad"];
													$klasorad=$resimliste_t["resimklasorad"];
													$resim=$resimliste_t["resim"];
													?>
														<tr id="tr<?=$resimid?>">
															<td><?=$resimid?></td>
															<td><?=$klasorad?><a></a></td>
															<td><a href="/m/r/<?=$klasorad?>/<?=$resim?>" target="_blank">
																<img src="/m/r/?resim=<?=$klasorad?>/<?=$resim?>&g=70&y=70" width="60" height="60"><span id="s<?=$resimid?>" style="margin-left:20px"><?=$resimad?></span></a>
															</td>
															<td>
																<div 
																	id="d_r<?=$resimid?>" 
																		style="display:none" class="form-group">
												                	<span style="line-height:20px; width=100%">
												                    	<a href="javascript:void(0)" onclick="document.getElementById('d_r<?=$resimid?>').style.display='none'" class="btn ink-reaction btn-flat btn-xs btn-danger">Kapat (x)</a>
												                    </span>
											                    	<div class="clear"></div>
											                		<input name="ResimID<?=$resimid?>" id="ResimID<?=$resimid?>" type="hidden" value="<?=$resimid?>" />
											                		<input name="rAd<?=$resimid?>" id="rAd<?=$resimid?>" type="text" value="<?=$resimad?>" class="form-control" style="float:left" />
											                		<span style="margin-left:10px; float:left">
											                    	<a href="javascript:void(0)" 
											                    		onclick="_islem.location='/_y/s/f/sil.php?degistir=resimad&id='+document.getElementById('ResimID<?=$resimid?>').value+'&ad='+document.getElementById('rAd<?=$resimid?>').value;" class="btn btn-block ink-reaction btn-primary" style="float:right"> Değiştir</a>
											                     	</span>
											                    </div>
																<a href="javascript:void(0)" onclick="document.getElementById('d_r<?=$resimid?>').style.display='block'" class="btn btn-icon-toggle" data-toggle="tooltip" data-placement="top" data-original-title="Edit row"><i class="fa fa-pencil"></i></a>
															</td>
															<td>
																<a 
																	id="resimsil"
																	href="#textModal"
																	class="btn btn-icon-toggle"
																	data-id="<?=$resimid?>" 
																	data-resim="/m/r/<?=$klasorad?>/<?=$resim?>" 
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
											unset($resimliste_d,$resimliste_v,$sayfaid,$sayfaadl);
											?>
											</tbody>
										</table>
									</div><!--end .card-body -->
									<?php sayfala("resimliste.php?sil=".q("sil"),$resimlertoplamsayfa,S(q("sayfa")));?>
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
						<h4 class="modal-title" id="simpleModalLabel">Resim Sil</h4>
					</div>
					<div class="modal-body">
						<p>Resim silmek istediğinize emin misiniz?</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
						<button type="button" class="btn btn-primary" id="silbutton">Sil</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div>
		<div class="modal fade" id="kirikresimpencere" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">Kırık Resim Sil</h4>
					</div>
					<div class="modal-body">
						<p>Yüklenmiş görünen fakat açılmayan tüm resimler temizlenecektir. Bu işlem geri alınamaz!</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
						<button type="button" class="btn btn-primary" id="kirikresimsilbutton">Sil</button>
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
			$resim="";
			$(document).ready(function()
			{
				$('a#resimsil').click(function ()
				{
					$silid=$(this).data("id");
					$resim=$(this).data("resim");
				});
				$('#silbutton').click(function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=resim&id="+$silid+"&resim="+$resim);
				});
				$('#kirikresimsilbutton').click(function ()
				{
					window.location.href="/_y/s/s/resimler/resimliste.php?sil=1";
				})
			 });
		</script>
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script>
			$("#resimlistephp").addClass("active");
		</script>
	</body>
</html>
