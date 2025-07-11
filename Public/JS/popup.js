document.querySelectorAll('.popup-close').forEach(function(closeButton) {
    closeButton.addEventListener('click', function () {
        var popup = closeButton.closest('.popup');
        if (popup) {
            // Popup'ı sil
            popup.remove();
            // İlgili scripti sil
            var script = document.querySelector(`script[src="/Public/JS/popup.min.js?v=4"]`);
            if (script) {
                script.remove();
            }
        }
        else{
            console.error('No popup found for close button');
        }
    });
});