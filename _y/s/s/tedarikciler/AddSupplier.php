<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php");
/**
 * @var AdminDatabase $db
 * @var Config $config
 * @var Helper $helper
 */

include_once MODEL . 'Admin/AdminSupplier.php';
$supplierModel = new AdminSupplier($db,$config);

$suppliers = $supplierModel->getAllSuppliers();

$querySupplierID = $_GET["supplierID"] ?? 0;
$supplierID = intval($querySupplierID);

if ($supplierID > 0) {
    $supplier = $supplierModel->getSupplier($supplierID);
    if (!empty($supplier)) {
        $supplier = $supplier[0];
        $supplierID = $supplier["supplierID"];
        $supplierName = $supplier["supplierName"];
        $supplierName = $helper->decrypt($supplierName, $config->key);
        $supplierSurname = $supplier["supplierSurname"];
        $supplierSurname = $helper->decrypt($supplierSurname, $config->key);
        $supplierTitle = $supplier["supplierTitle"];
        $supplierTitle = $helper->decrypt($supplierTitle, $config->key);
        $supplierPassword = $supplier["supplierPassword"];
        $supplierPassword = $helper->decrypt($supplierPassword, $config->key);
        $supplierIdentityNumber = $supplier["supplierIdentityNumber"];
        $supplierType = $supplier["supplierType"];
        $supplierPhoneNumber = $supplier["supplierPhone"];
        $supplierPhoneNumber = $helper->decrypt($supplierPhoneNumber, $config->key);
        $supplierEmail = $supplier["supplierEmail"];
        $supplierEmail = $helper->decrypt($supplierEmail, $config->key);
        $supplierInvoiceTitle = $supplier["supplierInvoiceTitle"];
        $supplierInvoiceTitle = $helper->decrypt($supplierInvoiceTitle, $config->key);
        $supplierTaxOffice = $supplier["supplierTaxOffice"];
        $supplierTaxOffice = $helper->decrypt($supplierTaxOffice, $config->key);
        $supplierTaxNumber = $supplier["supplierTaxNumber"];
        $supplierTaxNumber = $helper->decrypt($supplierTaxNumber, $config->key);
        $supplierDescription = $supplier["supplierDescription"];
        $supplierIsActive = $supplier["supplierIsActive"];
    }
}
$supplierID = $supplierID ?? 0;
$supplierName = $supplierName ?? "";
$supplierSurname = $supplierSurname ?? "";
$supplierTitle = $supplierTitle ?? "";
$supplierPassword = $supplierPassword ?? "";
$supplierIdentityNumber = $supplierIdentityNumber ?? "";
$supplierPhoneNumber = $supplierPhoneNumber ?? "";
$supplierEmail = $supplierEmail ?? "";
$supplierInvoiceTitle = $supplierInvoiceTitle ?? "";
$supplierTaxOffice = $supplierTaxOffice ?? "";
$supplierTaxNumber = $supplierTaxNumber ?? "";
$supplierDescription = $supplierDescription ?? "";
$supplierIsActive = $supplierIsActive ?? 1;



?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Tedarikçi Ekle Pozitif Eticaret</title>
    <!-- BEGIN META -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="your,keywords">
    <meta name="description" content="Short explanation about this website">
    <!-- END META -->

    <!-- BEGIN STYLESHEETS -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:300italic,400italic,300,400,500,700,900' rel='stylesheet'
          type='text/css'/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/bootstrap.css?1422792965"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/materialadmin.css?1425466319"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/font-awesome.min.css?1422529194"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/material-design-iconic-font.min.css?1421434286"/>
    <link type="text/css" rel="stylesheet" href="/_y/assets/css/theme-3/libs/wizard/wizard.css?1425466601"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/google/code-prettify/master/loader/prettify.css">
    <!-- END STYLESHEETS -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/html5shiv.js?1403934957"></script>
    <script type="text/javascript" src="/_y/assets/js/libs/utils/respond.min.js?1403934956"></script>
    <![endif]-->
</head>
<body class="menubar-hoverable header-fixed ">
<!-- BEGIN HEADER-->
<?php require_once(ROOT."/_y/s/b/header.php");?>
<!-- END HEADER-->
<!-- BEGIN BASE-->
<div id="base">
    <!-- BEGIN CONTENT-->
    <div id="content">
        <section>
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active">Tedarikçi Ekle / Düzenle</li>
                </ol>
            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-md-12">
                        <form name="addSupplierForm" id="addSupplierForm" class="form form-validation form-validate" role="form" method="post">
                            <input type="hidden" name="supplierID" id="supplierID" value="<?=$supplierID?>">
                            <div class="card">
                                <div class="card-body">

                                    <div class="card-head">
                                        <header class="text-s">KİŞİSEL BİLGİLER</header>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="supplierTitle"
                                                id="supplierTitle"
                                                value="<?=$supplierTitle?>"
                                                placeholder=""  >
                                            <label for="supplierNameSurname">Tedarilçi Kısa Ad</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="supplierIdentityNumber"
                                                id="supplierIdentityNumber"
                                                value="<?=$supplierIdentityNumber?>"
                                                data-rule-digits="true"
                                                data-rule-minlength="11"
                                                maxlength="11"
                                                placeholder=""  >
                                            <label for="supplierIdentityNumber">TC No</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="supplierName"
                                                id="supplierName"
                                                value="<?=$supplierName?>"
                                                placeholder=""  >
                                            <label for="supplierName">Tedarikçi Adını </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="supplierSurname"
                                                    id="supplierSurname"
                                                    value="<?=$supplierSurname?>"
                                                    placeholder=""  >
                                            <label for="supplierSurname">Tedarikçi Soyadını </label>
                                        </div>
                                    </div>
                                    <div class="row"></div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="supplierPassword"
                                                    id="supplierPassword"
                                                    value="<?=$supplierPassword?>"
                                                    data-rule-minlength="6"
                                                    maxlength="20"
                                                    placeholder=""  >
                                            <label for="supplierPassword">Tedarikçi Şifresi Yazın</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="supplierPhoneNumber"
                                                id="supplierPhoneNumber"
                                                value="<?=$supplierPhoneNumber?>"
                                                placeholder=""
                                                 >
                                            <label for="supplierPhoneNumber">Tedarikçi Cep Telefonu Yazın (5601234567)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                type="email"
                                                class="form-control"
                                                name="supplierEmail"
                                                id="supplierEmail"
                                                value="<?=$supplierEmail?>"
                                                placeholder=""  >
                                            <label for="supplierEmail">Tedarikçi Eposta Yazın</label>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-body">

                                    <div class="card-head">
                                        <header class="text-s">KURUMSAL FATURA BİLGİLERİ</header>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                                    <textarea
                                                            name="supplierInvoiceTitle"
                                                            id="supplierInvoiceTitle"
                                                            class="form-control"
                                                            rows="2"
                                                            maxlength="255"
                                                            style="
                                                            background-color:#efefef;
                                                            width:96%;
                                                            padding: 10px 1% 10px 1%;
                                                            margin:10px 0 0 0;
                                                            border:solid 1px #eee"
                                                    ><?=$supplierInvoiceTitle?></textarea>
                                            <label for="supplierInvoiceTitle">Tedarikçi Fatura Ünvan</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="supplierTaxOffice"
                                                    id="supplierTaxOffice"
                                                    value="<?=$supplierTaxOffice?>"
                                                    data-rule-minlength="2"
                                                    maxlength="255"
                                                    placeholder=""  >
                                            <label for="supplierTaxOffice">Tedarikçi Vergi Dairesi </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    name="supplierTaxNumber"
                                                    id="supplierTaxNumber"
                                                    value="<?=$supplierTaxNumber?>"
                                                    data-rule-digits="true"
                                                    data-rule-minlength="10"
                                                    maxlength="11"
                                                    placeholder=""  >
                                            <label for="supplierTaxNumber">Tedarikçi Vergi/TC No</label>
                                        </div>
                                    </div>
                                    <div class="row"></div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <textarea
                                                    name="supplierDescription"
                                                    id="supplierDescription"
                                                    class="form-control"
                                                    rows="2"
                                                    maxlength="255"
                                                    style="
                                                    background-color:#efefef;
                                                    width:96%;
                                                    padding: 10px 1% 10px 1%;
                                                    margin:10px 0 0 0;
                                                    border:solid 1px #eee"
                                            ><?=$supplierDescription?></textarea>
                                            <label for="supplierDescription">Tedarikçi Not</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Aktif mi</label>
                                            <div class="col-sm-12">
                                                <label class="radio-inline radio-styled">
                                                    <input type="radio" name="supplierIsActive" value="1" <?php if($supplierIsActive==1)echo'checked'; ?>><span>Aktif</span>
                                                </label>
                                                <label class="radio-inline radio-styled">
                                                    <input type="radio" name="supplierIsActive" value="0"><span>Pasif</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- submit -->
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-default">Kaydet</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <?php require_once(ROOT."/_y/s/b/menu.php");?>

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
    $("#addSupplierphp").addClass("active");

    function validateTCKimlik(value) {
        value = value.toString();
        if(value==="11111111111")
        {
            return true;
        }
        else if(value.length!== 11)
        {
            return false;
        }
        var isEleven = /^[0-9]{11}$/.test(value);
        var totalX = 0;
        for (var i = 0; i < 10; i++)
        {
            totalX += Number(value.substr(i, 1));
        }
        var isRuleX = totalX % 10 == value.substr(10,1);
        var totalY1 = 0;
        var totalY2 = 0;
        for (var i = 0; i < 10; i+=2)
        {
            totalY1 += Number(value.substr(i, 1));
        }
        for (var i = 1; i < 10; i+=2)
        {
            totalY2 += Number(value.substr(i, 1));
        }
        var isRuleY = ((totalY1 * 7) - totalY2) % 10 == value.substr(9,0);
        return isEleven && isRuleX && isRuleY;
    }

    function validateVergiNo(value) {
        if (value.length === 10)
        {
            if(value=="2222222222")
            {
                return true;
            }
            let v = []
            let lastDigit = Number(value.charAt(9))
            for (let i = 0; i < 9; i++) {
                let tmp = (Number(value.charAt(i)) + (9 - i)) % 10
                v[i] = (tmp * 2 ** (9 - i)) % 9
                if (tmp !== 0 && v[i] === 0) v[i] = 9
            }
            let sum = v.reduce((a, b) => a + b, 0) % 10
            return (10 - (sum % 10)) % 10 === lastDigit
        }
        if (value.length === 11){
            return validateTCKimlik(value)
        }
        return false
    }

    function validatePhoneNumber(phoneNumber) {
        // Telefon numarasının 10 haneli olup olmadığını kontrol edin.
        if (phoneNumber.length !== 10) {
            return false;
        }
        // Telefon numarasının +90, 90 veya 0 ile başlamadığını kontrol edin.
        if (phoneNumber.startsWith("+90") || phoneNumber.startsWith("90") || phoneNumber.startsWith("0") || phoneNumber.startsWith("+")) {
            return false;
        }
        // Telefon numarasının sadece sayılardan oluştuğunu kontrol edin.
        if (!/^\d+$/.test(phoneNumber)) {
            return false;
        }
        // Telefon numarası geçerlidir.
        return true;
    }

    function validateEmailAddress(email) {
        // E-posta adresinin '@' karakteri içerip içermediğini kontrol edin.
        if (!email.includes("@")) {
            return false;
        }

        // E-posta adresinin '.' karakteri içerip içermediğini kontrol edin.
        if (!email.includes(".")) {
            return false;
        }

        // E-posta adresinin geçerli bir formatta olup olmadığını kontrol edin.
        const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!regex.test(email)) {
            return false;
        }

        // E-posta adresi geçerlidir.
        return true;
    }
    //submit dinleyelim, tedarikçi kısa ad zorunlu olsun.
    //eposta, telefon,tckimlik,vergi no eğer doluysa doğrulamalarını yapalım

    $(document).on("submit", "#addSupplierForm", function (e) {
        e.preventDefault();
        //supplierNameSurname boş olamaz
        if ($("#supplierTitle").val() == "") {
            $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
            $("#alertMessage").text("Tedarikçi kısa adı boş olamaz");
            $("#alertModal").modal("show");
            return;
        }

        //supplierIdentityNumber doluysa doğrulama yapalım
        if ($("#supplierIdentityNumber").val() != "") {
            if (!validateTCKimlik($("#supplierIdentityNumber").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("TCKimlik numarası geçersiz");
                $("#alertModal").modal("show");
                return;
            }
        }
        //supplierTaxNumber doluysa doğrulama yapalım
        if ($("#supplierTaxNumber").val() != "") {
            if (!validateVergiNo($("#supplierTaxNumber").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Vergi numarası geçersiz");
                $("#alertModal").modal("show");
                return;
            }
        }
        //supplierPhoneNumber doluysa doğrulama yapalım
        if ($("#supplierPhoneNumber").val() != "") {
            if (!validatePhoneNumber($("#supplierPhoneNumber").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Telefon numarası geçersiz");
                $("#alertModal").modal("show");
                return;
            }
        }
        //supplierEmail doluysa doğrulama yapalım
        if ($("#supplierEmail").val() != "") {
            if (!validateEmailAddress($("#supplierEmail").val())) {
                $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                $("#alertMessage").text("Eposta adresi geçersiz");
                $("#alertModal").modal("show");
                return;
            }
        }

        let supplierID = $("#supplierID").val();

        let action = "addSupplier";

        if (supplierID > 0) {
            action = "updateSupplier";
        }

        var form = $(this);
        var formData = form.serialize();
        formData += "&action=" + action;

        $.ajax({
            url: "/App/Controller/Admin/AdminSupplierController.php",
            type: "POST",
            data: formData,
            success: function (response) {
                console.log(response);
                let data = JSON.parse(response);
                if (data.status === "success") {
                    $("#alertModal .card-head").removeClass("style-danger").addClass("style-success");
                    $("#alertMessage").text(data.message);
                    $("#alertModal").modal("show");
                    setTimeout(function () {
                        window.location.href = "/_y/s/s/tedarikciler/SupplierList.php";
                    }, 1000);
                }
                else {
                    $("#alertModal .card-head").removeClass("style-success").addClass("style-danger");
                    $("#alertMessage").text(data.message);
                    $("#alertModal").modal("show");
                }
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        const refAction = urlParams.get('refAction');
        if(refAction){
            modalMessage="Ürün eklemek için önce tedarilçi eklemelisiniz";
            $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");
            $("#alertModal #alertMessage").html(modalMessage);
            $("#alertModal").modal("show");
        }
    });

    const urlParams = new URLSearchParams(window.location.search);
    const refAction = urlParams.get('refAction');
    if(refAction){
        modalMessage="Ürün eklemek için önce tedarikçi eklemelisiniz";
        $("#alertModal .modal-header").removeClass("bg-success").addClass("bg-danger");
        $("#alertModal #alertMessage").html(modalMessage);
        $("#alertModal").modal("show");
    }
</script>

</body>
</html>