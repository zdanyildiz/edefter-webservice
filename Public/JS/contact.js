//jquery kullanmayalım js ile #contactForm submit dinleyelim
function resetTurnstile(){
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
document.getElementById("contactForm").addEventListener("submit", function (e) {
    e.preventDefault();
    var form = e.target;
    //console.log(form);
    //namesurname,email,telephone,message boş olamaz

    if (form.namesurname.value === "" || form.email.value === "" || form.phone.value === "" || form.message.value === "") {
        showPopup("error", "Please fill in all fields.");
        return;
    }

    var formData = new FormData(form);
    //console.log(formData);
    var languageCode = document.documentElement.lang;
    var csrfToken = document.getElementById("csrf_token-contact-form").value;
    var cfTurnStile = document.getElementById("cf-token-contact-form").value;
    
    formData.append("languageCode", languageCode);
    formData.append("cf-turnstile-response", cfTurnStile);
    formData.append("csrfToken", csrfToken);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/?/control/form/post/contactForm", true);
    xhr.send(formData);
    xhr.onload = function () {
        if (xhr.status === 200) {
            //console.log(xhr.responseText);
            var response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                showPopup("success", response.message);
                form.reset();
            } else {
                showPopup("error", response.message);
            }
        }
        resetTurnstile();
    };
});