<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";
$formtablo="urunbedengrup";

//düzenle
$sayfabaslik="Ürün Beden";
$formbaslik="Ürün Beden Liste";
$bedengrupid=q("bedengrupid");
$urunbedenek="";
if(S($bedengrupid)!=0)
{
	$urunbedenek=" and urunbeden.urunbedengrupid='".$bedengrupid."'";
}

Veri(true);
if(S(f("bedensirala"))==1)
{
	foreach($_POST['urunbedenid'] as $urunbedenid)
	{
    	$bedensira =S(f("bedensira".$urunbedenid));
    	guncelle("urunbedensira",$bedensira,"urunbeden","urunbedenid='".$urunbedenid."'",0);
    }
}
$urunbedengrup_d=0;
$urunbedengrup_s="
	SELECT 
		urunbeden.urunbedengrupid,urunbedengrupad,urunbedenad,urunbedensira,urunbedenid 
	FROM 
		urunbedengrup 
			inner join urunbeden on urunbeden.urunbedengrupid=urunbedengrup.urunbedengrupid
	WHERE urunbedengrup.urunbedengrupsil='0' $urunbedenek and urunbedensil='0'
	ORDER BY urunbedengrup.urunbedengrupid, urunbeden.urunbedenad Asc";//, urunbeden.urunbedensira
if($data->query($urunbedengrup_s))
{
	$urunbedengrup_v=$data->query($urunbedengrup_s);
	if($urunbedengrup_v->num_rows>0)$urunbedengrup_d=1;
	unset($urunbedengrup_s);
}else{hatalogisle("urunbedenliste",$data->error);}

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
							<li class="btn ink-reaction btn-raised btn-primary disabled">E-Ticaret</li>
							<li class="btn ink-reaction btn-raised btn-primary disabled">Ürun Bedenler</li>
							<li class="active"><a href="/_y/s/s/varyasyonlar/bedenekle.php" class="btn ink-reaction btn-raised btn-primary"><?=$sayfabaslik?> Ekle (+)</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">

							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary">
										<header><?=$formbaslik?></header>
										<div class="tools">
											<a class="btn btn-floating-action btn-default-light" href="/_y/s/s/varyasyonlar/bedenekle.php"><i class="fa fa-plus"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
							$bedenkategori_s="
								SELECT 
									urunbedengrupid,urunbedengrupad
								FROM 
									urunbedengrup
								WHERE
									urunbedengrupsil='0'
								ORDER BY 
									urunbedengrupad
							";
							if($data->query($bedenkategori_s))
							{
								$bedenkategori_v=$data->query($bedenkategori_s);unset($bedenkategori_s);
								if($bedenkategori_v->num_rows>0)
								{
									?>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<select name="bedengrup" id="bedengrup">
													<option value="0">Beden Grubu Seçin</option>
													<?php while ($bedenkategori_t=$bedenkategori_v->fetch_assoc())
													{
														$bedenkategori_id=$bedenkategori_t["urunbedengrupid"];
														$bedenkategori_ad=$bedenkategori_t["urunbedengrupad"];
														echo '<option value="'.$bedenkategori_id.'">'.$bedenkategori_ad.'</option>';
													}?>
												</select>
											</div>
										</div>
									</div>
									<?php
								}
							}
							else
							{
								hatalogisle("urunbedenkategorial",$data->error);
							}
						?>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<table class="table no-margin">
											<form class="form form-validation form-validate" role="form" method="post">
												<input type="hidden" name="bedensirala" value="1">
											<thead>
												<tr>
													<th>#</th>
													<th>Beden Grup Ad</th>
													<th>Beden Ad</th>
													<th>Beden Sıra</th>
													<th>İşlem</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($urunbedengrup_d==1)
											{
												while ($urunbedengrup_t=$urunbedengrup_v->fetch_assoc())
												{
													$urunbedenid=$urunbedengrup_t["urunbedenid"];
													$urunbedenad=$urunbedengrup_t["urunbedenad"];
													$urunbedensira=$urunbedengrup_t["urunbedensira"];
													$urunbedengrupad=$urunbedengrup_t["urunbedengrupad"];
                                                    $urunvarmi=S(teksatir("select sayfaid from urunozellikleri where urunbedenid=$urunbedenid","sayfaid"));
												?>
												<tr id="tr<?=$urunbedenid?>" <?php if($urunvarmi!=0)echo 'class="success"';?>>
													<td><?=$urunbedenid?></td>
													<td><?=$urunbedengrupad?></td>
													<td><?=$urunbedenad?></td>
													<td class="col-md-3"><input type="hidden" name="urunbedenid[]" value="<?=$urunbedenid?>"><input name="bedensira<?=$urunbedenid?>" type="number" value="<?=$urunbedensira?>" class="form-control"></td>
													<td>
														<a
															href="/_y/s/s/varyasyonlar/bedenekle.php?urunbedenid=<?=$urunbedenid?>"
															class="btn btn-icon-toggle"
															data-toggle="tooltip"
															data-placement="top"
															data-original-title="Düzenle">
															<i class="fa fa-pencil"></i>
														</a>
														<a
															href="#textModal"
															class="btn btn-icon-toggle bedensil"
															data-id="<?=$urunbedenid?>"
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
											unset($urunbedengrup_d,$urunbedengrup_v,$urunbedengrupid,$urunbedengrupkisaad);
											?>
												<tr>
													<td colspan="5"><button type="submit" class="btn btn-primary" id="sirala">Sırala</button></td>
												</tr>
											</tbody>
											</form>
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
						<h4 class="modal-title" id="simpleModalLabel">Beden Sil</h4>
					</div>
					<div class="modal-body">
						<p>Bedeni silmek istediğinize emin misiniz?</p>
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
                $(document).on("click",'a.bedensil',function ()
                {
                    $silid=$(this).data("id");
                });
                $(document).on("click",'#silbutton',function ()
                {
                    $('#_islem').attr('src', "/_y/s/f/sil.php?sil=beden&id="+$silid);
                });

                $( "#bedengrup" ).change(function()
                {
                    window.location.href = "/_y/s/s/varyasyonlar/bedenliste.php?bedengrupid="+$("#bedengrup").val();
                });
            });
			$("#bedenlistephp").addClass("active");
		</script>
	</body>
</html>
