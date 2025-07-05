<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();

include_once MODEL."Admin/AdminProductTransfer.php";
$transferModel = new AdminProductTransfer($db);

$transfers = $transferModel->getTransfersByLanguageID($languageID);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Ürün Aktar Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">

        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
              type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />
        <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."_y/s/b/header.php");?>
		<div id="base">
            <?php require_once(ROOT."_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Excel Ürün Liste</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <!-- dil listesi gelecek -->
                                            <select name="languageID" id="languageID" class="form-control">
                                                <option value="0">Dil Seçin</option>
                                                <?php foreach($languages as $language){
                                                    $selected = $language['languageID'] == $languageID ? "selected" : "";
                                                    ?>
                                                    <option value="<?php echo $language['languageID']; ?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <p class="help-block">Aktarılacak Ürün Listesi için Dil Seçin!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-head">
                                        <header class="card-head-title">Aktarım bekleyen ürünler.</header>
                                        <div class="tools">
                                            <button class="btn btn-primary-bright" id="beginTransferButton">Aktarımı Başlat</button>
                                        </div>
                                    </div>
                                    <div id="productTable" class="card-body table-responsive">
                                        <table id="productTable" class="table table-striped no-margin">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ürün Adı</th>
                                                <th>Stok Kodu</th>
                                                <th>Kategori</th>
                                                <th>Marka</th>
                                                <th>Model</th>
                                                <th>Satış Fiyat</th>
                                                <th>Liste Fiyat</th>
                                                <td>Para Birimi</td>
                                                <th>Stok</th>
                                                <th>Kargo</th>
                                                <th>Resim</th>
                                                <th>Aktarım</th>
                                                <th>Açıklama</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            if(!empty($transfers)){
                                                $pb = 0;
                                                foreach ($transfers as $transfer){
                                                    echo '<tr id="transfer-'.$transfer['id'].'">';
                                                    echo '<td>'.$pb++.'</td>';
                                                    echo '<td>'.$transfer['product_label'].'</td>';
                                                    echo '<td>'.$transfer['product_stock_code'].'</td>';
                                                    echo '<td>'.$transfer['category_information'].'</td>';
                                                    echo '<td>'.$transfer['brand_name'].'</td>';
                                                    echo '<td>'.$transfer['model'].'</td>';
                                                    echo '<td>'.$transfer['sale_price'].'</td>';
                                                    echo '<td style="text-decoration: line-through">'.$transfer['list_price'].'</td>';
                                                    echo '<td>'.$transfer['currency'].'</td>';
                                                    echo '<td>'.$transfer['stock_quantity'].'</td>';
                                                    echo '<td>'.$transfer['delivery_time'].'</td>';
                                                    echo '<td>';
                                                    if(!empty($transfer['images'])){
                                                        foreach (explode(",",$transfer['images']) as $image){
                                                            echo '<img src="'.$image.'" width="70" height="70">';
                                                        }
                                                    }

                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo $transfer['is_completed']==1 ? 'Tamamlandı' : 'Beklemede';
                                                    echo '</td>';
                                                    echo '<td>'.$transfer['transfer_description'].'</td>';
                                                    echo '</tr>';
                                                }
                                            }
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
			<?php require_once(ROOT."_y/s/b/menu.php");?>
		</div>

        <!-- alert uyarıları için modal oluşturalım -->
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="btn-popup-alert-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="alertModalLabel">Uyarı</h4>
                    </div>
                    <div class="modal-body">
                        <p id="alertMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /*.breadcrumb > li.active{font-size: inherit}*/
        </style>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

        <script src="/_y/assets/js/libs/dropzone/dropzone.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

        <script>
            $("#productTransferphp").addClass("active");

            $(document).ready(function(){
                $("#beginTransferButton").click(function(){
                    $("#beginTransferButton").prop("disabled", true);
                    $("#beginTransferButton").html("Aktarımı Çalıştırılıyor...");
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").html("Lütfen bekleyiniz...");
                    $("#alertModal").modal("show");

                    var languageID = $("#languageID").val();
                    var action = "runTransferProductList";
                    $.ajax({
                        url: "/App/Controller/Admin/AdminProductController.php",
                        type: "POST",
                        data: {
                            action: action,
                            languageID: languageID
                        },
                        success: function(data){
                            console.log(data);
                            var data = JSON.parse(data);
                            if(data.status === "success"){
                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                $("#alertMessage").html(data.message);
                                $("#alertModal").modal("show");
                                setTimeout(function(){
                                    window.location.href = "/_y/s/s/urunler/ProductTransfer.php";
                                },1500);
                            }
                            else{
                                $("#beginTransferButton").prop("disabled", false);
                                $("#beginTransferButton").html("Aktarımı Başlat");
                                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                $("#alertMessage").html(data.message);
                                $("#alertModal").modal("show");
                            }
                        }
                    });
                });
            });

        </script>
	</body>
</html>
