// Tüm küçük resimleri seç
var thumbnails = document.querySelectorAll('.product-image-thumb .thumbnail');

// Her bir küçük resim için bir "mouseover" olayı ekleyin
thumbnails.forEach(function(thumbnail) {
    thumbnail.addEventListener('mouseover', function() {
        // Küçük resmin "data-src" özelliğini al
        var newSrc = this.getAttribute('data-src');

        // Ana resmi seç
        var mainImage = document.querySelector('.product-image img');

        // Ana resmin "src" özelliğini küçük resmin "data-src" özelliği ile değiştir
        mainImage.setAttribute('src', newSrc);
    });
});

// Tüm küçük resimleri seç
var thumbnails = document.querySelectorAll('.product-image-thumb .thumbnail');

// Modalı ve içerisindeki öğeleri seç
var modal = document.getElementById('myModal');
var modalImg = document.querySelector('.modal-img');
var span = document.querySelector('.close');
var prev = document.querySelector('.prev');
var next = document.querySelector('.next');

// Her bir küçük resim için bir "click" olayı ekleyin
thumbnails.forEach(function(thumbnail, index) {
    thumbnail.addEventListener('click', function() {
        // Küçük resmin "data-src" özelliğini al
        var newSrc = this.getAttribute('data-src');

        newSrc = updateDimensionsInSrc(newSrc);

        // Modalı aç ve modal resminin "src" özelliğini küçük resmin "data-src" özelliği ile değiştir
        modalImg.src = newSrc;
        modal.style.display = "block";
        modalImg.classList.add('zoom'); // Animasyonu başlat


        // Animasyon bittiğinde 'zoom' sınıfını kaldır
        modalImg.addEventListener('animationend', function() {
            this.classList.remove('zoom');
        });
    });

    // Sol ok düğmesine bir "click" olayı ekleyin
    prev.addEventListener('click', function() {
        index = (index > 0) ? index - 1 : thumbnails.length - 1;

        var newSrc = thumbnails[index].getAttribute('data-src');
        newSrc = updateDimensionsInSrc(newSrc);
        modalImg.src = newSrc;
        modalImg.classList.add('zoom'); // Animasyonu başlat
        // Animasyon bittiğinde 'zoom' sınıfını kaldır
        modalImg.addEventListener('animationend', function() {
            this.classList.remove('zoom');
        });
    });

    // Sağ ok düğmesine bir "click" olayı ekleyin
    next.addEventListener('click', function() {
        index = (index < thumbnails.length - 1) ? index + 1 : 0;

        var newSrc = thumbnails[index].getAttribute('data-src');
        newSrc = updateDimensionsInSrc(newSrc);
        modalImg.src = newSrc;
        modalImg.classList.add('zoom'); // Animasyonu başlat
        // Animasyon bittiğinde 'zoom' sınıfını kaldır
        modalImg.addEventListener('animationend', function() {
            this.classList.remove('zoom');
        });
    });
});

// Kapat düğmesine bir "click" olayı ekleyin
span.addEventListener('click', function() {
    modal.style.display = "none";
});
function updateDimensionsInSrc(newSrc) {
    var screenWidth = window.innerWidth;
    var screenHeight = window.innerHeight;
    var dataSrcWidth = newSrc.match(/&width=(\d+)/);
    var dataSrcHeight = newSrc.match(/&height=(\d+)/);
    if(dataSrcWidth && dataSrcHeight){
        newSrc = newSrc.replace(dataSrcWidth[1], screenWidth);
        newSrc = newSrc.replace(dataSrcHeight[1], screenHeight);
    }

    return newSrc;
}

// Thumbnail resimlere event listener ekliyoruz
document.querySelectorAll('.thumbnail-container .thumbnail').forEach(function(thumbnail) {
    thumbnail.addEventListener('mouseover', function() {
        // Ana resmin src'sini thumbnail'in data-src'si ile değiştiriyoruz
        var container = thumbnail.closest('.product-image-container');
        var image = container.querySelector('.product-image.none');
        //src varsa değiştir
        if (image && thumbnail.dataset.src) {
            image.src = thumbnail.dataset.src;
        }
    });
});
function formatPrice(price) {
    return price.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
