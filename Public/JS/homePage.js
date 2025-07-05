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
