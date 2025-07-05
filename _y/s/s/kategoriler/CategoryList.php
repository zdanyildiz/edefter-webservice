<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<?php
/**
 * @var Config $config
 * @var Helper $helper
 * @var AdminDatabase $db
 * @var AdminCasper $adminCasper
 * @var AdminSession $adminSession
 */

include_once MODEL."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Kategori Liste Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">

    <style>
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
        .translateCategoryButton {
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
    <style>
        .translation-status {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
        }
        
        .translation-badge {
            font-size: 10px;
            padding: 2px 6px;
            margin: 1px;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-light.missing {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }
        
        .missing-translations {
            margin-top: 2px;
        }
        
        .filter-controls {
            margin-bottom: 15px;
        }
        
        .filter-controls select {
            margin-left: 10px;
        }
    </style>
</head>
<body class="menubar-hoverable header-fixed ">
<?php require_once(ROOT."_y/s/b/header.php");?>
<div id="base">
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Kategori Liste</li>
                </ol>
            </div>
            <div class="section-body contain-lg">                <div  class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-sm-6 form-group">
                                        <!-- dil listesi gelecek -->
                                        <select name="languageID" id="languageID" class="form-control">
                                            <option value="0">Dil Seçin</option>
                                            <?php foreach($languages as $language){ ?>
                                                <option value="<?php echo $language['languageID']; ?>"><?php echo $language['languageName']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <p class="help-block">KATEGORİ LİSTELEME İÇİN DİL SEÇİN!</p>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <select id="translationFilter" name="translationFilter" class="form-control">
                                            <option value="all">Tüm Kategoriler</option>
                                            <option value="untranslated">Çevrilmemiş</option>
                                            <option value="pending">Bekleyen Çeviri</option>
                                            <option value="completed">Çeviri Tamamlanmış</option>
                                            <option value="failed">Çeviri Başarısız</option>
                                        </select>
                                        <p class="help-block">Çeviri durumuna göre filtreleyebilirsiniz!</p>
                                    </div>
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
                            <div class="card-body">                                <div class="table-responsive">
                                    <table id="categoryList" class="table no-margin dataTable">                                        <thead>
                                        <tr>
                                            <th>Seç</th>
                                            <th class="sorting"><a href="#">#</a></th>
                                            <th class="sorting"><a href="#">Ad</a></th>
                                            <th class="sorting"><a href="#">Alt Kategori</a></th>
                                            <th><a href="#">Çeviri Durumu</a></th>
                                            <th>Düzenle</th>
                                            <th>Sil</th>
                                            <th>Gör</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>                                        <tfoot>
                                        <tr>
                                            <th>Seç</th>
                                            <th class="sorting"><a href="#">#</a></th>
                                            <th class="sorting"><a href="#">Ad</a></th>
                                            <th class="sorting"><a href="#">Alt Kategori</a></th>
                                            <th><a href="#">Çeviri Durumu</a></th>
                                            <th>Düzenle</th>
                                            <th>Sil</th>
                                            <th>Gör</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <!-- Çeviri Butonu -->
                                <div class="text-right" style="margin-top: 15px;">
                                    <button type="button" id="triggerCategoryTranslation" class="btn btn-info">
                                        <i class="fa fa-language"></i> Seçili Kategorileri Çevir
                                    </button>
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

<div class="modal fade" id="deleteCategoryConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryConfirmModalLabel" aria-hidden="true">
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
                <p id="alertMessage">Kategoriyi silmek istediğinize emin misiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-danger" id="deleteCategoryConfirmButton">Sil</button>
            </div>
        </div>
    </div>
</div>

<!-- Çeviri Modal -->
<div class="modal fade" id="categoryTranslationModal" tabindex="-1" role="dialog" aria-labelledby="categoryTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="card">
            <div class="card-head card-head-sm style-primary">
                <header class="modal-title" id="categoryTranslationModalLabel">Kategori Çevirisi</header>
                <div class="tools">
                    <div class="btn-group">
                        <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-close"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p>Seçili kategorileri hangi dillere çevirmek istiyorsunuz?</p>
                
                <div class="translation-action-buttons">
                    <button type="button" id="selectAllLanguages" class="btn btn-sm btn-default">
                        <i class="fa fa-check-square-o"></i> Tümünü Seç
                    </button>
                    <button type="button" id="deselectAllLanguages" class="btn btn-sm btn-default">
                        <i class="fa fa-square-o"></i> Tümünü Kaldır
                    </button>
                </div>
                
                <div id="translationLanguageOptions">
                    <!-- Dil seçenekleri buraya gelecek -->
                </div>
                
                <div id="translationProgress" style="display: none;">
                    <h5>Çeviri İlerlemesi</h5>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%">
                            <span class="sr-only">0% Complete</span>
                        </div>
                    </div>
                    <div id="translationStatus"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="startCategoryTranslation">
                    <i class="fa fa-language"></i> Çeviriyi Başlat
                </button>
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
    $("#categoryListphp").addClass("active");    
    
    function createCategoryRow(category) {
        console.log(category);
        var categoryID = category.categoryID;
        var categoryName = category.categoryName;
        var subCategoryCount = category.subCategoryCount;
        var categorySeoLink = category.categorySeoLink;
        var translationDetails = category.translationDetails || [];

        var btn = '-';
        if(subCategoryCount>0){
            btn = '<a href="javascript:void(0)" data-categoryID="'+categoryID+'" class="btn btn-primary-bright btn-sm showSubcategory">Alt Kategoriler ('+ subCategoryCount +')</a>';
        }

        // Çeviri durumu badge'lerini oluştur
        var translationStatusHtml = '';
        var hasActiveTranslations = false;
        
        if (translationDetails && Array.isArray(translationDetails) && translationDetails.length > 0) {
            translationStatusHtml += '<div class="translation-status">';
            
            translationDetails.forEach(function(translation) {
                let badgeClass = 'label-default';
                let icon = 'fa-question';
                let title = 'Bilinmeyen';

                switch (translation.translationStatus) {
                    case 'pending':
                        badgeClass = 'label-warning';
                        icon = 'fa-clock-o';
                        title = 'Beklemede';
                        break;
                    case 'completed':
                        badgeClass = 'label-success';
                        icon = 'fa-check';
                        title = 'Tamamlandı';
                        hasActiveTranslations = true; // En az bir çeviri tamamlandıysa
                        break;
                    case 'failed':
                        badgeClass = 'label-danger';
                        icon = 'fa-times';
                        title = 'Başarısız';
                        break;
                    default:
                        // Eğer çeviri durumu yoksa veya bilinmiyorsa, çevrilmemiş olarak kabul et
                        badgeClass = 'label-info';
                        icon = 'fa-plus';
                        title = 'Çevrilmemiş';
                }

                translationStatusHtml += `<span class="label ${badgeClass}" title="${title} - ${translation.languageName}" style="padding:5px;margin-right: 3px;">
                    <i class="fa ${icon}"></i> ${(translation.languageCode || '').toUpperCase()}
                </span>`;
            });

            translationStatusHtml += '</div>';
        } else {
            // Eğer translationDetails boşsa veya yoksa, çevrilmemiş olarak göster
            translationStatusHtml = '<span class="label label-info" style="padding:5px;margin-right: 3px;"><i class="fa fa-plus"></i> Çevrilmemiş</span>';
        }
        
        var tr = '<tr>';
        
        // Çevrilmiş kategoriler için checkbox'ı devre dışı bırak
        var checkboxDisabled = hasActiveTranslations ? ' disabled' : '';
        var checkboxTitle = hasActiveTranslations ? ' title="Bu kategori zaten çevrilmiş"' : '';
        
        tr += '<td><input type="checkbox" name="categoryID[]" value="' + categoryID + '"' + checkboxDisabled + checkboxTitle + '></td><td>' + categoryID + '</td>';
        tr += '<td>' + categoryName + '</td>';
        tr += '<td>' + btn + '</td>';
        tr += '<td>' + translationStatusHtml + '</td>';
        tr += '<td><a href="/_y/s/s/kategoriler/AddCategory.php?categoryID=' + categoryID + '" class="btn btn-primary btn-sm">Düzenle</a></td>';
        tr += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm deleteCategory" data-categoryID="' + categoryID + '">Sil</a></td>';
        tr += '<td><a href="' + categorySeoLink + '" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-link"></i></a></td>';
        tr += '</tr>';

        return tr;
    }

    $(document).ready(function() {
        
        $(document).on('change','#languageID',function () {
            var languageID = $(this).val();
            var action = "getCategoriesWithTranslationStatus";

            if (languageID > 0) {
                $.ajax({
                    url: '/App/Controller/Admin/AdminCategoryController.php',
                    type: 'POST',
                    data: {
                        languageID: languageID,
                        action: action
                    },
                    success: function (data) {

                        //console.log("category result: " + data);

                        var response = JSON.parse(data);
                        var status = response.status;
                        var message = response.message;
                        var categories = response.categories;
                        
                        //console.log("category categories: " + status);

                        if (status === "success") {

                            var tBody = $('#categoryList tbody');
                            tBody.empty();

                            $.each(categories, function (index, category) {
                                var categoryRow = createCategoryRow(category);
                                tBody.append(categoryRow);
                            });
                        }
                        else {
                            var modal = $('#alertModal');
                            modal.find('#alertMessage').text(message);
                            modal.modal('show');
                        }
                    }
                });
            }
        });

        <?php if(isset($_SESSION['languageID'])): ?>

        $('#languageID').val(<?php echo $_SESSION['languageID']; ?>).change();
        <?php else: ?>
        $('#languageID').val($('#languageID option[value!="0"]').first().val()).change();
        <?php endif; ?>


        $(document).on('keyup', '#q', function () {
            var q = $(this).val();

            if (q.length < 2) {

            }

            var languageID = $('#languageID').val();
            var action = "getCategoryWithInfoBySearch";

            $.ajax({
                url: '/App/Controller/Admin/AdminCategoryController.php',
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
                    var categories = response.categories;

                    if (status === "success") {

                        var tBody = $('#categoryList tbody');
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
                url: '/App/Controller/Admin/AdminCategoryController.php',
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

                        var tBody = $('#categoryList tbody');
                        tBody.empty();

                        var tr = '<tr>';
                        tr += '<td><a href="javascript:void(0)" class="btn btn-primary-bright btn-sm" id="backToMainCategory">Geri Dön</a></td>';
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

        $(document).on('click','.deleteCategory',function () {
            var categoryID = $(this).data('categoryid');
            $('#deleteCategoryConfirmModal').modal('show');
            $('#deleteCategoryConfirmButton').data('categoryid',categoryID);
        });
        
        $(document).on('click','#deleteCategoryConfirmButton',function () {
            var categoryID = $(this).data('categoryid');
            var languageID = $('#languageID').val();
            var action = "deleteCategory";

            $("#deleteCategoryConfirmModal").modal('hide');

            $.ajax({
                url: '/App/Controller/Admin/AdminCategoryController.php',
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

                    if (status === "success") {
                        $('#deleteCategoryConfirmModal').modal('hide');
                        $('#languageID').trigger('change');
                        var modal = $('#alertModal');
                        modal.find('#alertMessage').text(message);
                        modal.modal('show');
                        setTimeout(function () {
                            location.reload();
                        },1500);
                    }
                    else {
                        var modal = $('#alertModal');
                        modal.find('#alertMessage').text(message);
                        modal.modal('show');
                    }
                }
            });
        });        // Çeviri butonuna tıklandığında modal açma
        $(document).on('click', '#triggerCategoryTranslation', function() {
            // Dil listesini yükle
            $.ajax({
                url: '/App/Controller/Admin/AdminLanguageController.php',
                type: 'POST',
                data: {
                    action: 'getLanguagesForTranslation'
                },
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status === 'success') {
                        var languages = response.languages;
                        var languageCheckboxes = '';
                        
                        // Ana dili gösterme
                        var currentLanguageID = $('#languageID').val();
                        
                        languages.forEach(function(lang) {
                            if (lang.languageID != currentLanguageID) {
                                languageCheckboxes += '<div class="checkbox">';
                                languageCheckboxes += '<label>';
                                languageCheckboxes += '<input type="checkbox" name="targetLanguages[]" value="' + lang.languageID + '"> ';
                                languageCheckboxes += lang.languageName;
                                languageCheckboxes += '</label>';
                                languageCheckboxes += '</div>';
                            }
                        });
                        
                        $('#translationLanguageOptions').html(languageCheckboxes);
                        $('#categoryTranslationModal').modal('show');
                    }
                }
            });
        });

        // Tümünü seç/kaldır butonları
        $(document).on('click', '#selectAllLanguages', function() {
            $('#translationLanguageOptions input[type="checkbox"]').prop('checked', true);
        });

        $(document).on('click', '#deselectAllLanguages', function() {
            $('#translationLanguageOptions input[type="checkbox"]').prop('checked', false);
        });

        // Çeviri işlemini başlat
        $(document).on('click', '#startCategoryTranslation', function() {
            var checkedCategories = [];
            $('input[name="categoryID[]"]:checked').each(function() {
                checkedCategories.push($(this).val());
            });

            if (checkedCategories.length === 0) {
                $('#alertMessage').text('Lütfen çevirmek istediğiniz kategorileri seçin.');
                $('#alertModal').modal('show');
                return;
            }

            var targetLanguages = [];
            $('#translationLanguageOptions input[type="checkbox"]:checked').each(function() {
                targetLanguages.push($(this).val());
            });

            if (targetLanguages.length === 0) {
                $('#alertMessage').text('Lütfen çeviri yapılacak dilleri seçin.');
                $('#alertModal').modal('show');
                return;
            }

            var sourceLanguageID = $('#languageID').val();

            $.ajax({
                url: '/App/Controller/Admin/AdminCategoryController.php',
                type: 'POST',
                data: {
                    action: 'triggerCategoryTranslation',
                    categoryIDs: checkedCategories,
                    sourceLanguageID: sourceLanguageID,
                    targetLanguageIDs: targetLanguages
                },
                success: function(data) {
                    var response = JSON.parse(data);
                    $('#categoryTranslationModal').modal('hide');
                    $('#alertMessage').text(response.message);
                    $('#alertModal').modal('show');

                    if (response.status === 'success') {
                        setTimeout(function() {
                            $('#languageID').trigger('change');
                        }, 2000);
                    }
                }
            });
        });

        // Çeviri durumuna göre filtreleme
        $(document).on('change', '#translationStatusFilter', function() {
            var filterValue = $(this).val();
            var rows = $('#categoryList tbody tr');
            
            if (filterValue === '') {
                rows.show();
            } else {
                rows.each(function() {
                    var row = $(this);
                    var translationCell = row.find('td:eq(4)'); // Çeviri durumu sütunu
                    var badges = translationCell.find('.badge');
                    
                    var hasStatus = false;
                    badges.each(function() {
                        var badge = $(this);
                        if (filterValue === 'completed' && badge.hasClass('badge-success')) {
                            hasStatus = true;
                        } else if (filterValue === 'pending' && badge.hasClass('badge-warning')) {
                            hasStatus = true;
                        } else if (filterValue === 'missing' && badge.hasClass('missing')) {
                            hasStatus = true;
                        }
                    });
                    
                    if (hasStatus) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            }
        });

    });
</script>
</body>
</html>
