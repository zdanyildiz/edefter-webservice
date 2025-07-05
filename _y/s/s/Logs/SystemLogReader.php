<?php  require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Product $product
 * @var int $adminAuth
 * @var Helper $helper
 */

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Log İzleme</title>
    <meta charset="UTF-8">
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
                    <li class="active">Log İzleme</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <button type="button" id="getLogs" class="btn btn-primary">Log Getir</button>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" id="deleteLogButton" class="btn btn-danger">Sil</button>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <div class="checkbox checkbox-styled">
                                                    <label>
                                                        <input name="runTimer" id="runTimer" type="checkbox">
                                                        <span>Zamanlayıcıyı başlat</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tarih</th>
                                    <th>Mesaj</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="col-sm-4">
                                <button type="button" id="filterButton" class="btn btn-primary">Log Getir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deleteLogConfirm" tabindex="-1" role="dialog" aria-labelledby="deleteLogConfirmLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button id="btn-popup-sil-kapat" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Log Sil</h4>
                        </div>
                        <div class="modal-body">
                            <p>Log dosyasını silmek istediğinize emin misiniz?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="deleteConfirmButton">Sil</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php require_once(ROOT."/_y/s/b/menu.php");?>
</div>
<style>
    td {
        word-break: break-all;
    }
</style>
<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/jquery-ui/jquery-ui.min.js"></script>

<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="/_y/assets/js/libs/spin.js/spin.min.js"></script>
<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

<script src="/_y/assets/js/core/source/App.js"></script>
<script src="/_y/assets/js/core/source/AppNavigation.js"></script>
<script src="/_y/assets/js/core/source/AppOffcanvas.js"></script>
<script src="/_y/assets/js/core/source/AppCard.js"></script>
<script src="/_y/assets/js/core/source/AppForm.js"></script>
<script src="/_y/assets/js/core/source/AppNavSearch.js"></script>
<script src="/_y/assets/js/core/source/AppVendor.js"></script>

<script src="/_y/assets/js/libs/jquery/run_prettify.js"></script>

<script>
    $("#systemLogReaderphp").addClass("active");

    $("#filterButton").click(function(){
        $("#getLogs").click();
    });
    $(document).on("click","#getLogs",function(e){
        e.preventDefault();

        $.ajax({
            url: "/App/Controller/Admin/AdminLogController.php",
            type: "GET",
            data: {
                action: 'readMultiLineErrorLogs',
            },
            success: function(response){
                //console.log(response);
                var jsonResponse = JSON.parse(response);
                var logs = jsonResponse.logs;
                var tbody = $("table tbody");
                tbody.html('');
                for (var i = 0; i < logs.length; i++) {
                    var log = logs[i];
                    var tr = $("<tr>");
                    tr.append($("<td>").text(i + 1));
                    tr.append($("<td>").text(log.date));
                    tr.append($("<td>").text(log.message));
                    tbody.append(tr);
                }
            }
        });
    });

    $("#deleteLogButton").click(function(){
        $("#deleteLogConfirm").modal('show');
    });

    $("#deleteConfirmButton").click(function(){
        $.ajax({
            url: "/App/Controller/Admin/AdminLogController.php",
            type: "POST",
            data: {
                action: 'deleteSystemLog'
            },
            success: function(response){
                var jsonResponse = JSON.parse(response);
                if(jsonResponse.status === 'success'){
                    $("#deleteLogConfirm").modal('hide');
                    var tbody = $("table tbody");
                    tbody.html('');
                    $("#getLogs").click();
                }
            }
        });
    });

    $("#runTimer").change(function(){
        $("#getLogs").click();
        run();
    });

    function run(){
        if($("#runTimer").is(':checked')){
            setTimeout(function(){
                $("#getLogs").click();
                run();
            }, 10000);
        }
    }

</script>
</body>
</html>