
// svg-icon-container basket sınıfına sahip elementi bul
var basketIcon = document.querySelector('.svg-icon-container.basket a');

// aside-right-cart sınıfına sahip elementi bul
var asideCart = document.querySelector('.aside-right-cart');

// mouseover olayını dinle
if (basketIcon) {
    basketIcon.addEventListener('click', function () {
        // active sınıfını aside-right-cart elementine ekle
        //linke gitmesini önleyelim
        event.preventDefault();
        asideCart.classList.add('active');
    });
}

// aside-right-cart-close sınıfına sahip elementi bul
var closeIcon = document.querySelector('.aside-right-cart-close');

// aside-right-cart sınıfına sahip elementi bul
//var asideCart = document.querySelector('.aside-right-cart');

// click olayını dinle
if (closeIcon) {
    closeIcon.addEventListener('click', function () {
        // active sınıfını aside-right-cart elementinden kaldır
        asideCart.classList.remove('active');
    });
}