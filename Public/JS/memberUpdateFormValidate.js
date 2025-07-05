function validateUserUpdateForm(event) {
    var form = document.querySelector('.member-card form');
    var inputs = form.querySelectorAll('input');
    var isValid = true;

    inputs.forEach(function(input) {
        if (input.value.trim() === '') {
            //alert(input.name + ' alanı boş olamaz');
            showPopup("error", input.name + ' alanı boş olamaz');
            // göndermeyi durduralım ve ilgili alana gidelim
            input.focus();
            event.preventDefault();
            return;
            isValid = false;
        }
    });

    var tcKimlik = form.querySelector('#identificationNumber').value;
    if (!validateTCKimlik(tcKimlik)) {
        //alert('Geçersiz TC Kimlik numarası');
        showPopup("error", "Geçersiz TC Kimlik numarası");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#identificationNumber').focus();
        event.preventDefault();
        return;
        isValid = false;
    }

    var telefon = form.querySelector('#telephone').value;
    if (!validatePhoneNumber(telefon)) {
        //alert('Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.');
        showPopup("error", "Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#telephone').focus();
        event.preventDefault();
        return;
        isValid = false;
    }

    var email = form.querySelector('#email').value;
    if (!validateEmailAddress(email)) {
        //alert('Geçersiz e-posta adresi');
        showPopup("error", "Geçersiz e-posta adresi");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#email').focus();
        event.preventDefault();
        return;
        isValid = false;
    }

    var vergiNo = form.querySelector('#invoiceTaxNumber').value;
    if (!validateVergiNo(vergiNo)) {
        //alert('Geçersiz Vergi Numarası');
        showPopup("error", "Geçersiz Vergi Numarası");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#invoiceTaxNumber').focus();
        event.preventDefault();
        return;
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
}
function validatePasswordUpdateForm(event) {
    var form = document.querySelector('.member-password-form-container form');
    var inputs = form.querySelectorAll('input');
    var isValid = true;

    inputs.forEach(function(input) {
        if (input.value.trim() === '') {
            //alert(input.name + ' alanı boş olamaz');
            //_form_alanlaribosolamaz_yazi
            showPopup("error", input.name + ' alanı boş olamaz');
            // göndermeyi durduralım ve ilgili alana gidelim
            input.focus();
            event.preventDefault();
            return;
            isValid = false;
        }
    });

    var password = form.querySelector('#newPassword').value;
    if (password.length < 6) {
        //alert('Şifre en az 6 karakter olmalıdır');
        //_popup_sifre_yazi
        showPopup("error", "Şifre en az 6 karakter olmalıdır");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#newPassword').focus();
        event.preventDefault();
        return;
        isValid = false;
    }

    var oldPassword = form.querySelector('#password').value;
    if (oldPassword === password) {
        //alert('Yeni şifre eski şifre ile aynı olamaz');
        //_form_yenisifre_eskisi_olamaz
        showPopup("error", "Yeni şifre eski şifre ile aynı olamaz");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#newPassword').focus();
        event.preventDefault();
        return;
        isValid = false;
    }
    var passwordRepeat = form.querySelector('#newPasswordRe').value;
    if (password !== passwordRepeat) {
        //alert('Şifreler uyuşmuyor');
        //_form_sifreniztekraritutmuyor_yazi
        showPopup("error", "Şifreler uyuşmuyor");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#newPasswordRe').focus();
        event.preventDefault();
        return;
        isValid = false;
    }
    if (!isValid) {
        event.preventDefault();
    }
}
function validateTCKimlik(value) {
    value = value.toString();
    if(value=="11111111111")
    {
        return true;
    }
    else if(value.length!= 11)
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
    if (phoneNumber.startsWith("+90") || phoneNumber.startsWith("90") || phoneNumber.startsWith("0")) {
        return false;
    }
    // Telefon numarasının sadece sayılardan oluştuğunu kontrol edin.
    if (!/^\d+$/.test(phoneNumber)) {
        return false;
    }
    // Telefon numarası geçerlidir.
    return true;;
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

if(document.querySelector('.member-card form')) {
    document.querySelector('.member-card form').addEventListener('submit', validateUserUpdateForm);
}
if(document.querySelector('.member-password-container form')) {
    document.querySelector('.member-password-container form').addEventListener('submit', validatePasswordUpdateForm);
}
if(document.querySelector('#remind-password-in-profile')) {
    document.querySelector('#remind-password-in-profile').addEventListener('click', function () {
        document.querySelector('.remind-password-form-modal').classList.add('active');
    });
}
if(document.querySelector('.close-remind-password-form-modal')) {
    document.querySelector('.close-remind-password-form-modal').addEventListener('click', function () {
        document.querySelector('.remind-password-form-modal').classList.remove('active');
    });
}

if(document.querySelector('#remindPasswordForm')) {
    document.querySelector('#remindPasswordForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var form = document.querySelector('#remindPasswordForm');
        var action = form.querySelector('input[name="action"]').value;
        var languageCode = form.querySelector('#languageCode').value;
        var csrfToken = form.querySelector('#csrf_token-reminPasswordForm').value;
        var email = form.querySelector('#email-remindPasswordFormInProfile').value;

        if (!validateEmailAddress(email)) {
            //alert('Geçersiz e-posta adresi');
            showPopup("error", "Geçersiz e-posta adresi");
            // göndermeyi durduralım ve ilgili alana gidelim
            form.querySelector('#email-remindPasswordFormInProfile').focus();
            event.preventDefault();
            return;
        }

        var sendForm = new FormData();
        sendForm.append('email', email);
        sendForm.append('languageCode', languageCode);
        sendForm.append('action', action);
        sendForm.append('csrf_token', csrfToken);


        //formu xhr ile gönderelim
        var xhr = new XMLHttpRequest();
        xhr.open('POST', "/?/control/member/post/remindPasswordByEmailWithUserID", true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(sendForm);

        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                response = JSON.parse(xhr.responseText);
                $status = response.status;
                $message = response.message;

                showPopup($status, $message);

                if ($status === 'success') {
                    document.querySelector('.remind-password-form-modal').classList.remove('active');
                }

            } else {
                //alert('Bir hata oluştu. Lütfen tekrar deneyin');
                showPopup("error", "Bir hata oluştu. Lütfen tekrar deneyin");
            }
        };
    });
}