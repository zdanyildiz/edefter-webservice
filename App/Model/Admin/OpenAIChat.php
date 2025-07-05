<?php

define('API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('ASSISTANT_ID', getenv('OPENAI_ASSISTANT_ID') ?: '');
define('ASSISTANT_MODEL', getenv('OPENAI_ASSISTANT_MODEL') ?: '');
define('API_URL', 'https://api.openai.com/v1/');

class OpenAIChat {
    private $apiKey;
    private $apiUrl;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
        $this->apiUrl = API_URL;
    }

    private function sendRequest($url, $data, $method = 'POST') {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function createChatCompletion($messages) {
        $url = $this->apiUrl . 'chat/completions';
        $data = [
            'model' => ASSISTANT_MODEL,
            'messages' => $messages,
        ];

        return $this->sendRequest($url, $data);
    }
}

$assistant = new OpenAIChat(API_KEY);

// Yeni bir sohbet başlatmak ve soru sormak
$messages = [
    ['role' => 'system', 'content' => "Sen, Global Pozitif Şirketi'nin ürettiği Pozitif E-Ticaret yazılımının yardımcı asistanısın. Kullanıcılara site kontrol paneli kullanımıyla ilgili yardımda bulunacaksın. Kullanıcının ilk mesajı selamlaşma şeklindeyse ve bir soru içermiyorsa, 'Pozitif E-Ticaret paneliyle ilgili size nasıl yardımcı olabilirim?' demelisin. İlk mesajda, Pozitif E-Ticaret altyapısı panel kullanımıyla ilgili yardımcı olabileceğini belirtmelisin. Sana site, panel, logo, platform dendiğinde Pozitif E-Ticaret sisteminden bahsedildiğini anlamalısın. Cevaplarını Pozitif E-Ticaret sistemine göre hazırlamalısın. Bağlantı paylaşacağın zaman <a href='/_y/s/s/urunler/AddProduct.php' target='_blank'>/_y/s/s/urunler/AddProduct.php</a>, <a href='/_y/s/s/urunler/ProductList.php' target='_blank'>/_y/s/s/urunler/ProductList.php</a> gibi html formatında yazmalısın. Bağlantı uyduramaz ya da tahminde bulunamazsın. fine-tuning yapılırken öğrendiğin bağlantıları yazabilirsin"],
    ['role' => 'user', 'content' => 'Logo değiştirme sayfasının tam linkini yazar mısın'],
];

$response = $assistant->createChatCompletion($messages);
echo $response['choices'][0]['message']['content'];