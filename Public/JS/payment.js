window.openPayment = function (evt, paymentMethod) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none"
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "")
    }
    document.getElementById(paymentMethod).style.display = "block";
    evt.currentTarget.className += " active"
}
//#payment-bank-button havale ile öde butonunu dinleyelim. tıklanınca prevent yapıp xhr ile siparişi tamamlayalım
//<a href="/?/control/order/get/bankSubmit&amp;orderUniqID=SPRDXT00000000739&amp;languageCode=tr" id="payment-bank-button" class="btn btn-primary">Siparişi Onayla</a>
if(document.getElementById("payment-bank-button")){
    document.getElementById("payment-bank-button").addEventListener("click", function (e) {
        e.preventDefault();
        //linki alalım
        var link = document.getElementById("payment-bank-button").getAttribute("href");
        console.log(link);
        //link data üzerinden languagecode ve orderuniqid alıp get metoduyla gönderelim

        //linke disables classı atayalım
        document.getElementById("payment-bank-button").classList.add("disabled");
        //xhr ile linki çağıralım
        var xhr = new XMLHttpRequest();

        xhr.open("GET", link, true);
        xhr.send();
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                //gelen response ı json a çevirelim
                var response = JSON.parse(xhr.responseText);
                //eğer hata varsa popup gösterelim
                if (response.error) {
                    showPopupForPayment("error", response.message);
                    document.getElementById("payment-bank-button").classList.remove("disabled");
                } else {
                    //eğer hata yoksa başarılı popup gösterelim
                    showPopupForPayment("success", response.message);
                    //2 saniye sonra "/?/control/member/get/orders" sayfasına yönlendirelim
                    setTimeout(function () {
                        window.location.href = "/?/control/member/get/orders";
                    }, 2000);
                }
            }
            else {
                //eğer response 200 değilse hata popup gösterelim
                showPopupForPayment("error", "Bir hata oluştu. Lütfen tekrar deneyin.");
                document.getElementById("payment-bank-button").classList.remove("disabled");
            }
        }
    });
}
function showPopupForPayment(type, message) {
    //dil kodunu html lang parametresinden alalım
    var languageCode = document.documentElement.lang;
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `/?/control/popup/get/show&type=${type}&message=${message}&&languageCode=${languageCode}&autoClose=false`);
    xhr.send();

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
            //if (!isScriptLoaded('/Public/JS/popup.min.js?v=3')) {
            loadScript('/Public/JS/popup.min.js?v=4');
            //}
        }
    };
}