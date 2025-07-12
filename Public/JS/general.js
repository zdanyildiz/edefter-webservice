var languageCode = document.documentElement.lang;
// svg-icon-container basket sınıfına sahip elementi bul
var userIcon = document.querySelector('.svg-icon-container.member');
// aside-right-cart sınıfına sahip elementi bul
var asideUser = document.querySelector('.aside-left-user');
// mouseover olayını dinle
if(userIcon){
    userIcon.addEventListener('click', function() {
        // active sınıfını aside-right-cart elementine ekle
        event.preventDefault();
        asideUser.classList.add('active');
    });
}

// aside-right-cart-close sınıfına sahip elementi bul
var closeIcon = document.querySelector('.aside-left-user-close');
// click olayını dinle
if(closeIcon){
    closeIcon.addEventListener('click', function() {
        // active sınıfını aside-right-cart elementinden kaldır
        asideUser.classList.remove('active');
    });
}

var showAsideLeft = document.querySelector('#show-aside-left');
if(showAsideLeft){
    showAsideLeft.addEventListener('click', function() {
        var asideLeftUser = document.querySelector('.aside-left-user');
        if (asideLeftUser.classList.contains('active')) {
            asideUser.classList.remove('active');
            showAsideLeft.innerHTML = '<label class="arrow-label">></label>';
        } else {
            asideUser.classList.add('active');
            showAsideLeft.innerHTML = '<label class="arrow-label"><</label>';
        }
    });
}

//.svg-icon-container.search için dinleyici ekle, tıklandığında svg-icon-container'a mobile-search-active sınıfını ekle
if(document.querySelector('.svg-icon-container.search')){
    document.querySelector('.svg-icon-container.search').addEventListener('click', function(event) {
        event.preventDefault();
        var searchIcon = document.querySelector('.product-search-container');
        searchIcon.classList.add('mobile-search-active');
    });
}

//.close-search sınıfına sahip elemente tıklandığında mobile-search-active sınıfını kaldır
if(document.querySelector('.close-search')){
    document.querySelector('.close-search').addEventListener('click', function(event) {
        event.preventDefault();
        var searchIcon = document.querySelector('.product-search-container');
        searchIcon.classList.remove('mobile-search-active');
    });
}

function removeTrailingZeros(value) {
    console.log('removeTrailingZeros value: ' + value);
    return value.toString().replace(/\.?0+$/, '');
}

function showPopup(type, message) {
    //mesaj url ile gönderilirken bozulmasın
    message = encodeURIComponent(message);
    const xhr = new XMLHttpRequest();
    //console.log(`/?/control/popup/get/show&type=${type}&message=${message}&languageCode=${languageCode}`);
    xhr.open("GET", `/?/control/popup/get/show&languageCode=${languageCode}&type=${type}&message=${message}`);
    xhr.send();

    xhr.onload = function () {
        if (xhr.status === 200) {
            document.body.insertAdjacentHTML("beforeend", xhr.responseText);
        }
    };
}

const logoutBtn = document.querySelector('#logoutLink');

// Eğer sayfada çıkış butonu varsa (kullanıcı giriş yapmışsa)
if (logoutBtn) {
    
    logoutBtn.addEventListener('click', function(event) {
        
        // Linkin varsayılan yönlendirme davranışını hemen engelliyoruz.
        event.preventDefault(); 
        
        // Yönlendirilecek adresi linkin href'inden alıyoruz.
        const logoutUrl = this.href;

        // Yönlendirme fonksiyonunu tanımlıyoruz.
        const performLogout = function() {
            // Eğer bir URL varsa oraya yönlendir, yoksa ana sayfaya git.
            document.location.href = logoutUrl || '/'; 
        };

        // Google'a 'logout' olayını gönderiyoruz.
        console.log('Logout olayı GA4\'e gönderiliyor...');
        gtag('event', 'logout', {
            // Olay gönderildikten sonra performLogout fonksiyonunu çalıştır.
            'event_callback': performLogout,
            // 2 saniye içinde olay gönderilemezse yine de çıkış yap.
            'event_timeout': 2000 
        });
    });
}