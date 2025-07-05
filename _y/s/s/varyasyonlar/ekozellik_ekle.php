<?php
/**
 * @var AdminDatabase $db

 */
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Ek Özellikler";
$formbaslik="Ek Özellikler";
$butonisim="KAYDET";

if(S(f("ekozellik_ekle"))==1){
    $db->delete("DELETE FROM urunekozellikler Where ekozellikid!=0",[]);

    if(!empty(f("urunekozellik"))){
        foreach (f("urunekozellik") as $say => $urunekozellik)
        {
            if(strpos($urunekozellik, ":")!==false)
            {
                $urunekozellik_ad=trim(explode(":",$urunekozellik)[0]);
                $urunekozellik_deger=trim(explode(":",$urunekozellik)[1]);

                //die("$urunekozellik_ad : $urunekozellik_deger");

                $urunekozellik_id=teksatir("SELECT ekozellikid FROM urunekozellikler WHERE ekozellikad='".$urunekozellik_ad."' and ekozellikdeger='".$urunekozellik_deger."' and ekozelliksil='0'","ekozellikid");
                if(S($urunekozellik_id)==0)
                {

                    ekle("ekozellikad,ekozellikdeger,ekozelliksil",$urunekozellik_ad."|*_".$urunekozellik_deger."|*_0","urunekozellikler",0);
                }
            }
        }
    }

}
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title><?=$sayfabaslik?></title>

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
		<link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
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
							<li>Ayarlar</li>
							<li><?=$sayfabaslik?></li>
							<li><a href="/_y/s/s/varyasyonlar/ekozellikgrupliste.php">Varyant Grup Ekle</a></li>
                            <li style="float:right"><a href="#">Yardım</a></li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div class="row">
							<!-- BEGIN ADD CONTACTS FORM -->
							<div class="col-md-12">
								<div class="card">
									<div class="card-head style-primary form-inverse">
										<header><?=$formbaslik?></header>
									</div>
									<form name="formekozellik" id="formekozellik" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="ekozellik_ekle" value="1">
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="row card-body">
                                            <div class="row margin-bottom-xxl border-gray" style="background-color:antiquewhite;">
                                                <div class="col-lg-12"><h4>EK ÖZELLİKLERİ</h4><p>Varyantlar dışında da ürünlerinize ek özellikler ekleyebilirsiniz. Örneğin malzeme, şekil, boyut</p></div>
                                                <div class="col-lg-3 col-md-4">
                                                    <article class="margin-bottom-xxl">

                                                        <p>Her satıra bir özellik gelmelidir</p>
                                                        <p>Özellik değerlerini "<strong>:</strong>" ile ayırın</p>

                                                        <p style="font-weight: bold">Boyut : 120*22*70<br>
                                                            Şekli : Oval<br>
                                                            Malzeme : Deri<br>
                                                            Kaplama : Plastik
                                                            ... gibi</p>
                                                    </article>
                                                </div>
                                                <div class="col-lg-offset-1 col-md-8">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="input-group-content">
                                                                                <input type="text" id="ekozellikekle" class="form-control" placeholder="Malzeme:Deri">
                                                                                <label for="ekozellikekle">Özellik adı:değeri yazın</label>
                                                                            </div>
                                                                            <div class="input-group-btn">
                                                                                <button class="btn btn-primary ekozellikekle" type="button" ><i class="fa fa-plus"></i></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6" style="margin-top:20px">
                                                                    <ul id="ul_ekozellikler" class="list divider-full-bleed"><?php
                                                                        $ekozellikler_sql="SELECT * FROM urunekozellikler WHERE ekozelliksil=0 ORDER BY ekozellikad ASC";
                                                                        if($db->select($ekozellikler_sql))
                                                                        {
                                                                            $ekozellikler_sonuc=$db->select($ekozellikler_sql);
                                                                            if($ekozellikler_sonuc)
                                                                            {
                                                                                foreach ($ekozellikler_sonuc as $ekozellikler_yazdir)
                                                                                {
                                                                                    $eksecenek_dbid=$ekozellikler_yazdir['ekozellikid'];
                                                                                    $eksecenek_ad=$ekozellikler_yazdir['ekozellikad'];
                                                                                    $eksecenek_deger=$ekozellikler_yazdir['ekozellikdeger'];
                                                                                    $eksecenek_id=duzelt($eksecenek_ad."".$eksecenek_deger);
                                                                                    echo '
                                            <li id="li'.$eksecenek_id.'" class="tile text-danger">
                                                <input type="hidden" name="urunekozellik[]" value="'.$eksecenek_ad.':'.$eksecenek_deger.'">
                                                <div class="tile-text"><span class="text-primary">'.$eksecenek_ad.':</span>'.
                                                                                        $eksecenek_deger.'</div>'.
                                                                                        '<a class="btn btn-flat ink-reaction ekozelliksil" data-dbid="'.$eksecenek_dbid.'" data-id="li'.$eksecenek_id.'"><i class="md md-delete"></i></a>'.
                                                                                        '</li>';
                                                                                }
                                                                            }
                                                                        }
                                                                        ?></ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <em class="text-caption">Ek özellikler</em>
                                                </div>
                                            </div>
										</div>
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
												<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once($anadizin."/_y/s/b/menu.php");?>
		</div>
        <div class="modal fade in" id="varyantModal" tabindex="-1" role="dialog" aria-labelledby="varyantModalLabel" aria-hidden="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="textModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
		<script src="https://code.jquery.com/jquery-2.2.4.js"></script>
		<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

		<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
		<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>

		<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
		<script src="/_y/assets/js/libs/select2/select2.min.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>

		<script src="/_y/assets/js/libs/multi-select/jquery.multi-select.js"></script>
		<script src="/_y/assets/js/libs/inputmask/jquery.inputmask.bundle.min.js"></script>
		<script src="/_y/assets/js/libs/moment/moment.min.js"></script>

		<script src="/_y/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-multiselect/bootstrap-multiselect.js"></script>
		<script src="/_y/assets/js/libs/bootstrap-rating/bootstrap-rating-input.min.js"></script>

		<script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>
		<script src="/_y/assets/js/libs/microtemplating/microtemplating.min.js"></script>

		<script src="/_y/assets/js/core/source/App.js"></script>
		<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
		<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
		<script src="/_y/assets/js/core/source/AppCard.js"></script>
		<script src="/_y/assets/js/core/source/AppForm.js"></script>
		<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
		<script src="/_y/assets/js/core/source/AppVendor.js"></script>

		<script src="/_y/assets/js/core/demo/Demo.js"></script>
		<script src="/_y/assets/js/core/demo/DemoPageContacts.js"></script>
		<script src="/_y/assets/js/core/demo/DemoFormComponents.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
		<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
		
		<script>
			$("#ekozellikeklephp").addClass("active");

            $(document).on("click",".ekozellikekle",function (){
                if(!$("#ekozellikekle").val())
                {
                    $("#varyantModal").modal('show');
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Özellik adını:Değerini girin");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                    $("#ekozellikekle").focus();
                }
                else
                {
                    $yeniozellik=$("#ekozellikekle").val();
                    if($yeniozellik.indexOf(":")== -1)
                    {
                        $("#varyantModal").modal('show');
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Özellik değerini ayırmak için : (iki nokta kullanın)");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                        $("#ekozellikekle").focus();
                    }
                    else
                    {
                        $yeniozellik_ayir=$yeniozellik.split(':');
                        $yeniozellik_value=$yeniozellik.replaceAll('"',"");
                        li_ozellik_id=clearInput($yeniozellik);
                        $ozellik_li='' +
                            '<li id="li'+li_ozellik_id+'" class="tile text-danger">' +
                            '<input type="hidden" name="urunekozellik[]" value="'+$yeniozellik_value+'" '+
                            '<div class="tile-text"><span class="text-primary">'+$yeniozellik_ayir[0]+':</span>' +
                            $yeniozellik_ayir[1]+'</div>'+
                            '<a class="btn btn-flat ink-reaction ekozelliksil" data-dbid="0" data-id="li'+li_ozellik_id+'"><i class="md md-delete"></i></a>'+
                            '</li>';
                        $("#ul_ekozellikler").append($ozellik_li);
                        $("#ekozellikekle").val("");
                        $("#ekozellikekle").focus();
                    }
                }
            });
            $(document).on("click",".ekozelliksil",function (){
                $ozellik_silid=$(this).data("id");
                $ozellik_sildbid=$(this).data("dbid");
                if($ozellik_sildbid!=0)
                {
                    $.ajax({
                        type: 'GET',
                        url:"/_y/s/f/sil.php?sil=ekozellik&id="+$ozellik_sildbid,
                        dataType: "html",
                        success: function(data)
                        {
                            if($.trim(data))
                            {

                            }
                            else
                            {
                                $("#varyantModal").modal('show');
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Hata: Silme işlemi yapılamadı");
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                            }
                        }
                    });
                }

                $("#ul_ekozellikler>#"+$ozellik_silid).remove();
            });
            function clearInput(id)
            {
                /*
                 Türkçe karakterlerin değiştirilecekleri karşılıklarını tanımlıyoruz.
                 */
                var charMap = {Ç:'c',Ö:'o',Ş:'s',İ:'i',I:'i',Ü:'u',Ğ:'g',ç:'c',ö:'o',ş:'s',ı:'i',ü:'u',ğ:'g'};

                /*
                 Anlık input değerini (value) alıyoruz.
                 */
                var str = id;//$("#" + id).val();

                /*
                 Inputa girilen Türkçe karakterleri yukarıda tanımladığımız değerlerle değiştiriyoruz.
                 Bu zahmete katlanmamızın nedeni JavaScript ile Türkçe karakterleri sağlıklı biçimde,
                 herhangi bir bug oluşmasına mahal vermeden değiştirebilmek.
                 */
                str_array = str.split('');

                for(var i=0, len = str_array.length; i < len; i++) {
                    str_array[i] = charMap[ str_array[i] ] || str_array[i];
                }

                str = str_array.join('');

                /*
                 Alfanumerik olmayan özel karakterlerin temizlendiği yeni bir value oluşturuyoruz:
                 1. replace(" ","-") ile boşlukları tire (-) işaretiyle değiştiriyoruz.
                 2. replace("--","-") ile iki tane yan yana (--) tire işareti oluşmasının önüne geçiyoruz
                 3. replace(/[^a-z0-9-.]/gi,"") ile temizlik işlemini gerçekleştiriyor, - ve . gibi karakterlerin
                 temizlenme işleminden hariç tutulmasını sağlıyoruz.
                 4. toLowerCase() ile değişkeni tamamen küçük harflere çeviriyoruz.
                 */
                var clearStr = str.replace(" ","-").replace("--","-").replace(/[^a-z0-9-._çöşüğı]/gi,"").toLowerCase();

                /*
                 Son olarak işlemden geçirdiğimiz değeri tekrar inputa basıyoruz
                 */
                //$("#" + id).val(clearStr);
                return clearStr;

                /*
                Aşağıdaki olay tamamen işin geyiği, silebilirsiniz
                */
                //$("#result").html("Adeta sihir kullanıyormuşcasına...");
            }

            $("#formekozellik ul").sortable();
		</script>
        <style>#formekozellik ul li:hover{background-color:#eee;cursor:move}</style>
		<!-- END JAVASCRIPT -->
	</body>
</html>