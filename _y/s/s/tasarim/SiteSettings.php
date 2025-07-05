<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 */


$buttonName = "Kaydet";


$languageId = $_GET["languageID"] ?? $_SESSION["languageID"] ?? 1;
$languageID = intval($languageId);

include_once MODEL . 'Admin/AdminLanguage.php';
$languageModel = new AdminLanguage($db);

$languages = $languageModel->getLanguages();

include_once MODEL . 'Admin/AdminSiteSettings.php';
$siteSettingsModel = new AdminSiteSettings($db,$languageID);
$siteSettings = $siteSettingsModel->getAllSettings();

?><!DOCTYPE html>
<html lang="en">
<head>
    <title>Site Ayarları Pozitif Eticaret</title>
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
                    <li class="active">Site Ayarları</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <form class="form" method="post" id="siteSettingsForm">
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <article class="margin-bottom-xxl">
                                <h4>Dil</h4><p></p>
                                <p>
                                    Ayarların Uygulanacağı Dili Seçin
                                </p>
                            </article>
                        </div>
                        <div class="col-lg-offset-1 col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <select id="languageID" name="languageID" class="form-control">
                                            <?php
                                            foreach($languages as $lang){
                                                $selected = $lang["languageID"] == $languageID ? "selected" : "";
                                                echo '<option value="'.$lang["languageID"].'" '.$selected.'>'.$lang["languageName"].'</option>';
                                            }
                                            $selected = "";
                                            ?>
                                        </select>
                                        <p class="help-block">GİRDİĞİNİZ BİLGİLERİN SEÇTİĞİNİZ DİLLE UYUMLU OLMASINA DİKKAT EDİN!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bölüm</th>
                                    <th>Özellik</th>
                                    <th>Görünüm</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($siteSettings as $setting): ?>
                                <tr>
                                    <td><?=$setting['id']?><input type="hidden" name="ids[]" value="<?=$setting['id']?>" ></td>
                                    <td>
                                        <input type="text" name="sections[<?=$setting['id']?>]" value="<?=$setting['section']?>" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="elements[<?=$setting['id']?>]" value="<?=$setting['element']?>" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <?php
                                        $isVisible = $setting['is_visible'];
                                        ?>
                                        <div class="radio radio-styled">
                                            <label class="radio-inline radio-styled">
                                                <input class="form-check-input" type="radio" name="is_visibles[<?=$setting['id']?>]" id="is_visibles_<?=$setting['id']?>" value="1" <?=$isVisible==1?'checked':''?>> <span>Görünür</span></label>


                                            <label class="radio-inline radio-styled"><input class="form-check-input" type="radio" name="is_visibles[<?=$setting['id']?>]" id="is_visibles_<?=$setting['id']?>" value="0" <?=$isVisible==0?'checked':''?>> <span>Gizli</span></label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>

                    </div>
                    <div class="card-actionbar">
                        <div class="card-actionbar-row">
                            <button id="addSiteSettingsButton" type="button" data-target="#addSiteSettingsModal" data-toggle="modal" class="btn btn-primary btn-default" style="float: left">Yeni Ekle</button>
                            <button id="saveSiteSettings" type="button" class="btn btn-primary btn-default"><?=$buttonName?></button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>

    <div class="modal fade" id="addSiteSettingsModal" tabindex="-1" role="dialog" aria-labelledby="addSiteSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="card">
                <div class="card-head card-head-sm style-danger">
                    <header class="modal-title" id="addSiteSettingsModalLabel">Site Ayarları Ekle</header>
                    <div class="tools">
                        <div class="btn-group">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-hidden="true">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <input type="text" name="section" id="section" class="form-control" placeholder="Bölüm">
                    </div>

                    <div class="form-group">
                        <input type="text" name="element" id="element" class="form-control" placeholder="Öğe">
                    </div>
                    <div class="form-group">
                        <label for="is_visible">Görünürlük</label>
                        <div class="radio radio-styled">

                            <label class="radio-inline radio-styled">
                                <input class="form-check-input" type="radio" name="is_visible" id="is_visible_1" value="1" checked><span>Görünür</span>
                            </label>


                            <label class="form-check-label">
                                <input class="form-check-input" type="radio" name="is_visible" id="is_visible_0" value="0"><span>Gizli</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="addSiteSettings">Ekle</button>
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

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>
    $("#siteSettingsphp").addClass("active");

    $(document).on('change',"#languageID", function(){
        var languageID = $(this).val();
        window.location.href = "/_y/s/s/tasarim/SiteSettings.php?languageID="+languageID;
    });

    $("#saveSiteSettings").click(function(e){
        e.preventDefault();
        var formData = $("#siteSettingsForm").serialize();
        formData += "&action=saveSiteSettings";
        //console.log(formData);
        $.ajax({
            url: '/App/Controller/Admin/AdminSiteSettingsController.php',
            method: 'POST',
            data: formData,
            success: function(response){
                console.log(response);
                var response = JSON.parse(response);
                var status = response.status;
                var message = response.message;
                $('#alertMessage').html(message);
                if(status==='success'){
                    $('#alertModal .card-head').removeClass('style-danger').addClass('style-success');
                    setTimeout(function(){
                        $('#alertModal').modal('hide');
                    },2000);
                }
                else{
                    $('#alertModal .card-head').removeClass('style-success').addClass('style-danger');
                }
                $('#alertModal').modal('show');
            }
        });
    });

    $("#addSiteSettings").click(function(){
        var section = $("#section").val();
        var element = $("#element").val();
        var is_visible = $("input[name='is_visible']:checked").val() === '1' ? 1 : 0;

        var formData = {
            action: "addSiteSettings",
            languageID: $("#languageID").val(),
            section: section,
            element: element,
            is_visible: is_visible
        };

        $.ajax({
            url: '/App/Controller/Admin/AdminSiteSettingsController.php',
            method: 'POST',
            data: formData,
            success: function(response){
                console.log(response);
                var response = JSON.parse(response);
                var status = response.status;
                var message = response.message;
                $('#alertMessage').html(message);
                if(status==='success'){
                    $('#alertModal .card-head').removeClass('style-danger').addClass('style-success');
                    setTimeout(function(){
                        window.location.reload();
                    },1500);
                }
                else{
                    $('#alertModal .card-head').removeClass('style-success').addClass('style-danger');
                }
                $('#alertModal').modal('show');
            }
        });
    });
</script>

</body>
</html>
