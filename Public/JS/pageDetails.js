function initializeThumbnailModal(target) {
    // Tüm küçük resimleri seç
    var thumbnails = document.querySelectorAll(target);

    if (!thumbnails || thumbnails.length === 0) return; // Eğer küçük resim yoksa çıkış yap

    let currentIndex = 0;
    let prev = document.querySelector('.prev');
    let next = document.querySelector('.next');
    let modal = document.getElementById('pageModal');
    let modalImg = document.querySelector('.modal-img');
    let span = document.querySelector('.close');

    function updateDimensionsInSrc(src) {
        if (src.includes("&height=")) {
            return src.replace(/&height=\d+/g, "&height=450");
        } else {
            return src;
        }
    }

    // Her bir küçük resim için bir "mouseover" olayı ekleyin
    thumbnails.forEach(function(thumbnail) {
        thumbnail.addEventListener('mouseover', function() {
            var newSrc = this.getAttribute('data-src') + "&height=450";
            var mainImage = document.querySelector('.page-image img');
            if (mainImage) {
                mainImage.height = 450;
                mainImage.setAttribute('src', newSrc);
            }
        });
    });

    // Thumbnail tıklama
    thumbnails.forEach(function(thumbnail, index) {
        thumbnail.addEventListener('click', function() {
            currentIndex = index;
            var newSrc = thumbnail.getAttribute('data-src');
            newSrc = updateDimensionsInSrc(newSrc);
            let windowWidth = window.innerWidth;
            let windowHeight = window.innerHeight;

            modalImg.src = newSrc + '&width=' + windowWidth +'&height=' + windowHeight;
            modal.style.display = "block";
            modalImg.classList.add('zoom');
        });
    });

    // Sol ok
    prev.addEventListener('click', function() {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : thumbnails.length - 1;
        var newSrc = thumbnails[currentIndex].getAttribute('data-src');
        newSrc = updateDimensionsInSrc(newSrc);
        //pencere genişlik ve yüksekliğini alalım
        let windowWidth = window.innerWidth;
        let windowHeight = window.innerHeight;

        modalImg.src = newSrc + '&width=' + windowWidth +'&height=' + windowHeight;
        modalImg.classList.add('zoom');
    });

    // Sağ ok
    next.addEventListener('click', function() {
        currentIndex = (currentIndex < thumbnails.length - 1) ? currentIndex + 1 : 0;
        var newSrc = thumbnails[currentIndex].getAttribute('data-src');
        newSrc = updateDimensionsInSrc(newSrc);

        let windowWidth = window.innerWidth;
        let windowHeight = window.innerHeight;

        modalImg.src = newSrc + '&width=' + windowWidth +'&height=' + windowHeight;
        modalImg.classList.add('zoom');
    });

    // Modal Kapatma
    span.addEventListener('click', function() {
        modal.style.display = "none";
    });
}

var thumbnailContainer = document.querySelector('.thumbnail-container');
if (thumbnailContainer) {
    initializeThumbnailModal('.page-image .thumbnail');
}
//galleryImages
var galleryImages = document.querySelector('.galleryImages');
if (galleryImages) {
    initializeThumbnailModal('.galleryImages .thumbnail');
}