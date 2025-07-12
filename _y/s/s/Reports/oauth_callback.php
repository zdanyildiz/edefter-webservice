<?php require_once($_SERVER['DOCUMENT_ROOT']."/_y/s/global.php"); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Google Yönlendirme - Pozitif E-Ticaret</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Google ile bağlantı kuruluyor, lütfen bekleyin...</h1>

    <script src="/_y/assets/js/libs/jquery/jquery-1.11.2.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');

            if (code) {
                $.ajax({
                    url: '/webservice/google/get/callback?code=' + code, // Yeni URL
                    type: 'GET', // GET isteği
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Google hesabınız başarıyla bağlandı!');
                            window.location.href = '/_y/s/s/Reports/index.php';
                        } else {
                            alert('Hata: ' + response.message);
                            window.location.href = '/_y/s/s/Reports/index.php';
                        }
                    },
                    error: function() {
                        alert('Token alınırken bir hata oluştu.');
                        window.location.href = '/_y/s/s/Reports/index.php';
                    }
                });
            } else {
                alert('Yetkilendirme kodu bulunamadı.');
                window.location.href = '/_y/s/s/Reports/index.php';
            }
        });
    </script>
</body>
</html>
