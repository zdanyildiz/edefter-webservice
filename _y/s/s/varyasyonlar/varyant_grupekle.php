<?php
/**
 * @var Config $config
 * @var AdminDatabase $db
 */
require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
?>

<?php
/*
    $urunmalzemegrupSql="
        SELECT 
            urunmalzemegrup.urunmalzemegrupid,urunmalzemegrupad,urunmalzemegrup.benzersizid,
            urunmalzemeid,urunmalzemead,urunmalzemesira 
        FROM 
            urunmalzemegrup 
            inner join urunmalzeme on urunmalzemegrup.urunmalzemegrupid=urunmalzeme.urunmalzemegrupid
        WHERE 
            urunmalzemegrupsil='0' and urunmalzemesil='0'
        ORDER BY urunmalzemegrupad
    ";

    $urunmalzemegrupSonuc=$db->select($urunmalzemegrupSql);

    $urunmalzemegrupArray=[];
    if($urunmalzemegrupSonuc){
        foreach($urunmalzemegrupSonuc as $urunmalzemegrup){
            $urunmalzemegrupAd=$urunmalzemegrup["urunmalzemegrupad"];
            $urunmalzemegrupID=$urunmalzemegrup["urunmalzemegrupid"];
            $urunmalzemegrupBenzersizID=$urunmalzemegrup["benzersizid"];

            $urunmalzemead=$urunmalzemegrup["urunmalzemead"];
            $urunmalzemeid=$urunmalzemegrup["urunmalzemeid"];
            $urunmalzemesira=$urunmalzemegrup["urunmalzemesira"];


            $variantGroupAddSql="
                INSERT 
                    INTO urunvaryantgrup (varyantgrupad, benzersizid, varyantgrupsil) 
                    VALUES (:name, :unique_id,0)
            ";

            $params=[
                "name"=>$urunmalzemegrupAd,
                "unique_id"=>$urunmalzemegrupBenzersizID
            ];

            $checkVariantGroupSql="
                SELECT 
                    varyantgrupid 
                FROM 
                    urunvaryantgrup 
                WHERE 
                    varyantgrupad=:name and varyantgrupsil='0'
            ";
            if(!$db->select($checkVariantGroupSql,["name"=>$urunmalzemegrupAd])){
                $db->beginTransaction();
                if($db->insert($variantGroupAddSql,["name"=>$urunmalzemegrupAd,"unique_id"=>$urunmalzemegrupBenzersizID])){
                    $db->commit();
                }
                else{
                    $db->rollBack();
                }
            }


            $insertVariantSql="
                INSERT 
                    INTO urunvaryant (varyantgrupid, varyantad, varyantsira, varyantsil) 
                    VALUES (:group_id, :name, :order, 0)
            ";

            $insertVariantParams=[
                "group_id"=>$urunmalzemegrupID,
                "name"=>$urunmalzemead,
                "order"=>$urunmalzemesira
            ];

            $checkVariantSql="
                SELECT 
                    varyantid 
                FROM 
                    urunvaryant 
                WHERE 
                    varyantgrupid=:group_id and varyantad=:name and varyantsil='0'
            ";

            if(!$db->select($checkVariantSql,["group_id"=>$urunmalzemegrupID,"name"=>$urunmalzemead])){
                $db->beginTransaction();
                if($db->insert($insertVariantSql,$insertVariantParams)){
                    $db->commit();
                }
                else{
                    $db->rollBack();
                }
            }
        }
    }
*/
?>


<?php
$formhata=0;
$formhataaciklama="";

//düzenle
$sayfabaslik="Varyant Gruplar";
$formbaslik="Ürün Varyant Grupları";
$butonisim="KAYDET";

if(S(f("varyantgrup_ekle"))==1){
    if(s(f("variantNameEdit"))==0){
        if(f("yenisecenekgrupid"))
        {
            foreach (f("yenisecenekgrupid") as $say => $varyant_grupid)
            {
                $varyant_grupad=f("yenisecenekgrupad")[$say];
                if(f($varyant_grupid."_yenisecenekad"))
                {
                    //varyantgrup daha önceden eklenmiş mi
                    $varyant_grupid_db=teksatir("SELECT varyantgrupid FROM urunvaryantgrup WHERE varyantgrupad='".$varyant_grupad."' and varyantgrupsil='0'","varyantgrupid");
                    if(S($varyant_grupid_db)==0)
                    {
                        //varyantgrup daha önceden eklenmemiş, ekleyelim
                        $varyant_benzersiz=SifreUret(20,2);
                        ekle("varyantgrupad,benzersizid,varyantgrupsil",$varyant_grupad."|*_".$varyant_benzersiz."|*_0","urunvaryantgrup",0);
                        $varyant_grupid_db=teksatir("SELECT varyantgrupid FROM urunvaryantgrup WHERE benzersizid='".$varyant_benzersiz."' and varyantgrupsil='0'","varyantgrupid");
                    }
                    //varyant_grupid'yi db'den aldık
                    //varyantgruba daha önceden eklenen seçenekleri silelim. yeniden eklenecek
                    $db->delete("DELETE FROM urunvaryant WHERE varyantgrupid=:varyant_grupid_db",array("varyant_grupid_db"=>$varyant_grupid_db));
                    foreach (f($varyant_grupid."_yenisecenekad") as $secenek_say => $_yenisecenekad)
                    {
                        ekle("varyantgrupid,varyantad,varyantsira,varyantsil",$varyant_grupid_db."|*_".$_yenisecenekad."|*_".$secenek_say."|*_0","urunvaryant",0);
                    }
                }
            }

        }
        else
        {
            //hiç varyantgrup gelmemiş var olanları silelim
            $db->delete("DELETE FROM urunvaryantgrup WHERE varyantgrupid!='0'",[]);
        }
    }
    else{
        $variantID = s(f("variantGroupID"));
        $variantName = f("variantGroupName");

        $db->beginTransaction();
        try {
            $db->update("UPDATE urunvaryantgrup SET varyantgrupad=:variantName WHERE varyantgrupid=:variantID", ["variantName" => $variantName, "variantID" => $variantID]);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            $formhata = 1;
            $formhataaciklama = "Varyant grubu güncellenirken bir hata oluştu.";
        }
    }

}
$variantgrupName="";
if(s(q("varyantgrupid"))!=0)
{
    $variantgrupName=teksatir("SELECT varyantgrupad FROM urunvaryantgrup WHERE varyantgrupid='".s(q("varyantgrupid"))."'","varyantgrupad");
    $butonisim="Düzenle";
}
if(s(f("variantNameEdit"))!=0)
{
    $variantgrupName=teksatir("SELECT varyantgrupad FROM urunvaryantgrup WHERE varyantgrupid='".s(q("varyantgrupid"))."'","varyantgrupad");
    $butonisim="Düzenle";
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
									<form name="formvaryant" id="formvaryant" class="form form-validation form-validate" role="form" method="post">
										<input type="hidden" name="varyantgrup_ekle" value="1">
                                        <?php if(s(q("variantNameEdit"))!=0){?><input type="hidden" name="variantNameEdit" value="1"><?php }?>
                                        <?php if(s(q("variantNameEdit"))!=0){?><input type="hidden" name="variantGroupID" value="<?=q("varyantgrupid")?>"><?php }?>
										<!-- BEGIN DEFAULT FORM ITEMS -->
										<div class="row card-body">
                                            <?php if((S(q("varyantgrupid"))==0)||S(q("variantNameEdit"))==1){ ?>
                                            <div class="row yenisecenekdiv">
                                                <div class="form-group floating-label col-md-4">
                                                    <div class="input-group">
                                                        <div class="input-group-content">
                                                            <input type="text" class="form-control" <?=s(q("variantNameEdit"))!=0 ? 'name="variantGroupName"' : ''?> id="yenisecenekgrupad" value="<?=$variantgrupName?>">
                                                            <label for="yenisecenekgrupad">Grup Adını buraya yazın ÖRN: RENKLER</label>
                                                        </div>
                                                        <div class="input-group-btn">
                                                            <button class="btn btn-default-dark yenisecenekgrupbuton" type="<?= (s(q("varyantgrupid"))!=0) ?'submit' : 'button'?>"><?=$butonisim?></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }?>
                                            <div class="col-md-12 secenekgrupdiv">
                                                <?php
                                                $varyant_grup_sql_ek="";
                                                if(S(q("varyantgrupid"))!=0 && S(q("variantNameEdit"))==0){
                                                    $varyant_grup_sql_ek = " and varyantgrupid='" . S(q("varyantgrupid")) . "'";

                                                    $varyant_grup_sql = "
                                                        SELECT 
                                                            * 
                                                        FROM 
                                                            urunvaryantgrup 
                                                        WHERE 
                                                            varyantgrupsil=0 $varyant_grup_sql_ek 
                                                        ORDER BY 
                                                            varyantgrupid ASC
                                                    ";

                                                    //die($varyant_grup_sql);
                                                    $varyant_grup_sonuc = $db->select($varyant_grup_sql);

                                                    if ($varyant_grup_sonuc) {
                                                        //print_r($varyant_grup_sonuc);exit();
                                                        foreach($varyant_grup_sonuc as $varyant_grup_yazdir) {
                                                            $varyant_grup_db_id = $varyant_grup_yazdir["varyantgrupid"];
                                                            $varyant_grup_ad = $varyant_grup_yazdir["varyantgrupad"];
                                                            $varyant_grup_id = K(Duzelt($varyant_grup_ad));
                                                            echo '
                                                                <div class="col-md-4 border-gray varyantgrup margin-bottom-xxl" 
                                                                    id="div' . $varyant_grup_id . '">
                                                                    <div class="form-group has-success has-feedback">
                                                                        <label for="groupbutton17" class="col-sm-12 control-label opacity-100">
                                                                            <span class="secenekgrupad">' . $varyant_grup_ad . '</span> değerlerini ekleyin
                                                                        </label>
                                                                        <div class="col-sm-12 margin-bottom-xxl">
                                                                            <div class="input-group">
                                                                                <div class="input-group-content">
                                                                                    <input type="text" name="yenisecenekad" class="form-control" id="' . $varyant_grup_id . '">
                                                                                    <div class="form-control-line"></div>
                                                                                </div>
                                                                                <div class="input-group-btn">
                                                                                    <button class="btn btn-success altsecenekekle" type="button" data-id="' . $varyant_grup_id . '">
                                                                                        <i class="fa fa-plus"></i>
                                                                                    </button>';
                                                                                    if (S(q("varyantgrupid")) == 0) echo '<button class="btn btn-danger secenekgrupsil" type="button" data-dbid="' . $varyant_grup_db_id . '" data-id="' . $varyant_grup_id . '"><i class="fa fa-trash"></i></button>';
                                                                                    echo '
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-10">
                                                                        <ul class="list divider-full-bleed" id="ul' . $varyant_grup_id . '">
                                                                            <input type="hidden" name="yenisecenekgrupad[]" value="' . $varyant_grup_ad . '"> 
                                                                            <input type="hidden" name="yenisecenekgrupid[]" value="' . $varyant_grup_id . '">';

                                                                            $varyant_sql = "
                                                                                SELECT 
                                                                                    * 
                                                                                FROM 
                                                                                    urunvaryant 
                                                                                WHERE 
                                                                                    varyantgrupid='" . $varyant_grup_db_id . "' and varyantsil=0 
                                                                                ORDER BY varyantsira ASC
                                                                            ";
                                                                            $varyant_sonuc = $db->select($varyant_sql);

                                                                            if ($varyant_sonuc) {
                                                                                //print_r($varyant_sonuc);exit();

                                                                                foreach($varyant_sonuc as $varyant_yazdir) {
                                                                                    $varyant_db_id = $varyant_yazdir["varyantid"];
                                                                                    $varyant_ad = $varyant_yazdir["varyantad"];
                                                                                    $varyant_id = Duzelt($varyant_ad);
                                                                                    echo '
                                                                                        <li class="tile" id="li' . $varyant_id . '">
                                                                                            <input type="hidden" name="' . $varyant_grup_id . '_yenisecenekad[]" value="' . $varyant_ad . '"> 
                                                                                            <input type="hidden" name="' . $varyant_grup_id . '_yenisecenekid[]" value="' . $varyant_id . '"> 
                                                                                            <div class="tile-text">' . $varyant_ad . '</div>   
                                                                                            <a class="btn btn-flat ink-reaction" data-dbid="' . $varyant_db_id . '" data-id="' . $varyant_id . '" 
                                                                                            data-ul="' . $varyant_grup_id . '"><i class="md md-delete"></i></a>
                                                                                        </li>
                                                                                    ';
                                                                                }
                                                                            }
                                                                            echo '
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            ';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
										</div>
                                        <?php if(S(q("variantNameEdit"))==0): ?>
										<div class="card-actionbar">
											<div class="card-actionbar-row">
												<a class="btn btn-primary btn-default-bright" href="/_y/">İPTAL</a>
												<button type="submit" class="btn btn-primary btn-default"><?=$butonisim?></button>
											</div>
										</div>
                                        <?php endif; ?>
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
			$("#varyantgrupeklephp").addClass("active");

            //.yenisecenekgrupbuton tıklandığında sayfaya eklenecek hazır div grubu
            //[yenisecenekgrupad] inputa girilen değer ile replace olacak (renkler,bedenler,malzzemeler vs)
            //[yenisecenekgrupid] id oluşturulacak ve ilgili secenek grubunda ekleme,silme gibi işlem yapabilmek için replace edilecek
            $yenisecenek_grupkutu='' +
                '<div class="col-md-4 border-gray varyantgrup margin-bottom-xxl" id="div[yenisecenekgrupid]">' +
                '<div class="form-group has-success has-feedback">'+
                '<label for="groupbutton17" class="col-sm-12 control-label opacity-100">'+
                '<span class="secenekgrupad">[yenisecenekgrupad]</span> seçeneklerini ekleyin'+
                '</label>'+
                '<div class="col-sm-12 margin-bottom-xxl">'+
                '<div class="input-group">'+
                '<div class="input-group-content">'+
                '<input type="text" name="yenisecenekad" class="form-control" id="[yenisecenekgrupid]">'+
                '<div class="form-control-line"></div>'+
                '</div>'+
                '<div class="input-group-btn">'+
                '<button class="btn btn-success altsecenekekle" type="button" data-id="[yenisecenekgrupid]">'+
                '<i class="fa fa-plus"></i>'+
                '</button>'+
                '<button class="btn btn-danger secenekgrupsil" type="button" data-id="[yenisecenekgrupid]">'+
                '<i class="fa fa-trash"></i>'+
                '</button>'+
                '</div>'+
                '</div>'+
                '</div>'+
                '</div>' +
                '<div class="col-md-10"><ul class="list divider-full-bleed" id="ul[yenisecenekgrupid]">'+
                '<input type="hidden" name="yenisecenekgrupad[]" value="[yenisecenekgrupad_value]"> '+
                '<input type="hidden" name="yenisecenekgrupid[]" value="[yenisecenekgrupid_value]"> '+
                '</ul></div>'+
                '</div>';

            //yenisecenekgrupbuton tıklandığında #yenisecenekgrupad inputu boş mu bakıyor.
            //boş ise uyarı veriyor değilse input değerinden ad [yenisecenekgrupad] ve id [yenisecenekgrupid] oluşturuyor ve ilgili yerlerde replace ediliyor
            //İlgili alanlar düzenlendikten sonra $yenisecenek_grupkutu .secenekgrupdiv içine append ediliyor
            <?php
                if(S(q("variantNameEdit"))==0):
            ?>
            $(document).on("click",".yenisecenekgrupbuton",function ()
            {
                if($('#yenisecenekgrupad').val())
                {
                    $yenisecenekgrupad=$("#yenisecenekgrupad").val();
                    $yenisecenekgrupid= clearInput($yenisecenekgrupad);
                    if(!$("#"+$yenisecenekgrupid).length)
                    {
                        $yenisecenekkutu_degistir=$yenisecenek_grupkutu.replace("[yenisecenekgrupad]",$yenisecenekgrupad);
                        $yenisecenekkutu_degistir=$yenisecenekkutu_degistir.replaceAll("[yenisecenekgrupid]",$yenisecenekgrupid);
                        $yenisecenekkutu_degistir=$yenisecenekkutu_degistir.replaceAll("[yenisecenekgrupid_value]",$yenisecenekgrupid);
                        $yenisecenekkutu_degistir=$yenisecenekkutu_degistir.replace("[yenisecenekgrupad_value]",$yenisecenekgrupad.replaceAll('"',''));

                        $(".secenekgrupdiv").append($yenisecenekkutu_degistir);
                        $("#div"+$yenisecenekgrupid+" input").focus();
                    }
                    else
                    {
                        $("#varyantModal").modal('show');
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Bu VARYANT grubu zaten ekli");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                        $("#"+$yenisecenekgrupid).focus();
                    }
                    $("#yenisecenekgrupad").val("");
                }
                else
                {
                    $("#varyantModal").modal('show');
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("VARYANT Grubu Adı Girin");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                    $("#yenisecenekgrupad").focus();
                    $("#formvaryant ul").sortable();
                }
            });
            <?php endif; ?>
            //VARYANT GRUBU SİLME
            $(document).on("click",".secenekgrupsil",function ()
            {
                if (confirm('Varyant grubu silinsin mi?'))
                {
                    $secenekgrup_id = $(this).data("id");
                    $secenekgrup_silid=$(this).data("dbid");
                    $.ajax({
                        type: 'GET',
                        url:"/_y/s/f/sil.php?sil=varyantgrup&id="+$secenekgrup_silid,
                        dataType: "html",
                        success: function(data)
                        {
                            if($.trim(data))
                            {
                                $("#div" + $secenekgrup_id).remove();
                                $(".varyantolustur").click();
                            }
                            else
                            {
                                $("#varyantModal").modal('show');
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Hata: Varyant Grup Silme işlemi yapılamadı");
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                            }
                        }
                    });
                }
            });

            //Alt seçenekler
            //Seçenek grubuna alt seçenekler eklendiğinde append olacak alan Örneğin RENK grubuna Kırmızı,Mavi vs her renk eklendiğinde ilgili yerler append edilerek alt seçenekler oluşturulacak
            $altsecenek='' +
                '<li class="tile" id="li[altsecenek_id]">' +
                '<input type="hidden" name="[secenek_grupid]_yenisecenekad[]" value="[yenisecenekad_value]"> '+
                '<input type="hidden" name="[secenek_grupid]_yenisecenekid[]" value="[yenisecenekid_value]"> '+
                '<div class="tile-text">[altsecenek_ad]</div>'+
                '   <a class="btn btn-flat ink-reaction" data-id="[altsecenek_id]" data-ul="[secenek_grupid]"><i class="md md-delete"></i></a>'+
                '</li>';

            //.altsecenekekle butonu tıklandığında hangi grubun içine geleceğini öğrenmek için data-id'sini öğreniyoruz
            //inputtan seçenek adını alıyor ayrıca bu addan id üretiyoruz. Yukarıdaki $altsecenek içerisinden replace edilecek alanları değiştiriyoruz
            $(document).on("click",".altsecenekekle",function ()
            {
                $secenek_grupid=$(this).data("id");
                $altsecenek_ad=$("#"+$secenek_grupid).val();
                $altsecenek_id = clearInput($altsecenek_ad);
                $altsecenek_id = $altsecenek_id.replaceAll(".", "_");
                if($("#"+$secenek_grupid).val())
                {
                    if (!$("#ul"+$secenek_grupid +" #li" + $altsecenek_id).length)
                    {
                        $altsecenek_degistir = $altsecenek.replaceAll("[secenek_grupid]", $secenek_grupid);
                        $altsecenek_degistir = $altsecenek_degistir.replace("[altsecenek_ad]", $altsecenek_ad);
                        $altsecenek_degistir = $altsecenek_degistir.replaceAll("[altsecenek_id]", $altsecenek_id);
                        $altsecenek_degistir = $altsecenek_degistir.replace("[yenisecenekad_value]", $altsecenek_ad.replaceAll('"',""));
                        $altsecenek_degistir = $altsecenek_degistir.replaceAll("[yenisecenekid_value]", $altsecenek_id);

                        $("#ul" + $secenek_grupid).append($altsecenek_degistir);
                        $(".varyantolustur").removeClass("disabled");
                        $("input#"+$secenek_grupid).focus();
                    } else {
                        $("#varyantModal").modal('show');
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Seçenek değeri zaten ekli");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                        $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                        $("#"+$secenek_grupid).focus();
                    }
                    $("#"+$secenek_grupid).val("");
                }
                else
                {
                    $("#varyantModal").modal('show');
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Seçenek değeri girin");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                    $("#"+$secenek_grupid).focus();
                }
                $("#formvaryant ul").sortable();
            });

            //Altseçenek silme butonu işlevi
            $(document).on("click",".secenekgrupdiv a.btn",function ()
            {
                $ulsecenek_grupid=$(this).data("ul");
                $altsecenek_id=$(this).data("id");

                $altsecenek_silid=$(this).data("dbid");
                $.ajax({
                    type: 'GET',
                    url:"/_y/s/f/sil.php?sil=varyant&id="+$altsecenek_silid,
                    dataType: "html",
                    success: function(data)
                    {
                        if($.trim(data))
                        {
                            $("#ul"+$ulsecenek_grupid+" #li"+$altsecenek_id).remove();
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

            $("#formvaryant ul").sortable();
		</script>
        <style>#formvaryant ul li:hover{background-color:#eee;cursor:move}</style>
		<!-- END JAVASCRIPT -->
	</body>
</html>
