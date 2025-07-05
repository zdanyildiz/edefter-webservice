<?php
/**
 * @var Config $config
 * @var Database $db
 * @var Helper $helper
 * @var Session $session
 * @var array $requestData
 * @var Casper $casper
 */
$casper = $session->getCasper();

$action = $requestData['action'] ?? null;
if(!isset($action)){
    echo json_encode([
        'status' => 'error',
        'message' => 'Lütfen gerekli alanları doldurunuz.',
        'memberData' => []
    ]);
    exit();
}
$config = $casper->getConfig();
$helper = $config->Helper;
$languageCode = $requestData['languageCode'] ?? 'tr';
$languageModel = new Language($db,$languageCode);
$languageModel->getTranslations($languageCode);

include_once MODEL.'Form.php';

if($action=='contactForm'){

    $websites = $_GET['websites'] ?? $requestData['websites'];
    if (!empty($websites)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $csrfToken = $requestData['csrfToken'] ?? "";

    //csrf token kontrolü yapalım
    if(!$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
    $defaultSecretKey = $cloudflareConfig['default']['secret_key'];
    $currentHostname = $_SERVER['HTTP_HOST'];
    if (isset($cloudflareConfig['sites'][$currentHostname])) {
        $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['secret_key'];
    }

    $token = $_POST['cf-turnstile-response']; // Turnstile token
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret' => $defaultSecretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'], // Opsiyonel
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $verification = json_decode($result);

    if (!$verification->success) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $name = $requestData['namesurname'] ?? '';
    $email = $requestData['email'] ?? '';
    $message = $requestData['message'] ?? '';
    $phone = $requestData['phone'] ?? '';

    if (empty($name) || empty($email) || empty($message) || empty($phone)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_eksiksiz_doldurun_yazi
        ]);
        exit();
    }

    $encryptName = $helper->encrypt($name,$config->key);
    $encryptEmail = $helper->encrypt($email,$config->key);
    $encryptPhone = $helper->encrypt($phone,$config->key);

    $form = new Form($db);
    $formData = [
        'name' => $encryptName,
        'email' => $encryptEmail,
        'message' => $message,
        'phone' => $encryptPhone
    ];

    $form->beginTransaction("addContactForm");
    $formInsert = $form->addContactForm($formData);

    if (!$formInsert) {
        $form->rollBack("addContactForm");
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_basarisiz_yanit
        ]);
        exit();
    }

    $form->commit("addContactForm");
    echo json_encode([
        'status' => 'success',
        'message' => _form_gonderim_basarili
    ]);
    exit();
}
elseif ($action == 'newsletterForm') {

    $csrfToken = $requestData['csrf_token'] ?? "";

    //csrf token kontrolü yapalım
    if(!$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." csrf"
        ]);
        exit();
    }

    $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
    $defaultSecretKey = $cloudflareConfig['default']['secret_key'];
    $currentHostname = $_SERVER['HTTP_HOST'];
    if (isset($cloudflareConfig['sites'][$currentHostname])) {
        $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['secret_key'];
    }

    $token = $requestData['cf-turnstile-response'];
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret' => $defaultSecretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'], // Opsiyonel
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $verification = json_decode($result);

    if (!$verification->success) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi ." Cloudflare"
        ]);
        exit();
    }

    $name = $requestData['namesurname'] ?? '';
    $email = $requestData['email'] ?? '';

    if (empty($name) || empty($email)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_eksiksiz_doldurun_yazi
        ]);
        exit();
    }

    $encryptName = $helper->encrypt($name,$config->key);
    $encryptEmail = $helper->encrypt($email,$config->key);

    $form = new Form($db);

    $checkNewsletter = $form->checkNewsletter($encryptName);
    if (!empty($checkNewsletter)){
        echo json_encode([
            "status"=>"error",
            "message"=>_uye_eposta_kayitli
        ]);
        exit;
    }


    $form->beginTransaction("addNewsletter");
    $formInsert = $form->addNewsletter([
        'name' => $encryptName,
        'email' => $encryptEmail
    ]);

    if (!$formInsert) {
        $form->rollBack("addNewsletter");
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_basarisiz_yanit
        ]);
        exit();
    }

    $form->commit("addNewsletter");
    echo json_encode([
        'status' => 'success',
        'message' => _form_gonderim_basarili
    ]);
    exit();
}
elseif ($action == 'appointmentForm') {
    
    $websites = $_GET['websites'] ?? $requestData['websites'];
    if (!empty($websites)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $csrfToken = $requestData['csrfToken'] ?? "";

    //csrf token kontrolü yapalım
    if(!$helper->verifyCsrfToken($csrfToken)){
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }

    $cloudflareConfig = json_decode(file_get_contents(CONF . 'CloudFlare.json'), true);
    $defaultSecretKey = $cloudflareConfig['default']['secret_key'];
    $currentHostname = $_SERVER['HTTP_HOST'];
    if (isset($cloudflareConfig['sites'][$currentHostname])) {
        $defaultSiteKey = $cloudflareConfig['sites'][$currentHostname]['secret_key'];
    }

    $token = $_POST['cf-turnstile-response']; // Turnstile token
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $data = [
        'secret' => $defaultSecretKey,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'], // Opsiyonel
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $verification = json_decode($result);

    if (!$verification->success) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_spam_yazi
        ]);
        exit();
    }    $name = $requestData['name'] ?? '';
    $email = $requestData['email'] ?? '';
    $phone = $requestData['phone'] ?? '';
    $appointmentDate = $requestData['appointmentDate'] ?? '';
    $appointmentTime = $requestData['appointmentTime'] ?? '';
    $message = $requestData['message'] ?? '';

    if (empty($name) || empty($email) || empty($phone) || empty($appointmentDate) || empty($appointmentTime)) {
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_eksiksiz_doldurun_yazi
        ]);
        exit();
    }

    // Randevu tarih validasyonu - geçmiş tarih kontrolü
    $appointmentDateTime = strtotime($appointmentDate . ' ' . $appointmentTime);
    if ($appointmentDateTime <= time()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Randevu tarihi geçmiş bir tarih olamaz.'
        ]);
        exit();
    }

    $encryptName = $helper->encrypt($name,$config->key);
    $encryptEmail = $helper->encrypt($email,$config->key);
    $encryptPhone = $helper->encrypt($phone,$config->key);

    $form = new Form($db);    $formData = [
        'name' => $encryptName,
        'email' => $encryptEmail,
        'phone' => $encryptPhone,
        'appointmentDate' => $appointmentDate,
        'appointmentTime' => $appointmentTime,
        'message' => $message
    ];

    $form->beginTransaction("addAppointmentForm");
    $formInsert = $form->addAppointmentForm($formData);

    if (!$formInsert) {
        $form->rollBack("addAppointmentForm");
        echo json_encode([
            'status' => 'error',
            'message' => _uye_ol_form_basarisiz_yanit
        ]);
        exit();
    }

    $form->commit("addAppointmentForm");
    
    // Email gönderimi (isteğe bağlı)
    include_once Helpers. 'EmailSender.php';
    $emailSender = new EmailSender();
    
    $siteConfig = $casper->getSiteConfig();
    $companyInfo = $siteConfig['companySettings'];
    
    $subject = "Yeni Randevu Talebi - " . $name;
    $emailContent = "
        <h3>Yeni Randevu Talebi</h3>
        <p><strong>Ad Soyad:</strong> {$name}</p>
        <p><strong>Telefon:</strong> {$phone}</p>
        <p><strong>E-posta:</strong> {$email}</p>
        <p><strong>Randevu Tarihi:</strong> {$appointmentDate}</p>
        <p><strong>Randevu Saati:</strong> {$appointmentTime}</p>
        <p><strong>Mesaj:</strong> {$message}</p>
    ";
    
    $emailSender->sendEmail(
        $companyInfo['ayarfirmaeposta'],
        $subject,
        $emailContent
    );

    echo json_encode([
        'status' => 'success',
        'message' => 'Randevu talebiniz başarıyla alındı. En kısa sürede size dönüş yapılacaktır.'
    ]);
    exit();
}