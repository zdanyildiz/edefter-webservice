<?php

session_start();
require_once 'db_connect.php';

// "Beni Hatırla" kontrolü
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
    } else {
        setcookie('remember_me', '', time() - 3600, "/"); // Çerezi sil
    }
}

// Oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Abonelik kontrolü
$stmt = $pdo->prepare("SELECT subscription_end FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (new DateTime() > new DateTime($user['subscription_end'])) {
    $_SESSION['error'] = "Abonelik süreniz dolmuş. Lütfen yenileyin.";
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>e-defter berat to HTML Dönüştürücü Global Pozitif Teknolojiler</title>
    <style>
        html, body { margin: 0 auto; text-align: center; font-family: Arial, sans-serif; }
        #drop_zone {
            width: 400px;
            height: 200px;
            border: 2px dashed #ccc;
            text-align: center;
            padding: 20px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 5px;
        }
        #drop_zone.dragover { background-color: #e1e1e1; }
        .logo { max-width: 200px; margin: 10px auto; display: block; }
        .results {
            margin-top: 20px;
            text-align: center;
            padding: 15px;
            max-width: 600px;
            margin: 20px auto;
            border: 2px dashed #007bff;
            border-radius: 8px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .result-item {
            margin: 15px 0;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .result-item a {
            text-decoration: none;
            color: #007bff;
            padding: 8px 12px;
            border-radius: 4px;
            background-color: #e9ecef;
            transition: all 0.3s ease;
            margin: 0 5px;
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
        }
        .result-item a:hover {
            background-color: #007bff;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
        }
        .result-item a:active {
            transform: scale(0.98);
        }
        .error {
            color: #dc3545;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <img src="https://globalpozitif.com.tr/m/r/logo/logo-GU7RQ.png" alt="Global Pozitif Teknolojiler" class="logo">
    <h1>E-Defter Berat Görüntüle İndir</h1>
    <div id="drop_zone">XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)</div>
    <input type="file" id="file_input" style="display:none;" accept=".xml" multiple>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xml_file'])) {
        $xslt_file = "berat.xslt";
        $output_dir = __DIR__ . "/outputs";

        // Çıktı klasörünü kontrol et ve oluştur
        if (!is_dir($output_dir)) {
            mkdir($output_dir, 0777, true);
        }

        // Hata veya sonuç mesajlarını sakla
        $results = [];

        if (!file_exists($xslt_file)) {
            $results[] = "<p class='error'>Hata: berat.xslt dosyası bulunamadı!</p>";
        } elseif (!class_exists('XSLTProcessor')) {
            $results[] = "<p class='error'>Hata: XSLTProcessor sınıfı bulunamadı. Lütfen php-xsl uzantısını etkinleştirin!</p>";
        } else {
            // Birden fazla dosya için döngü
            foreach ($_FILES['xml_file']['tmp_name'] as $index => $tmp_file) {
                $file_name = $_FILES['xml_file']['name'][$index];

                // Dosya uzantısını kontrol et
                if (pathinfo($file_name, PATHINFO_EXTENSION) !== 'xml') {
                    $results[] = "<p class='error'>Hata: '$file_name' yalnızca XML dosyaları kabul edilir!</p>";
                    continue;
                }

                // Rastgele sayı ile benzersiz dosya adı oluştur
                $random_prefix = rand(1000, 9999);
                $output_file = "$output_dir/{$random_prefix}_output.html";
                $relative_output_file = "outputs/{$random_prefix}_output.html";

                try {
                    $xml = new DOMDocument();
                    $xml->load($tmp_file);

                    $xslt = new DOMDocument();
                    $xslt->load($xslt_file);

                    $proc = new XSLTProcessor();
                    $proc->importStylesheet($xslt);

                    $result = $proc->transformToDoc($xml);
                    $result->save($output_file);

                    $results[] = "<div class='result-item'>Çıktı oluşturuldu: <a href='$relative_output_file' download>İndirmek için tıklayınız ($file_name)</a> | <a id='view_link_$random_prefix' href='$relative_output_file' target='_blank'>Görüntülemek için tıklayınız</a></div>";
                    echo "<script>document.getElementById('view_link_$random_prefix').click();</script>";
                } catch (Exception $e) {
                    $results[] = "<p class='error'>Hata: '$file_name' dosyası işlenirken bir hata oluştu: " . $e->getMessage() . "</p>";
                }
            }
        }

        // Sonuçları ekranda göster
        if (!empty($results)) {
            echo "<div class='results'>";
            foreach ($results as $result) {
                echo $result;
            }
            echo "</div>";
        }
    }
    ?>

    <script>
        const dropZone = document.getElementById('drop_zone');
        const fileInput = document.getElementById('file_input');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            uploadFiles(files);
        });

        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            uploadFiles(fileInput.files);
        });

        function uploadFiles(files) {
            let xmlFiles = Array.from(files).filter(file => file.name.endsWith('.xml'));
            if (xmlFiles.length === 0) {
                alert('Lütfen bir veya daha fazla XML dosyası seçin!');
                return;
            }

            dropZone.innerHTML = "Dönüştürülüyor...";
            const formData = new FormData();
            xmlFiles.forEach(file => formData.append('xml_file[]', file));

            fetch('', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(html => {
                    document.body.innerHTML = html;
                    dropZone.innerHTML = "XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)";
                    document.querySelectorAll('[id^="view_link_"]').forEach(link => link.click());
                });
        }
    </script>
</body>
</html>