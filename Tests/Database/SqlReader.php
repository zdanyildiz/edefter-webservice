<?php

// Bu script, projenin ana dizininden çalıştırılmak üzere tasarlanmıştır.
// Komut: php Tests/Database/SqlReader.php

if (!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', '/');
}

// Proje kök dizinini mevcut çalışma dizini olarak varsayıyoruz.
$root = getcwd() . DIRECTORY_SEPARATOR;

$sqlFilePath = $root . 'App' . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'database.sql';

if (!file_exists($sqlFilePath)) {
    fwrite(STDERR, "Hata: database.sql dosyasi bulunamadi: " . $sqlFilePath);
    exit(1); // Hata kodu ile çık
}

// Dosyayı satır satır oku ve standart çıktıya yazdır.
$handle = fopen($sqlFilePath, 'r');
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        echo $line; // Satırı doğrudan yazdır
    }
    fclose($handle);
} else {
    fwrite(STDERR, "Hata: Dosya acilamadi.");
    exit(1); // Hata kodu ile çık
}
