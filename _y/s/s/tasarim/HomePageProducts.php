<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var int $adminAuth
 */
include_once MODEL . 'Admin/AdminHomePage.php';
$adminHomePage = new AdminHomePage($db);

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);

$languages = $adminLanguage->getLanguages();

$languageCode = $adminLanguage->getLanguageCode($languageID);
$productGroups = $adminHomePage->getProductGroups();

?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Ana Sayfa Ürün Yönetimi Pozitif Eticaret</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
        <!-- END META -->

        <!-- BEGIN STYLESHEETS -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/nestable/nestable.css?1423393667" />
        
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
            <?php require_once(ROOT."/_y/s/b/leftCanvas.php");?>
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Ana Sayfa Ürün Yönetimi</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <select name="languageID" id="languageID" class="form-control">
                                                <option value="0">Dil Seçin</option>
                                                <?php foreach($languages as $language){
                                                    $selected = $language['languageID'] == $languageID ? 'selected' : '';
                                                    ?>
                                                    <option value="<?php echo $language['languageID']; ?>" data-languagecode="<?=strtolower($language['languageCode'])?>" <?=$selected?>><?php echo $language['languageName']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <p class="help-block">ANA SAYFA DÜZENLEME İÇİN DİL SEÇİN!</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-head">
                                <header>Bloklar</header>
                                <div class="tools">
                                    <button id="addProductGroup" class="btn btn-primary">Yeni Grup Ekle</button>
                                </div>

                            </div>

                            <div id="productGroups">
                            </div>
                            <?php if (empty($productGroups)): ?>
                                <button type="button" id="initializeDefaultGroups" class="btn btn-info">Varsayılan Grupları Yükle</button>
                            <?php endif; ?>
                        </div>

					</div>
				</section>
			</div>

			<?php require_once(ROOT."/_y/s/b/menu.php");?>

            <div id="editGroupModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Grup Düzenle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="editGroupForm">
                                <input type="hidden" name="group_id" id="group_id">
                                <div class="form-group">
                                    <label for="group_title">Grup Başlığı</label>
                                    <input type="text" class="form-control" id="group_title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="product_search">Ürün Ara</label>
                                    <input type="text" class="form-control" id="product_search" placeholder="Ürün adı girin">
                                    <ul id="product_search_results" class="list-group list"></ul>
                                </div>
                                <div class="form-group">
                                    <label>Seçilen Ürünler</label>
                                    <ul id="selected_products" class="list-group list ui-sortable">
                                        <!-- Seçilen ürünler buraya eklenecek -->
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label for="product_count">Gösterilecek Ürün Sayısı</label>
                                    <input type="number" class="form-control" id="product_count" name="product_count" min="1" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                            <button type="button" id="saveGroupButton" class="btn btn-primary">Kaydet</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="card">
                        <div class="card-head card-head-sm style-danger">
                            <header class="modal-title" id="alertModalLabel">Uyarı</header>
                            <div class="tools">
                                <div class="btn-group">
                                    <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                        <i class="fa fa-close"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p id="alertMessage"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
		</div>

        <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
        <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
        <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

        <script src="/_y/assets/js/libs/nestable/jquery.nestable.js"></script>
        <script src="/_y/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js"></script>

        <script src="/_y/assets/js/core/source/App.js"></script>
        <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
        <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
        <script src="/_y/assets/js/core/source/AppCard.js"></script>
        <script src="/_y/assets/js/core/source/AppForm.js"></script>
        <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
        <script src="/_y/assets/js/core/source/AppVendor.js"></script>

        <script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>
		<!-- END JAVASCRIPT -->
		<script>
			$("#homePageProductsphp").addClass("active");

            $(document).ready(function () {
                loadProductGroups();

                $("#selected_products").sortable({
                    //handel: ".tile-icon i"
                    handle: ".tile-icon i"
                });

                // Yeni grup ekleme
                $("#addProductGroup").click(function() {
                    const newGroup = {
                        type: prompt("Grup tipi girin (ör: discounted_products):"),
                        title: prompt("Grup başlığı girin (ör: İndirimdeki Ürünler):"),
                        product_count: 10,
                        position: 1,
                        is_active: 1
                    };

                    if (newGroup.type && newGroup.title) {
                        $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                            action: 'addProductGroup',
                            data: JSON.stringify(newGroup)
                        }, function(response) {
                            alert(response.message);
                            loadProductGroups();
                        }, 'json');
                    }
                });

                // Ürün gruplarını yükle
                function loadProductGroups() {
                    console.log("loading product groups");
                    $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                            action: 'getProductGroups'
                        },
                        function(response) {
                            console.log(response);
                            let container = $("#productGroups");
                            container.empty();

                            let status = response.status;
                            if (status === "error") {
                                container.append('<p>Henüz grup eklenmemiş.</p>');
                                return;
                            }

                            let data = response.data;
                            if (data.length === 0) {
                                container.append('<p>Henüz grup eklenmemiş.</p>');
                                return;
                            }

                            data.forEach(group => {
                                const productIds = JSON.parse(group.product_ids || '[]'); // JSON'dan ID'leri çözümle
                                const productIdsDisplay = productIds.length > 0 ? productIds.join(', ') : 'Ürün eklenmemiş';

                                container.append(`
                                    <div class="product-group border-black margin-bottom-lg" data-id="${group.id}" style="padding: 10px">
                                        <h4>${group.title} (${group.type})</h4>
                                        <p>Ürün Sayısı: ${group.product_count}</p>
                                        <p>Ürün ID'leri: ${productIdsDisplay}</p>
                                        <p>Durum: ${group.is_active ? 'Aktif' : 'Pasif'}</p>
                                        <button class="btn btn-warning editGroup" data-id="${group.id}">Düzenle</button>
                                        <button class="btn btn-danger deleteGroup" data-id="${group.id}">Sil</button>
                                    </div>
                                `);
                            });
                        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Hatası:", textStatus, errorThrown);
                        console.error("Hata Ayrıntıları:", jqXHR.responseText);
                    });
                }


                // Grup düzenleme
                $(document).on('click', '.editGroup', function () {
                    const groupId = $(this).data('id');
                    // Backend'den grup bilgilerini al
                    $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                        action: 'getProductGroup',
                        group_id: groupId
                    }, function(response) {
                        console.log(response);
                        const group = response.data;

                        $('#group_id').val(group.id);
                        $('#group_title').val(group.title);
                        $('#product_count').val(group.product_count);

                        const productIds = JSON.parse(group.product_ids || '[]');
                        const selectedProductsContainer = $('#selected_products');
                        selectedProductsContainer.empty();
                        productIds.forEach(productId => {
                            selectedProductsContainer.append(`<li class="list-group-item">${productId}</li>`);
                        });

                        $('#editGroupModal').modal('show');
                        $('#editGroupModal .modal-dialog').width('80%');
                        $('#editGroupModal .modal-dialog').height('90%');
                        $('#editGroupModal .modal-dialog .modal-content').css('min-height', '100%');
                    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("AJAX error:", textStatus, errorThrown);
                        console.error("Response details:", jqXHR.responseText);
                    });
                });


                // Ürün arama ve ekleme
                $('#product_search').on('input', function () {
                    const query = $(this).val();
                    if (query.length < 3){$('#product_search_results').html(''); return;}
                    var languageID = $('#languageID').val();
                    var action = "productSearch";

                    $.post('/App/Controller/Admin/AdminProductController.php', {
                        action: action,
                        languageID: languageID,
                            searchText: query
                    },
                    function (response) {
                        console.log(response);
                        response = JSON.parse(response);
                        const productsArray = response.searchResultProducts;

                        if (!Array.isArray(productsArray)) {
                            console.error("searchResultProducts bir dizi değil:", productsArray);
                            return;
                        }
                        const results = response.searchResultProducts.flat(); // Diziyi düzleştir
                        console.log(results);
                        console.log(results);
                        const searchResultsContainer = $('#product_search_results');
                        searchResultsContainer.empty();
                        results.forEach(product => {
                            console.log(product);
                            let productImages = product.resim_url ?? '';
                            if(productImages !==""){
                                productImages = product.resim_url.split(",");
                                productImages = "/Public/Image?width=40&height=40&imagePath=" + productImages[0];
                            }
                            else{
                                productImages = "/Public/Image/bos.jpg";
                            }
                            searchResultsContainer.append(`
                                <li class="tile" data-id="${product.sayfaid}" data-name="${product.sayfaad}" data-image="${productImages}">
                                    <a class="tile-content ink-reaction">
                                        <div class="tile-icon">
                                            <img src="${productImages}" alt="">
                                        </div>
                                        <div class="tile-text">
                                            ${product.sayfaad}

                                        </div>
                                        <div class="tile-icon">
                                            <button class="btn btn-sm btn-success float-right addProduct">Ekle</button>
                                        </div>
                                    </a>
                                </li>
                            `);
                        });
                    });
                });

                // Ürün ekleme
                $(document).on('click', '.addProduct', function (e) {
                    e.preventDefault();
                    const productId = $(this).closest('li').data('id');
                    const productName = $(this).closest('li').data('name');
                    const productImages = $(this).closest('li').data('image');

                    const selectedProductsContainer = $('#selected_products');
                    //selectedProductsContainer içinde aynı ürün var mı bakalım
                    let productExists = false;
                    selectedProductsContainer.find('li').each(function () {
                        if ($(this).data('id') === productId) {
                            productExists = true;
                        }
                    });

                    if (productExists) {
                        alert('Bu ürün zaten ekli.');
                        return;
                    }
                    selectedProductsContainer.append(`
                        <li class="tile" data-id="${productId}" data-name="${productName}" data-image="${productImages}">
                            <a class="tile-content ink-reaction">
                            <div class="tile-icon">
                            <img src="${productImages}" alt="">
                            </div>
                            <div class="tile-text">
                                ${productName}

                            </div>
                            <div class="tile-icon">
                                <button class="btn btn-sm btn-warning float-right removeProduct">Çıkar</button>
                            </div>
                            <div class="tile-icon">
                                <i class="fa fa-arrows"></i>
                            </div>
                        </a>
                    </li>
                    `);
                    //ekleneni sonuçtan silelim
                    $(this).closest('li').remove();
                });

                // Grup kaydetme
                $('#saveGroupButton').on('click', function () {
                    const groupId = $('#group_id').val();
                    const title = $('#group_title').val();
                    const productCount = $('#product_count').val();

                    const productIds = [];
                    $('#selected_products li').each(function () {
                        productIds.push($(this).data('id'));
                    });

                    $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                        action: 'updateProductGroup',
                        group_id: groupId,
                        title: title,
                        product_count: productCount,
                        product_ids: JSON.stringify(productIds)
                    }, function (response) {
                        if (response.status === 'success') {
                            alert('Grup başarıyla güncellendi.');
                            $('#editGroupModal').modal('hide');
                            loadProductGroups();
                        } else {
                            alert(response.message);
                        }
                    });
                });


                // Grup silme
                $(document).on("click", ".deleteGroup", function() {
                    const groupId = $(this).closest(".product-group").data("id");

                    if (confirm("Bu grubu silmek istediğinize emin misiniz?")) {
                        $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                            action: 'deleteProductGroup',
                            id: groupId
                        }, function(response) {
                            alert(response.message);
                            loadProductGroups();
                        }, 'json');
                    }
                });

                $("#initializeDefaultGroups").click(function(e) {
                    e.preventDefault();
                    $.post('/App/Controller/Admin/AdminHomePageDesignController.php', {
                        action: 'initializeDefaultProductGroups'
                    }, function(response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                });

            });
        </script>
	</body>
</html>