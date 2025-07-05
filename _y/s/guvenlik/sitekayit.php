<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
	//form değişkenler
	$f_domain="";
	$f_ssl=0;
	$f_cokludil=0;
	$f_uyelik="";
	$f_sitetip=0;
	$f_uyelik="";
	$f_dbname="";
	$f_dbuser="";
	$f_dbpass="";
	$f_adminauth=0;
	$f_adminname="";
	$f_adminmail="";
	$f_admingsm="";
	$formhata=0;
	$formhataaciklama="";
	$anahtarkod=SifreUret(32,3);
	//form tanımlı mı
	if (isset($_POST['olustur'])) 
	{
		if(f('olustur')==1)
		{
			$f_domain=f('domain');
			$f_ssl=S(f('ssl'));
			$f_cokludil=S(f('cokludil'));
			$f_uyelik=S(f('uyelik'));
			$f_sitetip=f('sitetip');
			$f_dbname=f('dbname');
			$f_dbuser=f('dbuser');
			$f_dbpass=f('dbpass');
			$f_adminname=f('adminname');
			$f_adminmail=f('adminmail');
			$f_admingsm=f('admingsm');
			$f_adminauth=f('adminauth');
			if(!empty($f_domain))
			{
				$domainsayfa = $anadizin.'/sistem/veri/domain/domain.php';
				file_put_contents($domainsayfa,'<?php $siteDomain="'.$f_domain.'";?>');
				unset($domainsayfa);
			}
			else
			{
				$formhata=1;
				$formhataaciklama="<br>Lütfen alan adınızı girin";
			}

			if($formhata==0)
			{	
				if(!empty($f_dbname) && !empty($f_dbuser) && !empty($f_dbpass))
				{
					// Check connection
					//$data = new mysqli("localhost", $f_dbuser, $f_dbpass,$f_dbname);
					mysqli_report(MYSQLI_REPORT_STRICT);
					try 
					{
						$connection = new mysqli('localhost', $f_dbuser, $f_dbpass, $f_dbname);

						$sqlsayfa = $anadizin.'/sistem/veri/sql/sql.php';
						file_put_contents($sqlsayfa,'<?php'.PHP_EOL.'$servername = "localhost";'.PHP_EOL.'$database = "'.$f_dbname.'";'.PHP_EOL.'$username = "'.$f_dbuser.'";'.PHP_EOL.'$password = "'.$f_dbpass.'";'.PHP_EOL.'?>');
						unset($sqlsayfa);
						$conn = new mysqli('localhost', $f_dbuser, $f_dbpass, $f_dbname);
						// Check connection
						if ($conn->connect_error)
						{
						    $formhataaciklama= "<br>Veri tabanı bağlantısı yapılamadı. SQL bilgilerini kontrol ediniz.";
							$formhata=1;
						} 
						else
						{
							
							$conn->query("DELETE FROM ayargenel");
							$sql = "
								INSERT INTO ayargenel(ayargenelid,domain,ssldurum,sitetip,cokludil,uyelik)
								VALUES 				 ('1','".$f_domain."', '".$f_ssl."','".$f_sitetip."','".$f_cokludil."','".$f_uyelik."')";

							if ($conn->query($sql) === TRUE) {
							    $formhataaciklama= "<br>Domain kaydı yapıldı";
							} else {
							    hatalogisle("DomainKayit",$conn->error);
							    $formhataaciklama= "<br>Veritabanına Domain kaydı yapılamadı";
							    $formhata=1;
							}
							unset($sql);
							$conn->close();
						}
					} 
					catch (Exception $e ) 
					{
						hatalogisle("VeritabanıAyarları","Hatalı Veritabanı Bilgileri[adı/kullanıcı adı/parolası]");
						$formhata=1;
						$formhataaciklama="<br>Veri tabanı bağlantısı sağlanamadı,bilgileri kontrol ediniz ";
					}
				}
			}
			if($formhata==0)
			{	
				if(!empty($f_adminname) && !empty($f_adminmail))
				{
					$data = new mysqli("localhost", $f_dbuser, $f_dbpass,$f_dbname);
					// Check connection
					if ($data->connect_error)
					{
						die("Bağlantı Hatası: " . $data->connect_error);
					} 
					$simdi=date("Y-m-d H:i:s");
					$sifre=SifreUret(5,2);
					$yAnahtar=SifreUret(20,2);
					$sql = "
					INSERT INTO yoneticiler 
						(
						yoneticianahtar,
						olusturmatarihi, 
						guncellemetarihi,
						yoneticiyetki,
						yoneticiadsoyad,
						yoneticieposta,
						yoneticiceptelefon,
						yoneticisifre,
						yoneticisifretarih,
						yoneticiaktif,
						yoneticisil
						)
					VALUES 
						(
						'".$yAnahtar."',
						'".$simdi."',
						'".$simdi."',
						'".$f_adminauth."',
						'".$f_adminname."',
						'". sifrele($f_adminmail,$anahtarkod) ."',
						'". sifrele($f_admingsm,$anahtarkod) ."',
						'".$sifre."',
						'".$simdi."',
						'1',
						'0'
						)";

					if ($data->query($sql) === TRUE)
					{
						$anahtarsayfa = $anadizin.'/sistem/veri/anahtar/anahtar.php';
						file_put_contents($anahtarsayfa,'<?php $anahtarkod="'.$anahtarkod.'";?>');
						$mailIcerik="<p>Sayin $f_adminname Sistem Yazılım Merkezi dünyasına hoş geldiniz.</p><p>$f_domain sitenizin bazı verileri, kullanıcı bilgileri vs. özel bir anahtar kod ile şifrelenmektedir. Siteniz çalıştığı sürece bu anahtar koduna ihtiyacınız olmayacaktır. Fakat ilerde yeni kurulum yapmanız ya da şifreli verileri çözmeniz gerekirse lütfen aşağıdaki anahtar kodunu webmaster/sistem yöneticisi yetkilisine bildiriniz.</p><p>Anahtar Kod: $anahtarkod </p><p>Zafer DANYILDIZ<br>Sistem Yazılım Merkezi Yazılım Geliştirme<br>
						5326270247 | zd@sistemyazilimmerkezi.com</p>";
						MailGonder($f_adminmail,"$f_domain Anahtar Kod Bilgilendirmesi",$mailIcerik);
						unset($anahtarsayfa,$anahtarkod);
					}
					else
					{
						hatalogisle("YöneticiEkleKayit",$data->error);
						$formhata=1;
						$formhataaciklama = "Yönetici Eklenemedi! Lütfen sonra tekrar deneyin ";
					}
					unset($simdi,$sifre,$sql);
					$data->close();
				}
				else
				{
					$formhata=1;
					$formhataaciklama="<br>Yönetici adı ve epostası boş olamaz, lütfen kontrol ediniz ";
				}
			}
		}
	}
	if($formhata==0)
	{
		//domain kontrol
		$domainsayfa = $anadizin.'/sistem/veri/domain/domain.php';
		//veritabanı kontrol
		$sqlsayfa = $anadizin.'/sistem/veri/sql/sql.php';
		//Anahtar Kod kontrol
		$anahtarsayfa = $anadizin.'/sistem/veri/anahtar/anahtar.php';
		if (file_exists($domainsayfa) && file_exists($sqlsayfa) && file_exists($anahtarsayfa))
		{
			git('/_y/s/guvenlik/giris.php');
		} 
	}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Sistem Yazılım Merkezi - Site Yönetim Paneli</title>
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
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">

		<!-- BEGIN LOGIN SECTION -->
		<section class="section-account">
			<div class="img-backdrop" style="background-image: url('/_y/assets/img/img16.jpg')"></div>
			<div class="spacer"></div>
			<div class="card contain-sm style-transparent">
				<!-- BEGIN VALIDATION FORM WIZARD -->
				<div class="row">
					<div class="col-lg-12">
						<h3 id="formuyari" class="text-danger"><?=$formhataaciklama?></h3>
						<div class="card">
							<div class="card-body ">
								<div id="rootwizard2" class="form-wizard form-wizard-horizontal">
									<form id="domainform" class="form floating-label form-validation form-validate" role="form" method="post" novalidate>
										<input type="hidden" name="olustur" value="1">
										<div class="form-wizard-nav">
											<div class="progress"><div class="progress-bar progress-bar-primary"></div></div>
											<ul class="nav nav-justified">
												<li class="active"><a href="#step1" data-toggle="tab"><span class="step">1</span> <span class="title">DOMAİN</span></a></li>
												<li><a href="#step2" data-toggle="tab"><span class="step">2</span> <span class="title">SQL BİLGİLERİ</span></a></li>
												<li><a href="#step3" data-toggle="tab"><span class="step">3</span> <span class="title">YÖNETİCİ OLUŞTUR</span></a></li>
												<li><a href="#step4" data-toggle="tab"><span class="step">4</span> <span class="title">GÖNDER</span></a></li>
											</ul>
										</div><!--end .form-wizard-nav -->
										<div class="tab-content clearfix">
											<div class="tab-pane active" id="step1">
												<br><br>
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<input type="text" name="domain" id="domain" class="form-control" data-rule-minlength="7" required value="<?=$f_domain?>">
															<label for="domain" class="control-label">Alan Adınız (örn:sistemyazilimmerkezi.com)</label>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<input type="text" name="domaintekrar" id="domaintekrar" class="form-control" data-rule-minlength="7" required="" data-rule-equalto="#domain" value="<?=$f_domain?>">
															<label for="domaintekrar" class="control-label">Alan Adı Tekrar</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-4">
														<label class="radio-inline radio-styled">
															<input type="checkbox" name="ssl" value="1" <?php if($f_ssl==1)echo 'checked' ?>>
															<span>SSL var mı</span>
														</label>
													</div>
													<div class="col-sm-4">
														<label class="radio-inline radio-styled">
															<input type="checkbox" name="cokludil" value="1"<?php if($f_cokludil==1)echo 'checked' ?>>
															<span>Ek dil var mı</span>
														</label>
													</div>
													<div class="col-sm-4">
														<label class="radio-inline radio-styled">
															<input type="checkbox" name="uyelik" value="1"<?php if($f_uyelik==1)echo 'checked' ?>>
															<span>Üyelik var mı</span>
														</label>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<select id="sitetip" name="sitetip" class="form-control static dirty">
																<option value="0" <?php if($f_sitetip==0)echo 'selected'; ?>>Tek Sayfa Site</option>
																<option value="1" <?php if($f_sitetip==1)echo 'selected'; ?>>E-Ticaret Sitesi</option>
																<option value="2" <?php if($f_sitetip==2)echo 'selected'; ?>>Kişisel Site</option>
																<option value="3" <?php if($f_sitetip==3)echo 'selected'; ?>>Kurumsal Site</option>
																<option value="4" <?php if($f_sitetip==4)echo 'selected'; ?>>Ürün Tanıtım</option>
																<option value="5" <?php if($f_sitetip==5)echo 'selected'; ?>>Haber Sitesi</option>
																<option value="6" <?php if($f_sitetip==6)echo 'selected'; ?>>Emlak Sitesi</option>
															</select>
														</div>
													</div>
												</div>
											</div><!--end #step1 -->
											<div class="tab-pane" id="step2">
												<br/><br/>
												<div class="form-group">
													<input type="text" name="dbname" id="dbname" class="form-control" required value="<?=$f_dbname?>">
													<label for="dbname" class="control-label">Veritabanı Adı</label>
												</div>
												<div class="form-group">
													<input type="text" name="dbuser" id="dbuser" class="form-control" required value="<?=$f_dbuser?>">
													<label for="dbuser" class="control-label">Veritabanı Kullanıcı Adı</label>
												</div>
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<input type="password" name="dbpass" id="dbpass" class="form-control" required="" data-rule-minlength="5" value="<?=$f_dbpass?>">
															<label for="dbpass" class="control-label">Veri Tabanı Şifre</label>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<input type="password" name="dbpassrepeat" id="dbpassrepeat" class="form-control" data-rule-equalto="#dbpass" required="" value="<?=$f_dbpass?>">
															<label for="passwordrepeat" class="control-label">Şifre Tekrar</label>
														</div>
													</div>
												</div>
											</div><!--end #step3 -->
											<div class="tab-pane" id="step3">
												<br/><br/>
												<div class="form-group">
													<input type="text" name="adminname" id="adminname" class="form-control" required value="<?=$f_adminname?>">
													<label for="adminname" class="control-label">Yönetici Adı Soyadı</label>
												</div>
												<div class="form-group">
													<input type="text" name="adminmail" id="adminmail" class="form-control" data-rule-email="true" required value="<?=$f_adminmail?>">
													<label for="adminmail" class="control-label">E-posta</label>
												</div>
												<div class="form-group">
													<input type="tel" name="admingsm" id="admingsm" class="form-control" data-inputmask="'mask': '(532) 999-9999'" value="<?=$f_admingsm?>" data-rule-minlength="10" maxlength="10">
													<label>GSM NO</label>
													<p class="help-block">Başında 0 ve boşluk kullanmayınız! Cep telefonu: 5321234567</p>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label">Yönetici Yetkileri</label>
													<div class="col-sm-9">
														<label class="radio-inline radio-styled">
															<input type="radio" name="adminauth" value="0" checked><span>Süper Yönetici</span>
														</label>
														<label class="radio-inline radio-styled">
															<input type="radio" name="adminauth" value="1"><span>Tam Yetki</span>
														</label>
														<label class="radio-inline radio-styled">
															<input type="radio" name="adminauth" value="2"><span>Kısıtlı Yetki</span>
														</label>
													</div><!--end .col -->
												</div>
											</div><!--end #step3 -->
											<div class="tab-pane" id="step4">
												<br/><br/>
												<div class="form-group">
													<p class="tile-text">*Alan Adı</p>
													<p id="yalanadi" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*SSL</p>
													<p id="yssl" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*Site Tipi</p>
													<p id="ysitetip" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*Veritabanı Adı</p>
													<p id="yveritabaniadi" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*Veritabanı Kullanıcı Adı</p>
													<p id="yveritabanikullaniciadi" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*Veritabanı Şifre</p>
													<p id="yveritabanisifre" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*Yönetici Adı</p>
													<p id="yyoneticiadi" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">*Yönetici Eposta</p>
													<p id="yyoneticieposta" class="text-danger"></p>
												</div>
												<div class="form-group">
													<p class="tile-text">Yönetici Telefon</p>
													<p id="yyoneticitelefon" class="text-danger"></p>
												</div>
												<div class="card-actionbar">
													<div class="card-actionbar-row">
														<button id="gonderbt" type="button" class="btn btn-flat btn-primary ink-reaction">GÖNDER</button>
													</div>
												</div>
											</div><!--end #step4 -->
										</div><!--end .tab-content -->
										<ul class="pager wizard">
											<li class="previous first"><a class="btn-raised" href="javascript:void(0);">İlk</a></li>
											<li class="previous"><a class="btn-raised" href="javascript:void(0);">Geri</a></li>
											<li class="next last"><a class="btn-raised" href="javascript:void(0);">Son</a></li>
											<li class="next"><a class="btn-raised" href="javascript:void(0);">İleri</a></li>
										</ul>
								</div><!--end #rootwizard -->
							</div><!--end .card-body -->
						</div><!--end .card -->
							<em class="text-caption">Tüm alanları eksiksiz doldurunuz</em>
						
					</div><!--end .col -->
				</div><!--end .row -->
				</form>
				<!-- END VALIDATION FORM WIZARD -->
			</div><!--end .card -->
		</section>
		<!-- END LOGIN SECTION -->
		<!-- BEGIN JAVASCRIPT -->
		<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
		<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
		<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		<script src="/_y/assets/js/libs/wizard/jquery.bootstrap.wizard.min.js"></script>
		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>
		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormWizard.js"></script>
		<!-- END JAVASCRIPT -->
		<script>

		$(".step,.btn-raised").on("click", function(e)
		{ 
			$("#yalanadi").text($("#domain").val());
			$ssltext="-";
			if($('input[name=ssl]:checked').val()==1){$ssltext="Var";}else{$ssltext="Yok";}
			$("#yssl").text($ssltext);
			$("#ysitetip").text($('#sitetip option:selected').text());
			$("#yveritabaniadi").text($("#dbname").val());
			$("#yveritabanikullaniciadi").text($("#dbuser").val());
			$password  = $("#dbpass").val().replace(/./g, '*');
			$("#yveritabanisifre").text($password);
			$("#yyoneticiadi").text($("#adminname").val());
			$("#yyoneticieposta").text($("#adminmail").val());
			$("#yyoneticitelefon").text($("#admingsm").val());
		});
		$("#gonderbt").on("click", function(e)
		{
			if ( $( "#domain" ).val() == "" )
			{
				$( "#formuyari" ).text( "Lütfen alan adınızı girin" ).show();
				return;
			}
			else if ( $( "#domain" ).val() != $( "#domaintekrar" ).val() )
			{
				$( "#formuyari" ).text( "Alan adı ve tekrarı aynı değil" ).show();
				return;
			}
			else if ( $( "#dbname" ).val() == "" )
			{
				$( "#formuyari" ).text( "Lütfen veritabanı adını girin" ).show();
				return;
			}
			else if ( $( "#dbuser" ).val() == "" )
			{
				$( "#formuyari" ).text( "Lütfen veritabanı kullanıcı adını girin" ).show();
				return;
			}
			else if ( $( "#dbpass" ).val() == "" )
			{
				$( "#formuyari" ).text( "Lütfen veritabanı şifresini girin" ).show();
				return;
			}
			else if ( $( "#dbpass" ).val() != $( "#dbpassrepeat" ).val() )
			{
				$( "#formuyari" ).text( "Veritabanı şifresini ve tekrarı aynı değil" ).show();
				return;
			}
			else if ( $( "#adminname" ).val() == "" )
			{
				$( "#formuyari" ).text( "Lütfen yönetici adını girin" ).show();
				return;
			}
			else if ( $( "#adminmail" ).val() == "" )
			{
				$( "#formuyari" ).text( "Lütfen yönetici epostası girin" ).show();
				return;
			}
			$('#gonderbt').attr("disabled", true);
			$( "#formuyari" ).text( "Gönderiliyor!.." ).show().fadeOut( 2000 );
		  	//e.preventDefault();
			$("#domainform").submit();
		});
		</script>

	</body>
</html>