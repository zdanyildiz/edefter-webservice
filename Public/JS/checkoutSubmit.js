var languageCode = document.documentElement.lang;
document.querySelector('.cart-summary a#submit').addEventListener('click', function(event) {
    event.preventDefault();
    // Kargo adresi, fatura adresi ve ürün seçimlerini kontrol et
    var cargoAddressSelected = document.querySelector('input[name="cargoAddressID"]:checked') !== null;
    var invoiceAddressSelected = document.querySelector('input[name="invoiceAddressID"]:checked') !== null;
    var productSelected = document.querySelector('input[name="cartItem[]"]:checked') !== null;

    var invoiceName = document.querySelector('input[name="invoiceName"]').value;
    var invoiceTaxOffice = document.querySelector('input[name="invoiceTaxOffice"]').value;
    var invoiceTaxNumber = document.querySelector('input[name="invoiceTaxNumber"]').value;
    var customerNote = document.querySelector('textarea[name="customerNote"]').value;
    var acceptContract = document.querySelector('input[name="acceptContract"]').checked;

    // Eğer herhangi biri seçili değilse, popup mesajı göster ve form gönderimini engelle
    if (!cargoAddressSelected) {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen kargo adresi seçiniz.');
        return;
    }
    else if (!invoiceAddressSelected) {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen fatura adresi seçiniz.');
        return;
    }
    else if (!productSelected) {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen ürün seçiniz.');
        return;
    }
    else if (invoiceName === '') {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen fatura adı giriniz.');
        return;
    }
    else if (invoiceTaxOffice === '') {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen vergi dairesi giriniz.');
        return;
    }
    else if (invoiceTaxNumber === '') {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen vergi numarası giriniz.');
        return;
    }
    else if (!validateVergiNo(invoiceTaxNumber)) {
        event.preventDefault();
        showPopupCheckout("error", "Geçersiz Vergi Numarası");
        form.querySelector('#invoiceTaxNumber').focus();
        return;
    }
    else if (!acceptContract) {
        event.preventDefault();
        showPopupCheckout('error', 'Lütfen sözleşmeyi kabul ediniz.');
        return;
    }

    //butonu disable yapalım
    document.querySelector('.cart-summary a#submit').classList.add('disabled');

    // Seçiler kargo adresi, fatura adresi ve ürünleriform nesnesi haline getirip xhr ile session'a atalaım
    var form = new FormData();
    form.append('cargoAddressID', document.querySelector('input[name="cargoAddressID"]:checked').value);
    form.append('invoiceAddressID', document.querySelector('input[name="invoiceAddressID"]:checked').value);
    document.querySelectorAll('input[name="cartItem[]"]:checked').forEach(function (element) {
        form.append('cartItem[]', element.value);
    });
    var csrfToken = document.querySelector('#csrf_token-paymentForm').value;
    form.append('csrf_token', csrfToken);
    form.append('invoiceName', invoiceName);
    form.append('invoiceTaxOffice', invoiceTaxOffice);
    form.append('invoiceTaxNumber', invoiceTaxNumber);
    form.append('customerNote', customerNote);
    form.append('languageCode', languageCode);
    form.append('action', 'submit');

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/?/control/checkout/post/submit");
    xhr.send(form);

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log(xhr.responseText);
            response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {

                showPopupCheckout('success', response.message);
                // Eğer session'a başarılı bir şekilde atıldıysa, 2 saniye sonra formu gönderelim
                setTimeout(function () {
                    var href = document.querySelector('.cart-summary a#submit').href;
                    window.location.href = href;
                }, 500);

            }
            else {
                //butonu enable yapalım
                document.querySelector('.cart-summary a#submit').classList.remove('disabled');
                // Eğer session'a atılırken bir hata oluştuysa, popup mesajı gösterelim
                showPopupCheckout('error', 'Bir hata oluştu. Lütfen tekrar deneyiniz.');
            }
        }
    }
});
function showPopupCheckout(type, message) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `/?/control/popup/get/show&type=${type}&message=${message}&languageCode=${languageCode}&autoClose=false`);
    xhr.send();

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
            loadScript('/Public/JS/popup.min.js?v=4');
        }
    };
}