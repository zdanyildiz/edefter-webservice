<?php

class Visitor {
    public $bankIps = ["195.244.55.195","212.252.97.250","185.187.184.84"];

    public function getVisitorInformation() {

        $visitorUniqID=$this->createPassword(20,2);
        $visitorEntryTime=date("Y-m-d H:i:s");
        $visitorIP=$this->getIP();
        $visitorBrowser=$_SERVER['HTTP_USER_AGENT'] ?? null;
        if(empty($visitorBrowser)){
            Log::write("Ziyaretçi tarayıcı bilgisi alınamadı.","error");
            return false;
        }
        $visitorIsMember=['memberStatus'=>false];
        $visitorVisitCount=1;
        $HTTP_ACCEPT_LANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'tr-TR';
        $visitorLanguage=substr($HTTP_ACCEPT_LANGUAGE, 0, 2);
        $visitorRemember = false;

        //ziyaretçi banka robotu değilse
        if(!in_array($visitorIP,$this->bankIps)){
            $visitorGeo = $this->getGeoPlugin($visitorIP);
        }
        else{
            $visitorGeo = [];
        }

        $visitorGetCart = false;


        return [
            'visitorUniqID' => $visitorUniqID,
            'visitorEntryTime' => $visitorEntryTime,
            'visitorIP' => $visitorIP,
            'visitorBrowser' => $visitorBrowser,
            'visitorIsMember' => $visitorIsMember,
            'visitorVisitCount'=>$visitorVisitCount,
            'visitorLanguage'=>$visitorLanguage,
            'visitorGeo'=>$visitorGeo,
            'visitorCart'=>[],
            'visitorRemember' => $visitorRemember,
            'visitorGetCart' => $visitorGetCart
        ];
    }

    public function getGeoPlugin($visitorIP) {

        if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) {
            return $_SERVER['HTTP_CF_IPCOUNTRY'];
        }

        $isBot = $this->isBot();

        if ($isBot) {
            return [];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://www.geoplugin.net/php.gp?ip=' . $visitorIP,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1,
            // php.gp uç noktası serialize edilmiş PHP döndürdüğü için Content-Type header'ına gerek yok
            // CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ));

        $content = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // cURL hata kontrolü
        if ($content === false) {
            $error = curl_error($curl);
            curl_close($curl);
            Log::write('Geoplugin API cURL hatası: ' . $error, 'error');
            return null; // veya uygun bir hata değeri
        }

        // HTTP durum kodu kontrolü
        if ($httpCode !== 200) {
            curl_close($curl);
            Log::write('Geoplugin API HTTP hatası: Kod ' . $httpCode . ' - Yanıt: ' . $content, 'error');
            return null;
        }

        curl_close($curl);

        // İçeriği unserialize edelim.
        // @ operatörü, unserialize başarısız olursa uyarı vermesini engeller, ancak sonrasında kontrol etmek daha güvenlidir.
        $parsedContent = @unserialize($content);

        // Unserialize işleminin başarılı olup olmadığını kontrol edelim.
        // $content === 'b:0;' durumu, serialize edilmiş false değeridir ve geçerlidir.
        if ($parsedContent === false && $content !== 'b:0;') {
            Log::write('PHP unserialize hatası. Gelen içerik: ' . $content, 'error');
            return null;
        }

        if (is_array($parsedContent) && isset($parsedContent['geoplugin_countryCode'])) {
            return $parsedContent['geoplugin_countryCode'];
        } else {
            Log::write('geoplugin_countryCode bulunamadı veya unserialize sonucu dizi değil.', 'error');
            // Hata ayıklama için tüm yanıtı loglayabilirsiniz:
            // Log::write('Tam Geoplugin Yanıtı (unserialize sonrası): ' . print_r($parsedContent, true), 'debug');
            return null;
        }
    }

    public function isBot() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $botAgents = array("Googlebot", "Bingbot", "Slurp", "DuckDuckBot", "Baiduspider", "YandexBot", "Sogou", "Exabot", "facebot", "ia_archiver");
        if(empty($userAgent)){
            return false;
        }
        foreach($botAgents as $bot) {
            if (str_contains($userAgent, $bot)) {
                return true; // Bot tespit edildi
            }
        }

        return false; // Bot tespit edilmedi
    }

    public function createPassword($value,$type){

        if($type==0) $chars = "0123456789";
        if($type==1) $chars = "ABCDEFGHJKMNPRSTUVYZQWX";
        if($type==2) $chars = "ABCDEFGHJKMNPRSTUVYZQWX23456789";
        if($type==3) $chars = "abcdefghjklmnoprstuvyzqxABCDEFGHJKLMNOPRSTUVYZQWX0123456789%=*";
        unset($Nasil);
        return substr(str_shuffle($chars),0,$value);
    }

    public function getIP(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $startTime = microtime(true);
            $ip = $_SERVER['REMOTE_ADDR'] ?? "127.0.0.1";
            $url = "https://api.ipify.org";
            $timeoutSeconds = 1;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => $timeoutSeconds,
                CURLOPT_TIMEOUT => $timeoutSeconds,
            ));


            $externalIpResponse = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlErrorNo = curl_errno($curl);
            $curlErrorMessage = curl_error($curl);
            curl_close($curl);

            if($curlErrorNo === 0 && $httpCode === 200){
                $trimmedIp = trim($externalIpResponse);
                if(filter_var($trimmedIp, FILTER_VALIDATE_IP)){
                    $ip = $trimmedIp;
                }
                else{
                    Log::write("Harici IP servisi geçerli bir ip döndürmedi veya bir hata oluştu");
                }
            }
            else{
                Log::write("Harici IP servisine ulaşılamadı");
            }
            $endTime = microtime(true);
            $durationMilliseconds = ($endTime - $startTime) * 1000;
            Log::write("IP alma süresi: ". round($durationMilliseconds),"info");
        }
        return $ip;
    }

    public function getBankIps():array{
        return $this->bankIps;
    }
}
