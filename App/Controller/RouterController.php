<?php
class Router {

    private $config;
    public string $url;
    public string $referrer;
    public string $query = "";

    public string $contentType="CONTENT";
    public string $contentUniqID=""; //DetectContent.php kullanıyor
    public string $contentName="";

    public string $message="";
    public string $status="";
    public string $controllerName="";
    public array $requestData = [];

    public int $languageID = 1;
    public string $languageCode = "tr";

    public string $seoTitle = "404";
    public string $seoDescription = "Sayfa bulunamadı";
    public string $seoLink = "/404";
    public string $seoKeywords = "404";
    public string $seoImage = "";

    public function __construct($config) {

        $this->config = $config;
        $subdomain =$config->subDomain;

        //url al
        $url = $this->getUrl();

        //geldiği sayfayı ve tipini belirle
        $this->setReferrer($url);

        //isteği bul
        if($this->detectRequest($url)) return;

        //query'i işle
        if($this->processQuery($url)) return;

        //içerik tipini nul
        $this->setContentType($url);

    }

    private function getUrl() {
        $url = $_SERVER['REQUEST_URI'] ?? null;
        if ($url === "favicon.ico") exit;
        $this->url = $url;
        return $url;
    }

    private function setReferrer($url){
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            $this->referrer = 'json';
        }
        else
        {
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;

            if (empty($referrer)) {
                $this->referrer = "/";
            }
            else
            {
                $parsedreferrer = parse_url($referrer);

                if (is_array($parsedreferrer) && isset($parsedreferrer['host'])) {
                    $domain = $this->config->domain;

                    if (in_array($parsedreferrer['host'], $domain)) {
                        $this->referrer = $referrer;
                    }
                    else
                    {
                        $this->referrer = "/?outSideReferrer";
                    }
                }
                else
                {
                    $this->referrer = "/?referrerError";
                }
            }
        }
        //die($this->referrer);
    }

    public function detectRequest($url){

        //öncelikle url içinde /control/ parametresi var mı diye bakacağız
        //varsa ikinci parametre controller adı olacak ve bu controller dosyası var mı diye bakacağız

        $url = str_replace("/?","", $url);
        $url = explode('/', $url);

        ############## /external control/ var mı kontrol et ##############

        if($this->isExternalControl($url)){
            return true;
        }

        ############## /control/ var mı kontrol et ##############

        if($this->isControl($url)){
            return true;
        }

        ############## /view/ var mı kontrol et ##############

        if($this->isView($url)){
            return true;
        }

        ############## /webservice/ var mı kontrol et ##############

        if($this->isWebService($url)){
            return true;
        }

        if($this->isAdmin($url)){
            return true;
        }

        return false;

    }

    private function processQuery($url) {
        // Eğer URL'de ? karakteri yoksa ama & varsa
        if (!str_contains($url, '?') && str_contains($url, '&')) {
            // İlk & işaretini ? ile değiştir
            $url = preg_replace('/&/', '?', $url, 1);
            $this->url = $url;
        }

        // URL'deki ? ve & sıralamasını kontrol edin
        if (strpos($url, '?') > strpos($url, '&')) {
            // İlk & işaretini ? ile değiştirin
            $url = preg_replace('/&/', '?', $url, 1);
            $this->url = $url;
        }

        // Query'i al
        $parsedQuery = parse_url($url, PHP_URL_QUERY);
        if ($parsedQuery !== null) {
            $this->query = $parsedQuery;
        }

        $query = $this->query;

        if (!empty($query)) {
            parse_str($query, $params);

            if (isset($params['q']) && (isset($params['languageID']) || isset($params['dilid']))) {
                $languageID = $params['languageID'] ?? $params['dilid'] ?? 1;
                $this->contentType = "CONTENT";
                $this->contentName = "Search";
                $this->languageID = $languageID;

                return true;
            }
        }

        return false;
    }

    private function isControl($url){

        ############## Controller Talebi Mi ##############

        if(isset($url[1]) && $url[1] == 'control'){

            if(isset($url[2])){

                ############## Controller Adını Alalım ##############

                $controllerName = $url[2];

                $controllerName = ucfirst($controllerName) . 'Controller';
                $this->controllerName = $controllerName;

                $controllerPath =  'App/Controller/'. $controllerName . '.php';

                ############## Controller'ı Doğrulayalım ##############

                if (file_exists($controllerPath)) {

                    ############## Metodu Belirleyelim [GET, POST] ##############

                    if(isset($url[3])){

                        $method = $url[3];

                        ############## Action Alalım [list, add, update] ##############
                        if(isset($url[4])){

                            $action = $url[4];
                            switch ($method) {
                                case 'get':
                                    $requestData = $_GET;
                                    $action = explode('&', $action)[0];
                                    break;

                                case 'post':
                                    // $_POST ve JSON verilerini php://input üzerinden alalım
                                    $requestData = $_POST;

                                    // Eğer $_POST boşsa, JSON verileri alalım
                                    if (empty($requestData)) {
                                        $jsonInput = file_get_contents('php://input');
                                        $jsonDecoded = json_decode($jsonInput, true);
                                        if ($jsonDecoded !== null) {
                                            $requestData = $jsonDecoded;
                                        }
                                    }
                                    break;

                                default:
                                    $requestData = [];
                                    break;
                            }

                            $requestData = array_map([$this, 'recursiveTrim'], $requestData);
                            $requestData['referrer'] = $this->referrer;
                            $requestData['action'] = $action;

                            $this->contentType = "CONTROLLER";
                            $this->contentName = $controllerName;
                            $this->requestData = $requestData;
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function isExternalControl($url){

        ############## Controller Talebi Mi ##############

        if(isset($url[1]) && $url[1] == 'external-control'){

            if(isset($url[2])){

                ############## Controller Adını Alalım ##############

                $controllerName = $url[2];

                $controllerName = ucfirst($controllerName) . 'Controller';
                $this->controllerName = $controllerName;

                $controllerPath =  'App/Controller/_External/'. $controllerName . '.php';

                ############## Controller'ı Doğrulayalım ##############

                if (file_exists($controllerPath)) {

                    ############## Metodu Belirleyelim [GET, POST] ##############

                    if(isset($url[3])){

                        $method = $url[3];

                        ############## Action Alalım [list, add, update] ##############
                        if(isset($url[4])){

                            $action = $url[4];
                            switch ($method) {
                                case 'get':
                                    $requestData = $_GET;
                                    $action = explode('&', $action)[0];
                                    break;

                                case 'post':
                                    // $_POST ve JSON verilerini php://input üzerinden alalım
                                    $requestData = $_POST;

                                    // Eğer $_POST boşsa, JSON verileri alalım
                                    if (empty($requestData)) {
                                        $jsonInput = file_get_contents('php://input');
                                        $jsonDecoded = json_decode($jsonInput, true);
                                        if ($jsonDecoded !== null) {
                                            $requestData = $jsonDecoded;
                                        }
                                    }
                                    break;

                                default:
                                    $requestData = [];
                                    break;
                            }

                            $requestData = array_map([$this, 'recursiveTrim'], $requestData);
                            $requestData['referrer'] = $this->referrer;
                            $requestData['action'] = $action;

                            $this->contentType = "CONTROLLER";
                            $this->contentName = '_External/'.$controllerName;
                            $this->requestData = $requestData;
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function isView($url){

        ############## View Talebi Mi ##############

        if(isset($url[1]) && $url[1] == 'view'){

            if(isset($url[2])){

                ############## View Adını Alalım ##############

                $viewName = $url[2];

                $viewName = ucfirst($viewName);

                $viewPath =  'App/View/'. $viewName;

                if (is_dir($viewPath)) {

                    ############## Talebi Belirleyelim [add, list, ...] ##############

                    if(isset($url[3])){

                        $action = $url[3];

                        ############## App\Helpers\ViewLoader talebe göre istek getirecek ###

                        $viewFile = $viewPath . '/' . $action . '.php';

                        if (file_exists($viewFile)) {
                            $this->contentName = $viewName . '/' . $action;
                            return true;
                        }
                    }
                }
            }

        }
        return false;
    }

    private function isWebService($url){

        ############## WebService Talebi Mi ##############

        if(isset($url[1]) && $url[1] == 'webservice'){

            if(isset($url[2])){

                ############## WebService Adını Alalım ##############

                $webserviceName = $url[2];

                $webserviceName = ucfirst($webserviceName);

                $webservicePath =  'App/Webservice/'. $webserviceName . '.php';

                ############## WebService'ı Doğrulayalım ##############

                if (file_exists($webservicePath)) {

                    ############## Metodu Belirleyelim [GET, POST] ##############

                    if(isset($url[3])){

                        $method = $url[3];

                        ############## Action Alalım [list, add, update] ##############
                        if(isset($url[4])){

                            $action = $url[4];

                            switch ($method) {
                                case 'get':
                                    $action = explode('&', $action)[0];
                                    $requestData = $_GET;
                                    break;
                                case 'post':
                                    $requestData = $_POST;
                                    if (empty($requestData)) {
                                        $jsonInput = file_get_contents('php://input');
                                        $jsonDecoded = json_decode($jsonInput, true);
                                        if ($jsonDecoded !== null) {
                                            $requestData = $jsonDecoded;
                                        }
                                    }
                                    break;
                                default:
                                    $requestData = [];
                                    break;
                            }

                            $_POST = array_map([$this, 'recursiveTrim'], $_POST);
                            $requestData['referrer'] = $this->referrer;
                            $requestData['action'] = $action;

                            $this->contentType = "WEBSERVICE";
                            $this->controllerName = $webserviceName;
                            $this->contentName = $webserviceName;
                            $this->requestData = $requestData;
                            return true;
                        }
                    }
                }
            }
        }
        
        // Gemini Asistan API İstekleri
        if(isset($url[1]) && $url[1] == 'assistant-api'){
            if(isset($url[2])){
                $assistantAction = $url[2];
                $requestData = $_POST;
                
                if (empty($requestData)) {
                    $jsonInput = file_get_contents('php://input');
                    $jsonDecoded = json_decode($jsonInput, true);
                    if ($jsonDecoded !== null) {
                        $requestData = $jsonDecoded;
                    }
                }

                $requestData = array_map([$this, 'recursiveTrim'], $requestData);
                $requestData['action'] = $assistantAction;
                $requestData['referrer'] = $this->referrer;

                $this->contentType = "ASSISTANT_API";
                $this->controllerName = "AssistantController";
                $this->contentName = "AssistantController";

                $this->requestData = $requestData;

                return true;
            }
        }
        
        return false;
    }

    private function isAdmin($url){

        ############## Admin Controller Talebi Mi ##############

        // /?/admin/product/list/getProductList
        if(isset($url[1]) && $url[1] == 'admin'){

            if(isset($url[2])){

                ############## Controller Adını Alalım ##############

                $controllerName = ucfirst($url[2]);

                $controllerName = 'Admin/'.$controllerName.'Controller';
                $this->controllerName = $controllerName;

                $controllerPath =  'App/Controller/'. $controllerName . '.php';

                ############## Controller'ı Doğrulayalım ##############

                if (file_exists($controllerPath)) {

                    ############## Metodu Belirleyelim [list, add, update] ##############

                    if(isset($url[3])){

                        $method = $url[3];

                        ############## Action Alalım [getPageList, addProduct, updateProduct] ##############
                        if(isset($url[4])){

                            $action = $url[4];
                            switch ($method) {
                                case 'get':
                                    $requestData = $_GET;
                                    $action = explode('&', $action)[0];
                                    break;

                                case 'post':
                                    // $_POST ve JSON verilerini php://input üzerinden alalım
                                    $requestData = $_POST;

                                    // Eğer $_POST boşsa, JSON verileri alalım
                                    if (empty($requestData)) {
                                        $jsonInput = file_get_contents('php://input');
                                        $jsonDecoded = json_decode($jsonInput, true);
                                        if ($jsonDecoded !== null) {
                                            $requestData = $jsonDecoded;
                                        }
                                    }
                                    break;

                                default:
                                    $requestData = [];
                                    break;
                            }

                            $requestData = array_map([$this, 'recursiveTrim'], $requestData);
                            $requestData['referrer'] = $this->referrer;
                            $requestData['action'] = $action;
                            $requestData['url'] = $url;


                            $this->contentType = "ADMIN";
                            $this->contentName = $controllerName;

                            $this->requestData = $requestData;

                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function setContentType($url) {

        if ( (empty($url) || $url == '/') ) {
            $this->contentType = "CONTENT";
            $this->contentName = "HomePage";
            $this->controllerName = "HomePage";
            return;
        }
    }

    public function recursiveTrim($data) {
        if (is_array($data)) {
            return array_map([$this, 'recursiveTrim'], $data);
        }
        return is_string($data) ? trim($data) : $data;
    }
}