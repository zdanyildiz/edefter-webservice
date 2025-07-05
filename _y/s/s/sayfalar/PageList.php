<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */

$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;
$languageID = intval($languageId);

include_once MODEL . 'Admin/AdminLanguage.php';
$adminLanguage = new AdminLanguage($db);
$languages = $adminLanguage->getLanguages();
?>
<!DOCTYPE html>
<html lang="tr">
	<head>
		<title>Aktif Sayfalar Pozitif Eticaret</title>
        
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
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/select2/select2.css?1424887856" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/multi-select/multi-select.css?1424887857" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-datepicker/datepicker3.css?1424887858" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />

        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/bootstrap-tagsinput/bootstrap-tagsinput.css?1424887862" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/typeahead/typeahead.css?1424887863" />
        <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/dropzone/dropzone-theme.css?1424887864" />        <style>
            .translation-badges {
                white-space: nowrap;
            }
            .translation-badges .label {
                font-size: 10px;
                margin-right: 2px;
                margin-bottom: 2px;
                display: inline-block;
            }
            .translation-status {
                min-width: 120px;
            }
            .translatePageButton {
                margin-left: 5px;
            }
            #translationProgress .progress {
                margin-bottom: 10px;
            }
            .card-head.style-primary {
                background-color: #2196F3;
                color: white;
            }
            
            /* Çeviri modal stil düzenlemeleri */
            #translationModal .modal-dialog {
                max-width: 650px;
            }
            
            #languageSelection {
                max-height: 300px;
                overflow-y: auto;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                background-color: #f9f9f9;
            }
            
            #languageSelection .checkbox {
                margin-bottom: 8px;
            }
            
            #languageSelection .checkbox label {
                font-weight: normal;
                cursor: pointer;
                padding-left: 20px;
            }
            
            #languageSelection .checkbox input[type="checkbox"] {
                margin-right: 8px;
            }
            
            .translation-action-buttons {
                margin-bottom: 15px;
            }
            
            .translation-action-buttons .btn {
                margin-right: 5px;
            }
        </style>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
		<script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed ">
		<?php require_once(ROOT."/_y/s/b/header.php");?>
		<div id="base">
			<div id="content">
				<section>
					<div class="section-header">
						<ol class="breadcrumb">
							<li class="active">Aktif Sayfalar</li>
						</ol>
					</div>
					<div class="section-body contain-lg">
						<div  class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6 form-group">
                                                <select id="languageID" name="languageID" class="form-control">
                                                    <?php
                                                    foreach($languages as $language){
                                                        $selected = $languageID == $language['languageID'] ? "selected" : "";
                                                        echo "<option value='".$language['languageID']."' ".$selected.">".$language['languageName']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <p class="help-block">Aradığınız kategoriyi kolayca bulmanızı sağlar!</p>
                                            </div>
                                            <div class="col-sm-6 form-group">
                                                <select id="translationFilter" name="translationFilter" class="form-control">
                                                    <option value="all">Tüm Sayfalar</option>
                                                    <option value="untranslated">Çevrilmemiş</option>
                                                    <option value="pending">Bekleyen Çeviri</option>
                                                    <option value="completed">Çeviri Tamamlanmış</option>
                                                    <option value="failed">Çeviri Başarısız</option>
                                                </select>
                                                <p class="help-block">Çeviri durumuna göre filtreleyebilirsiniz!</p>
                                            </div>
                                        </div>
                                        <div class="row" id="categoryContainer">
                                            <div id="categoryList0" class="categoryList col-sm-6 form-group floating-label">
                                                <select data-layer="0" class="col-sm-12 form-control">

                                                </select>
                                                <p class="help-block">Kategori Seçin</p>
                                            </div>
                                        </div>
										<div class="form-group">
											<input type="text" name="q" id="q" class="form-control" placeholder="Arama:Sayfa Başlığını yazın" value="">
										</div>
										<div class="form-group">
											<a href="/_y/s/s/sayfalar/PageList.php">Sıfırla</a> 
										</div>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger" id="bulkDeletePagesButton" style="display:none;">Seçilenleri Sil</button>
                                        </div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-body ">
										    <table id="pageListTable" class="table no-margin">
                                                <thead>                                                    <tr>
                                                        <th><input type="checkbox" id="selectAllPages"></th>
                                                        <th class="sorting"><a href="javascript(void:0)"># <i class="fa fa-unsorted"></i></a></th>
                                                        <th class="sorting"><a href="javascript(void:0)">Kategori <i class="fa fa-unsorted"></i></a></th>
                                                        <th class="sorting"><a href="javascript(void:0)">Sayfa Ad <i class="fa fa-unsorted"></i></a></th>
                                                        <th class="sorting"><a href="javascript(void:0)">Sayfa Sira <i class="fa fa-unsorted"></i></a></th>
                                                        <th><a href="javascript(void:0)">Çeviri Durumu <i class="fa fa-unsorted"></i></a></th>
                                                        <th><a href="javascript(void:0)">İşlem <i class="fa fa-unsorted"></i></a></th>
                                                        <th>Gör</th>
                                                        <th>Sırala</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
										    </table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
            
			<?php require_once(ROOT."/_y/s/b/menu.php");?>
            
		</div>

        <div class="modal fade" id="deletePageConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deletePageConfirmModalLabel" aria-hidden="true">
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
                        <p id="alertMessage">Sayfayı silmek istediğinize emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                        <button type="button" class="btn btn-danger" id="deletePageConfirmButton">Sil</button>
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
			$("#pageListphp").addClass("active");

            $("#languageID").change(function(){
                var languageID = $(this).val();
                window.location.href = "/_y/s/s/sayfalar/PageList.php?languageID="+languageID;
            });

            async function changeLanguageID() {
                const languageID = $('#languageID').val();
                const action = "getCategories";
                const categoryContainer = $('#categoryContainer select');
                const modal = $('#alertModal');
                const categoryList = $('#categoryList0 select');

                categoryContainer.empty();

                if (languageID > 0) {
                    try {
                        const response = await $.ajax({
                            url: '/App/Controller/Admin/AdminCategoryController.php',
                            type: 'POST',
                            data: {
                                languageID: languageID,
                                action: action
                            }
                        });

                        const { status, message, categories: categories } = JSON.parse(response);

                        if (status === "success") {
                            $("#pageCategoryID").val(0);
                            categoryList.empty();
                            categoryList.append('<option value="0" selected>Kategori Seçin</option>');
                            categories.forEach(({ categoryID, categoryName }) => {
                                categoryList.append(`<option value="${categoryID}">${categoryName}</option>`);
                            });
                            //console.log("Dil değişikliği başarılı, kategoriler yüklendi");
                            //url'den categoryID gelmişse listeden ilgili category'i seçelim
                            const urlParams = new URLSearchParams(window.location.search);
                            const categoryID = urlParams.get('categoryID');
                            if (categoryID) {
                                categoryList.val(categoryID);
                                //kategori değişim olayını tetikleyelim
                                categoryList.trigger('change');
                            }
                        } else {
                            modal.find('#alertMessage').text(message);
                        }
                    } catch (error) {
                        console.error(error);
                    }
                }
            }

            async function loadCategories(selectedElement) {
                const layer = selectedElement.data('layer');
                const categoryID = selectedElement.val();
                const action = "getSubCategories";

                if (categoryID > 0) {
                    try {
                        const response = await $.ajax({
                            url: '/App/Controller/Admin/AdminCategoryController.php',
                            type: 'POST',
                            data: {
                                categoryID: categoryID,
                                action: action
                            }
                        });
                        //console.log(response);
                        const data = JSON.parse(response);
                        const { status, subCategories } = data;

                        if (status === "success") {
                            const categoryContainer = $('#categoryContainer');
                            const newLayer = layer + 1;
                            const categoryListId = 'categoryList' + newLayer;
                            const categoryListSelector = '#' + categoryListId;

                            if (!$(categoryListSelector).length) {
                                const newCategoryList = `
                                <div id="${categoryListId}" class="categoryList col-sm-6 form-group floating-label">
                                    <select data-layer="${newLayer}" class="col-sm-12 form-control"></select>
                                    <p class="help-block">Alt kategori Seçin</p>
                                </div>`;
                                categoryContainer.append(newCategoryList);
                            }

                            const categoryList = $(categoryListSelector + ' select');
                            categoryList.empty();
                            categoryList.append('<option value="0" selected>Alt Kategori Seçin</option>');
                            $.each(subCategories, function (index, category) {
                                categoryList.append(`<option value="${category.categoryID}">${category.categoryName}</option>`);
                            });

                        } else {
                            $(`#categoryList${layer + 1}`).remove();
                        }
                    } catch (error) {
                        console.error(`Error loading categories: ${error.message}`);
                    }
                }
            }

            function savePageOrder(pageID, pageOrder) {
                return new Promise((resolve, reject) => {
                    const action = "savePageOrder";
                    $.ajax({
                        url: '/App/Controller/Admin/AdminPageController.php',
                        type: 'POST',
                        data: {
                            pageID: pageID,
                            pageOrder: pageOrder,
                            action: action
                        },
                        success: function(response) {
                            //console.log(response);
                            response = JSON.parse(response);
                            if (response.status === "success") {
                                //console.log(response.message);
                                resolve(true);
                            } else {
                                console.error(response.message);
                                reject(false);
                            }
                        },
                        error: function(error) {
                            console.error(error);
                            reject(error);
                        }
                    });
                });
            }

            $(document).ready(function() {
                changeLanguageID();

                $('#categoryContainer').on('change', 'select', function() {

                    loadCategories($(this));

                    //seçilen optionun değerini alalım
                    const categoryID = $(this).val();
                    loadPagesWithTranslationStatus(categoryID);
                    /*
                    //console.log(categoryID);
                    const languageID = $('#languageID').val();
                    const translationFilter = $('#translationFilter').val();
                    const action = "getPagesWithTranslationStatus";

                    $.ajax({
                        url: '/App/Controller/Admin/AdminPageController.php',
                        type: 'POST',
                        data: {
                            languageID: languageID,
                            translationFilter: translationFilter,
                            categoryID: categoryID,
                            action: action
                        },success: function(response) {
                            ////console.log(response);
                            responseJson = JSON.parse(response);
                            if (responseJson.status === "success") {
                                const { pages } = responseJson;
                                const tableBody = $('table#pageListTable tbody');
                                const adminPermission = <?=$adminAuth?>;
                                let disabled = '';
                                tableBody.empty();
                                pages.forEach((page, index) => {
                                    const { pageID, pageUniqID, pageCategoryName, pageName, pageOrder, pageTypePermission, pageSeoLink, translationDetails} = page;
                                    //console.log(pageName + " " + translationDetails)
                                    disabled = pageTypePermission=== 0 ? '' : '';

                                    if(adminPermission === 0){
                                        disabled = '';
                                    }

                                    // Çeviri durumu HTML'ini oluştur
                                    let translationStatusHtml = generateTranslationStatusHtml(translationDetails || [], pageID);

                                    const row = `
                                    <tr id="tr-${pageID}">
                                        <td>${index + 1}</td>
                                        <td>${pageCategoryName}</td>
                                        <td>${pageName}</td>
                                        <td class="pageOrder">${pageOrder}</td>
                                        <td class="translation-status">${translationStatusHtml}</td>
                                        <td>
                                            <a href="/_y/s/s/sayfalar/AddPage.php?pageID=${pageID}" class="btn btn-sm btn-primary ${disabled}">Düzenle</a>
                                            <button class="btn btn-sm btn-danger deletePageButton ${disabled}" data-pageid="${pageID}">Sil</button>
                                            <button class="btn btn-sm btn-success translatePageButton ${disabled}" data-pageid="${pageID}" data-pagename="${pageName}">Çeviri</button>
                                        </td>
                                        <td>
                                            <a href="${pageSeoLink}" target="_blank"><i class="fa fa-link"></i></a>
                                        </td>
                                        <td>
                                            <a class="tile-content ink-reaction dragDropPage" data-id="${pageID}" style="cursor:grab"><div class="tile-icon"><i class="fa fa-arrows"></i></div></a>
                                        </td>
                                    </tr>`;
                                    tableBody.append(row);
                                });
                            }
                        },
                        error: function(error) {
                            console.error(error);
                        }
                    });
                    */
                });
                
                $('#q').keyup(function(){
                    const q = $(this).val();
                    const categoryID = $('#categoryContainer select:last').val() || 0;
                    loadPagesWithTranslationStatus(categoryID, q);
                });

                $(document).on('click', '.deletePageButton', function() {
                    const pageID = $(this).data('pageid');
                    $('#deletePageConfirmModal').modal('show');
                    $('#deletePageConfirmModal').data('pageid', pageID);
                });

                $('#deletePageConfirmButton').click(function() {

                    $('#deletePageConfirmModal').modal('hide');

                    const pageID = $('#deletePageConfirmModal').data('pageid');
                    const action = "deletePage";
                    $.ajax({
                        url: '/App/Controller/Admin/AdminPageController.php',
                        type: 'POST',
                        data: {
                            pageID: pageID,
                            action: action
                        },
                        success: function(response) {
                            const responseJson = JSON.parse(response);
                            if (responseJson.status === "success") {
                                $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                                $('#alertModal').modal('show');
                                $('#alertModal').find('#alertMessage').text(responseJson.message);
                                $("#tr-"+pageID).remove();
                                //1,5 sanıye sonra sayfayı yenile
                                setTimeout(function() {
                                    $("#alertModal").modal("hide");
                                }, 1500);
                            } else {
                                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                                $('#alertModal').modal('show');
                                $('#alertModal').find('#alertMessage').text(responseJson.message);
                            }
                        },
                        error: function(error) {
                            console.error(error);
                        }
                    });
                });

                $('table#pageListTable tbody').sortable({
                    handle: '.dragDropPage', // Sürükleme işlemi için kullanılacak eleman
                    axis: 'y', // Y ekseni boyunca sıralama
                    update: function (event, ui) {
                        // tbody altındaki tüm tr'leri 1 den başlayarak arttıralım ve .pageOrder class'ına yazdıralım
                        $('table#pageListTable tbody tr').each(function (index) {

                            let pageID = $(this).find('.dragDropPage').data('id');
                            let pageOrder = index + 1;

                            if(savePageOrder(pageID, pageOrder)){
                                $(this).find('.pageOrder').text(pageOrder);
                            }
                            else{
                                //alert message gösterip döngüyü durduralım
                                $('#alertModal').modal('show');
                                $('#alertModal').find('#alertMessage').text("Sıralama işlemi başarısız oldu");
                                return false;
                            }

                        });
                    }
                });

                // Çeviri filtresi değiştiğinde sayfaları yeniden yükle
                $('#translationFilter').change(function(){
                    const categoryID = $('#categoryList0 select').val() || 0;
                    loadPagesWithTranslationStatus(categoryID);
                });

                // İlk yüklemede çeviri durumu ile sayfaları yükle
                loadPagesWithTranslationStatus(0);

            });

            // Çeviri durumu ile sayfaları yükle
            function loadPagesWithTranslationStatus(categoryId = 0, searchText = '') {
                const languageID = $('#languageID').val();
                const translationFilter = $('#translationFilter').val();
                const action = "getPagesWithTranslationStatus";
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminPageController.php',
                    type: 'POST',
                    data: {
                        languageID: languageID,
                        translationFilter: translationFilter,
                        categoryID: categoryId,
                        searchText: searchText,
                        action: action
                    },
                    success: function(response) {
                        //console.log(response);
                        const responseJson = JSON.parse(response);
                        if (responseJson.status === "success") {
                            renderPagesTable(responseJson.pages);
                        } else if (responseJson.status === "error"){
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message || 'Sayfalar yüklenirken hata oluştu');
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        $('#alertModal').modal('show');
                        $('#alertModal').find('#alertMessage').text('Sayfalar yüklenirken hata oluştu');
                    }
                });
            }            // Çeviri butonunun gösterilip gösterilmeyeceğini belirleyen yardımcı fonksiyon
            function shouldShowTranslateButton(isMainLanguage, translationDetails, mainLanguageEquivalent, totalLanguages) {
                // Kural 1: Sayfa dili anadil ise ASLA ÇEVİR butonunu gösterme.
                if (isMainLanguage) {
                    return false;
                }

                // Kural 2: Sayfa dili anadil değilse:
                // Eğer bu sayfa ana dildeki bir sayfanın çevirisi ise (mainLanguageEquivalent dolu ise),
                // çeviri butonu görünmemeli. Çeviri ana dilden yönetilir.
                /*if (mainLanguageEquivalent) {
                    return false;
                }*/

                // Eğer bu sayfa ikincil dilde orijinal bir sayfa ise (mainLanguageEquivalent boş ise):
                // Çeviri durumunu kontrol et.
                // translationDetails, bu sayfanın diğer dillere olan çeviri durumlarını içermeli.

                // Eğer hiç çeviri detayı yoksa (yani hiçbir dile çeviri başlatılmamışsa) butonu göster.
                if (!translationDetails || translationDetails.length === 0) {
                    return true;
                }

                // Eğer çeviri detaylarında 'pending' veya 'failed' durumunda bir çeviri varsa butonu göster.
                const needsAttention = translationDetails.some(detail =>
                    detail.translationStatus === 'pending' || detail.translationStatus === 'failed'
                );

                return needsAttention;
            }

            // Sayfalar tablosunu render et
            function renderPagesTable(pages) {
                console.log('Pages data received by renderPagesTable:', pages);
                // php $languages toplamına göre başka dil var mı kontrol edelim
                const languageCount = <?=count($languages)?>;
                const tableBody = $('table#pageListTable tbody');
                const adminPermission = <?=$adminAuth?>;
                tableBody.empty();
                
                pages.forEach((page, index) => {
                    const { pageID, pageUniqID, pageCategoryName, pageName, pageOrder, pageTypePermission, pageSeoLink, translationDetails, isMainLanguage, mainLanguageEquivalent} = page;

                    let mainPageName = '';

                    let disabled = pageTypePermission === 0 ? '' : '';
                    if(adminPermission === 0){
                        disabled = '';
                    }
                    
                    // Çeviri durumu HTML'ini oluştur
                    let translationStatusHtml;

                    if (isMainLanguage) {
                        // Ana dildeyse çeviri durumunu göster
                        translationStatusHtml = generateTranslationStatusHtml(translationDetails || [], pageID);
                    }
                    else {
                        // Ana dilde değilse
                        if (mainLanguageEquivalent) {
                            // Ana dil karşılığı varsa, o sayfanın mevcut dile çeviri durumunu göster
                            const statusDetail = translationDetails && translationDetails.length > 0 ? translationDetails[0] : null;
                            mainPageName = mainLanguageEquivalent.mainPageName ?? '';
                            if (statusDetail) {
                                let statusText = '';
                                let badgeClass = 'label-default';
                                switch(statusDetail.translationStatus) {
                                    case 'pending':
                                        statusText = 'Beklemede';
                                        badgeClass = 'label-warning';
                                        break;
                                    case 'completed':
                                        statusText = 'Çevrildi';
                                        badgeClass = 'label-success';
                                        break;
                                    case 'failed':
                                        statusText = 'Başarısız';
                                        badgeClass = 'label-danger';
                                        break;
                                    default:
                                        statusText = 'Çevrilmemiş';
                                        badgeClass = 'label-info';
                                        break;
                                }
                                translationStatusHtml = `<span class="label ${badgeClass}">${statusText}</span>`;
                            } else {
                                translationStatusHtml = `<span class="label label-info">Çevrilmemiş</span>`;
                            }
                        } else {
                            // Ana dil karşılığı yoksa (ikincil dilde orijinal sayfa), çeviri bilgisi yok
                            translationStatusHtml = `<span class="label label-warning">Ana Dil Karşılığı Yok</span>`;
                        }
                    }

                    const row = `
                    <tr id="tr-${pageID}">
                        <td><input type="checkbox" class="page-checkbox" value="${pageID}"></td>
                        <td>${index + 1}</td>
                        <td>${pageCategoryName}</td>
                        <td>${pageName}<br>${mainPageName}</td>
                        <td class="pageOrder">${pageOrder}</td>
                        <td class="translation-status">${translationStatusHtml}</td>
                        <td>
                            <a href="/_y/s/s/sayfalar/AddPage.php?pageID=${pageID}" class="btn btn-sm btn-primary ${disabled}">Düzenle</a>
                            <button class="btn btn-sm btn-danger deletePageButton ${disabled}" data-pageid="${pageID}">Sil</button>
                            ${shouldShowTranslateButton(isMainLanguage, translationDetails || [], mainLanguageEquivalent, languageCount) ? `<button class="btn btn-sm btn-success translatePageButton ${disabled}" data-pageid="${pageID}" data-pagename="${pageName}">Çeviri</button>` : ''}
                        </td>
                        <td>
                            <a href="${pageSeoLink}" target="_blank"><i class="fa fa-link"></i></a>
                        </td>
                        <td>
                            <a class="tile-content ink-reaction dragDropPage" data-id="${pageID}" style="cursor:grab"><div class="tile-icon"><i class="fa fa-arrows"></i></div></a>
                        </td>
                    </tr>`;
                    tableBody.append(row);
                });
            }

            // Çeviri durumu HTML'ini oluştur
            function generateTranslationStatusHtml(translationDetails, pageID) {
                //console.log('generateTranslationStatusHtml called for pageID:', pageID);
                //console.log('translationDetails:', translationDetails);
                
                if (!translationDetails || translationDetails.length === 0) {
                    //console.log('No translation details found');
                    return '<span class="label label-warning">Çeviri Bilgisi Yok</span>';
                }

                let html = '<div class="translation-badges">';
                  translationDetails.forEach(detail => {
                    //console.log('Processing detail:', detail);
                    const { languageCode, languageName, translationStatus } = detail;
                    let badgeClass = 'label-default';
                    let icon = 'fa-question';
                    let title = 'Bilinmeyen';

                    //console.log('translationStatus value:', translationStatus, 'type:', typeof translationStatus);

                    switch(translationStatus) {
                        case 'pending': // Beklemede
                            badgeClass = 'label-warning';
                            icon = 'fa-clock-o';
                            title = 'Beklemede';
                            break;
                        case 'completed': // Tamamlandı
                            badgeClass = 'label-success';
                            icon = 'fa-check';
                            title = 'Tamamlandı';
                            break;
                        case 'failed': // Başarısız
                            badgeClass = 'label-danger';
                            icon = 'fa-times';
                            title = 'Başarısız';
                            break;
                        default:
                            if (!translationStatus) {
                                badgeClass = 'label-info';
                                icon = 'fa-plus';
                                title = 'Çevrilmemiş';
                            }
                    }

                    //console.log('Final badge class:', badgeClass, 'title:', title);

                    html += `<span class="label ${badgeClass}" title="${title} - ${languageName}" style="margin-right: 3px;">
                                <i class="fa ${icon}"></i> ${languageCode.toUpperCase()}
                             </span>`;
                });

                html += '</div>';
                //console.log('Generated HTML:', html);
                return html;
            }            // Çeviri butonuna tıklama olayı
            $(document).on('click', '.translatePageButton', function() {
                const pageID = $(this).data('pageid');
                const pageName = $(this).data('pagename');
                
                // Çeviri modalını aç
                showTranslationModal(pageID, pageName);
            });

            // Aktif dilleri al (ana dil hariç)
            async function getAvailableLanguagesForTranslation() {
                try {
                    const response = await $.ajax({
                        url: '/App/Controller/Admin/AdminLanguageController.php',
                        type: 'POST',
                        data: {
                            action: 'getLanguagesForTranslation'
                        }
                    });
                    
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        return result.languages;
                    } else {
                        throw new Error(result.message || 'Diller alınamadı');
                    }
                } catch (error) {
                    console.error('Diller yüklenirken hata:', error);
                    throw error;
                }
            }// Çeviri modalını göster
            function showTranslationModal(pageID, pageName) {
                // Aktif dilleri al (ana dil hariç)
                getAvailableLanguagesForTranslation().then(languages => {
                    // Dil seçimi checkbox'larını oluştur
                    let languageCheckboxes = '';
                    languages.forEach(lang => {
                        languageCheckboxes += `
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" class="translation-language" value="${lang.languageID}" checked>
                                <i class="input-helper"></i>
                                ${lang.languageName} (${lang.languageCode.toUpperCase()})
                            </label>
                        </div>`;
                    });

                    // Modal HTML'ini oluştur
                    const modalHtml = `
                    <div class="modal fade" id="translationModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="card">
                                <div class="card-head card-head-sm style-primary">
                                    <header class="modal-title">Sayfa Çevirisi</header>
                                    <div class="tools">
                                        <div class="btn-group">
                                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                                <i class="fa fa-close"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Sayfa:</strong> ${pageName}</p>
                                            <p>Bu sayfayı aşağıdaki dillere çevirmek istediğinizi seçiniz:</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Çevrilecek Diller:</label>
                                                <div id="languageSelection">
                                                    ${languageCheckboxes}
                                                </div>
                                            </div>
                                        </div> 
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Toplu İşlemler:</label>
                                                <div class="translation-action-buttons">
                                                    <button type="button" class="btn btn-sm btn-info" id="selectAllLanguages">
                                                        <i class="fa fa-check-square-o"></i> Tümünü Seç
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-warning" id="deselectAllLanguages">
                                                        <i class="fa fa-square-o"></i> Tümünü Kaldır
                                                    </button>
                                                </div>
                                                <p>
                                                    <small><i class="fa fa-info-circle"></i> Çeviri işlemi kuyruğa eklenir ve arka planda tamamlanır.</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="translationProgress" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                                                    <span class="sr-only">0% Tamamlandı</span>
                                                </div>
                                            </div>
                                            <p id="translationStatus">Çeviri başlatılıyor...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                                    <button type="button" class="btn btn-primary" id="startTranslationButton" data-pageid="${pageID}">
                                        <i class="fa fa-language"></i> Seçilen Dillere Çevir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    
                    // Eski modalı kaldır ve yenisini ekle
                    $('#translationModal').remove();
                    $('body').append(modalHtml);
                    $('#translationModal').modal('show');
                    
                    // Toplu seçim butonları için event handler'lar
                    $('#selectAllLanguages').click(function() {
                        $('.translation-language').prop('checked', true);
                    });
                    
                    $('#deselectAllLanguages').click(function() {
                        $('.translation-language').prop('checked', false);
                    });
                    
                }).catch(error => {
                    console.error('Diller yüklenirken hata oluştu:', error);
                    alert('Diller yüklenirken hata oluştu. Lütfen sayfayı yenileyin.');
                });
            }// Çeviri başlatma butonu
            
            $(document).on('click', '#startTranslationButton', function() {
                const pageID = $(this).data('pageid');
                startTranslation(pageID);
            });            // Çeviri işlemini başlat
            function startTranslation(pageID) {
                const progressDiv = $('#translationProgress');
                const statusText = $('#translationStatus');
                const startButton = $('#startTranslationButton');
                
                // Seçilen dilleri al
                const selectedLanguageIDs = [];
                $('.translation-language:checked').each(function() {
                    selectedLanguageIDs.push(parseInt($(this).val()));
                });
                
                // Hiç dil seçilmemişse uyarı ver
                if (selectedLanguageIDs.length === 0) {
                    alert('Lütfen en az bir dil seçiniz!');
                    return;
                }
                
                // UI güncelle
                progressDiv.show();
                startButton.prop('disabled', true);
                statusText.html(`<i class="fa fa-spinner fa-spin"></i> ${selectedLanguageIDs.length} dil için çeviri başlatılıyor...`);
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminPageController.php',
                    type: 'POST',
                    data: {
                        action: 'triggerTranslation',
                        pageID: pageID,
                        targetLanguageIDs: selectedLanguageIDs
                    },                    success: function(response) {
                        //console.log(response);
                        const result = JSON.parse(response);
                        
                        if (result.status === 'success') {
                            const summary = result.summary || {};
                            const totalCategories = summary.totalCategoryTranslations || 0;
                            const totalPages = summary.totalPageTranslations || 0;
                            
                            let message = `<i class="fa fa-check text-success"></i> Çeviri kuyruğa eklendi!`;
                            if (totalCategories > 0) {
                                message += `<br><small>${totalCategories} kategori ve ${totalPages} sayfa çevirisi işlenecek.</small>`;
                            } else {
                                message += `<br><small>${totalPages} sayfa çevirisi işlenecek.</small>`;
                            }
                            
                            statusText.html(message);
                            $('.progress-bar').css('width', '100%').attr('aria-valuenow', 100);
                            
                            // 3 saniye sonra modalı kapat ve sayfayı yenile
                            setTimeout(() => {
                                $('#translationModal').modal('hide');
                                loadPagesWithTranslationStatus(0); // Sayfaları yeniden yükle
                            }, 3000);
                        } else {
                            statusText.html(`<i class="fa fa-times text-danger"></i> Çeviri başarısız: ${result.message}`);
                            startButton.prop('disabled', false);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                        statusText.html(`<i class="fa fa-times text-danger"></i> Çeviri sırasında hata oluştu`);
                        startButton.prop('disabled', false);
                    }
                });
            }

            // ... existing code ...
		// ... existing code ...

            // Toplu silme işlemleri için JavaScript
            $('#selectAllPages').change(function() {
                $('.page-checkbox').prop('checked', $(this).prop('checked'));
                toggleBulkDeleteButton();
            });

            $(document).on('change', '.page-checkbox', function() {
                toggleBulkDeleteButton();
            });

            function toggleBulkDeleteButton() {
                if ($('.page-checkbox:checked').length > 0) {
                    $('#bulkDeletePagesButton').show();
                } else {
                    $('#bulkDeletePagesButton').hide();
                }
            }

            $(document).on('click', '#bulkDeletePagesButton', function() {
                const selectedPageIDs = [];
                $('.page-checkbox:checked').each(function() {
                    selectedPageIDs.push($(this).val());
                });

                if (selectedPageIDs.length > 0) {
                    $('#deletePageConfirmModal').modal('show');
                    $('#deletePageConfirmModal').data('pageids', selectedPageIDs);
                    $('#alertMessage').text(`${selectedPageIDs.length} adet sayfayı silmek istediğinize emin misiniz?`);
                } else {
                    $('#alertModal').modal('show');
                    $('#alertModal').find('#alertMessage').text('Lütfen silmek için en az bir sayfa seçiniz.');
                }
            });

            // Tekil silme butonu için mevcut click handler'ı güncelle
            $(document).off('click', '.deletePageButton').on('click', '.deletePageButton', function() {
                const pageID = $(this).data('pageid');
                $('#deletePageConfirmModal').modal('show');
                $('#deletePageConfirmModal').data('pageids', [pageID]); // Tekil ID'yi dizi olarak gönder
                $('#alertMessage').text('Sayfayı silmek istediğinize emin misiniz?');
            });

            // Onay butonuna tıklandığında
            $('#deletePageConfirmButton').off('click').on('click', function() {
                $('#deletePageConfirmModal').modal('hide');

                const pageIDs = $('#deletePageConfirmModal').data('pageids');
                const action = "deletePages"; // Yeni toplu silme action'ı

                $.ajax({
                    url: '/App/Controller/Admin/AdminPageController.php',
                    type: 'POST',
                    data: {
                        pageIDs: pageIDs,
                        action: action
                    },
                    success: function(response) {
                        const responseJson = JSON.parse(response);
                        if (responseJson.status === "success") {
                            $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message);
                            pageIDs.forEach(pageID => {
                                $("#tr-" + pageID).remove();
                            });
                            toggleBulkDeleteButton(); // Buton görünürlüğünü güncelle
                            $('#selectAllPages').prop('checked', false); // Tümünü seç kutucuğunu kaldır
                            setTimeout(function() {
                                $("#alertModal").modal("hide");
                            }, 1500);
                        } else {
                            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                            $('#alertModal').modal('show');
                            $('#alertModal').find('#alertMessage').text(responseJson.message);
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });

            // Sayfalar tablosunu render ettikten sonra checkbox durumunu güncelle
            const originalRenderPagesTable = renderPagesTable;
            renderPagesTable = function(pages) {
                originalRenderPagesTable(pages);
                toggleBulkDeleteButton(); // Tablo yüklendikten sonra butonu kontrol et
            };

		</script>
	</body>
</html>
