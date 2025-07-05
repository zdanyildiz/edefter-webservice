<?php
/**
 * @var Database $db
 * @var Helper $helper
 * @var Config $config
 * @var array $requestData
 * @var Json $json
 */

// Gelen isteği logla
$logData = [
    'time' => date('Y-m-d H:i:s'),
    'action' => $requestData['action'] ?? 'belirtilmedi',
    'method' => $_SERVER['REQUEST_METHOD'],
    'request_data' => $requestData,
    'ip_address' => $_SERVER['REMOTE_ADDR']
];

// Log dosyasının yolu
$logFile = LOG_DIR . 'chatbot_requests.log';

// Log verisini JSON formatında dosyaya ekle
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);


header("Content-Type: application/json; charset=utf-8");

// Veritabanı tablolarını kontrol et ve oluştur
$sqlFile = ROOT . 'temp/chatbot_tables.sql';
if (file_exists($sqlFile)) {
    $db->runSqlFile($sqlFile);
}

$action = $requestData['action'] ?? null;


require_once MODEL . 'Member.php';
$member = new Member($db);
require_once APP .'Webservice/MemberModel.php';
$memberModel = new MemberModel($db);
require_once APP . 'Webservice/ChatbotModel.php';
$chatbotModel = new ChatbotModel($db);

function returnAndExit($status, $message, $data = [], $httpStatusCode = 200){
    http_response_code($httpStatusCode);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Kullanıcı kimlik doğrulama kontrolü
$computerID = $requestData['computerId'] ?? "";
$email = $requestData['email'] ?? "";

if(empty($email)){
    returnAndExit("error", "E-posta boş olamaz", [], 401);
}

if(empty($computerID)){
    returnAndExit("error", "Bilgisayar id boş olamaz", [], 401);
}

$encryptedEmail = $helper->encrypt($email,$config->key);
$memberResult = $member->getMemberInfoByEmail($encryptedEmail);

if(!$memberResult){
    returnAndExit("error", "Üyeliğiniz bulunamadı", [], 401);
}

$memberResult = $memberResult[0];
$memberID = $memberResult['uyeid'];

$userSession = $memberModel->checkUsers($memberID, $computerID);

if (!$userSession) {
    returnAndExit("error", "Yetkisiz erişim veya oturum geçersiz.", [], 401); // HTTP 401 Unauthorized
}

// Kullanıcının chatbot kullanım bilgilerini al veya oluştur
$userChatbotUsage = $chatbotModel->getUserChatbotUsage($memberID);
$currentDate = date('Y-m-d');

if (!$userChatbotUsage) {
    // Kullanıcı için chatbot kullanım kaydı yoksa, varsayılan olarak 'standard' paketini ata
    $standardPackage = $chatbotModel->getPackageByName('standard');
    if (!$standardPackage) {
        returnAndExit("error", "Chatbot paket bilgileri bulunamadı.", [], 500);
    }
    $standardPackage = $standardPackage[0];

    $chatbotModel->createUserChatbotUsage(
        $memberID,
        $standardPackage['id'],
        $standardPackage['daily_message_limit'],
        $standardPackage['daily_token_limit'],
        $currentDate
    );
    $userChatbotUsage = $chatbotModel->getUserChatbotUsage($memberID);
    $userChatbotUsage = $userChatbotUsage[0];
} else {
    $userChatbotUsage = $userChatbotUsage[0];
    // Günlük limit sıfırlama mantığı
    if ($userChatbotUsage['last_message_date'] != $currentDate) {
        $packageInfo = $chatbotModel->getPackageByName($userChatbotUsage['package_name']);
        if (!$packageInfo) {
            returnAndExit("error", "Chatbot paket bilgileri bulunamadı.", [], 500);
        }
        
        $packageInfo = $packageInfo[0];

        $userChatbotUsage['remaining_messages'] = $packageInfo['daily_message_limit'];
        $userChatbotUsage['remaining_tokens'] = $packageInfo['daily_token_limit'];
        $userChatbotUsage['last_message_date'] = $currentDate;
        $chatbotModel->updateUserChatbotUsage(
            $memberID,
            $userChatbotUsage['remaining_messages'],
            $userChatbotUsage['remaining_tokens'],
            $userChatbotUsage['last_message_date'],
            $userChatbotUsage['total_tokens_used'] // Total token'ı sıfırlamıyoruz
        );
    }
}

if ($action == "status"){
    returnAndExit("success", "OK", [
        "package_type" => $userChatbotUsage['package_name'],
        "remaining_messages" => $userChatbotUsage['remaining_messages'],
        "daily_message_limit" => $userChatbotUsage['daily_message_limit']
    ]);
} else if ($action == "consent"){
    $consentAction = $requestData['consentAction'] ?? null;

    if (!in_array($consentAction, ['accept', 'decline'])) {
        returnAndExit("error", "Geçersiz onay aksiyonu.", [], 400);
    }

    $result = $chatbotModel->logUserConsent($memberID, $consentAction);
    if ($result) {
        returnAndExit("success", "Kullanıcı onayı başarıyla kaydedildi.");
    } else {
        returnAndExit("error", "Kullanıcı onayı kaydedilemedi.", [], 500);
    }
} else if ($action == "message"){
    $userMessage = $requestData['message'] ?? null;

    if (empty($userMessage)) {
        returnAndExit("error", "Mesaj boş olamaz.", [], 400);
    }

    // Yeni eklenen ön-kontrol: Kalan token limiti çok düşükse API çağrısını engelle
    $minimumRequiredTokens = 500; // Bu değeri ihtiyaca göre ayarlayabilirsiniz
    if ($userChatbotUsage['remaining_tokens'] < $minimumRequiredTokens) {
        returnAndExit("error", "Günlük token limitiniz çok düşük (" . $userChatbotUsage['remaining_tokens'] . " token kaldı). Yeni bir mesaj göndermek için yeterli tokenınız bulunmamaktadır. Daha fazla kullanım için Premium pakete geçin veya limitin sıfırlanmasını bekleyin.", [], 403);
    }

    if ($userChatbotUsage['remaining_messages'] <= 0) {
        returnAndExit("error", "Günlük mesaj limitinize ulaştınız. Daha fazla kullanım için Premium pakete geçin.", [], 403);
    }

    // Gemini API ile HTTP isteği gönder
    $geminiApiKey = '';
    $envFile = ROOT . '.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
        foreach ($lines as $line) {
            //Log::write(".env: " .$line, "info");
            if (str_starts_with(trim($line), '#')) {
                continue;
            }
            if (str_contains($line, 'GEMINI_API_KEY')) {
                list($name, $value) = explode('=', $line, 2);
                $geminiApiKey = trim($value);
                break;
            }
        }
    }

    if (empty($geminiApiKey)) {
        returnAndExit("error", "Gemini API anahtarı bulunamadı. Lütfen .env dosyasını kontrol edin.", [], 500);
    }

    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=" . $geminiApiKey;

    $assistantPromptFile = APP . 'Webservice/assistant_prompt.txt';
    $assistantPrompt = "";
    if (file_exists($assistantPromptFile)) {
        $assistantPrompt = file_get_contents($assistantPromptFile);
    }

    $systemInfo = $requestData['systemInfo'] ?? null; // systemInfo'yu al

    // Sohbet geçmişini al
    $chatHistory = $chatbotModel->getChatHistory($memberID, $currentDate);
    $contents = [];

    $initialPrompt = $assistantPrompt;
    if (!empty($systemInfo)) {
        $initialPrompt .= "\n\nKullanıcı Sistem Bilgileri: " . json_encode($systemInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);;
    }
    $contents[] = ['parts' => [['text' => $initialPrompt]], 'role' => 'user'];

    // Sohbet geçmişini ekle (en eskiden en yeniye doğru) 
    foreach (array_reverse($chatHistory) as $chat) {
        $contents[] = ['parts' => [['text' => $chat['user_message']]], 'role' => 'user'];
        $contents[] = ['parts' => [['text' => $chat['chatbot_response']]], 'role' => 'model'];
    }

    // Kullanıcının mevcut mesajını ekle
    $contents[] = ['parts' => [['text' => $userMessage]], 'role' => 'user'];

    // DEBUG: Sohbet geçmişini ve gönderilecek contents dizisini logla
    //error_log("DEBUG: Chat History: " . print_r($chatHistory, true));
    //error_log("DEBUG: Contents to Gemini: " . print_r($contents, true));

    $postData = json_encode(['contents' => $contents]);

    // Gemini API'ye gönderilen veriyi logla
    $geminiRequestLogFile = LOG_DIR . 'chatbot_gemini_request.log';
    file_put_contents($geminiRequestLogFile, date('Y-m-d H:i:s') . " - Gemini Request: " . $postData . "\n", FILE_APPEND);

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => $postData,
            'ignore_errors' => true // Hataları yakalamak için
        ]
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($apiUrl, false, $context);

    if ($result === FALSE) {
        error_log("Gemini API isteği başarısız oldu.");
        returnAndExit("error", "Chatbot ile iletişimde bir sorun oluştu. Lütfen daha sonra tekrar deneyin.", [], 500);
    }

    $response = json_decode($result, true);

    $geminiResponse = "";
    $promptTokens = 0;
    $completionTokens = 0;
    $totalTokens = 0;

    if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        $geminiResponse = $response['candidates'][0]['content']['parts'][0]['text'];

        // Gerçek token hesaplaması için usageMetadata kontrolü
        if (isset($response['usageMetadata'])) {
            $promptTokens = $response['usageMetadata']['promptTokenCount'] ?? 0;
            $completionTokens = $response['usageMetadata']['candidatesTokenCount'] ?? 0;
            $totalTokens = $response['usageMetadata']['totalTokenCount'] ?? 0;
        } else {
            // usageMetadata yoksa eski tahmin yöntemini kullan (yedek olarak)
            $promptTokens = strlen($userMessage) / 4; // Yaklaşık olarak 4 karakter 1 token
            $completionTokens = strlen($geminiResponse) / 4;
            $totalTokens = $promptTokens + $completionTokens;
        }

    } else if (isset($response['error']['message'])) {
        error_log("Gemini API Hatası: " . $response['error']['message']);
        returnAndExit("error", "Gemini API'den hata döndü: " . json_encode($response['error']['message']), [], 500);
    } else {
        error_log("Gemini API'den beklenmedik yanıt: " . $result);
        returnAndExit("error", "Chatbot ile iletişimde beklenmedik bir sorun oluştu.", [], 500);
    }

    

    // Kullanım bilgilerini güncelle
    $newRemainingMessages = $userChatbotUsage['remaining_messages'] - 1;
    $newRemainingTokens = $userChatbotUsage['remaining_tokens'] - $totalTokens;
    $newTotalTokensUsed = $userChatbotUsage['total_tokens_used'] + $totalTokens;

    $chatbotModel->updateUserChatbotUsage(
        $memberID,
        $newRemainingMessages,
        $newRemainingTokens,
        $currentDate,
        $newTotalTokensUsed
    );

    // İsteği logla
    $chatbotModel->logChatbotRequest(
        $memberID,
        $promptTokens,
        $completionTokens,
        $totalTokens,
        $userMessage,
        $geminiResponse,
        $result // Ham API yanıtını ekle
    );

    returnAndExit("success", "OK", [
        "response" => $geminiResponse,
        "remaining_messages" => $newRemainingMessages,
        "remaining_tokens" => $newRemainingTokens
    ]);
} else {
    returnAndExit("error", "Geçersiz aksiyon.");
}


?>