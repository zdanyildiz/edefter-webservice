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

include_once MODEL."Admin/AdminProduct.php";
$productModel = new AdminProduct($db,$config);
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Ürün Liste Pozitif Eticaret</title>
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
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Ürün Liste</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
                        <div  class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body ">
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
                                            <p class="help-block">KATEGORİ LİSTELEME İÇİN DİL SEÇİN!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div  class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										<div class="row" id="categoryContainer">
											<input type="hidden" name="categoryID" id="CategoryID" value="">
											<div id="categoryList0" class="categoryList col-sm-6 form-group floating-label">
                                                <select data-layer="0" class="col-sm-12 form-control">

                                                </select>
                                                <p class="help-block">Kategori Seçin</p>
											</div>
										</div>
										<div class="form-group">
											<input type="text" name="q" id="q" class="form-control" placeholder="Arama:Ürün Başlığını yazın" value="">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body">
										<div class="table-responsive">
											<table id="productList" class="table no-margin">
												<thead>
													<tr>
														<th>Seç</th>
														<th class="sorting"><a href="">#</a></th>
														<th>Resim</th>
														<th class="sorting"><a href="#">Ad</a></th>
														<th class="sorting"><a href="#">Kategori</a></th>
                                                        <th class="sorting"><a href="#">Fiyat</a></th>
														<th>Stok</th>
														<th>İşlem</th>
														<th>Gör</th>
													</tr>
												</thead>
                                                <tbody>

                                                </tbody>
												<tfoot>
                                                    <tr>
                                                        <th>Seç</th>
                                                        <th class="sorting"><a href="">#</a></th>
                                                        <th>Resim</th>
                                                        <th class="sorting"><a href="">Ad</a></th>
                                                        <th class="sorting"><a href="">Kategori</a></th>
                                                        <th class="sorting"><a href="">Fiyat</a></th>
                                                        <th>Stok</th>
                                                        <th>İşlem</th>
                                                        <th>Gör</th>
                                                    </tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</section>
			</div>
			<?php require_once(ROOT."_y/s/b/menu.php");?>
		</div>

        <div class="modal fade" id="deleteProductConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteProductConfirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header alert-warning">
                        <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Ürün Sil</h4>
                    </div>
                    <div class="modal-body">
                        <p>Ürünü silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="deleteConfirmButton">Sil</button>
                    </div>
                </div>
            </div>
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

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

        <script>
            $("#productListphp").addClass("active");
            //sayfa tamamen yüklendikten sonra çalışacak kodlar
            function categoriesMobileShow(){
                //ekran çözünürlüğü 1024'ten küçükse
                if($(window).width() <= 1024){

                    $("#productList img").css({"width":"50px","height":"50px"});
                }
            }

            $(document).ready(function() {

                let imageRoot = "<?=imgRoot?>?imagePath=";
                //dil seçildiğinde
                $(document).on('change','#languageID',function () {
                    var languageID = $(this).val();
                    var action = "getProductCategories";

                    //#categoryContainer içindeki tüm selectleri boşaltalım
                    $('#categoryContainer select').empty();

                    if (languageID > 0) {
                        $.ajax({
                            url: '/App/Controller/Admin/AdminProductController.php',
                            type: 'POST',
                            data: {
                                languageID: languageID,
                                action: action
                            },
                            success: function (data) {
                                console.log(data);
                                //json olarak alacağız
                                var response = JSON.parse(data);
                                var status = response.status;
                                var message = response.message;
                                var categories = response.productCategories;

                                if (status === "success") {
                                    var categoryList = $('#categoryList0 select');
                                    categoryList.empty();
                                    categoryList.append('<option value="0">Kategori Seçin</option>');
                                    $.each(categories, function (index, category) {
                                        //console.log(category);
                                        categoryList.append('<option value="' + category.productCategoryID + '">' + category.productCategoryName + '</option>');
                                    });
                                } else {
                                    var modal = $('#alertModal');
                                    modal.find('#alertMessage').text(message);
                                }
                            }
                        });
                    }
                });

                var languageID = $('#languageID').val();
                if(languageID>0){
                    $('#languageID').trigger('change');
                }
                else{
                    $('#languageID').val($('#languageID option[value!="0"]').first().val()).change();
                }

                //categoryList içinde select değiştiğinde data-layer değerine göre yeni selectler oluşturulacak ve seçilen kategorinin alt kategorileri gelecek
                $(document).on('change', '#categoryContainer select', function () {
                    var layer = $(this).data('layer');
                    var categoryID = $(this).val();
                    var action = "getSubCategories";

                    if (categoryID > 0) {
                        $.ajax({
                            url: '/App/Controller/Admin/AdminProductController.php',
                            type: 'POST',
                            data: {
                                categoryID: categoryID,
                                action: action
                            },
                            success: function (data) {
                                //console.log(data);
                                //json olarak alacağız
                                var response = JSON.parse(data);
                                var status = response.status;
                                var message = response.message;
                                var subCategories = response.subCategories;

                                layer++;

                                if (status === "success") {
                                    //$('#categoryList' + layer + ' div yoksa oluşturalım
                                    if ($('#categoryList' + layer).length == 0) {
                                        var newCategoryList = '<div id="categoryList' + layer + '" class="categoryList col-sm-6 form-group floating-label">';
                                        newCategoryList += '<select data-layer="' + layer + '" class="col-sm-12 form-control"></select>';
                                        newCategoryList += '<p class="help-block">Alt kategori Seçin</p>';
                                        newCategoryList += '</div>';
                                        $('#categoryContainer').append(newCategoryList);
                                    }
                                    var categoryList = $('#categoryList' + layer + ' select');
                                    categoryList.empty();
                                    categoryList.append('<option value="0">Alt Kategori Seçin</option>');
                                    $.each(subCategories, function (index, category) {
                                        //console.log(category);
                                        categoryList.append('<option value="' + category.productCategoryID + '">' + category.productCategoryName + '</option>');
                                    });
                                    categoriesMobileShow();
                                } else {
                                    if ($('#categoryList' + layer).length > 0){
                                        $('#categoryList' + layer).remove();
                                    }
                                }
                            }
                        });
                    }
                });

                //son seçilen kategoriye tıklandığında ürünleri getir
                $(document).on('change', '#categoryContainer select:last', function () {

                    var categoryID = $(this).val();
                    var action = "getProductsByCategoryID";

                    if (categoryID > 0) {

                        $.ajax({
                            url: '/App/Controller/Admin/AdminProductController.php',
                            type: 'POST',
                            data: {
                                categoryID: categoryID,
                                action: action
                            },
                            success: function (data) {
                                //console.log(data);

                                var response = JSON.parse(data);
                                var status = response.status;
                                var message = response.message;
                                var products = response.products;

                                if (status === "success") {
                                    //ürünleri tabloya yazdıralım
                                    var productTable = $('#productList tbody');
                                    productTable.empty();
                                    var tr = '';
                                    $.each(products, function (index, product) {
                                        //console.log(product);
                                        var productImages = product.productImages ? product.productImages.split('||') : [];
                                        var firstImageDetails = productImages[0] ?productImages[0].split('|') : [];
                                        var firstImageUrl = 'bos.jpg';
                                        $.each(firstImageDetails, function (index, detail) {
                                            if (detail.trim().startsWith('imageUrl')) {
                                                firstImageUrl = detail.split(':')[1].trim();
                                            }
                                        });

                                        tr += '<tr id="tr-' + product.productID + '">';
                                        tr += '<td><input type="checkbox" name="productID[]" value="' + product.productID + '"></td>';
                                        tr += '<td>' + product.productID + '</td>';
                                        tr += '<td><img src="' + imageRoot + firstImageUrl + '&width=100&height=100" width="100" height="100" style="max-width:100px;max-height:100px"></td>';
                                        tr += '<td>' + product.productName + '</td>';
                                        tr += '<td>' + product.productCategoryName + '</td>';
                                        tr += '<td>' + product.productSalePrice + " " + product.productCurrencySymbol + '</td>';
                                        tr += '<td>' + product.productStock + '</td>';
                                        tr += '<td><a href="/_y/s/s/urunler/AddProduct.php?productID=' + product.productID + '" class="btn btn-primary btn-sm">Düzenle</a> ';
                                        tr += '<a href="#deleteProductConfirm" data-id="' + product.productID + '" data-target="#deleteProductConfirm" data-toggle="modal" class="btn btn-sm btn-danger deleteProductLink" target="_blank"><i class="fa fa-trash"></i></a></td>';
                                        tr += '<td><a href="' + product.productSeoLink + '" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-link"></i> </td>';
                                        tr += '</tr>';
                                    });
                                    productTable.append(tr);

                                }
                                else {
                                    var modal = $('#alertModal');
                                    modal.find('#alertMessage').text(message);
                                }
                            }
                        });
                    }
                });

                //#q değiştiğinde ürünleri ara
                $(document).on('keyup', '#q', function () {
                    var q = $(this).val();

                    if (q.length < 2) {

                    }

                    var languageID = $('#languageID').val();
                    var action = "productSearch";
                    var categoryID = $('#categoryContainer select:last').val();

                    $.ajax({
                        url: '/App/Controller/Admin/AdminProductController.php',
                        type: 'POST',
                        data: {
                            searchText: q,
                            languageID: languageID,
                            action: action,
                            categoryID: categoryID
                        },
                        success: function (response) {
                            //console.log(response);

                            var searchResult = JSON.parse(response);
                            var searchResultProducts = searchResult.searchResultProducts;

                            //ürünleri tabloya yazdıralım
                            var productTable = $('#productList tbody');
                            productTable.empty();
                            var tr ='';
                            for (var i = 0; i < searchResultProducts.length; i++){
                                var product = searchResultProducts[i][0];
                                var productID = product.sayfaid;
                                var productLink = product.link;
                                var productImage = product.resim_url ? product.resim_url.split(",")[0] : "bos.jpg";
                                var productName = product.sayfaad;
                                var productCategory = product.kategoriad;
                                var productCurrencySymbol = product.parabirimsimge;
                                var productPrice = product.urunsatisfiyat;
                                var productStock = product.urunstok;

                                tr += '<tr id="tr-' + productID + '">';
                                tr += '<td><input type="checkbox" name="productID[]" value="' + productID + '"></td>';
                                tr += '<td>' + productID + '</td>';
                                tr += '<td><img src="' + imageRoot + productImage + '&width=100&height=100" width="100" height="100" style="max-width:100px;max-height:100px"></td>';
                                tr += '<td>' + productName + '</td>';
                                tr += '<td>' + productCategory + '</td>';
                                tr += '<td>' + productPrice + " " + productCurrencySymbol + '</td>';
                                tr += '<td>' + productStock + '</td>';
                                tr += '<td><a href="/_y/s/s/urunler/AddProduct.php?productID=' + productID + '" class="btn btn-primary btn-sm">Düzenle</a> ';
                                tr += '<a href="#deleteProductConfirm"  data-id="' + product.productID + '" data-target="#deleteProductConfirm" data-toggle="modal" class="btn btn-sm btn-danger deleteProductLink" target="_blank"><i class="fa fa-trash"></i></a></td>';
                                tr += '<td><a href="' + productLink + '" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-link"></i> </td>';
                                tr += '</tr>';
                            }
                            productTable.append(tr);

                        }
                    });
                });

                $(document).on("click",".deleteProductLink",function(){
                    var productID = $(this).data("id");
                    $("#deleteConfirmButton").data("id",productID);
                });

                $(document).on("click","#deleteConfirmButton",function(){
                    $("#deleteProductConfirm").modal("hide");
                    var productID = $(this).data("id");
                    var action = "deleteProduct";
                    $.ajax({
                        url: "/App/Controller/Admin/AdminProductController.php",
                        type: "POST",
                        data: {
                            action: action,
                            productID: productID
                        },
                        success: function (data) {
                            console.log(data);
                            var response = JSON.parse(data);
                            if(response.status === "success"){
                                //location.reload();
                                $("#alertModal .modal-header").removeClass("alert-danger").addClass("alert-success");
                                $("#tr-"+productID).remove();
                                $("#alertMessage").text("Ürün başarıyla silindi");
                                $("#alertModal").modal("show");
                                //1 saniye sonra modalı kapatalım
                                setTimeout(function(){
                                    $("#alertModal").modal("hide");
                                },1000);
                            } else {
                                $("#alertModal .modal-header").removeClass("alert-success").addClass("alert-danger");
                                $("#alertMessage").text(response.message);
                                $("#alertModal").modal("show");
                            }
                        }
                    });
                });

                categoriesMobileShow();
            });
        </script>
	</body>
</html>
