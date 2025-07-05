<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */

$menuLocation = $_GET["menuLocation"] ?? 0;
$menuLocation = intval($menuLocation);

$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;
$languageID = intval($languageId);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminMenu.php';
$menuModel = new AdminMenu($db);

$menuData = $menuModel->getMenuByLanguageAndLocation($languageID, $menuLocation) ?? [];

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Menü Yerleşimi Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">

    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/jquery-ui/jquery-ui-theme.css?1423393666" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/nestable/nestable.css?1423393667" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
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
                    <li class="active">Menü Düzenle</li>
                </ol>
            </div>
            <div class="section-body">
                <div class="card">
                    <div class="card-body" style="background: antiquewhite">
                        <h4>Menü İşlemleri</h4>
                        <div class="row">
                            <div class="col-md-6">
                                Kayıtlı kategori ve sayfalarınızı menülere eklemek için <i class="fa fa-list"></i> butonuna tıklayarak açılan pencereden arama yapın.<br>
                                Gelen sonuçları çift tıklayın ya da tutup sürükleyerek istediğiniz menü alanına ekleyin.
                            </div>
                            <div class="col-md-6 text-right">
                                <p>
                                    Menüleri <i class="fa fa-arrows"></i> butonuyla sürükleyerek istediğiniz konuma ve sıraya getirebilirsiniz.
                                </p>
                                <p>
                                    Yeni menü eklemek için <i class="fa fa-plus"></i> butonuna tıklayın.
                                </p>
                                <p>
                                    Menüleri düzenlemek için <i class="fa fa-pencil"></i> butonuna tıklayın.
                                </p>
                                <p>
                                    Bir menüyü silmek için <i class="fa fa-trash"></i> butonuna tıklayın.
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            Kategorilerin yanında bir seçim kutusu bulunur. Bu kutuyu işaretlediğinizde kategorinin alt kategorileri varsa siteniz menüsünde otomatik yüklenerek görüntülenir.
                        </div>
                    </div>
                </div>
                <form name="menuLocationForm" id="menuLocationForm" class="form form-validation form-validate" role="form" method="post">
                    <input type="hidden" name="menuLocation" id="menuLocation" value="<?=$menuLocation?>">
                    <a href="#offcanvas-searchContent"
                          id="searchContentButton2"
                          data-toggle="offcanvas"
                          data-backdrop="false"
                          class="hidden"
                          title="Kategori/Sayfa Seç"></a>
                    <div class="row" style="background: #fff">
                        <div class="col-sm-3">
                            <article class="margin-bottom-xxl">
                                <h4>Menü Dil</h4><p></p>
                                <p>
                                    Düzenleyeceğiniz Menü için Dil Seçin
                                </p>
                            </article>
                        </div>
                        <div class="col-sm-3">
                                <input type="hidden" name="menuLocation" value="<?=$menuLocation?>">
                                <div class="card-body">
                                    <div class="form-group">
                                        <select id="languageID" name="languageID" class="form-control">
                                            <?php
                                            foreach ($languages as $lang) {
                                                $selected = $lang['languageID'] == $languageID ? 'selected' : '';
                                                echo '<option value="'.$lang['languageID'].'" '.$selected.'>'.$lang['languageName'].'</option>';
                                            }
                                            ?>
                                        </select>
                                        <p class="help-block">Dil Seçin</p>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card">

                            <div id="menuArea" class="card-body">
                            <?php
                            $menuAreaCount = 8;
                            for ($i = 1; $i <= $menuAreaCount; $i++) {

                                $menuArea = [];

                                if(!empty($menuData)){

                                    //menusira == $i olanları alalım
                                    $menuArea = array_filter($menuData, function ($menu) use ($i) {
                                        return $menu["menusira"] == $i;
                                    });
                                }
                                ?>
                                <div class="col-md-3">
                                    <div class="card-head form-inverse">
                                        <div class="tools">
                                            <a
                                                    id="searchContentButton"
                                                    class="btn btn-sm"
                                                    href="#offcanvas-searchContent"
                                                    data-id="<?=$i?>"
                                                    data-toggle="offcanvas"
                                                    data-backdrop="false"
                                                    title="Kategori/Sayfa Seç">
                                                <i class="fa fa-list"></i>
                                            </a>
                                            <a
                                                    id="createMenuButton"
                                                    class="btn btn-sm"
                                                    href="#createMenuModal"
                                                    data-id="<?=$i?>"
                                                    data-toggle="modal"
                                                    data-placement="top"
                                                    data-original-title="Menü Oluştur"
                                                    data-target="#createMenuModal"
                                                    data-backdrop="true"
                                                    title="Yeni Menü Ekle">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="menuArea<?=$i?>">Menü Alanı <?=$i?></label>
                                        <div class="menuAreaContainer">
                                            <ul class="list" id="menuArea<?=$i?>" data-id="<?=$i?>" name="menuArea<?=$i?>">
                                                <?php
                                                if(!empty($menuArea)){
                                                    foreach ($menuArea as $menu) {
                                                        $contentUniqID = $menu["menubenzersizid"];
                                                        $contentOrjUniqID = $menu["orjbenzersizid"];
                                                        $menuName = $menu["menuad"];
                                                        $menuLink = $menu["menulink"];
                                                        $menuArea = $menu["menusira"];
                                                        $menuLayer = $menu["menukatman"];
                                                        $getSubCategory = $menu["altkategori"];
                                                        $menuParent = $menu["ustmenuid"];
                                                        ?>
                                                        <li
                                                                data-id="<?=$menu["menuid"]?>"
                                                                data-uniqid="<?=$menu["menubenzersizid"]?>"
                                                                data-orjuniqid="<?=$menu["orjbenzersizid"]?>"
                                                                data-link="<?=$menu["menulink"]?>"
                                                                data-type="custom">
                                                            <div class="col-md-2">
                                                                <a class="btn btn-flat ink-reaction btn-default handle"><i class="fa fa-arrows"></i></a>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span><?=$menu["menuad"]?></span>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <a class="btn btn-flat ink-reaction btn-default editMenu"><i class="fa fa-pencil"></i></a>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <a class="btn btn-flat ink-reaction btn-default menuDelete"><i class="fa fa-trash"></i></a>
                                                            </div>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                //her 4 tane de <div class="row"></div> açalım
                                if($i % 4 == 0){
                                    echo '<div class="row"></div>';
                                }
                            }
                            ?>
                            </div>
                            <div class="card-actionbar">
                                <div class="card-actionbar-row">
                                    <button type="button" id="addMenuArea" class="btn btn-primary-bright" style="float: left"><i class="fa fa-plus"></i> Yeni Menü Alanı Ekle</button>
                                    <button type="button" id="deleteMenu" class="btn btn-danger" style="float: left"><i class="fa fa-remove"></i> Menüyü Tamamen Boşalt</button>
                                    <button type="submit" class="btn btn-primary btn-default">Kaydet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>

    <div class="modal fade" id="addNewMenuModal" tabindex="-1" role="dialog" aria-labelledby="addNewMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-primary-bright">
                    <header class="modal-title" id="alertModalLabel">Menü Ekle</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Menü Ad, Menü Link gireceğimiz form alanları ekleyelim-->
                    <div class="form-group">
                        <label for="menuName">Menü Adı</label>
                        <input type="text" class="form-control" id="menuName" name="menuName" placeholder="Menü Adı">
                    </div>
                    <div class="form-group">
                        <label for="menuLink">Menü Link</label>
                        <input type="text" class="form-control" id="menuLink" name="menuLink" placeholder="Menü Link">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="addNewMenuButton">Ekle</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteMenuConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteMenuConfirmModalLabel" aria-hidden="true">
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
                    <p id="message">Menüyü tamamen silmek istediğinize emin misiniz?<br>Bu durumda menü içerikleri tamamen boşalacaktır.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-danger" id="deleteMenuConfirmButton">Sil</button>
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

<style>
    #searchContentResult li,.menuAreaContainer li{
        min-height: 30px; line-height: 30px;display: inline-flex;width: 100%;
    }
    .menuAreaContainer li{
        background-color: #f9f9f9; min-height: 30px; line-height: 30px; border-bottom: solid 1px #999;
    }
    .menuAreaContainer{
        background: aliceblue;border: 1px solid #ccc;
    }
    .menuAreaContainer ul{
        min-height: 150px;
    }
</style>

<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="/_y/assets/js/libs/nestable/jquery.nestable.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<?php
if($menuLocation==0)$pageID="menusTopphp";
if($menuLocation==1)$pageID="menusMainphp";
if($menuLocation==2)$pageID="menusLeftphp";
if($menuLocation==3)$pageID="menusRightphp";
if($menuLocation==4)$pageID="menusBottomphp";
?>
<script>

    $("#<?=$pageID?>").addClass("active");

    function menuAreaContainerAutoHeight(){
        var maxHeight = 150;
        $(".menuAreaContainer ul").each(function () {
            var height = $(this).height();
            if (height > maxHeight) {
                maxHeight = height;
            }
            console.log(maxHeight+"-"+height);
        });
        $(".menuAreaContainer ul").css("min-height",maxHeight+"px")
    }

    function createContentListItem(contentType, contentID, contentUniqID, seoTitle, seoLink, targetMenu, subCategory, contentOriginalTitle="") {
        console.log(contentType, contentID, contentUniqID, seoTitle, seoLink, targetMenu, subCategory);
        let contentList = '<li ';
        contentList += 'data-id="' + contentID + '" ';
        contentList += 'data-uniqid="'+ targetMenu + '-' + contentUniqID + '" ';
        contentList += 'data-orjuniqid="' + contentUniqID + '" ';
        contentList += 'data-link="' + seoLink + '"';
        contentList += 'data-type="' + contentType + '"> ';
        contentList += '<div class="col-md-2">';
        contentList += '<a class="btn btn-flat ink-reaction btn-default handle"><i class="fa fa-arrows"></i></a>';
        contentList += '</div>';
        if(contentOriginalTitle !== ""){
            contentOriginalTitle = '<br><label class="text-sm text-default-light">['+ contentOriginalTitle +']</label>';
        }
        if (subCategory>0) {
            contentList += "<div class='col-md-6'><div class='checkbox checkbox-styled tile-text'><label><input type='checkbox'><span>"+ seoTitle +"</span></label></div></div>";
        } else {
            contentList += '<div class="col-md-6"><span>'+ seoTitle +  contentOriginalTitle +'</span> </div>';
        }
        contentList += '<div class="col-md-2">';
        contentList += '<a class="btn btn-flat ink-reaction btn-default editMenu"><i class="fa fa-pencil"></i></a>';
        contentList += '</div>';
        contentList += '<div class="col-md-2">';
        contentList += '<a class="btn btn-flat ink-reaction btn-default menuDelete"><i class="fa fa-trash"></i></a>';
        contentList += '</div>';
        contentList += '</li>';
        return contentList;
    }

    $(document).ready(function() {

        $('#searchContentResult ul').sortable({
            connectWith: '.menuAreaContainer ul',
            handle: '.handle',
            placeholder: 'placeholder',
            forcePlaceholderSize: true,
            revert: 100,
            tolerance: 'pointer',
            cursorAt: { top: 10, left: 10 },
            start: function(event, ui) {
                ui.placeholder.height(ui.helper.outerHeight());
                ui.helper.css('min-height', '30px');
                ui.placeholder.css({
                    visibility: 'visible',
                    background: '#f0f0f0',
                    border: '2px dashed #ddd'
                });
            },
            stop: function(event, ui) {
                //style ilgili her şeyi silelim
                ui.item.removeAttr('style');
                menuAreaContainerAutoHeight();
            },
            change: function(event, ui) {
                ui.placeholder.css({
                    visibility: 'visible',
                    background: '#f0f0f0',
                    border: '2px dashed #ddd'
                });
            },
            receive: function(event, ui) {
                // Alıcı ul içine bırakıldığında çalışır
                var inMenuID = $(this).data("id");
                var orjuniqid = inMenuID + "-" + ui.item.data("orjuniqid");
                ui.item.attr("data-uniqid", orjuniqid);
            },
            remove: function(event, ui) {
                // Öğenin eski listeden çıkarıldığında çalışır
                var $closestUl = ui.item.closest(".menuAreaContainer ul");
                if ($closestUl.length === 0) {
                    // Öğeyi eski yerine geri döndür
                    $(this).sortable('cancel');
                }
            }
        }).disableSelection();

        $('.menuAreaContainer ul').sortable({
            connectWith: '.menuAreaContainer ul',
            handle: '.handle',
            placeholder: 'placeholder',
            forcePlaceholderSize: true,
            revert: 100,
            tolerance: 'pointer',
            cursorAt: { top: 10, left: 10 },
            start: function(event, ui) {
                ui.placeholder.height(ui.helper.outerHeight());
                ui.helper.css('min-height', '30px');
                ui.placeholder.css({
                    visibility: 'visible',
                    background: '#f0f0f0',
                    border: '2px dashed #ddd'
                });
            },
            stop: function(event, ui) {
                ui.item.removeAttr('style');
                menuAreaContainerAutoHeight();
            },
            change: function(event, ui) {
                ui.placeholder.css({
                    visibility: 'visible',
                    background: '#f0f0f0',
                    border: '2px dashed #ddd'
                });
            },
            receive: function(event, ui) {
                var inMenuID = $(this).attr("data-id");
                var orjuniqid = inMenuID + "-" + ui.item.data("orjuniqid");
                ui.item.attr("data-uniqid", orjuniqid);
            },
            remove: function(event, ui) {
                var $closestUl = ui.item.closest(".menuAreaContainer ul");
                if ($closestUl.length === 0) {
                    $(this).sortable('cancel');
                }
            }
        }).disableSelection();

        $(document).on("dblclick", "#searchContentResult li", function () {
            var targetMenu = $("#targetMenu").val();
            //li'yi olduğu gibi targetMenüye copyalayalım
            var li = $(this).clone();
            $("#menuArea" + targetMenu).append(li);
            menuAreaContainerAutoHeight();
        });

        //.menuAreaContainer ul altındaki .menuDelete tıklandığında kapsayan li silelim
        $(document).on("click", ".menuAreaContainer ul .menuDelete", function () {
            $(this).closest("li").remove();
        });

        $(document).on("click","#editMenuButton",function () {
            var targetMenu = $(this).data("id");
            $("#targetMenu").val(targetMenu);
        });

        $(document).on("click","#searchContentButton",function () {
            var targetMenu = $(this).data("id");
            $("#targetMenu").val(targetMenu);
            if(targetMenu > 8){
                console.log(targetMenu);
                $("#searchContentButton2").click();
            }

        });

        $(document).on("click","#createMenuButton",function () {
            var targetMenu = $(this).data("id");
            $("#targetMenu").val(targetMenu);
        });

        $(document).on("keyup","#searchContent",function () {
            var searchText = $(this).val();
            var languageID = $("#languageID").val();
            var targetMenu = $("#targetMenu").val();
            $.ajax({
                url: "/App/Controller/Admin/AdminContentController.php",
                type: "GET",
                data: {
                    action: "searchContent",
                    searchText: searchText,
                    languageID: languageID
                },
                success: function (response) {
                    console.log(response);
                    data = JSON.parse(response);
                    if (data.status === "success") {
                        var contentResult = data.contentData;
                        var contentList = "";
                        for (var i = 0; i < contentResult.length; i++) {
                            let contentType = contentResult[i].contentType;
                            let contentID = contentResult[i].contentID;
                            let contentUniqID = contentResult[i].contentUniqID;
                            let contentTitle = contentResult[i].contentTitle;
                            let seoTitle = contentResult[i].seoTitle;
                            let seoLink = contentResult[i].seoLink;
                            let subCategory = contentResult[i].subCategory;
                            let contentOriginalTitle = contentResult[i].contentOriginalTitle;

                            contentList += createContentListItem(contentType, contentID, contentUniqID, contentTitle, seoLink, targetMenu, subCategory, contentOriginalTitle);
                        }
                        $("#searchContentResult ul").html(contentList);
                    }
                }
            });
        });

        //#searchType value category ise kategori listeleyelim, page ise sayfa listeleyelim. listeleme işlemini dile göre yapalım
        $(document).on("change","#searchType",function () {
            var targetMenu = $("#targetMenu").val();
            var searchType = $(this).val();
            var languageID = $("#languageID").val();
            var searchText = $("#searchContent").val();
            $.ajax({
                url: "/App/Controller/Admin/AdminContentController.php",
                type: "GET",
                data: {
                    action: "searchContentBySearchType",
                    searchText: searchText,
                    languageID: languageID,
                    searchType: searchType
                },
                success: function (response) {
                    console.log(response);
                    data = JSON.parse(response);
                    if (data.status == "success") {
                        var contentResult = data.contentData;
                        var contentList = "";
                        for (var i = 0; i < contentResult.length; i++) {
                            let contentType = contentResult[i].contentType;
                            let contentID = contentResult[i].contentID;
                            let contentUniqID = contentResult[i].contentUniqID;
                            let contentTitle = contentResult[i].contentTitle;
                            let seoTitle = contentResult[i].seoTitle;
                            let seoLink = contentResult[i].seoLink;
                            let subCategory = contentResult[i].subCategory;
                            let contentOriginalTitle = contentResult[i].contentOriginalTitle;

                            contentList += createContentListItem(contentType, contentID, contentUniqID, contentTitle, seoLink, targetMenu, subCategory,contentOriginalTitle);
                        }
                        $("#searchContentResult ul").html(contentList);
                    }
                }
            });
        });

        //#createMenuButton tıklandığında data-id alalım, addNewMenuModal açılsın
        $(document).on("click","#createMenuButton",function () {
            var targetMenu = $(this).data("id");
            $("#targetMenu").val(targetMenu);
            $("#addNewMenuModal").modal("show");
        });
        //addNewMenuButton tıklandığında data-id ve menuName,menuLink alalım ve menü alanına ekleyelim
        $(document).on("click","#addNewMenuButton",function () {
            var targetMenu = $("#targetMenu").val();
            var menuName = $("#menuName").val();
            var menuLink = $("#menuLink").val();
            var seoLink = menuLink;
            //ad ve link boş olamaz
            if (menuName == "" || menuLink == "") {
                $("#alertMessage").text("Menü Adı ve Linki Boş Bırakılamaz");
                $("#alertModal").modal("show");
                return;
            }
            var orjUniqID = $(this).data("orjuniqid") ?? Math.floor(Math.random() * 1000000);
            var contentType = $(this).data("type") ?? "custom";
            var contentID = $(this).data("id") ?? 0;
            var contentUniqID = $(this).data("uniqid") ?? targetMenu + "-" + orjUniqID;;
            var seoTitle = menuName;
            var subCategory = 0;

            if(contentType !== "custom"){
                seoLink = $(this).data("link") ?? menuLink;
            }

            var li = createContentListItem(contentType, contentID, contentUniqID, seoTitle, seoLink, targetMenu, subCategory,"");
            //append yapmadan li data-("orjuniqid) ile contentUniqID eşleşen varsa önce onu silelim
            $("#menuArea" + targetMenu + " li").each(function () {
                if ($(this).data("orjuniqid") == contentUniqID) {
                    $(this).remove();
                }
            });
            $("#menuArea" + targetMenu).append(li);
            menuAreaContainerAutoHeight();
            $("#addNewMenuModal").modal("hide");
            //modalın inputlaını temizleyelim
            $("#menuName").val("");
            $("#menuLink").val("");
            $("#menuLink").removeAttr("disabled");
            //butondaki tüm data özelliklerini silelim
            $("#addNewMenuButton").removeData();
        });

        //düzenlemeye tıklandığında li'nin tüm data özelliklerini addNewMenuButton'a alalım,isim ve link inputlarını dolduralım
        $(document).on("click",".editMenu",function () {
            var li = $(this).closest("li");
            var orjUniqID = li.data("orjuniqid");
            var contentType = li.data("type");
            var contentID = li.data("id");
            var contentUniqID = li.data("orjuniqid");
            var seoTitle = li.find("span").text();
            var seoLink = li.data("link");

            var targetMenu = li.closest("ul").data("id");

            $("#targetMenu").val(targetMenu);

            $("#addNewMenuButton").data("orjuniqid",orjUniqID);
            $("#addNewMenuButton").data("type",contentType);
            $("#addNewMenuButton").data("id",contentID);
            $("#addNewMenuButton").data("uniqid",contentUniqID);
            $("#addNewMenuButton").data("link",seoLink);

            $("#menuName").val(seoTitle);
            $("#menuLink").val(seoLink);

            //contentType custom değilse link inputunu disable yapalım
            if(contentType != "custom"){
                $("#menuLink").attr("disabled","disabled");
            }else{
                $("#menuLink").removeAttr("disabled");
            }

            $("#addNewMenuModal").modal("show");
        });

        //addMenuArea tıklandığında yeni bir menü alanı ekleyelim
        $(document).on("click","#addMenuArea",function () {
            var menuAreaCount = $(".menuAreaContainer").length;
            //alan sayısı 4 ve katı ise <div class="row"></div> açalım
            if(menuAreaCount % 4 == 0){
                $("#menuArea").append('<div class="row"></div>');
            }
            var newMenuArea = menuAreaCount + 1;
            var newMenuAreaHtml = '<div class="col-md-3">';
            newMenuAreaHtml += '<div class="card-head form-inverse">';
            newMenuAreaHtml += '<div class="tools">';
            newMenuAreaHtml += '<a id="searchContentButton" class="btn btn-sm" href="#offcanvas-searchContent" data-id="'+newMenuArea+'" data-toggle="offcanvas" data-backdrop="false" title="Kategori/Sayfa Seç"><i class="fa fa-list"></i></a>';
            newMenuAreaHtml += '<a id="createMenuButton" class="btn btn-sm" href="#createMenuModal" data-id="'+newMenuArea+'" data-toggle="modal" data-placement="top" data-original-title="Menü Oluştur" data-target="#createMenuModal" data-backdrop="true" title="Yeni Menü Ekle"><i class="fa fa-plus"></i></a>';
            newMenuAreaHtml += '</div>';
            newMenuAreaHtml += '</div>';
            newMenuAreaHtml += '<div class="form-group">';
            newMenuAreaHtml += '<label for="menuArea'+newMenuArea+'">Menü Alanı '+newMenuArea+'</label>';
            newMenuAreaHtml += '<div class="menuAreaContainer">';
            newMenuAreaHtml += '<ul class="list" id="menuArea'+newMenuArea+'" data-id="'+newMenuArea+'" name="menuArea'+newMenuArea+'"></ul>';
            newMenuAreaHtml += '</div>';
            newMenuAreaHtml += '</div>';
            newMenuAreaHtml += '</div>';
            $("#menuArea").append(newMenuAreaHtml);

        });

        //menuLocationForm submit olduğunda menüleri kaydedelim
        $(document).on("submit","#menuLocationForm",function (e) {
            e.preventDefault();
            var menuLocation = $("#menuLocation").val();
            var languageID = $("#languageID").val();
            var menuData = [];
            $(".menuAreaContainer ul").each(function () {
                var menuArea = $(this).data("id");
                var i = 0;
                $(this).find("li").each(function () {
                    var menu = {};
                    menu.menuLocation = menuLocation;
                    menu.languageID = languageID;
                    menu.menuArea = menuArea;

                    menu.contentUniqID = $(this).data("uniqid");
                    menu.contentOrjUniqID = $(this).data("orjuniqid");
                    menu.menuName = $(this).find("span").text();
                    //menu.menuName içeriğinden kçşeli parantez içeriğini kalfıralım [...]
                    menu.menuName = menu.menuName.replace(/\[(.*?)\]/g, '');
                    menu.menuLink = $(this).data("link");
                    menu.menuType = $(this).data("type");

                    if(i == 0) {
                        menu.menuParent = 0;
                        menu.menuLayer = 0;
                    } else {
                        menu.menuParent = 1;
                        menu.menuLayer = 1;
                    }

                    menu.getSubCategory = 0;

                    if($(this).find("input[type='checkbox']").is(":checked")){
                        menu.getSubCategory = 1;
                    }

                    menuData.push(menu);
                    i++;
                });
            });
            //console.log(menuData);
            $.ajax({
                url: "/App/Controller/Admin/AdminMenuController.php",
                type: "POST",
                data: {
                    action: "saveMenu",
                    languageID: languageID,
                    menuLocation: menuLocation,
                    menuData: menuData
                },
                success: function (response) {
                    console.log(response);
                    jsonResponse = JSON.parse(response);
                    if(jsonResponse.status === "error"){
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        $("#alertMessage").html(jsonResponse.message);
                        $("#alertModal").modal("show");
                    }
                    else{
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        $("#alertMessage").html(jsonResponse.message);
                        $("#alertModal").modal("show");
                        //2 saniye sonra modalı kapatalım
                        setTimeout(function(){
                            $("#alertModal").modal("hide");
                        },2000);
                    }
                }
            });
        });

        //#languageID değiştiğinde menuLocation ile sayfayı languageID olarak yönlendir
        $(document).on("change","#languageID",function () {
            var languageID = $(this).val();
            var menuLocation = $("#menuLocation").val();
            window.location.href = "/_y/s/s/menuler/Menus.php?menuLocation="+menuLocation+"&languageID="+languageID;
        });

        $(document).on("click","#deleteMenu",function(){
            $("#deleteMenuConfirmModal").modal("show");
        });

        $(document).on("click","#deleteMenuConfirmButton",function(){
            let languageID = $("#languageID").val();
            let menuLocation = $("#menuLocation").val();
            let action = "deleteMenu";

            $.ajax({
                url: "/App/Controller/Admin/AdminMenuController.php",
                type: "POST",
                data: {
                    action: action,
                    languageID: languageID,
                    menuLocation: menuLocation
                },
                success: function (response) {
                    jsonResponse = JSON.parse(response);
                    if(jsonResponse.status === "error"){
                        $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                        $("#alertMessage").html(jsonResponse.message);
                        $("#alertModal").modal("show");
                    }
                    else{
                        $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                        $("#alertMessage").html(jsonResponse.message);
                        $("#alertModal").modal("show");
                        //1 saniye sonra modalı kapatalım
                        setTimeout(function(){
                            //sayfayı yenileyelim
                            window.location.href = "/_y/s/s/menuler/Menus.php?menuLocation="+menuLocation;
                        },2000);
                    }
                }
            })
        });
    });

</script>
<!-- END JAVASCRIPT -->
</body>
</html>
