<?php
/**
 * GetLocalDomain.php - Yerel Alan Adı Bulucu Betiği
 *
 * Bu betik, App/Config/Domain.php dosyasını okuyarak 'l.' ile başlayan
 * yerel alan adını bulur ve döndürür. Bu sayede, farklı projelerde
 * (l.erhanozel, l.pozitif, l.plasfed vb.) çalışırken doğru yerel
 * adresi dinamik olarak tespit edebiliriz.
 *
 * Kullanımı: php Tests/System/GetLocalDomain.php
 */

// Kök dizin tanımı
$rootDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
$configFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Domain.php';

if (!file_exists($configFile)) {
    echo "HATA: Domain.php dosyası bulunamadı: {$configFile}\n";
    exit(1);
}

/**
 * Yerel domain adını döndürür
 */
function getLocalDomain() {
    // Kök dizin tanımı
    $rootDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
    $configFile = $rootDir . 'App' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Domain.php';

    if (!file_exists($configFile)) {
        throw new Exception("Domain.php dosyası bulunamadı: {$configFile}");
    }

    // Domain.php dosyasının içeriğini oku
    $fileContent = file_get_contents($configFile);

    // 'l.' ile başlayan alan adını bulmak için regex kullanıyoruz
    if (preg_match('/[\'"]l\.[a-zA-Z0-9._-]+[\'"]/', $fileContent, $matches)) {
        // Tek tırnak veya çift tırnak işaretlerini kaldır
        return trim($matches[0], '\'"');
    } else {
        throw new Exception("'l.' ile başlayan yerel alan adı bulunamadı.");
    }
}

// Domain.php dosyasının içeriğini oku
$fileContent = file_get_contents($configFile);

// 'l.' ile başlayan alan adını bulmak için regex kullanıyoruz
// Örnek: "l.erhanozel", "l.pozitif", "l.plasfed" vb.
if (preg_match('/[\'"]l\.[a-zA-Z0-9._-]+[\'"]/', $fileContent, $matches)) {
    // Tek tırnak veya çift tırnak işaretlerini kaldır
    $localDomain = trim($matches[0], '\'"');
    
    // CLI kullanımında echo yap
    if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === 'GetLocalDomain.php') {
        echo $localDomain;
    }
} else {
    if (php_sapi_name() === 'cli' && isset($argv[0]) && basename($argv[0]) === 'GetLocalDomain.php') {
        echo "HATA: 'l.' ile başlayan yerel alan adı bulunamadı.\n";
        exit(1);
    }
}
