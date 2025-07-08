<?php
exit(header('Location: /tr/anasayfa/global-pozitif-e-defter-goruntuleyici'));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>e-defter Dönüştürücü Global Pozitif Teknolojiler</title>
    <style>
        html, body { margin: 0 auto; text-align: center; font-family: Arial, sans-serif; }
        .tabs {
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
            margin: 0 auto;
            max-width: 600px;
        }
        .tab {
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            background-color: inherit;
            font-size: 16px;
        }
        .tab:hover {
            background-color: #ddd;
        }
        .tab.active {
            background-color: #007bff;
            color: white;
        }
        .tabcontent {
            display: none;
            padding: 20px;
            border-top: none;
            max-width: 600px;
            margin: 0 auto;
        }
        .tabcontent.active {
            display: block;
        }
        #drop_zone_berat, #drop_zone_defterraporu, #drop_zone_kebir, #drop_zone_yevmiye {
            width: 400px;
            height: 200px;
            border: 2px dashed #ccc;
            text-align: center;
            padding: 20px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 5px;
        }
        #drop_zone_berat.dragover, #drop_zone_defterraporu.dragover, #drop_zone_kebir.dragover, #drop_zone_yevmiye.dragover { background-color: #e1e1e1; }
        .logo { max-width: 200px; margin: 10px auto; display: block; }
        .results {
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
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-VQBXWE608X"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-VQBXWE608X');
	</script
</head>
<body>
<a href="/">
<img src="https://globalpozitif.com.tr/m/r/logo/logo-GU7RQ.png" alt="Global Pozitif Teknolojiler" class="logo">
</a>
<h1>e-Defter Dönüştürücü</h1>

<div class="tabs">
    <button class="tab active" onclick="openTab('Berat')">Berat</button>
    <button class="tab" onclick="openTab('DefterRaporu')">DefterRaporu</button>
    <button class="tab" onclick="openTab('Kebir')">Kebir</button>
    <button class="tab" onclick="openTab('Yevmiye')">Yevmiye</button>
</div>

<div id="Berat" class="tabcontent active">
    <div id="drop_zone_berat">XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)</div>
    <input type="file" id="file_input_berat" style="display:none;" accept=".xml" multiple>
</div>

<div id="DefterRaporu" class="tabcontent">
    <div id="drop_zone_defterraporu">XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)</div>
    <input type="file" id="file_input_defterraporu" style="display:none;" accept=".xml" multiple>
</div>

<div id="Kebir" class="tabcontent">
    <div id="drop_zone_kebir">XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)</div>
    <input type="file" id="file_input_kebir" style="display:none;" accept=".xml" multiple>
</div>

<div id="Yevmiye" class="tabcontent">
    <div id="drop_zone_yevmiye">XML dosyasını buraya sürükleyin veya tıklayın (Birden fazla dosya seçebilirsiniz)</div>
    <input type="file" id="file_input_yevmiye" style="display:none;" accept=".xml" multiple>
</div>

<div id="results"></div>

<script>
    function openTab(tabName) {
        // Tab isimleri ile metin eşleştirmesi
        const tabMap = {
            'Berat': 'Berat',
            'DefterRaporu': 'DefterRaporu',
            'Kebir': 'Kebir',
            'Yevmiye': 'Yevmiye'
        };

        // Tüm sekmeleri ve içeriklerini kapat
        const tabs = document.getElementsByClassName('tab');
        const tabContents = document.getElementsByClassName('tabcontent');

        // Koleksiyonların varlığını kontrol et
        if (!tabs || !tabContents) {
            console.error('Tab veya tabcontent elementleri bulunamadı.');
            return;
        }

        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
            tabContents[i].classList.remove('active');
        }

        // Seçilen sekmeyi ve içeriğini aç
        const selectedTabContent = document.getElementById(tabName);
        if (selectedTabContent) {
            selectedTabContent.classList.add('active');
        }

        // Tab butonlarını kontrol et ve hata ayıklama için log ekle
        console.log('Aranan Tab Name:', tabName);
        const tabTexts = Array.from(tabs).map(t => t.textContent.trim());
        console.log('Mevcut Tab Textler:', tabTexts);

        const tabIndex = Array.from(tabs).findIndex(t => t.textContent.trim() === tabMap[tabName]);
        if (tabIndex !== -1) {
            tabs[tabIndex].classList.add('active');
        } else {
            console.error(`Tab '${tabName}' (${tabMap[tabName]}) bulunamadı. Mevcut tab metinleri:`, tabTexts);
            return;
        }

        // Aktif sekme için dosya yükleme işlevini kur
        setupFileUpload(tabName);
    }

    const resultsDiv = document.getElementById('results');

    function setupFileUpload(tabName) {
        const dropZoneId = `drop_zone_${tabName.toLowerCase()}`;
        const fileInputId = `file_input_${tabName.toLowerCase()}`;
        const dropZone = document.getElementById(dropZoneId);
        const fileInput = document.getElementById(fileInputId);

        if (!dropZone || !fileInput) {
            console.error(`Drop zone veya file input '${tabName.toLowerCase()}' bulunamadı.`);
            return;
        }

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
                    uploadFiles(files, tabName);
                },
                click: () => fileInput.click()
            };

            fileInputListeners = {
                change: () => {
                    uploadFiles(fileInput.files, tabName);
                }
            };

            Object.entries(dropZoneListeners).forEach(([event, handler]) => {
                dropZone.addEventListener(event, handler);
            });
            Object.entries(fileInputListeners).forEach(([event, handler]) => {
                fileInput.addEventListener(event, handler);
            });
        }

        function uploadFiles(files, tabType) {
            let xmlFiles = Array.from(files).filter(file => file.name.endsWith('.xml'));
            if (xmlFiles.length === 0) {
                alert('Lütfen bir veya daha fazla XML dosyası seçin!');
                return;
            }

            dropZone.innerHTML = "Dönüştürülüyor...";
            const formData = new FormData();
            xmlFiles.forEach(file => formData.append('xml_file[]', file));
            formData.append('type', tabType); // Hangi tür dönüşüm yapılacağını belirt

            fetch('process.php', {
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

        // Event listener’ları kur
        setupEventListeners();
    }

    // İlk açıldığında Berat sekmesini aktif et
    document.addEventListener('DOMContentLoaded', () => {
        openTab('Berat');
    });
</script>
</body>
</html>