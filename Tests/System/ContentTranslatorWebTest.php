<?php
// Tests/System/ContentTranslatorWebTest.php
// ContentTranslator cron job'unu web üzerinden test etmek için

// Proje kök dizinini belirle
$documentRoot = str_replace("\\","/",realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContentTranslator Cron Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2c3e50; color: white; padding: 15px; margin: -20px -20px 20px -20px; border-radius: 8px 8px 0 0; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .info { background: #cce7ff; border-color: #80c5ff; color: #0056b3; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .log-output { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px; max-height: 400px; overflow-y: auto; white-space: pre-wrap; font-family: monospace; font-size: 12px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 ContentTranslator Cron Job Test Arayüzü</h1>
            <p>Zamanlanan içerik çeviri işlemlerini test etmek için kullanılır</p>
        </div>

        <?php
        try {
            // Config yükle
            include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';
            $config = new Config();

            echo '<div class="test-section success">';
            echo '<h3>✅ Sistem Durumu</h3>';
            echo '<p><strong>Proje Kök:</strong> ' . $documentRoot . '</p>';
            echo '<p><strong>Sunucu:</strong> ' . $config->serverName . '</p>';
            echo '<p><strong>Localhost:</strong> ' . ($config->localhost ? 'Evet' : 'Hayır') . '</p>';
            echo '<p><strong>Veritabanı:</strong> ' . $config->dbName . '</p>';
            echo '<p><strong>Test Zamanı:</strong> ' . date('Y-m-d H:i:s') . '</p>';
            echo '</div>';

            // Veritabanı bağlantısını test et
            include_once DATABASE . "AdminDatabase.php";
            $db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

            echo '<div class="test-section success">';
            echo '<h3>✅ Veritabanı Bağlantısı</h3>';
            echo '<p>Başarıyla bağlandı: ' . $config->dbServerName . '</p>';
            echo '</div>';

            // Model dosyalarını kontrol et
            $models = [
                'AdminLanguage' => MODEL . 'Admin/AdminLanguage.php',
                'AdminCategory' => MODEL . 'Admin/AdminCategory.php', 
                'AdminPage' => MODEL . 'Admin/AdminPage.php',
                'AdminSeo' => MODEL . 'Admin/AdminSeo.php',
                'AdminChatCompletion' => MODEL . 'Admin/AdminChatCompletion.php'
            ];

            echo '<div class="test-section">';
            echo '<h3>📋 Model Dosyaları Kontrolü</h3>';
            foreach ($models as $name => $path) {
                if (file_exists($path)) {
                    echo '<p>✅ ' . $name . '</p>';
                } else {
                    echo '<p style="color: red;">❌ ' . $name . ' - Dosya bulunamadı</p>';
                }
            }
            echo '</div>';

            // Bekleyen işlemleri kontrol et
            $pendingCategories = 0;
            $pendingPages = 0;
            $completedCategories = 0;
            $completedPages = 0;
            $failedCategories = 0;
            $failedPages = 0;            try {
                // Kategori istatistikleri
                $stmt = $db->pdo->query("SELECT translation_status, COUNT(*) as count FROM language_category_mapping GROUP BY translation_status");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    switch ($row['translation_status']) {
                        case 'pending': $pendingCategories = $row['count']; break;
                        case 'completed': $completedCategories = $row['count']; break;
                        case 'failed': $failedCategories = $row['count']; break;
                    }
                }

                // Sayfa istatistikleri
                $stmt = $db->pdo->query("SELECT translation_status, COUNT(*) as count FROM language_page_mapping GROUP BY translation_status");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    switch ($row['translation_status']) {
                        case 'pending': $pendingPages = $row['count']; break;
                        case 'completed': $completedPages = $row['count']; break;
                        case 'failed': $failedPages = $row['count']; break;
                    }
                }
            } catch (Exception $e) {
                echo '<div class="test-section warning">';
                echo '<h3>⚠️ İstatistik Hatası</h3>';
                echo '<p>' . $e->getMessage() . '</p>';
                echo '</div>';
            }

            // İstatistikler
            echo '<div class="stats">';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #ffc107;">' . $pendingCategories . '</div>';
            echo '<div>Bekleyen Kategori</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #ffc107;">' . $pendingPages . '</div>';
            echo '<div>Bekleyen Sayfa</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #28a745;">' . $completedCategories . '</div>';
            echo '<div>Tamamlanan Kategori</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #28a745;">' . $completedPages . '</div>';
            echo '<div>Tamamlanan Sayfa</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #dc3545;">' . $failedCategories . '</div>';
            echo '<div>Başarısız Kategori</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #dc3545;">' . $failedPages . '</div>';
            echo '<div>Başarısız Sayfa</div>';
            echo '</div>';
            echo '</div>';

        } catch (Exception $e) {
            echo '<div class="test-section error">';
            echo '<h3>❌ Hata</h3>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h3>🚀 Test İşlemleri</h3>
            <p>Aşağıdaki butonlar ile çeşitli test işlemlerini gerçekleştirebilirsiniz:</p>
            
            <a href="?action=run_cron" class="btn btn-primary">
                🤖 Cron Job'u Çalıştır
            </a>
            
            <a href="?action=check_models" class="btn btn-success">
                📋 Model Sınıflarını Test Et
            </a>
            
            <a href="?action=view_logs" class="btn btn-warning">
                📝 Log Kayıtlarını Görüntüle
            </a>
            
            <a href="?action=simulate_translation" class="btn btn-info">
                🔄 Çeviri Simülasyonu
            </a>
            
            <a href="?" class="btn" style="background: #6c757d; color: white;">
                🔄 Sayfayı Yenile
            </a>
        </div>

        <?php
        // İşlem kontrolü
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            
            echo '<div class="test-section info">';
            echo '<h3>📊 Test Sonuçları</h3>';
            
            switch ($action) {
                case 'run_cron':
                    echo '<h4>🤖 Cron Job Çalıştırılıyor...</h4>';
                    echo '<div class="log-output">';
                    
                    ob_start();
                    try {
                        // Cron job'u include et ve çalıştır
                        include $documentRoot . '/App/Cron/ContentTranslator.php';
                        $output = ob_get_contents();
                        echo $output ? $output : "Cron job sessizce çalıştı, log dosyalarını kontrol edin.";
                    } catch (Exception $e) {
                        echo "HATA: " . $e->getMessage();
                    }
                    ob_end_clean();
                    
                    echo '</div>';
                    break;
                    
                case 'check_models':
                    echo '<h4>📋 Model Sınıfları Test Ediliyor...</h4>';
                    echo '<div class="log-output">';
                    
                    try {
                        include_once MODEL."Admin/AdminLanguage.php";
                        $adminLanguage = new AdminLanguage($db);
                        echo "✅ AdminLanguage sınıfı yüklendi\n";
                        
                        include_once MODEL."Admin/AdminCategory.php";
                        $adminCategory = new AdminCategory($db);
                        echo "✅ AdminCategory sınıfı yüklendi\n";
                        
                        include_once MODEL."Admin/AdminPage.php";
                        $adminPage = new AdminPage($db);
                        echo "✅ AdminPage sınıfı yüklendi\n";
                        
                        include_once MODEL."Admin/AdminSeo.php";
                        $adminSeo = new AdminSeo($db);
                        echo "✅ AdminSeo sınıfı yüklendi\n";
                        
                        include_once MODEL.'Admin/AdminChatCompletion.php';
                        $adminChatCompletion = new AdminChatCompletion($db, 1);
                        echo "✅ AdminChatCompletion sınıfı yüklendi\n";
                        
                        echo "\n🎯 Tüm model sınıfları başarıyla yüklendi!";
                        
                    } catch (Exception $e) {
                        echo "❌ HATA: " . $e->getMessage();
                    }
                    
                    echo '</div>';
                    break;
                    
                case 'view_logs':
                    echo '<h4>📝 Son Log Kayıtları</h4>';
                    echo '<div class="log-output">';
                    
                    $logFile = LOG_DIR . 'Admin/' . date('Y-m-d') . '.log';
                    if (file_exists($logFile)) {
                        $logContent = file_get_contents($logFile);
                        $logLines = explode("\n", $logContent);
                        $lastLines = array_slice($logLines, -50); // Son 50 satır
                        
                        foreach ($lastLines as $line) {
                            if (!empty(trim($line))) {
                                echo $line . "\n";
                            }
                        }
                    } else {
                        echo "Log dosyası bulunamadı: " . $logFile;
                    }
                    
                    echo '</div>';
                    break;
                    
                case 'simulate_translation':
                    echo '<h4>🔄 Çeviri Simülasyonu</h4>';
                    echo '<div class="log-output">';
                    
                    try {
                        include_once MODEL.'Admin/AdminChatCompletion.php';
                        $adminChatCompletion = new AdminChatCompletion($db, 1);
                        
                        $testText = "Merhaba Dünya";
                        $targetLanguage = "English";
                        
                        echo "Test metni: " . $testText . "\n";
                        echo "Hedef dil: " . $targetLanguage . "\n";
                        echo "Çeviri yapılıyor...\n\n";
                        
                        $translated = $adminChatCompletion->translateConstant($testText, $targetLanguage);
                        
                        if ($translated) {
                            echo "✅ Çeviri başarılı: " . $translated;
                        } else {
                            echo "❌ Çeviri başarısız";
                        }
                        
                    } catch (Exception $e) {
                        echo "❌ HATA: " . $e->getMessage();
                    }
                    
                    echo '</div>';
                    break;
            }
            
            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h3>📚 Bilgilendirme</h3>
            <ul>
                <li><strong>Cron Job Dosyası:</strong> /App/Cron/ContentTranslator.php</li>
                <li><strong>Log Dosyaları:</strong> /Public/Log/Admin/</li>
                <li><strong>Bekleyen İşlemler:</strong> language_category_mapping ve language_page_mapping tablolarında translation_status='pending'</li>
                <li><strong>AI Çeviri:</strong> AdminChatCompletion sınıfı kullanılır</li>
            </ul>
        </div>
    </div>
</body>
</html>
