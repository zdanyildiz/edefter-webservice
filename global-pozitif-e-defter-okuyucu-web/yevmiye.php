<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yevmiye Raporu Dönüştürücü - Global Pozitif Teknolojiler</title>
    <style>
        html, body { margin: 0 auto; text-align: center; font-family: Arial, sans-serif; }
        #drop_zone {
            width: 400px;
            height: 200px;
            border: 2px dashed #ccc;
            text-align: center;
            padding: 20px;
            margin: 20px auto;
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
            border: 2px dashed #007bff; /* Noktalı mavi çerçeve */
            border-radius: 8px;
            background-color: #f8f9fa; /* Hafif gri arka plan */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .result-item {
            margin: 15px 0;
            font-size: 14px;
            color: #333;
            line-height: 1.6; /* Satır aralığı */
        }
        .result-item a {
            text-decoration: none;
            color: #007bff; /* Mavi link rengi */
            padding: 8px 12px;
            border-radius: 4px;
            background-color: #e9ecef; /* Hafif gri arka plan */
            transition: all 0.3s ease;
            margin: 0 5px;
            display: inline-block; /* Linklerin düzgün hizalanması */
            font-weight: bold; /* Kalın yazı */
            text-transform: uppercase; /* Büyük harf */
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
            color: #dc3545; /* Kırmızı hata rengi */
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<img src="https://globalpozitif.com.tr/m/r/logo/logo-GU7RQ.png" alt="Global Pozitif Teknolojiler" class="logo">
<h1>Yevmiye Raporu Görüntüle İndir</h1>
<div id="drop_zone">XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)</div>
<input type="file" id="file_input" style="display:none;" accept=".xml" multiple>
<div id="results"></div> <!-- Sonuçlar buraya dinamik olarak eklenecek -->

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xml_file'])) {
    $xslt_file = "yevmiye.xslt";
    $output_dir = __DIR__ . "/outputs";

    // Çıktı klasörünü kontrol et ve oluştur
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0777, true);
    }

    // Hata veya sonuç mesajlarını sakla
    $results = [];
    $errors = [];

    if (!file_exists($xslt_file)) {
        $errors[] = "Hata: yevmiye.xslt dosyası bulunamadı!";
    } elseif (!class_exists('XSLTProcessor')) {
        $errors[] = "Hata: XSLTProcessor sınıfı bulunamadı. Lütfen php-xsl uzantısını etkinleştirin!";
    } else {
        // Birden fazla dosya için döngü
        foreach ($_FILES['xml_file']['tmp_name'] as $index => $tmp_file) {
            $file_name = $_FILES['xml_file']['name'][$index];

            // Dosya uzantısını kontrol et
            if (pathinfo($file_name, PATHINFO_EXTENSION) !== 'xml') {
                $errors[] = "Hata: '$file_name' yalnızca XML dosyaları kabul edilir!";
                continue;
            }

            // Rastgele sayı ile benzersiz dosya adı oluştur
            $random_prefix = rand(1000, 9999);
            $output_file = "$output_dir/{$random_prefix}_output_yevmiye.html";
            $relative_output_file = "outputs/{$random_prefix}_output_yevmiye.html";

            try {
                $xml = new DOMDocument();
                $xml->load($tmp_file);

                $xslt = new DOMDocument();
                if (!$xslt->load($xslt_file)) {
                    $errors[] = "Hata: $xslt_file dosyası yüklenemedi!";
                    continue;
                }

                $proc = new XSLTProcessor();
                if (!$proc->importStylesheet($xslt)) {
                    $errors[] = "Hata: XSLT stylesheet import edilemedi. Hata: " . $proc->getLastError();
                    continue;
                }

                $result = $proc->transformToDoc($xml);
                if (!$result->save($output_file)) {
                    $errors[] = "Hata: $output_file dosyası kaydedilemedi! Klasör izinlerini kontrol edin.";
                    continue;
                }

                $results[] = "<div class='result-item'>Çıktı oluşturuldu: <a href='$relative_output_file' download>İndirmek için tıklayınız ($file_name)</a> | <a id='view_link_$random_prefix' href='$relative_output_file' target='_blank'>Görüntülemek için tıklayınız</a></div>";
            } catch (Exception $e) {
                $errors[] = "Hata: '$file_name' dosyası işlenirken bir hata oluştu: " . $e->getMessage();
            }
        }
    }

    // JSON formatında sonuçları döndür (PHP'den JavaScript'e)
    header('Content-Type: application/json');
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
    } elseif (!empty($results)) {
        echo json_encode(['success' => true, 'results' => $results]);
    } else {
        echo json_encode(['success' => false, 'errors' => ['Bilinmeyen bir hata oluştu.']]);
    }
    exit; // İşlem burada sonlanmalı, HTML çıktısı vermiyoruz
}
?>

<script>
    const dropZone = document.getElementById('drop_zone');
    const fileInput = document.getElementById('file_input');
    const resultsDiv = document.getElementById('results');

    let dropZoneListeners = {};
    let fileInputListeners = {};

    function setupEventListeners() {
        dropZoneListeners = {
            dragover: (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            },
            dragleave: () => {
                dropZone.classList.remove('dragover');
            },
            drop: (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                uploadFiles(files);
            },
            click: () => fileInput.click()
        };

        fileInputListeners = {
            change: () => {
                uploadFiles(fileInput.files);
            }
        };

        Object.entries(dropZoneListeners).forEach(([event, handler]) => {
            dropZone.addEventListener(event, handler);
        });
        Object.entries(fileInputListeners).forEach(([event, handler]) => {
            fileInput.addEventListener(event, handler);
        });
    }

    function uploadFiles(files) {
        let xmlFiles = Array.from(files).filter(file => file.name.endsWith('.xml'));
        if (xmlFiles.length === 0) {
            alert('Lütfen bir veya daha fazla XML dosyası seçin!');
            return;
        }

        dropZone.innerHTML = "Dönüştürülüyor...";
        const formData = new FormData();
        xmlFiles.forEach(file => formData.append('xml_file[]', file));

        fetch('', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Sunucu hatası: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                resultsDiv.innerHTML = '';

                if (data.success) {
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(result => {
                            resultsDiv.innerHTML += result;
                        });
                        document.querySelectorAll('[id^="view_link_"]').forEach(link => link.click());
                    }
                } else {
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(error => {
                            resultsDiv.innerHTML += `<p class="error">${error}</p>`;
                        });
                    } else {
                        resultsDiv.innerHTML += '<p class="error">Bilinmeyen bir hata oluştu.</p>';
                    }
                }

                dropZone.innerHTML = "XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)";
                setupEventListeners();
            })
            .catch(error => {
                console.error('Hata:', error);
                resultsDiv.innerHTML += `<p class="error">Bir hata oluştu, lütfen tekrar deneyin: ${error.message}</p>`;
                dropZone.innerHTML = "XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)";
                setupEventListeners();
                alert('Bir hata oluştu, lütfen tekrar deneyin: ' + error.message);
            });
    }

    // Sayfa yüklendiğinde event listener’ları kur
    setupEventListeners();
</script>
</body>
</html>