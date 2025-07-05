<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Pozitif Eticaret Site Kurulum</title>
    <!-- BEGIN META -->
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
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-default/libs/wizard/wizard.css?1425466601" />
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">

<div id="base">

    <!-- BEGIN CONTENT-->
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Kurulum Sihirbazı</li>
                </ol>
            </div>
            <div class="section-body contain-lg">

                <!-- BEGIN INTRO -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="text-primary">Kurulum Sihirbazı</h1>
                    </div><!--end .col -->
                    <div class="col-lg-8">
                        <article class="margin-bottom-xxl">
                            <p class="lead">
                                Hadi yeni bir site oluşturalım!
                            </p>
                        </article>
                    </div>
                </div>
                <!-- END INTRO -->

                <!-- BEGIN FORM WIZARD -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body ">
                                <div id="rootwizard1" class="form-wizard form-wizard-horizontal">
                                    <form id="createSite" class="form floating-label">
                                        <div class="form-wizard-nav">
                                            <div class="progress"><div class="progress-bar progress-bar-primary"></div></div>
                                            <ul class="nav nav-justified">
                                                <li><a id="firstClick" href="#tab1" data-toggle="tab"><span class="step">1</span> <span class="title">Alan Adları</span></a></li>
                                                <li><a href="#tab2" data-toggle="tab"><span class="step">2</span> <span class="title">SQL Bilgileri</span></a></li>
                                                <li><a href="#tab3" data-toggle="tab"><span class="step">3</span> <span class="title">Anahtar Kodu</span></a></li>
                                                <li><a href="#tab4" data-toggle="tab"><span class="step">4</span> <span class="title">Kontrol</span></a></li>
                                            </ul>
                                        </div><!--end .form-wizard-nav -->

                                        <div class="tab-content clearfix">
                                            <div class="tab-pane active" id="tab1">
                                                <br/><br/>
                                                <div class="form-group">
                                                    <input type="text" name="domain" id="domain" class="form-control dirty">
                                                    <label for="domain" class="control-label">Ana alan adı.</label>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" name="domains" id="domains" class="form-control dirty">
                                                    <label for="domains" class="control-label">Diğer alan adları! Her bir alan adını boşluksuz virgül ile ayırarak girin.</label>
                                                </div>
                                            </div><!--end #tab1 -->
                                            <div class="tab-pane" id="tab2">
                                                <br/><br/>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <input type="text" name="serverUrl" id="serverUrl" class="form-control dirty" value="localhost">
                                                        <label for="serverUrl" class="control-label">Sunucu Adresi</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="databaseName" id="databaseName" class="form-control dirty">
                                                        <label for="databaseName" class="control-label">Veri Tabanı adı</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="username" id="username" class="form-control dirty">
                                                            <label for="username" class="control-label">Veri Tabanı Kullanıcı Adı</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="password" id="password" class="form-control dirty" value="Global2019*">
                                                            <label for="password" class="control-label">Veri Tabanı Şifre</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <input type="text" name="localServerUrl" id="localServerUrl" class="form-control dirty" value="localhost">
                                                        <label for="localServerUrl" class="control-label">Lokal Sunucu Adresi</label>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="text" name="localDatabaseName" id="localDatabaseName" class="form-control dirty">
                                                        <label for="localDatabaseName" class="control-label">Lokal Veri Tabanı adı</label>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="localUsername" id="localUsername" class="form-control dirty" value="root">
                                                            <label for="localUsername" class="control-label">Lokal Veri Tabanı Kullanıcı Adı</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" name="localPassword" id="localPassword" class="form-control dirty" value="Global2019*">
                                                            <label for="localPassword" class="control-label">Lokal Veri Tabanı Şifre</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div><!--end #tab2 -->
                                            <div class="tab-pane" id="tab3">
                                                <br/><br/>
                                                <div class="form-group">
                                                    <input type="text" name="keyCode" id="keyCode" class="form-control dirty">
                                                    <label for="keyCode" class="control-label">32 Haneli Anahtar Kod</label>
                                                    <p class="help-block" id="createKeyCode">Üretmek İçin Tıklayın</p>
                                                </div>
                                            </div><!--end #tab3 -->
                                            <div class="tab-pane" id="tab4">
                                                <div class="row">
                                                    <br/><br/>
                                                    <span class="domain"></span>
                                                    <span class="domains"></span>
                                                    <span class="serverUrl"></span>
                                                    <span class="databaseName"></span>
                                                    <span class="username"></span>
                                                    <span class="password"></span>
                                                    <span class="localServerUrl"></span>
                                                    <span class="localDatabaseName"></span>
                                                    <span class="localUsername"></span>
                                                    <span class="localPassword"></span>
                                                    <span class="keyCode"></span>
                                                    <span class="row"> ... </span>
                                                    <span class="result"></span>
                                                    <span class="row"> .. </span>
                                                    <!-- submit buyonu koyalım -->
                                                    <button type="submit" id="createButton" class="btn btn-primary">Oluştur</button>
                                                    <button type="button" id="resetButton" class="btn btn-primary">Reset</button>
                                                </div>
                                            </div><!--end #tab4 -->
                                        </div><!--end .tab-content -->

                                        <ul class="pager wizard">
                                            <li class="previous first"><a class="btn-raised" href="javascript:void(0);">İlk</a></li>
                                            <li class="previous"><a class="btn-raised" href="javascript:void(0);">Geri</a></li>
                                            <li class="next last"><a class="btn-raised" href="javascript:void(0);">Son</a></li>
                                            <li class="next"><a class="btn-raised" href="javascript:void(0);">İleri</a></li>
                                        </ul>
                                    </form>
                                </div><!--end #rootwizard -->
                            </div><!--end .card-body -->
                        </div><!--end .card -->
                        <em class="text-caption">Form wizard</em>
                    </div><!--end .col -->
                </div><!--end .row -->
                <!-- END FORM WIZARD -->
            </div>
        </section>
    </div>
</div>

<style>
    #createKeyCode {
        cursor: pointer;
        color: #337ab7;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
    }
</style>

<!-- BEGIN JAVASCRIPT -->
<script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
<script src="/_y/assets/js/libs/jquery/jquery-migrate-1.2.1.min.js"></script>
<script src="/_y/assets/js/libs/bootstrap/bootstrap.min.js"></script>

<script src="/_y/assets/js/libs/autosize/jquery.autosize.min.js"></script>

<script src="/_y/assets/js/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/_y/assets/js/libs/jquery-validation/dist/additional-methods.min.js"></script>
<script src="/_y/assets/js/libs/wizard/jquery.bootstrap.wizard.min.js"></script>



<!-- END JAVASCRIPT -->
<script>
    function handleTabShow(tab, navigation, index, wizard){
        var total = navigation.find('li').length;
        var current = index + 0;
        var percent = (current / (total - 1)) * 100;
        var percentWidth = 100 - (100 / total) + '%';

        navigation.find('li').removeClass('done');
        navigation.find('li.active').prevAll().addClass('done');

        wizard.find('.progress-bar').css({width: percent + '%'});
        $('.form-wizard-horizontal').find('.progress').css({'width': percentWidth});
    }

    $('#rootwizard1').bootstrapWizard({
        onTabShow: function(tab, navigation, index) {
            handleTabShow(tab, navigation, index, $('#rootwizard1'));
        }
    });

    $("#firstClick").click();

    //createKeyCode tıklanınca 32 haneli harf, rakam ve özel karakterlerden oluşan bir kod üret
    document.getElementById("createKeyCode").addEventListener("click", function() {
        var keyCode = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#%^*()_";
        for (var i = 0; i < 32; i++)
            keyCode += possible.charAt(Math.floor(Math.random() * possible.length));
        document.getElementById("keyCode").value = keyCode;
        //ilgili label'ı da değiştirelim
        document.querySelector("label[for='keyCode']").innerHTML = keyCode ;

        document.querySelector(".serverUrl").innerHTML = "Sunucu Adresi: " + document.getElementById("serverUrl").value + "<br>";
        document.querySelector(".databaseName").innerHTML = "Veri Tabanı Adı: " + document.getElementById("databaseName").value + "<br>";
        document.querySelector(".username").innerHTML = "Veri Tabanı Kullanıcı Adı: " + document.getElementById("username").value + "<br>";
        document.querySelector(".password").innerHTML = "Veri Tabanı Şifre: " + document.getElementById("password").value + "<br>";
        document.querySelector(".localServerUrl").innerHTML = "Lokal Sunucu Adresi: " + document.getElementById("localServerUrl").value + "<br>";
        document.querySelector(".localDatabaseName").innerHTML = "Lokal Veri Tabanı Adı: " + document.getElementById("localDatabaseName").value + "<br>";
        document.querySelector(".localUsername").innerHTML = "Lokal Veri Tabanı Kullanıcı Adı: " + document.getElementById("localUsername").value + "<br>";
        document.querySelector(".localPassword").innerHTML = "Lokal Veri Tabanı Şifre: " + document.getElementById("localPassword").value + "<br>";
        document.querySelector(".keyCode").innerHTML = "Anahtar Kodu: " + keyCode + "<br>";

        document.getElementById("keyCode").focus();
    });

    //alanlar girildikçe ilgili class içine yaz
    document.getElementById("domain").addEventListener("keyup", function() {
        document.querySelector(".domain").innerHTML = "Ana Alan Adı: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='domain']").innerHTML = this.value ;
        //databaseName, username ve localDatabaseName otomatik dolduralım
        var domain = this.value;
        document.getElementById("databaseName").value = domain;
        document.getElementById("username").value = domain;
        document.getElementById("localDatabaseName").value = domain;
    });

    document.getElementById("domains").addEventListener("keyup", function() {
        document.querySelector(".domains").innerHTML = "Diğer Alan Adları: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='domains']").innerHTML = this.value ;
    });

    document.getElementById("serverUrl").addEventListener("keyup", function() {
        document.querySelector(".serverUrl").innerHTML = "Sunucu Adresi: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='serverUrl']").innerHTML = this.value ;
    });

    document.getElementById("databaseName").addEventListener("keyup", function() {
        document.querySelector(".databaseName").innerHTML = "Veri Tabanı Adı: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='databaseName']").innerHTML = this.value ;
    });

    document.getElementById("username").addEventListener("keyup", function() {
        document.querySelector(".username").innerHTML = "Veri Tabanı Kullanıcı Adı: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='username']").innerHTML = this.value ;
    });

    document.getElementById("password").addEventListener("keyup", function() {
        document.querySelector(".password").innerHTML = "Veri Tabanı Şifre: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='password']").innerHTML = this.value ;
    });

    document.getElementById("localServerUrl").addEventListener("keyup", function() {
        document.querySelector(".localServerUrl").innerHTML = "Lokal Sunucu Adresi: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='localServerUrl']").innerHTML = this.value ;
    });

    document.getElementById("localDatabaseName").addEventListener("keyup", function() {
        document.querySelector(".localDatabaseName").innerHTML = "Lokal Veri Tabanı Adı: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='localDatabaseName']").innerHTML = this.value ;
    });

    document.getElementById("localUsername").addEventListener("keyup", function() {
        document.querySelector(".localUsername").innerHTML = "Lokal Veri Tabanı Kullanıcı Adı: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='localUsername']").innerHTML = this.value ;
    });

    document.getElementById("localPassword").addEventListener("keyup", function() {
        document.querySelector(".localPassword").innerHTML = "Lokal Veri Tabanı Şifre: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='localPassword']").innerHTML = this.value ;
    });

    document.getElementById("keyCode").addEventListener("keyup", function() {
        document.querySelector(".keyCode").innerHTML = "Anahtar Kodu: " + this.value + "<br>";
        //ilgili label'ı da değiştirelim
        //document.querySelector("label[for='keyCode']").innerHTML = this.value ;
    });

    //form submit edildiğinde (hiçbir alan boş olamaz)
    document.getElementById("createSite").addEventListener("submit", function(e) {
        e.preventDefault();

        //createButton disbled yapalım
        document.getElementById("createButton").disabled = true;

        var domain = document.getElementById("domain").value;
        var domains = document.getElementById("domains").value;
        var serverUrl = document.getElementById("serverUrl").value;
        var databaseName = document.getElementById("databaseName").value;
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;
        var localServerUrl = document.getElementById("localServerUrl").value;
        var localDatabaseName = document.getElementById("localDatabaseName").value;
        var localUsername = document.getElementById("localUsername").value;
        var localPassword = document.getElementById("localPassword").value;
        var keyCode = document.getElementById("keyCode").value;

        if(domain == "" || domains == "" || serverUrl == "" || databaseName == "" || username == "" || password == "" || localServerUrl == "" || localDatabaseName == "" || localUsername == "" || localPassword == "" || keyCode == "") {
            alert("Tüm alanları doldurunuz!");
            return;
        }

        var actions = ['createDomain', 'createKey', 'createSql', 'createCloudflare', 'createSite', 'setupSite1', 'setupSite2', 'setupSite3', 'setupSite4', 'setupSite5', 'remoteDB'];
        var currentActionIndex = 0;

        function performNextAction() {
            if (currentActionIndex >= actions.length) {
                document.querySelector(".result").innerHTML = document.querySelector(".result").innerHTML + "Site oluşturuldu!";
                return;
            }

            console.log(actions[currentActionIndex]);

            var action = actions[currentActionIndex];
            var postData = {
                action: action,
                domain: domain,
                domains: domains,
                serverUrl: serverUrl,
                databaseName: databaseName,
                username: username,
                password: password,
                localServerUrl: localServerUrl,
                localDatabaseName: localDatabaseName,
                localUsername: localUsername,
                localPassword: localPassword,
                keyCode: keyCode
            };

            $.ajax({
                url: 'create.php',
                type: 'POST',
                data: postData,
                timeout: 30000,
                success: function(response) {
                    console.log(response); // Sunucudan gelen yanıtı kontrol edebilirsiniz
                    try {
                        let jsonResponse = JSON.parse(response);
                        let status = jsonResponse.status;
                        let message = jsonResponse.message;

                        document.querySelector(".result").innerHTML = document.querySelector(".result").innerHTML + message + "<br>";

                        if (status === "error") {
                            return;
                        }
                        currentActionIndex++;
                        //tüm actionlar tamamlandığında /index.php sayfasına yönlendirilecek
                        if (currentActionIndex >= actions.length) {
                            //document.location.href = "/";
                        }
                        performNextAction(); // Bir sonraki işlemi başlat
                    } catch (error) {
                        document.querySelector(".result").innerHTML = document.querySelector(".result").innerHTML + "Yanıt işlenirken hata oluştu: " + error.message + "<br>";
                        console.error("JSON parse hatası:", error);
                    }
                },
                error: function(xhr, status, error) {
                    document.querySelector(".result").innerHTML = document.querySelector(".result").innerHTML + "Hata oluştu: " + error + "<br>";
                }
            });
        }

        performNextAction(); // İlk işlemi başlat
    });

    //resetButton tıklanınca #result div'ını temizle ve createButton'ı aktif et
    document.getElementById("resetButton").addEventListener("click", function() {
        document.querySelector(".result").innerHTML = "";
        document.getElementById("createButton").disabled = false;
        //konsolu temizle
        console.clear();
    });

</script>
</body>
</html>
