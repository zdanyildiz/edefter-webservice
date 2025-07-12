<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var int $adminAuth
 */

$languageID = $_GET['languageID'] ?? $_SESSION['languageID'] ?? 1;
$languageID = intval($languageID);

include_once MODEL ."Admin/AdminLanguage.php";
$languageModel = new AdminLanguage($db);
$languages = $languageModel->getLanguages();

// Platform Tracking Manager'ı yükle
include_once ROOT . '/App/Helpers/PlatformTrackingManager.php';
$trackingManager = new PlatformTrackingManager($db, $config);

// Mevcut platform konfigürasyonlarını getir
$platforms = PlatformTrackingManager::PLATFORMS;
$activePlatforms = [];

foreach ($platforms as $platformKey => $platformInfo) {
    $platformConfig = $trackingManager->getPlatformConfig($platformKey, $languageID);
    $activePlatforms[$platformKey] = [
        'info' => $platformInfo,
        'config' => $platformConfig ? json_decode($platformConfig['config'], true) : [],
        'status' => $platformConfig ? $platformConfig['status'] : 0
    ];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Platform Tracking Yönetimi - Pozitif Eticaret</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet' type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194" />
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286" />
    
    <style>
        .platform-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .platform-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .platform-header {
            background: #f5f5f5;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 8px 8px 0 0;
        }
        .platform-body {
            padding: 20px;
        }
        .platform-status {
            float: right;
            margin-top: -5px;
        }
        .field-group {
            margin-bottom: 15px;
        }
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .preview-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="menubar-hoverable header-fixed">
    <?php require_once(ROOT."/_y/s/b/header.php");?>
    
    <div id="base">
        <div id="content">
            <section>
                <div class="section-header">
                    <ol class="breadcrumb">
                        <li><a href="/_y/">Ana Sayfa</a></li>
                        <li><a href="#">Eklentiler</a></li>
                        <li class="active">Platform Tracking</li>
                    </ol>
                </div>
                
                <div class="section-body contain-lg">
                    <!-- Dil Seçimi -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="languageID">Dil Seçimi</label>
                                                <select id="languageID" name="languageID" class="form-control">
                                                    <?php foreach($languages as $lang): ?>
                                                        <option value="<?=$lang['languageID']?>" <?=$lang['languageID'] == $languageID ? 'selected' : ''?>>
                                                            <?=$lang['languageName']?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="help-block">Girdiğiniz bilgilerin seçtiğiniz dille uyumlu olmasına dikkat edin!</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div style="margin-top: 25px;">
                                                <button id="previewAll" class="btn btn-info">
                                                    <i class="fa fa-eye"></i> Tüm Kodları Önizle
                                                </button>
                                                <button id="saveAll" class="btn btn-success">
                                                    <i class="fa fa-save"></i> Tümünü Kaydet
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Platform Kartları -->
                    <div class="row">
                        <?php foreach ($activePlatforms as $platformKey => $platform): ?>
                        <div class="col-md-6">
                            <div class="platform-card card" data-platform="<?=$platformKey?>">
                                <div class="platform-header">
                                    <h4 style="margin: 0; display: inline-block;">
                                        <?=$platform['info']['name']?> 
                                        <small>(<?=$platform['info']['code']?>)</small>
                                    </h4>
                                    <div class="platform-status">
                                        <label class="switch">
                                            <input type="checkbox" class="platform-toggle" 
                                                   data-platform="<?=$platformKey?>" 
                                                   <?=$platform['status'] ? 'checked' : ''?>>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="platform-body">
                                    <?php foreach ($platform['info']['fields'] as $field): ?>
                                    <div class="field-group">
                                        <label for="<?=$platformKey?>_<?=$field?>">
                                            <?=ucfirst(str_replace('_', ' ', $field))?>
                                        </label>
                                        <input type="text" 
                                               class="form-control platform-field" 
                                               id="<?=$platformKey?>_<?=$field?>" 
                                               name="<?=$field?>"
                                               data-platform="<?=$platformKey?>"
                                               data-field="<?=$field?>"
                                               value="<?=$platform['config'][$field] ?? ''?>" 
                                               placeholder="<?=$field?> değerini girin">
                                        <div class="help-text">
                                            <?php
                                            switch($field) {
                                                case 'tracking_id':
                                                    echo 'Google Analytics Tracking ID (örn: GA-XXXXX-X)';
                                                    break;
                                                case 'measurement_id':
                                                    echo 'Google Analytics 4 Measurement ID (örn: G-XXXXXXXXXX)';
                                                    break;
                                                case 'pixel_id':
                                                    echo 'Facebook/TikTok Pixel ID (örn: 123456789)';
                                                    break;
                                                case 'conversion_id':
                                                    echo 'Google Ads Conversion ID (örn: AW-123456789)';
                                                    break;
                                                case 'conversion_label':
                                                    echo 'Google Ads Conversion Label';
                                                    break;
                                                case 'partner_id':
                                                    echo 'LinkedIn Partner ID';
                                                    break;
                                                default:
                                                    echo ucfirst(str_replace('_', ' ', $field)) . ' değeri';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <div style="margin-top: 15px;">
                                        <button class="btn btn-sm btn-info preview-platform" 
                                                data-platform="<?=$platformKey?>">
                                            <i class="fa fa-eye"></i> Önizle
                                        </button>
                                        <button class="btn btn-sm btn-success save-platform" 
                                                data-platform="<?=$platformKey?>">
                                            <i class="fa fa-save"></i> Kaydet
                                        </button>
                                    </div>
                                    
                                    <div class="preview-box" id="preview_<?=$platformKey?>" style="display: none;">
                                        <!-- Önizleme alanı -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
        
        <?php require_once(ROOT."/_y/s/b/menu.php");?>
        
        <!-- Alert Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="card">
                    <div class="card-head card-head-sm style-success">
                        <header class="modal-title">Bilgi</header>
                        <div class="tools">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal">
                                <i class="fa fa-close"></i>
                            </a>
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
        
        <!-- Preview Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="card">
                    <div class="card-head">
                        <header class="modal-title">Kod Önizleme</header>
                        <div class="tools">
                            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal">
                                <i class="fa fa-close"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="previewContent" style="font-family: monospace; background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;">
                            <!-- Önizleme içeriği -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>
    <script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>
    
    <!-- Core Admin JavaScript Files -->
    <script src="/_y/assets/js/core/source/App.js"></script>
    <script src="/_y/assets/js/core/source/AppNavigation.js"></script>
    <script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
    <script src="/_y/assets/js/core/source/AppCard.js"></script>
    <script src="/_y/assets/js/core/source/AppForm.js"></script>
    <script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
    <script src="/_y/assets/js/core/source/AppVendor.js"></script>

    <style>
        /* Toggle Switch CSS */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #2196F3;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>

    <script>
        $(document).ready(function() {
            // Dil değiştirme
            $("#languageID").change(function(){
                var languageID = $(this).val();
                window.location.href = window.location.pathname + "?languageID=" + languageID;
            });
            
            // Platform toggle
            $(".platform-toggle").change(function(){
                var platform = $(this).data('platform');
                var status = $(this).is(':checked') ? 1 : 0;
                
                // Platform kartındaki alanları etkinleştir/devre dışı bırak
                var card = $(this).closest('.platform-card');
                card.find('.platform-field').prop('disabled', !status);
                card.find('.preview-platform, .save-platform').prop('disabled', !status);
            });
            
            // Platform önizleme
            $(".preview-platform").click(function(){
                var platform = $(this).data('platform');
                previewPlatform(platform);
            });
            
            // Platform kaydetme
            $(".save-platform").click(function(){
                var platform = $(this).data('platform');
                savePlatform(platform);
            });
            
            // Tümünü önizle
            $("#previewAll").click(function(){
                previewAllPlatforms();
            });
            
            // Tümünü kaydet
            $("#saveAll").click(function(){
                saveAllPlatforms();
            });
            
            // Platform önizleme fonksiyonu
            function previewPlatform(platform) {
                var config = getPlatformConfig(platform);
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminPluginsController.php',
                    type: 'POST',
                    data: {
                        action: 'previewPlatformTracking',
                        platform: platform,
                        config: config,
                        languageID: $("#languageID").val()
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            $("#preview_" + platform).html('<pre>' + data.code + '</pre>').show();
                        } else {
                            showAlert('error', 'Önizleme oluşturulamadı: ' + data.message);
                        }
                    }
                });
            }
            
            // Platform kaydetme fonksiyonu
            function savePlatform(platform) {
                var config = getPlatformConfig(platform);
                var status = $(".platform-toggle[data-platform='" + platform + "']").is(':checked') ? 1 : 0;
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminPluginsController.php',
                    type: 'POST',
                    data: {
                        action: 'savePlatformTracking',
                        platform: platform,
                        config: config,
                        status: status,
                        languageID: $("#languageID").val()
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            showAlert('success', platform + ' platform ayarları kaydedildi');
                        } else {
                            showAlert('error', 'Kaydetme hatası: ' + data.message);
                        }
                    }
                });
            }
            
            // Tüm platformları önizle
            function previewAllPlatforms() {
                $.ajax({
                    url: '/App/Controller/Admin/AdminPluginsController.php',
                    type: 'POST',
                    data: {
                        action: 'previewAllPlatforms',
                        languageID: $("#languageID").val()
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            $("#previewContent").html('<pre>' + data.code + '</pre>');
                            $("#previewModal").modal('show');
                        } else {
                            showAlert('error', 'Önizleme oluşturulamadı: ' + data.message);
                        }
                    }
                });
            }
            
            // Tüm platformları kaydet
            function saveAllPlatforms() {
                var allConfigs = {};
                
                $(".platform-card").each(function(){
                    var platform = $(this).data('platform');
                    var config = getPlatformConfig(platform);
                    var status = $(".platform-toggle[data-platform='" + platform + "']").is(':checked') ? 1 : 0;
                    
                    allConfigs[platform] = {
                        config: config,
                        status: status
                    };
                });
                
                $.ajax({
                    url: '/App/Controller/Admin/AdminPluginsController.php',
                    type: 'POST',
                    data: {
                        action: 'saveAllPlatforms',
                        platforms: JSON.stringify(allConfigs),
                        languageID: $("#languageID").val()
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            showAlert('success', 'Tüm platform ayarları kaydedildi');
                        } else {
                            showAlert('error', 'Kaydetme hatası: ' + data.message);
                        }
                    }
                });
            }
            
            // Platform konfigürasyonunu al
            function getPlatformConfig(platform) {
                var config = {};
                $(".platform-field[data-platform='" + platform + "']").each(function(){
                    var field = $(this).data('field');
                    var value = $(this).val().trim();
                    if (value) {
                        config[field] = value;
                    }
                });
                return config;
            }
            
            // Alert göster
            function showAlert(type, message) {
                var $modal = $("#alertModal");
                var $header = $modal.find(".card-head");
                
                $header.removeClass("style-success style-danger");
                $header.addClass(type === 'success' ? "style-success" : "style-danger");
                
                $("#alertMessage").text(message);
                $modal.modal('show');
            }
            
            // Sayfa yüklendiğinde platform durumlarını ayarla
            $(".platform-toggle").each(function(){
                var status = $(this).is(':checked');
                var card = $(this).closest('.platform-card');
                card.find('.platform-field').prop('disabled', !status);
                card.find('.preview-platform, .save-platform').prop('disabled', !status);
            });
        });
    </script>
</body>
</html>
