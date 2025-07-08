<?php
/**
 * @var array $page
 * @var $view
 * @var $config
 * @var Casper $casper
 * @var string $sliderBanner
 * @var string$middleBanner
 */
$config = $casper->getConfig();
$helper = $config->Helper;
$pageTitle = $page['sayfaad'];
$pageContent = $page['sayfaicerik'];
$imageUrls = $page['resim_url'];
$imageUrls = ($imageUrls) ? explode(",",$imageUrls) : [];

$pageGallery = $page['pageGallery'] ?? [];
$pageFiles = $page['pageFiles'] ?? [];
$pageVideos = $page['pageVideos'] ?? [];
?>
<div class="page-container">
    <?php echo ($sliderBanner) ? htmlspecialchars_decode($sliderBanner) : '';?>
    <div class="page-title">
        <h1><?php echo $pageTitle; ?></h1>
    </div>
    <div class="page-content">
        <?php
        if (count($imageUrls) > 0) {
            ?>
            <figure class="page-image">
                <img src="<?php echo imgRoot."?imagePath=". $imageUrls[0]; ?>&height=450" alt="<?php echo $pageTitle; ?>" title="<?php echo $pageTitle; ?>" id="firstImage" />
                <?php
                if (count($imageUrls) > 1) {?>
                    <div class="thumbnail-container">
                    <?php
                    for ($i = 0; $i < count($imageUrls); $i++) {
                        ?>
                        <img src="<?php echo imgRoot."?imagePath=". trim($imageUrls[$i]); ?>&height=75" alt="<?php echo $pageTitle; ?>" title="<?php echo $pageTitle; ?>" class="thumbnail" data-src="<?php echo imgRoot."?imagePath=". trim($imageUrls[$i]); ?>" />
                        <?php
                    }
                    ?>
                    </div>
                <?php }
                ?>
                <figcaption><?php echo $pageTitle; ?></figcaption>
            </figure>
            <?php
        }
        ?>
        <div class="page-text">
            <?php echo (!empty($pageContent)) ? htmlspecialchars_decode($pageContent) : ''; ?>
        </div>
        <div class="page-content">
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
            <div id="usage-status"></div>
        </div>
    </div>
    <?php echo ($middleBanner) ? htmlspecialchars_decode($middleBanner) : '';?>
    <?php
    if(!empty($pageFiles)){
        ?>
        <div class="page-file-container">
        <?php
        foreach ($pageFiles as $pageFile){
            $fileID = $pageFile['fileID'];
            $fileName = $pageFile['fileName'];
            $filePath = $pageFile['filePath'];
            $fileSize = $pageFile['fileSize'];
            $fileExtension = $pageFile['fileExtension'];
            $fileFolderName = "Page";//$pageFile['fileFolderName'];
            $fileIcon = fileRoot. $fileExtension . ".png";
            ?>
                <a class="page-file-box" href="<?php echo fileRoot.$fileFolderName . '/' . $filePath; ?>" target="_blank">
                    <img src="<?php echo $fileIcon; ?>" alt="<?php echo $fileName; ?>" width="50" height="50">
                    <span><?php echo $fileName; ?></span>
                </a>
            <?php
        }
        ?>
        </div>
        <?php
    }
    if(!empty($pageGallery)){
        $galleryName = $pageGallery['galleryName'];
        $galleryDescription = $pageGallery['galleryDescription'];
        $galleryImages = $pageGallery['galleryImages'];
        ?>
        <div class="galleryConteyner">
            <div class="galleryTitle">
                <h2><?=$galleryName?></h2>
            </div>
            <?php if(!empty($galleryDescription)):?>
            <div class="galleryDescription">
                <p><?=$galleryDescription?></p>
            </div>
            <?php endif;?>
            <div class="galleryImages">
                <?php
                foreach($galleryImages as $galleryImage) {
                    $imageID = $galleryImage['imageID'];
                    $galleryImageFolderName = $galleryImage['imageFolderName'];
                    $galleryImagePath = $galleryImage['imagePath'];
                    $galleryImageName = $galleryImage['imageName'];
                    $galleryImageWidth = $galleryImage['imageWidth'];
                    $galleryImageHeight = $galleryImage['imageHeight'];
                    ?>
                    <div class="galleryImage">
                        <img class="thumbnail" src="<?=imgRoot."?imagePath=".$galleryImageFolderName.'/'.$galleryImagePath?>&width=300" alt="<?=$galleryImageName?>" width="300"   data-src="<?php echo imgRoot."?imagePath=". $galleryImageFolderName.'/'.$galleryImagePath; ?>">
                    </div>
                    <?php
                }
            ?>
        </div>
    <?php
    }
    if(!empty($pageVideos)){
        ?>
        <div class="page-video-container">
            <?php
            //Array ( [0] => Array ( [video_id] => 1 [created_at] => 2024-12-12 16:33:29 [updated_at] => 2024-12-12 16:33:29 [video_name] => Food Containers Anime [video_file] => [video_extension] => [video_size] => [video_width] => 0 [video_height] => 0 [unique_id] => FCK85YTADQU9MPRBH36J [video_iframe] => https://www.youtube.com/embed/FCK85YTADQU9MPRBH36J?rel=0 [description] => Food Containers Anime [is_deleted] => 0 ) )
            foreach($pageVideos as $pageVideo) {
                $videoIframe = $pageVideo['video_iframe'];
                $videoName = $pageVideo['video_name'];
                $videoID = $pageVideo['video_id'];
                ?>
                <div class="page-video-box">
                    <div class="page-video-title"><?=$videoName?></div>
                    <div class="page-video-iframe">
                        <?=$videoIframe?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
            <?php
        }

    //banner

    ?>
</div>
<?php if (count($imageUrls) > 0) {?>
<div id="pageModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <a class="prev">&#10094;</a>
        <img class="modal-img" src="" alt="">
        <a class="next">&#10095;</a>
    </div>
</div>
<?php }?>
<style>
        .tabs {
            overflow: hidden;
            border: var(--border-style) var(--border-color) var(--border-width);
            background-color:var(--body-bg-color);
            margin: 0 auto;
            text-align: center;
            border-radius: var(--border-radius-base);
    box-shadow: var(--box-shadow-base);
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
            max-width: 100%;
            margin: 0 auto;
        }
        .tabcontent.active {
            display: block;
        }
        #drop_zone_berat, #drop_zone_defterraporu, #drop_zone_kebir, #drop_zone_yevmiye {
            width: 100%;
            height: 200px;
            border: 2px dashed #ccc;
            text-align: center;
            padding: 20px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        #drop_zone_berat.dragover, #drop_zone_defterraporu.dragover, #drop_zone_kebir.dragover, #drop_zone_yevmiye.dragover { 
            background-color: #e1e1e1; 
            border-color: #007bff;
        }
        
        #results {
            text-align: center;
            padding: 15px;
            max-width: 100%;
            margin: 20px auto;
            border: 2px dashed #007bff;
            border-radius: 8px;
            background-color: #f8f9fa;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            min-height: 50px;
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
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .usage-info {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .usage-info.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .usage-info.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        #usage-status {
            text-align: center;
            margin: 20px auto;
            max-width: 100%;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>


<script>
    function openTab(tabName) {
        // Tab isimleri ile metin eslestirmesi
        const tabMap = {
            'Berat': 'Berat',
            'DefterRaporu': 'DefterRaporu',
            'Kebir': 'Kebir',
            'Yevmiye': 'Yevmiye'
        };

        // Tum sekmeleri ve iceriklerini kapat
        const tabs = document.getElementsByClassName('tab');
        const tabContents = document.getElementsByClassName('tabcontent');

        // Koleksiyonlarin varligini kontrol et
        if (!tabs || !tabContents) {
            console.error('Tab veya tabcontent elementleri bulunamadi.');
            return;
        }

        for (let i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove('active');
            tabContents[i].classList.remove('active');
        }

        // Secilen sekmeyi ve icerigini ac
        const selectedTabContent = document.getElementById(tabName);
        if (selectedTabContent) {
            selectedTabContent.classList.add('active');
        }

        // Tab butonlarini kontrol et ve hata ayiklama icin log ekle
        const tabTexts = Array.from(tabs).map(t => t.textContent.trim());

        const tabIndex = Array.from(tabs).findIndex(t => t.textContent.trim() === tabMap[tabName]);
        if (tabIndex !== -1) {
            tabs[tabIndex].classList.add('active');
        } else {
            console.error(`Tab '${tabName}' (${tabMap[tabName]}) bulunamadi. Mevcut tab metinleri:`, tabTexts);
            return;
        }

        // Aktif sekme icin dosya yukleme islevini kur
        setupFileUpload(tabName);
    }

    const resultsDiv = document.getElementById('results');
    const usageStatusDiv = document.getElementById('usage-status');

    function setupFileUpload(tabName) {
        const dropZoneId = `drop_zone_${tabName.toLowerCase()}`;
        const fileInputId = `file_input_${tabName.toLowerCase()}`;
        const dropZone = document.getElementById(dropZoneId);
        const fileInput = document.getElementById(fileInputId);

        if (!dropZone || !fileInput) {
            console.error(`Drop zone veya file input '${tabName.toLowerCase()}' bulunamadi.`);
            return;
        }

        // Eski event listener'lari temizle
        const newDropZone = dropZone.cloneNode(true);
        const newFileInput = fileInput.cloneNode(true);
        dropZone.parentNode.replaceChild(newDropZone, dropZone);
        fileInput.parentNode.replaceChild(newFileInput, fileInput);

        // Yeni referanslari al
        const freshDropZone = document.getElementById(dropZoneId);
        const freshFileInput = document.getElementById(fileInputId);

        function setupEventListeners() {
            freshDropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                freshDropZone.classList.add('dragover');
            });

            freshDropZone.addEventListener('dragleave', () => {
                freshDropZone.classList.remove('dragover');
            });

            freshDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                freshDropZone.classList.remove('dragover');
                const files = e.dataTransfer.files;
                uploadFiles(files, tabName, freshDropZone, freshFileInput);
            });

            freshDropZone.addEventListener('click', () => {
                freshFileInput.click();
            });

            freshFileInput.addEventListener('change', () => {
                uploadFiles(freshFileInput.files, tabName, freshDropZone, freshFileInput);
            });
        }

        function uploadFiles(files, tabType, dropZoneElement, fileInputElement) {
            let xmlFiles = Array.from(files).filter(file => file.name.endsWith('.xml'));
            if (xmlFiles.length === 0) {
                alert('Lutfen bir veya daha fazla XML dosyasi secin!');
                return;
            }

            // Dosya yukleme sirasinda UI'yi kilitle
            dropZoneElement.innerHTML = "⏳ Dosyalar isleniyor, lutfen bekleyin...";
            dropZoneElement.style.pointerEvents = 'none';
            dropZoneElement.style.opacity = '0.7';
            
            const formData = new FormData();
            xmlFiles.forEach(file => formData.append('xml_file[]', file));
            formData.append('type', tabType);

            fetch('/?/control/EDefter/post/process', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Sunucu hatasi: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Donusum sonucu:', data);
                resultsDiv.innerHTML = '';
                
                if (data.success) {
                    // Basarili islem
                    if (data.results && data.results.length > 0) {
                        data.results.forEach(result => {
                            resultsDiv.innerHTML += result;
                        });
                    }
                    
                    // Kullanim bilgisi goster
                    if (data.usage_info) {
                        const usageInfo = data.usage_info;
                        const usageMessage = `✅ Islem başarılı! Günlük kullanım: ${usageInfo.current_usage}/${usageInfo.daily_limit} (Kalan: ${usageInfo.remaining_usage})`;
                        resultsDiv.innerHTML += `<div class="usage-info success">${usageMessage}</div>`;
                        
                        // Usage status'u guncelle
                        updateUsageStatus(usageInfo);
                    }
                } else {
                    // Hata durumu
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(error => {
                            resultsDiv.innerHTML += `<p class="error">❌ ${error}</p>`;
                        });
                        
                        // Kullanim siniri asimi kontrolu
                        if (data.usage_info && data.usage_info.is_limit_exceeded) {
                            const usageInfo = data.usage_info;
                            const limitMessage = usageInfo.user_type === 'member' 
                                ? '💡 Ipucu: Gunluk siniriniz dolmustur. Yarin tekrar deneyebilirsiniz.'
                                : '💡 Ipucu: Uye olarak gunluk 20 islem yapabilirsiniz!';
                            resultsDiv.innerHTML += `<div class="usage-info warning">${limitMessage}</div>`;
                            
                            // Usage status'u guncelle
                            updateUsageStatus(usageInfo);
                        }
                    } else {
                        resultsDiv.innerHTML += '<p class="error">❌ Bilinmeyen bir hata olustu.</p>';
                    }
                }
            })
            .catch(error => {
                console.error('Fetch hatasi:', error);
                resultsDiv.innerHTML = `<p class="error">❌ Bir hata olustu: ${error.message}</p>`;
            })
            .finally(() => {
                // Her durumda UI'yi sifirla
                resetDropZone(dropZoneElement, fileInputElement);
            });
        }
        
        function resetDropZone(dropZoneElement, fileInputElement) {
            // Dropzone'u sifirla
            dropZoneElement.innerHTML = "XML dosyasini buraya surukleyin veya tiklayin (Birden fazla dosya secebilirsiniz)";
            dropZoneElement.style.pointerEvents = 'auto';
            dropZoneElement.style.opacity = '1';
            dropZoneElement.classList.remove('dragover');
            
            // File input'u temizle - KRITIK!
            fileInputElement.value = '';
        }

        // Event listener'lari kur
        setupEventListeners();
    }

    function updateUsageStatus(usageInfo) {
        if (!usageInfo) return;
        
        const statusClass = usageInfo.is_limit_exceeded ? 'warning' : 'success';
        const userTypeText = usageInfo.user_type === 'member' ? 'Uye' : 'Ziyaretci';
        const statusText = usageInfo.is_limit_exceeded ? 'SINIR AŞILDI' : 'Normal';
        
        usageStatusDiv.innerHTML = `
            <div class="usage-info ${statusClass}">
                📊 Kullanım Durumu: ${userTypeText} | ${usageInfo.current_usage}/${usageInfo.daily_limit} | Kalan: ${usageInfo.remaining_usage} | ${statusText}
            </div>
        `;
    }

    // Ilk acildiginda Berat sekmesini aktif et
    document.addEventListener('DOMContentLoaded', () => {
        openTab('Berat');
    });
</script>
