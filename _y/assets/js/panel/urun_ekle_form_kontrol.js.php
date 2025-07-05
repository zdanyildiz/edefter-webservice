$("#urunekleform").submit(function (event)
{
    $stokkodu_arr=[];
    $formhata=0;$("#ekozellik_sonuclar").html('');
    if($("#kategoriid").val()==0||!$("#kategoriid").val()) {

        $('#kategoridivler').focus($("#kategoridivler").css({'border': '1px solid red'}));
        $("#formHataUyari").modal("show");
        $("#modalHataAciklama").html("Kategori Seçin<br>Daha önce kategori eklemediyseniz E-Ticaret/Ürün Kategorileri/Kategori Ekle sayfasına gidin");
        $('[href="#tab_kategori"]').tab('show');
        event.preventDefault();event.stopPropagation();
        $formhata=1;
    }
    else if($("#tedarikciid").val()==0) {

        $('#tedarikciid').focus($("#tedarikciid").css({
            'border': '1px solid red'
        }));
        $("#formHataUyari").modal("show");
        $("#modalHataAciklama").html("Tedarikçi Seçin<br>Daha önce tedarikçi eklemediyseniz E-Ticaret/Tedarikçiler/Tedarikçi Ekle sayfasına gidin");
        $("#urunekleform > div.card > div.card-head > ul > li:nth-child(1) > a").click();
        event.preventDefault();event.stopPropagation();
        $formhata=1;
    }
    else if($("#markaid").val()==0) {

        $('#markaid').focus($("#markaid").css({'border': '1px solid red'}));
        $("#formHataUyari").modal("show");
        $("#modalHataAciklama").html("Marka Seçin<br>Daha önce marka eklemediyseniz E-Ticaret/Markalar/Marka Ekle sayfasına gidin");
        $('[href="#tab_kategori"]').tab('show');
        event.preventDefault();event.stopPropagation();
        $formhata=1;
    }
    else if(!$("#urunmodel").val()){

        $('#tedarikciid').focus($(this).css({'border': '1px solid red'}));
        $("#modalHataAciklama").html("Tedarikçi Seçin<br>Daha önce tedarikçi eklemediyseniz E-Ticaret/Tedarikçiler/Tedarikçi Ekle sayfasına gidin");
        $('[href="#tab_kategori"]').tab('show');
        event.preventDefault();event.stopPropagation();
        $formhata=1;
    }
    else if(!$("#sayfaad").val()){

        $(this).focus($(this).css({'border': '1px solid red'}));
        $('[href="#tab_kategori"]').tab('show');
        event.preventDefault();event.stopPropagation();
        $formhata=1;
    }
    else if($("#otovaryant").val()==0) {
        $(".varyant_stokkodu_div input").each(function()
        {
            $stokkodu=$(this).val();
            if(!$stokkodu)
            {
                $("#varyantModal").modal('show');
                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").html("Stok Kodu Boş Olamaz<br><a href='javascript:void(0)' class='alert-success' id='otostokkoduolustur'>Stok kodunu otomatik oluştur</a>");
                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                $(this).focus($(this).css({'border': '1px solid red'}));
                $('[href="#tab_varyant"]').tab('show');
                event.preventDefault();event.stopPropagation();
                $formhata=1;
            }
            else
            {
                if($stokkodu_arr.indexOf($stokkodu) > -1)
                {
                    $("#varyantModal").modal('show');
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").html("Her bir varyant stok kodu birbirinden farklı olmalı<br><a href='javascript:void(0)' class='alert-success' id='otostokkoduolustur'>Stok kodunu otomatik oluştur</a>");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
                    $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
                    $(this).focus($(this).css({'border': '1px solid red'}));
                    $('[href="#tab_varyant"]').tab('show');
                    event.preventDefault();event.stopPropagation();
                    $formhata=1;
                }
                else
                {
                    $stokkodu_arr.push($stokkodu);
                }
            }
            $stokkodu="";
        });
    }
    else if(!$("#seokelime").val()){

        $('#seokelime').focus($("#seokelime").css({
            'border': '1px solid red'
        }));
        event.preventDefault();event.stopPropagation();
        $formhata=1;
    }

    $(".varyant_stok_div input").each(function()
    {
        if(!$(this).val()||!$.isNumeric($(this).val()))
        {
            $("#varyantModal").modal('show');
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Her bir varyant için stok sayısı rakam olarak girilmelidir");
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
            $(this).focus($(this).css({'border': '1px solid red'}));
            $('[href="#tab_varyant"]').tab('show');
            event.preventDefault();event.stopPropagation();
            $formhata=1;
        }
    });
    $(".varyant_satisfiyat_div input").each(function()
    {
        if(!$(this).val()||!$.isNumeric($(this).val()))
        {
            $("#varyantModal").modal('show');
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").text("Fiyat Rakam ve Ayracı . (nokta) ile yazılmalıdır. 17.50");
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-success");
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").removeClass("alert-danger");
            $("#varyantModal > div.modal-dialog > div > div.modal-body > p").addClass("alert alert-danger");
            $(this).focus($(this).css({
                'border': '1px solid red'
            }));
            $('[href="#tab_varyant"]').tab('show');
            event.preventDefault();event.stopPropagation();
            $formhata=1;
        }
    });

    if($formhata==1){event.preventDefault();event.stopPropagation();}
});