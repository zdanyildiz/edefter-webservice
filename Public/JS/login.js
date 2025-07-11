var languageCode = document.documentElement.lang;
document.querySelector('#remind-password').addEventListener('click', function () {
    document.querySelector('.remind-password-form-modal').classList.add('active');
}
);
document.querySelector('.close-remind-password-form-modal').addEventListener('click', function () {
    document.querySelector('.remind-password-form-modal').classList.remove('active');
}
);

function resetTurnstile() {
    // --- TURNSTILE RESETLEME KISMI ---
    // Kullanıcı sayfada kalıyorsa ve tekrar deneme ihtimali varsa Turnstile'ı resetle.
    const turnstileContainerElement = document.getElementById('turnstile-container');
    if (typeof turnstile !== 'undefined' && turnstileContainerElement) {
        try {
            console.log("Turnstile widget'ı sıfırlanıyor (#turnstile-container).");
            turnstile.reset(turnstileContainerElement);
            // turnstile.reset() çağrıldıktan sonra, eğer Turnstile yeni bir token üretebilirse,
            // sizin body.php'deki `onloadTurnstileCallback` içindeki `callback: (token) => { ... }`
            // fonksiyonunuz otomatik olarak yeni token ile tekrar çalışacak ve
            // tüm formlardaki ilgili hidden input'ları güncelleyecektir.
        } catch (e) {
            console.error("Turnstile sıfırlanırken hata oluştu:", e);
        }
    }
    // --- TURNSTILE RESETLEME KISMI BİTTİ ---
}

//#remindPasswordForm formunu submit edildiğinde form alanlarını kontrol edelim.
document.querySelector('#remindPasswordForm').addEventListener('submit', function (event) {
    event.preventDefault();
    var form = document.querySelector('#remindPasswordForm');
    var action = form.querySelector('input[name="action"]').value;
    var languageCode = form.querySelector('#languageCode').value;
    var email = form.querySelector('#email-remindPasswordForm').value;
    var websites = form.querySelector("#websites-remindPasswordForm").value;
    var cfToken = form.querySelector('#cf-token-remind-password-form');
    var csrfToken = form.querySelector('#csrf_token-remind-password-form').value;

    if (!validateEmailAddress(email)) {
        //alert('Geçersiz e-posta adresi');
        showPopup("error", "Geçersiz e-posta adresi");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#email').focus();
        event.preventDefault();
        return;
    }

    var sendForm = new FormData();
    sendForm.append('action', action);
    sendForm.append('languageCode', languageCode);
    sendForm.append('email', email);
    sendForm.append('websites', websites);
    sendForm.append('cf-turnstile-response', cfToken.value);
    sendForm.append('csrf_token', csrfToken);

    //formu xhr ile gönderelim
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/?/control/member/post/remindPasswordByEmail', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Başlığı ayarlıyoruz
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

document.querySelector('#memberReqisterForm').addEventListener('submit', function (event) {
    event.preventDefault();
    var form = document.querySelector('#memberReqisterForm');
    var websites = form.querySelector("#websites-memberReqisterForm").value;
    var action = form.querySelector('input[name="action"]').value;
    var email = form.querySelector('#email-memberReqisterForm').value;
    if (!validateEmailAddress(email)) {
        //alert('Geçersiz e-posta adresi');
        showPopup("error", "Geçersiz e-posta adresi");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#email-memberReqisterForm').focus();
        event.preventDefault();
        return;
    }
    var phone = form.querySelector('#phone-memberReqisterForm').value;
    if (!validatePhoneNumber(phone)) {
        //alert('Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.');
        showPopup("error", "Geçersiz telefon numarası. Telefon numarası 10 haneli olmalı ve +90, 90 veya 0 ile başlamamalı.");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#phone-memberReqisterForm').focus();
        event.preventDefault();
        return;
    }
    var name = form.querySelector('#name-memberReqisterForm').value;
    var surname = form.querySelector('#surname-memberReqisterForm').value;
    //boş olamaz
    if (name === "" || surname === "") {
        //alert('Ad ve Soyad alanları boş olamaz');
        showPopup("error", "Ad ve Soyad alanları boş olamaz");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#name-memberReqisterForm').focus();
        event.preventDefault();
        return;
    }

    var password = form.querySelector('#password-memberReqisterForm').value;
    if (!validatePassword(password)) {
        //alert('Şifre alanları boş olamaz');
        showPopup("error", "Şifre en az 8 karakterden oluşmalı, en fazla 20 karakter olmalı ve en az bir küçük harf, en az bir büyük harf olmalı");
        // göndermeyi durduralım ve ilgili alana gidelim
        form.querySelector('#password-memberReqisterForm').focus();
        event.preventDefault();
        return;
    }

    var csrfToken = form.querySelector('#csrf_token-register-form').value;
    var cfToken = form.querySelector('#cf-token-register-form').value;


    var sendForm = new FormData();
    sendForm.append('action', action);
    sendForm.append('languageCode', languageCode);
    sendForm.append('name', name);
    sendForm.append('surname', surname);
    sendForm.append('email', email);
    sendForm.append('phone', phone);
    sendForm.append('password', password);
    sendForm.append('csrf_token', csrfToken);
    sendForm.append('websites', websites);
    sendForm.append('cf-turnstile-response', cfToken);

    //formu xhr ile gönderelim
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/?/control/member/post/register', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Başlığı ayarlıyoruz
    xhr.send(sendForm);

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log(xhr.responseText);
            response = JSON.parse(xhr.responseText);
            $status = response.status;
            $message = response.message;

            showPopup($status, $message);

            // Otomatik login durumunda ana sayfaya yönlendir
            if ($status === 'success' && response.autoLogin === true) {
                // 2 saniye sonra ana sayfaya yönlendir
                setTimeout(function () {
                    window.location.href = response.redirectUrl || '/';
                }, 2000);
            }

        } else {
            //alert('Bir hata oluştu. Lütfen tekrar deneyin');
            showPopup("error", "Bir hata oluştu. Lütfen tekrar deneyin");
        }

        resetTurnstile();
    };
});

document.querySelector("#memberLoginForm").addEventListener('submit', function (event) {
    event.preventDefault();
    let form = document.querySelector('#memberLoginForm');
    var websites = form.querySelector("#websites-memberLoginForm").value;
    let action = form.querySelector('input[name="action"]').value;
    let email = form.querySelector('#email-memberLoginForm').value;
    let password = form.querySelector('#password-memberLoginForm').value;
    let remember = form.querySelector('#remember').value;
    var csrfToken = form.querySelector('#csrf_token-login-form').value;
    var cfToken = form.querySelector('#cf-token-login-form').value;

    let sendForm = new FormData();
    sendForm.append('action', action);
    sendForm.append('languageCode', languageCode);
    sendForm.append('email', email);
    sendForm.append('password', password);
    sendForm.append('remember', remember);
    sendForm.append('csrf_token', csrfToken);
    sendForm.append('websites', websites);
    sendForm.append('cf-turnstile-response', cfToken);

    //formu xhr ile gönderelim
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/?/control/member/post/login', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Başlığı ayarlıyoruz
    xhr.send(sendForm);

    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log(xhr.responseText);
            response = JSON.parse(xhr.responseText);
            $status = response.status;
            $message = response.message;

            showPopup($status, $message);
            if ($status === 'success') {
                document.location.href = "/";
            }

        } else {
            //alert('Bir hata oluştu. Lütfen tekrar deneyin');
            showPopup("error", "Bir hata oluştu. Lütfen tekrar deneyin");
        }
        resetTurnstile();
    };
});

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

//en az 8 en fazla 20 haneli olmalı, n az bir büyük bir de küçük harf olmalı
function validatePassword(password) {
    if (password.length < 8) {
        return false;
    }
    if (password.length > 20) {
        return false;
    }
    if (!/[a-z]/.test(password)) {
        return false;
    }
    if (!/[A-Z]/.test(password)) {
        return false;
    }
    //if(!/[0-9]/.test(password)){
    // return false;
    //}
    return true;
}