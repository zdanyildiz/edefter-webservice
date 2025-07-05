//kargoTakipBilgisi getirir.
$(document).on('click', '.kargoTakipBilgileriButton',function () {

    var kargoTakipNo = $("#kargoTakipNo").attr("data-kargoTakipNo");
    var kargoFirmaCode = $("#kargoFirmaCode").attr("data-kargoFirmaCode");
    $.ajax({
        data: {kargoFirmaCode: kargoFirmaCode, kargoTakipNo: kargoTakipNo},
        method: "POST",
        url: "/_y/s/s/siparisler/kargoTakipAdmin.php",
        cache: false,
        beforeSend: function( xhr ) {
            $(".kargoTakipBilgileriTable").html("<tr><td colspan='5' class='text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/></td></tr>");
        }
    })
        .done(function (xhr) {
            $(".kargoTakipBilgileriTable").html(xhr);
        });
});

$(document).on("click",".sevkiyatBaslatButton",function () {
    const clickedItem = $(this);
    console.log(clickedItem.attr("data-id")+":"+clickedItem.attr("data-kargoCode"));
    $.ajax({
        //FIXME: sevkiyat=A düzeltilmeli.
        data: "siparisbenzersizid=" + clickedItem.attr("data-id") + "&sevkiyat="+clickedItem.attr("data-kargoCode"),
        method: "POST",
        url: "/sistem/sevkiyat/sevkiyatSiparis.php",
        beforeSend: function( xhr ) {
           $("#simpleModalSevkiyat").html("<p class='row col-md-12 text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/><br>Gönderiliyor...</p>");

            }
        })
        .done(function (xhr) {
            $("#simpleModalSevkiyat").html(xhr);
            $('#simpleModalSevkiyat').modal('show');
        });
});

$(document).on('click', '.kargoTakipDialogButton',function () {
    var siparisTakipButton = $(this);
    $.ajax({
        data: {siparisbenzersizid: $(siparisTakipButton).attr("data-id"), kargoTakipDialog: true},
        method: "POST",
        url: "/yapi/main/hesabim/kargoTakipClient.php",
        beforeSend: function( xhr ) {
            $("#simpleModalKargoTakip").html("<p class='row col-md-12 text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/><br>Gönderiliyor...</p>");
        }
    })
        .done(function (xhr) {
            $("#simpleModalKargoTakip").html(xhr);
            $('#simpleModalKargoTakip').modal('show');
        });
});

$("#simpleModalSevkiyat").on('click', '#sevkiyatGerceklestir',function () {
    var benzersizId = $("#sevkiyatGerceklestir").attr("data-benzersizId");
    var kargoFirmaCode = $("#siparisKargoFirmaCode").val();
    var checkString = $("#sevkiyatGerceklestir").attr("data-checkString");
    var kargoDesi = $("#kargoDesi").val();
    var kargoAgirlik = $("#kargoAgirlik").val();
    var kargoOdemeTipi = $("#kargoodemetipi option:selected").val();

    $(".sevkiyatSonuc").html("");
    $.ajax({
        data: {kargoFirmaCode : kargoFirmaCode , benzersizId : benzersizId , checkString : checkString , kargoDesi : kargoDesi , kargoAgirlik : kargoAgirlik , kargoOdemeTipi : kargoOdemeTipi},
        method: "POST",
        url: "/sistem/sevkiyat/Sevkiyat.php",
        beforeSend: function( xhr ) {
                $(".sevkiyatSonuc").html("<p class='row col-md-12 text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/><br>Gönderiliyor...</p>");
            }
        })
        .done(function (xhr) {
            if (!JSON.parse(xhr)["isHata"]){
                $("#sevkiyatGerceklestir").attr("disabled","disabled");
                $("#siparisKargoFirmaCode").attr("disabled","disabled");
                $("#kargoDesi").attr("disabled","disabled");
                $("#kargoAgirlik").attr("disabled","disabled");
                $(".sevkiyatBaslatButton[data-id="+benzersizId+"]").attr("disabled", "disabled");
                $(".barkodYazdirButton[data-id="+benzersizId+"]").removeAttr("disabled");
            }
            $(".sevkiyatSonuc").html(JSON.parse(xhr)["message"]);
        });
});

$(document).on('click', '.barkodYazdirButton',function () {
    var barkodYazdirButton = $(event.target);
    $.ajax({
        data: {
            siparisbenzersizid: barkodYazdirButton.attr("data-id"),
            kargoCode: barkodYazdirButton.attr("data-kargoCode"),
            kargoOdemeTipi: barkodYazdirButton.attr("data-kargoOdemeTipi"),
        },
        method: "POST",
        url: "/sistem/sevkiyat/sevkiyatSiparis.php",
        beforeSend: function( xhr ) {
            $("#simpleModalSevkiyat").html("<p class='row col-md-12 text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/><br>Gönderiliyor...</p>");
        }
    })
        .done(function (xhr) {
            $("#simpleModalSevkiyat").html(xhr);
            $('#simpleModalSevkiyat').modal('show');
        });
});

$(document).on('click', '.barkodOlusturButton', function() {
    var barcodeOlusturButton = $(".barkodOlusturButton");
    $.ajax({
        data: {
            benzersizId: barcodeOlusturButton.attr("data-id"),
            kargoFirmaCode: barcodeOlusturButton.attr("data-kargoCode"),
            checkString: barcodeOlusturButton.attr("data-checkString"),
            generateBarcode: true,
            tempBarcodeNumber: barcodeOlusturButton.attr("data-tempBarcodeNumber"),
            kargoOdemeTipi: barcodeOlusturButton.attr("data-kargoOdemeTipi")
        },
        method: "POST",
        url: "/sistem/sevkiyat/Sevkiyat.php",
        beforeSend: function (xhr) {
            $(".barcodeModel .barcodeImage").html("<p class='row col-md-12 text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/><br>Gönderiliyor...</p>");
        },
        dataType: 'json'
    }).done(function (xhr) {
        $(".barcodeModel .barcodeImage").html(xhr["message"]);
        if (xhr["isHata"]){
            $(".barcodeModel .barcodeImage").append("<p>Hata Kodu: "+xhr["resultCode"]+"</p>");
        }else{
            $(".printBarcodeButton").removeClass("disabled");
        }
    });
});

$(document).on('click', '.printBarcodeButton', function() {
    $(".barcodeSonrasiSiparisIlerletButton").removeClass("disabled");
});

$(document).on('click', '.barcodeSonrasiSiparisIlerletButton', function() {
    var barcodeOlusturButton = $(".barkodOlusturButton");
    var benzersizId = barcodeOlusturButton.attr("data-id");
    $.ajax({
        data: {
            benzersizId: benzersizId,
            siparisIlerlet: true
        },
        method: "POST",
        url: "/sistem/sevkiyat/Sevkiyat.php",
        beforeSend: function (xhr) {
            $(".barcodeModel .barcodeImage").html("<p class='row col-md-12 text-center'><img width='32' height='32' src='/_y/assets/img/loading.gif'/><br>Gönderiliyor...</p>");
        },
        dataType: 'json'
    }).done(function (xhr) {
        $("#simpleModalSevkiyat").hide()
        $(".urun"+benzersizId).remove();
    });


});

$("#kargoTakipFormGosterButton").click(function () {
    $(".kargoForm").addClass("hidden");
    var kargoFirmaCode = $("#kargoid").val();
    $(".kargoForm-"+kargoFirmaCode).removeClass("hidden");
});