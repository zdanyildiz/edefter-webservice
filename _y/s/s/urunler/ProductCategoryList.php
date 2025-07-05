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

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Kategori Liste Pozitif E-Ticaret</title>
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
							<li class="active">Ürün Kategori Liste</li>
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
                                                    $selected = $language['languageID'] == $languageID ? 'selected' : '';
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
										<div class="form-group">
											<input type="text" name="q" id="q" class="form-control" placeholder="Arama:Kategori Başlığını yazın" value="">
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
											<table id="productCategoryList" class="table no-margin dataTable">
												<thead>
													<tr>
                                                        <th>Seç</th>
                                                        <th class="sorting"><a href="#">#</a></th>
                                                        <th class="sorting"><a href="#">Ad</a></th>
                                                        <th class="sorting"><a href="#">Alt Kategori</a></th>
                                                        <th class="sorting"><a href="#">Ürün Sayısı</a></th>
                                                        <th>İşlem</th>
                                                        <th>Gör</th>
													</tr>
												</thead>
												<tbody>

												</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Seç</th>
                                                        <th class="sorting"><a href="#">#</a></th>
                                                        <th class="sorting"><a href="#">Ad</a></th>
                                                        <th class="sorting"><a href="#">Alt Kategori</a></th>
                                                        <th class="sorting"><a href="#">Ürün Sayısı</a></th>
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
            $("#productCategoryListphp").addClass("active");

            function createCategoryRow(category) {
                var categoryID = category.productCategoryID;
                var categoryName = category.productCategoryName;
                var subCategoryCount = category.subCategoryCount;
                var productCount = category.productCount;
                var categorySeoLink = category.productCategorySeoLink;

                var btn = '-';
                if(subCategoryCount>0){
                    //alt kategorisi varsa buton oluşturalım data olarak categoryID ekleyelim
                    btn = '<a href="javascript:void(0)" data-categoryID="'+categoryID+'" class="btn btn-primary-bright btn-sm showSubcategory">Alt Kategoriler ('+ subCategoryCount +')</a>';
                }

                var tr = '';
                tr += '<tr>';
                tr += '<td><input type="checkbox" name="categoryID[]" value="' + categoryID + '"></td><td>' + categoryID + '</td>';
                tr += '<td>' + categoryName + '</td>';
                tr += '<td>' + btn + '</td>';
                tr += '<td>' + productCount + '</td>';
                tr += '<td><a href="/_y/s/s/urunler/AddProductCategory.php?categoryID=' + categoryID + '" class="btn btn-primary btn-sm">Düzenle</a></td>';
                tr += '<td><a href="' + categorySeoLink + '" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-link"></i></a></td>';
                tr += '</tr>';

                return tr;
            }

            $(document).ready(function() {

                $(document).on('change','#languageID',function () {
                    var languageID = $(this).val();
                    var action = "getProductCategoriesWithInfo";

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

                                var response = JSON.parse(data);
                                var status = response.status;
                                var message = response.message;
                                var categories = response.productCategories;

                                if (status === "success") {

                                    var tBody = $('#productCategoryList tbody');
                                    tBody.empty();

                                    $.each(categories, function (index, category) {
                                        var categoryRow = createCategoryRow(category);
                                        tBody.append(categoryRow);
                                    });
                                }
                                else {
                                    var modal = $('#alertModal');
                                    modal.find('#alertMessage').text(message);
                                }
                            }
                        });
                    }
                });

                //languageID değerini alalım
                var languageID = $('#languageID').val();
                if(languageID>0){
                    $('#languageID').trigger('change');
                }
                else{
                    $('#languageID').val($('#languageID option[value!="0"]').first().val()).change();
                }

                $(document).on('keyup', '#q', function () {
                    var q = $(this).val();

                    if (q.length < 2) {

                    }

                    var languageID = $('#languageID').val();
                    var action = "getProductCategoryWithInfoBySearch";

                    $.ajax({
                        url: '/App/Controller/Admin/AdminProductController.php',
                        type: 'POST',
                        data: {
                            searchText: q,
                            languageID: languageID,
                            action: action
                        },
                        success: function (response) {

                            console.log(response);

                            response = JSON.parse(response);
                            var status = response.status;
                            var message = response.message;
                            var categories = response.productCategories;

                            if (status === "success") {

                                var tBody = $('#productCategoryList tbody');
                                tBody.empty();

                                $.each(categories, function (index, category) {
                                    var categoryRow = createCategoryRow(category);
                                    tBody.append(categoryRow);
                                });

                            }
                            else {
                                var modal = $('#alertModal');
                                modal.find('#alertMessage').text(message);

                                if (q.length < 2) {
                                    $('#languageID').trigger('change');
                                }
                            }

                        }
                    });
                });

                $(document).on('click','.showSubcategory',function () {
                    var categoryID = $(this).data('categoryid');
                    var languageID = $('#languageID').val();
                    var action = "getSubCategoriesWithInfo";

                    $.ajax({
                        url: '/App/Controller/Admin/AdminProductController.php',
                        type: 'POST',
                        data: {
                            categoryID: categoryID,
                            languageID: languageID,
                            action: action
                        },
                        success: function (data) {

                            console.log(data);

                            var response = JSON.parse(data);
                            var status = response.status;
                            var message = response.message;
                            var categories = response.subCategories;

                            if (status === "success") {

                                var tBody = $('#productCategoryList tbody');
                                tBody.empty();

                                var tr = '<tr>';
                                tr += '<td><a href="javascript:void(0)" class="btn btn-primary-bright btn-sm" id="backToMainCategory">Geri Dön</a></td>';
                                tr += '<td></td>';
                                tr += '<td></td>';
                                tr += '<td></td>';
                                tr += '<td></td>';
                                tr += '<td></td>';
                                tr += '<td></td>';
                                tr += '</tr>';
                                tBody.append(tr);
                                $.each(categories, function (index, category) {
                                    var categoryRow = createCategoryRow(category);
                                    tBody.append(categoryRow);
                                });
                                tBody.append(tr);
                            }
                            else {
                                var modal = $('#alertModal');
                                modal.find('#alertMessage').text(message);
                            }
                        }
                    });
                });

                $(document).on('click','#backToMainCategory',function () {
                    $('#languageID').trigger('change');
                });
            });
        </script>
	</body>
</html>
