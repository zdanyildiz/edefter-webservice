<?php

class Session
{
    private string $cookieExpire;
    private string $cookiePath;
    private string $cookieDomain;
    private bool $cookieSecure;
    private bool $cookieHttpOnly;
    private string $cookieSameSite;
    private $key;
    private Helper $helper;

    public function __construct($key,$expire = 3600, $path = "/", $domain = "", $secure = false, $httponly = false, $samesite = "")
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();

            //Log::write("Session->Oturum Başlatılmamış","special");

            if (!array_key_exists("casper", $_SESSION)) {

                //Log::write("Session->Casper oturumu yok","special");

                $this->addSession("casper", new Casper());
            }
            /*else{
                Log::write("Session->Casper oturumu var","special");
            }*/
        }

        $this->cookieExpire = $expire * 24 * 30;
        $this->cookiePath = $path;
        $this->cookieDomain = $domain;
        $this->cookieSecure = $secure;
        $this->cookieHttpOnly = $httponly;
        $this->cookieSameSite = $samesite;

        $this->key = $key;
        $this->helper = new Helper();
    }

    public function getCasper()
    {
        //Log::write("Session->Casper alındı","special");

        return $_SESSION["casper"];
    }
    // Add data to the session
    public function addSession($key, $value): void
    {
        //Log::write("Session->Oturum oluşturuldu: $key","special");

        $_SESSION[$key] = $value;
    }

    // Get data from the session
    public function getSession($key): mixed
    {
        return $_SESSION[$key] ?? [];
    }

    function updateSession(string $key, mixed $value): bool
    {
        //Log::write("Session->Oturum güncelleniyor: $key","special");
        // Oturum başlatılmamışsa başlatalım

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Anahtar mevcut değilse hata mesajı verip false dönelim
        if (!array_key_exists($key, $_SESSION)) {
            Log::write("Oturum bulunamadı: $key","special");
            echo "Oturumda '$key' anahtarı bulunamadı.";
            return false;

        }

        // Yeni değeri atayalım
        $_SESSION[$key] = $value;

        //Log::write("Session->Oturum güncellendi: $key","special");
        // Oturumdaki değişiklikleri kaydedelim
        //session_commit();

        return true;
    }
    // Remove data from the session
    public function removeSession($key):void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Add or update object in the session
    public function setObject($key, $object):void
    {
        $_SESSION[$key] = serialize($object);
    }

    // Get object from session
    public function getObject($key): ?object
    {
        return isset($_SESSION[$key]) ? unserialize($_SESSION[$key]) : null;
    }

    // Add a cookie
    public function addCookie($cookieName, array $value, $expire = 1):void
    {
        //Log::write("Cookie oluşturuluyor->$cookieName: ". json_encode($value),"special");
        //gelen expire değerini ay olarak kabul edelim ve bugün itibariyle  x ay ekleyelim.
        $expire = time() + ($this->cookieExpire * $expire);

        $cookieSettings=[
            'expires' => $expire,
            'path' => $this->cookiePath,
            'domain' => $this->cookieDomain,
            'secure' => $this->cookieSecure,
            'httponly' => $this->cookieHttpOnly,
            'samesite' => $this->cookieSameSite,
        ];

        //Log::write("Cookie Ayarları->$cookieName:" .json_encode($cookieSettings),"error");

        $encodedValue = json_encode($value);
        $encryptedValue = $this->helper->encrypt($encodedValue,$this->key);

        //Log::write("Cookie değeri şifrelendi->$cookieName: $encryptedValue","special");

        $cookieSize = strlen($encryptedValue);
        if ($cookieSize > 4096) {
            Log::write("Çerez boyutu 4096 byte'ı aşıyor->$cookieName: $cookieSize byte","error");
            Log::write("Çerez içeriği: $encodedValue","info");
            // Burada uygun bir işlem yapabilirsiniz (örneğin hata mesajı göstermek veya veriyi küçültmek)
            return;
        }

        setcookie($cookieName, $encryptedValue, $cookieSettings);
    }

    // Get a cookie
    public function getCookie($cookieName): ?array
    {
        if(isset($_COOKIE[$cookieName])) {
            //Log::write("Cookie alındı->$cookieName: ". json_decode($_COOKIE[$cookieName]),"special");
            $decryptedValue = $this->helper->decrypt($_COOKIE[$cookieName], $this->key);
            return json_decode($decryptedValue, true);
        }
        return [];
    }

    // Delete a cookie
    public function deleteCookie($cookieName): bool
    {
        if(isset($_COOKIE[$cookieName])){
            unset($_COOKIE[$cookieName]);
            setcookie($cookieName, "", -1, '/');
            return true;
        } else {
            return false;
        }
    }
}