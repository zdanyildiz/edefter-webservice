<?php

class Config {

    public array $domain; //Listeden gelen
    public $hostDomain; // sunucu bilgisi
    public $subDomain; // sunucu bilgisi
    public $http; //localde http sunucuda https olacak
    public bool $ampStatus=false;
    public string $ampPrefix="";
    public string $ampLayout='loading="lazy"';
    public string $ampImgEnd="";
    public string $ampFormAct="";

    public bool $localhost;
    public string $serverName;

    public string $key;
    public string $dbServerName;
    public string $dbUsername;
    public string $dbPassword;
    public string $dbName;

    public Helper $Helper;

    public $cookieSecure;
    public $cookieHttpOnly;
    public $cookieSameSite;

    public Json $Json;

    public $HeadTrackingInjector;

    public function __construct() {

        $this->setServer();
        $this->setHeaders();
        $this->configureDateTimeAndLocale();
        $this->setFilesystemConstants();
        $this->configureErrorReporting();
        $this->checkDomain();

        register_shutdown_function([$this, 'shutdownHandler']);
    }

    public function shutdownHandler() {
        $error = error_get_last();
        if ($error !== NULL) {
            $errno   = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr  = $error["message"];

            if (defined('ADMIN') && ADMIN) {
                Log::adminWrite("Fatal error [$errno] $errstr on line $errline in file $errfile", "error");
            }
            else {
                Log::write("Fatal error [$errno] $errstr on line $errline in file $errfile", "error");
            }
        }
    }
    public function setServer() {
        // CLI modu kontrolü
        if (php_sapi_name() === 'cli') {
            try {
                $this->hostDomain = $this->getLocalDomainFromConfig();
                $this->subDomain = 'l';
            } catch (Exception $e) {
                throw new Exception("CLI modunda yerel domain alınamadı: " . $e->getMessage());
            }
            $this->ampStatus = false;
            return;
        }
        
        $this->hostDomain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->subDomain = explode(".", $this->hostDomain)[0];

        $ampStatus = (substr($_SERVER['REQUEST_URI'] ?? '', 0, 4) === "/amp");
        $this->ampStatus=$ampStatus;

        if($ampStatus==1)
        {
            $this->ampPrefix="amp-";
            $this->ampLayout='layout="responsive"';
            $this->ampImgEnd="</amp-img>";
            $this->ampFormAct="-xhr";
        }
    }
    private function setHeaders() {
        // CLI modunda header göndermiyoruz
        if (php_sapi_name() === 'cli') {
            return;
        }
        
        header("Content-Type: text/html; charset=utf-8");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
    }

    private function configureErrorReporting() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', LOG_DIR . 'errors.log');
        //dosya yoksada oluştur
        if (!file_exists(LOG_DIR . 'errors.log')) {
            $file = fopen(LOG_DIR . 'errors.log', 'w');
            fclose($file);
        }
        set_error_handler([$this, "customError"]);
    }

    public function customError($errno, $errstr, $errfile, $errline) {
        $errorLevels = [
            E_WARNING => 'Warning',
            E_NOTICE => 'Notice',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
        ];

        $levelName = isset($errorLevels[$errno]) ? $errorLevels[$errno] : 'Unknown Error';
        $message = "$levelName: [$errno] $errstr on line $errline in file $errfile";
        if (defined('ADMIN') && ADMIN) {
            Log::adminWrite($message, "error");
        }
        else {
            Log::write($message, "error");
        }
    }
    private function configureDateTimeAndLocale() {
        date_default_timezone_set('Europe/Istanbul');
        setlocale(LC_TIME, "turkish");
        setlocale(LC_ALL,'turkish');
    }
    private function setFilesystemConstants() {
        // CLI modunda document root'u elle belirliyoruz
        if (php_sapi_name() === 'cli') {
            $documentRoot = dirname(dirname(__DIR__)); // proje kök dizini
        } else {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? dirname(dirname(__DIR__));
        }
        
        $documentRoot = str_replace("\\","/",$documentRoot);
        $directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
        if (!defined("ROOT")) define("ROOT", $documentRoot . $directorySeparator);
        if (!defined("PUBL")) define("PUBL", ROOT . "Public" . $directorySeparator);
        if (!defined("LOG")) define("LOG", PUBL . "log" . $directorySeparator);

        if (!defined("APP")) define("APP", ROOT . "App" . $directorySeparator);
        if (!defined("MODEL")) define("MODEL", APP . "Model" . $directorySeparator);
        if (!defined("VIEW")) define("VIEW", APP . "View" . $directorySeparator);
        if (!defined("CONTROLLER")) define("CONTROLLER", APP . "Controller" . $directorySeparator);
        if (!defined("CONF")) define("CONF", APP . "Config" . $directorySeparator);
        if (!defined("CORE")) define("CORE", APP . "Core" . $directorySeparator);
        if (!defined("DATABASE")) define("DATABASE", APP . "Database" . $directorySeparator);

        if (!defined("Helpers")) define("Helpers", APP . "Helpers" . $directorySeparator);

        if (!defined("LOG_DIR")) define("LOG_DIR", PUBL . "Log" . $directorySeparator);
        if (!defined("JSON_DIR")) define("JSON_DIR", PUBL . "Json" . $directorySeparator);
        if (!defined("IMG")) define("IMG", PUBL . "Image" . $directorySeparator);
        if (!defined("imgRoot")) define("imgRoot","/Public/Image/");
        if (!defined("FILE")) define("FILE", PUBL . "File" . $directorySeparator);
        if (!defined("fileRoot")) define("fileRoot","/Public/File/");
        if (!defined("JS")) define("JS", PUBL . "JS" . $directorySeparator);
        if (!defined("CSS")) define("CSS", PUBL . "CSS" . $directorySeparator);

        
        if(file_exists(CONF . 'Domain.php')===false || file_exists(CONF . 'Key.php')===false || file_exists(CONF . 'Sql.php')===false)
        {
            // CLI modunda setup'a yönlendirme yapmayız
            if (php_sapi_name() !== 'cli') {
                header("Location: /Setup/index.php");
                exit;
            } else {
                echo "⚠️ Config dosyaları eksik: Domain.php, Key.php, Sql.php\n";
                echo "Setup klasöründeki index.php dosyasını çalıştırın.\n";
                exit(1);
            }
        }
        
        /** @var array $domain */
        include_once CONF . 'Domain.php';
        
        // Domain array'inin varlığını kontrol et
        if (!isset($domain) || !is_array($domain)) {
            Log::write("domain: ". json_encode($domain), "error");
            throw new Exception("Domain yapılandırması geçersiz: \$domain array'i bulunamadı veya geçersiz.");
        }
        
        $this->domain = $domain;

        /** @var string $key */
        include_once CONF . 'Key.php';
        $this->key = $key;

        include_once CORE.'Log.php';
        include_once CORE.'Casper.php';

        include_once Helpers.'Helper.php';
        $this->Helper = new Helper();

        include_once DATABASE.'Database.php';

        include_once MODEL.'Session.php';
        include_once MODEL.'Visitor.php';

        include_once CONTROLLER.'RouterController.php';

        include_once MODEL.'SiteConfig.php';
        include_once MODEL.'Location.php';

        include_once CORE.'Json.php';
        $this->Json = new Json(JSON_DIR);

        include_once MODEL.'Menu.php';
        include_once MODEL.'Language.php';
        include_once MODEL.'SchemaGenerator.php';

        include_once Helpers.'ControllerLoader.php';

        include_once CONTROLLER.'BannerController.php';
    }
    public function includeClass($className) {
        // BannerManager özel durumu
        if ($className === 'BannerManager') {
            $documentRoot = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
            $classFile = $documentRoot . '/App/Core/BannerManager.php';
        } else {
            $classFile = MODEL . $className . ".php";
        }

        if (file_exists($classFile)) {
            include_once($classFile);
        } else {
            throw new Exception("Class file not found: $classFile");
        }
    }
    public function loadView($viewName, $data = [])
    {
        // yol kontrolü ve dosya varsa yükleme
        $path = VIEW . $viewName . '.php';

        if (file_exists($path)) {
            // $data dizisini extract methodu ile değişkene dönüştürülür
            if (is_array($data)) {
                extract($data);
            }
            require_once $path;
        } else {
            // hata mesajı
            Log::write("View $viewName not found!", "error");
            throw new Exception("View $viewName not found!");
        }
    }
    public function checkDomain() {
        // CLI modunda domain kontrolü yapmayız
        if (php_sapi_name() === 'cli') {
            // hostDomain zaten setServer()'da belirlendi
            $this->serverName = $this->hostDomain;
            $this->localhost = true;
            $this->http = "http://";

            /**
             * @var string $dbServerName
             * @var string $dbUsername
             * @var string $dbPassword
             * @var string $dbName
             * @var string $dbLocalServerName
             * @var string $dbLocalUsername
             * @var string $dbLocalPassword
             * @var string $dbLocalName
             */
            include_once CONF . 'Sql.php';

            $this->dbServerName = $this->Helper->decrypt($dbLocalServerName, $this->key);
            $this->dbUsername = $this->Helper->decrypt($dbLocalUsername, $this->key);
            $this->dbPassword = $this->Helper->decrypt($dbLocalPassword, $this->key);
            $this->dbName = $this->Helper->decrypt($dbLocalName, $this->key);


            $this->cookieSecure=false;
            $this->cookieHttpOnly='';
            $this->cookieSameSite='';
            return;
        }
        
        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $this->serverName = $serverName;
        if (!in_array($serverName, $this->domain)) {
            // Eşleşme çıkmazsa sayfa yüklemesini durduralım
            die("Domain'e izin verilmiyor ($serverName)");
        }

        /**
         * @var string $dbServerName
         * @var string $dbUsername
         * @var string $dbPassword
         * @var string $dbName
         * @var string $dbLocalServerName
         * @var string $dbLocalUsername
         * @var string $dbLocalPassword
         * @var string $dbLocalName
         */
        include_once CONF . 'Sql.php';

        if (str_starts_with($serverName, 'l.')) {
            $this->localhost = true;
            $this->http = "http://";

            $this->dbServerName = $this->Helper->decrypt($dbLocalServerName, $this->key);
            $this->dbUsername = $this->Helper->decrypt($dbLocalUsername, $this->key);
            $this->dbPassword = $this->Helper->decrypt($dbLocalPassword, $this->key);
            $this->dbName = $this->Helper->decrypt($dbLocalName, $this->key);


            $this->cookieSecure=false;
            $this->cookieHttpOnly='';
            $this->cookieSameSite='';
        }
        else {
            $this->localhost = false;
            $this->http = "https://";

            $this->dbServerName = $this->Helper->decrypt($dbServerName, $this->key);
            $this->dbUsername = $this->Helper->decrypt($dbUsername, $this->key);
            $this->dbPassword = $this->Helper->decrypt($dbPassword, $this->key);
            $this->dbName = $this->Helper->decrypt($dbName, $this->key);

            $this->cookieSecure=true;
            $this->cookieHttpOnly=true;
            $this->cookieSameSite='None';

            ini_set('display_errors', 0);
        }
    }
    
    /**
     * Head Tracking Injector'ı yükle ve döndür
     * 
     * @param object $database Veritabanı bağlantısı
     * @return object HeadTrackingInjector instance
     */
    public function getHeadTrackingInjector($database) {
        if (!isset($this->HeadTrackingInjector)) {
            include_once ROOT . '/App/Helpers/HeadTrackingInjector.php';
            $this->HeadTrackingInjector = new HeadTrackingInjector($database, $this);
        }
        
        return $this->HeadTrackingInjector;
    }

    /**
     * Domain.php dosyasından yerel domain (l. ile başlayan) adını alır
     * GetLocalDomain.php'deki algoritmanın aynısı kullanılıyor
     */
    private function getLocalDomainFromConfig(): string {
        $rootDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
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
}
?>
