<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
if(BosMu(q("formhataaciklama")))$formhataaciklama="";else $formhataaciklama=q("formhataaciklama");
$formtablo="yorum";

//düzenle
$sayfabaslik="Yorum Listesi";
$formbaslik="Yorum Listesi";

Veri(true);
$yorum_d=0;
$yorum_s="SELECT yorumid, yorumurunid,yorumcevapid,yorumicerik,yorumsil,uyeid,yorumtarih,sayfa.sayfaad FROM yorum INNER JOIN sayfa ON sayfa.sayfaid=yorum.yorumurunid WHERE yorumsil='0' and yorumcevapid='0' GROUP BY yorumid ORDER BY yorumid ASC";
$yorum_v=$data->query($yorum_s);
if($yorum_v->num_rows>0)$yorum_d=1;
unset($yorum_s);
function cevapgetir($yorumid){
	global $data;
	$cevap_d=0;
	$cevap_s="SELECT yorumid,yorumurunid,yorumcevapid,yorumicerik,yorumsil,uyeid,yorumtarih FROM yorum WHERE yorumsil='0' and yorumcevapid='".$yorumid."'";
	$cevap_v=$data->query($cevap_s);
	if($cevap_v->num_rows>0)$cevap_d=1;
	unset($cevap_s);
	if($cevap_d==1)
	{
		while ($cevap_t=$cevap_v->fetch_assoc()) 
		{
			$yorumtarih=$cevap_t["yorumtarih"];
			$yorumcevapid=$cevap_t["yorumid"];
			$uyeid=$cevap_t["uyeid"];
			$yorumurunid=$cevap_t["yorumurunid"];
			$cevapyorum=$cevap_t["yorumicerik"];?>

			<tr style="background-color:#fbf7f7"><td colspan="2"><?=$yorumtarih?> </td><td colspan="3"><?=$cevapyorum?></td><td colspan="5">
				<?php if(dogrula("yorum","yorumcevapid='".$yorumid."' and uyeid!='0' "))
				{
				?>
				<button type="button" data-id="<?=$yorumcevapid?>" class="cevapver">CEVAPLA</td></tr>
				<?php }	?>
			<tr class="ackapa" style="display:none" id="form-<?=$yorumcevapid?>">
				<td colspan="8">
					<form method="post" class="yorumcevap" action=""><input type="hidden" name="yorumcevap" value="1">
						<input type="hidden" name="uyeid" value="<?=$uyeid?>">
						<input type="hidden" name="yorumurunid" value="<?=$yorumurunid?>">
						<input type="hidden" id="yorumcevapid" name="yorumcevapid" value="<?=$yorumcevapid?>">
						<textarea rows="6" cols="70" name="yorumicerik" id="yorumicerik" required=""></textarea><br>
						<button type="submit" class="iptal" data-id="<?=$yorumcevapid?>">İPTAL</button><button type="submit" class="gonder">GÖNDER</button>
					</form>
				</td>
			</tr>
			<?php
			cevapgetir($yorumcevapid);
		}
	}
}
if(S(f("yorumcevap"))==1)
{
	$formhata=0;
	$formhataaciklama="";
	$yorumicerik=f("yorumicerik");
	$yorumurunid=s(f("yorumurunid"));
	$yorumcevapid=S(f("yorumcevapid"));
	$uyeid=S(f("uyeid"));
	$simdi=date("Y-m-d H:i:s");
	if(!BosMu($yorumicerik))
	{
		$tablo='yorum';
		$sutunlar="yorumurunid,yorumcevapid,yorumicerik,yorumsil,yorumtarih,uyeid";
		$degerler=$yorumurunid."|*_".$yorumcevapid."|*_".$yorumicerik."|*_0|*_".$simdi."|*_0";
		ekle($sutunlar,$degerler,$tablo,0);
		$uyebilgi=coksatir("SELECT uyeadsoyad,uyeeposta FROM uye WHERE uyeid='".$uyeid."' ","");
		if($uyebilgi){
					$uyeadsoyad=$uyebilgi["uyeadsoyad"];
					$uyeeposta=$uyebilgi["uyeeposta"];
					$uyeeposta=coz($uyeeposta,$anahtarkod);
					}
		$mailicerik='<b>Gönderim Tarihi:</b> '.$simdi.'<br><b> Sayın: </b>'.$uyeadsoyad.'<br><b>Mesaj Cevap İçeriği:</b> '.$yorumicerik.'
		';
		MailGonder($uyeeposta,"$siteDomain Yorum Cevabınız Var", $mailicerik);
		$formhataaciklama="<p>Mesajınız gönderilmiştir.</p>";
		$uyaribaslik="İşlem Başarılı";
		$_SESSION["formhata"]=$uyaribaslik.'||'.$formhataaciklama;
		exit(header("Location: /_y/s/s/yorum/yorumliste.php"));
	}
	else
	{
		$formhata=1;$formhataaciklama="<p>Mesaj içeriği boş olamaz.</p>";
		$uyaribaslik="Dikkat";
		$_SESSION["formhata"]=$uyaribaslik.'||'.$formhataaciklama;
	}
}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title><?=$formbaslik?></title>
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
							<li class="btn ink-reaction btn-raised btn-primary disabled">Ayarlar</li>
							<li class="btn ink-reaction btn-raised btn-primary disabled">Yorum Listesi</li>
						</ol>
					</div>			
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
										<?=$formhataaciklama?>
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
													<th>Üye Adı ve Soyadı</th>
													<th>Üye Telefon Numarası</th>
													<th>Ürün Adı</th>
													<th>Ürün Yorumu</th>
													<th>İşlem</th>

												</tr>
											</thead>
											<tbody>
											<?php
											if($yorum_d==1)
											{	
												while ($yorum_t=$yorum_v->fetch_assoc()) 
												{	
													$yorumid=$yorum_t["yorumid"];
													$uyeid=$yorum_t["uyeid"];
													$yorumurunid=$yorum_t["yorumurunid"];
													$yorumicerik=$yorum_t["yorumicerik"];
													$sayfaad=$yorum_t["sayfaad"];											
													$uyebilgi=coksatir("SELECT uyeadsoyad,uyetelefon FROM uye WHERE uyeid='".$uyeid."' ");
													if($uyebilgi){
																$uyeadsoyad=$uyebilgi["uyeadsoyad"];
																$uyetelefon=$uyebilgi["uyetelefon"];
																$uyetelefon=coz($uyetelefon,$anahtarkod);
																}		
												?>
												<tr id="tr<?=$yorumid?>">
													<td><?=$yorumid?></td>
													<td><?=$uyeadsoyad?></td>
													<td><?=$uyetelefon?></td>
													<td><?=$sayfaad?></td>
													<td><?=$yorumicerik?></td>
													<td>
														<a 
															href="/_y/s/s/yorum/yorumcevapyaz.php?yorumid=<?=$yorumid?>" 
															class="btn btn-icon-toggle" 
															data-toggle="tooltip" 
															data-placement="top" 
															data-original-title="Cevap Yaz">
															<i class="fa fa-pencil"></i>
														</a>
														<a  id="yorumsil"
															href="#textModal"
															class="btn btn-icon-toggle"
															data-id="<?=$yorumid?>" 
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
											cevapgetir($yorumid);
												}
											}
											unset($iptaliadedegisim_d,$iptaliadedegisim_v,$talepid,$siparisid);
											?>
											</tbody>
										</table>
										
									</div><!--end .card-body -->
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

			<!-- BEGIN OFFCANVAS RIGHT -->
			<?php require_once($anadizin."/_y/s/b/sagpopup.php");?>
			<!-- END OFFCANVAS RIGHT -->
		</div><!--end #base-->
		<!-- END BASE -->

		<!-- BEGIN JAVASCRIPT -->
		<div class="modal fade" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title" id="simpleModalLabel">Yorumu Sil</h4>
					</div>
					<div class="modal-body">
						<p>Yorumu silmek istediğinize emin misiniz?</p>
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
		<iframe src="" name="_islem" id="_islem" scrolling="no" frameborder="0" style="display: none;"></iframe>
		<script>
			$silid=0;
			$(document).ready(function()
			{
				$('a#yorumsil').click(function ()
				{	
					$silid=$(this).data("id");
				});
				$('#silbutton').click(function ()
				{
					$('#_islem').attr('src', "/_y/s/f/sil.php?sil=yorum&id="+$silid);
				});
			 });
		</script>
		<script>
			$("#yorumlistephp").addClass("active");
		</script>
		<script type="text/javascript">
				$(".cevapver").on("click",function()
				{
					$cevapid=$(this).data("id");
					$( "#form-"+$cevapid ).show();
				});

					$(".iptal").on("click",function()
				{
					$cevapid=$(this).data("id");
					$( "#form-"+$cevapid ).hide();
				});
		</script>
	</body>
</html>
