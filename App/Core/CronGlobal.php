<?php
// App/Core/CronGlobal.php
// Bu dosya, cron job'lar (zamanlanmış görevler) için hafif bir başlangıç noktasıdır.
// Yönetici oturumu veya giriş kontrolü yapmadan, temel yapılandırmayı ve veritabanı bağlantısını başlatır.

// Proje ana dizinini belirle
$documentRoot = str_replace("\\","/",realpath(dirname(__FILE__, 3)));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);

// Ana yapılandırma dosyasını dahil et
include_once $documentRoot . $directorySeparator . 'App/Core/Config.php';

################# CONFIG ###################################
// Ön tanımlı ayarları yap
$config = new Config();
$helper = $config->Helper;
$json = $config->Json;

################# DATABASE #################################

// Veritabanı modelini ve bağlantısını dahil et
include_once DATABASE . "AdminDatabase.php";
$db = new AdminDatabase($config->dbServerName, $config->dbName, $config->dbUsername, $config->dbPassword);

// Bu dosyada session veya login kontrolü bulunmaz.

