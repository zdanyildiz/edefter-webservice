<?php
$documentRoot = str_replace("\\","/",realpath($_SERVER['DOCUMENT_ROOT']));
$directorySeparator = str_replace("\\","/",DIRECTORY_SEPARATOR);
include_once $documentRoot . $directorySeparator . 'App/Controller/Admin/AdminGlobal.php';
/**
 * @var Config $config
 * @var AdminDatabase $db
 * @var Session $adminSession
 * @var AdminCasper $adminCasper
 * @var array $requestData
 * @var Helper $helper
 * @var Json $json
 */
$errorResponse = function($message) use ($helper) {
    $helper->jsonErrorResponse($message);
};
$successResponse = function ($message, $data = []) use ($helper) {
    $helper->jsonSuccessResponse($message, $data);
};
$warningResponse = function ($message) use ($helper) {
    $helper->jsonWarningResponse($message);
};

$action = $requestData["action"] ?? null;

if (!isset($action)) {
    $errorResponse('Geçersiz İşlem');
}

class AdminFormController
{
    private AdminForm $formModel;
    private Helper $helper;
    private Config $config;
    private AdminCasper $adminCasper;

    public function __construct(AdminForm $formModel, Helper $helper, Config $config, AdminCasper $adminCasper)
    {
        $this->formModel = $formModel;
        $this->helper = $helper;
        $this->config = $config;
        $this->adminCasper = $adminCasper;
    }

    public function getContactForms($sort = 'tarih DESC', $limit = 10, $offset = 0)
    {
        $forms = $this->formModel->getContactForms($sort, $limit, $offset);

        foreach ($forms as &$form) {
            $form['adsoyad'] = $this->helper->decrypt($form['adsoyad'], $this->config->key);
            $form['telefon'] = $this->helper->decrypt($form['telefon'], $this->config->key);
            $form['eposta'] = $this->helper->decrypt($form['eposta'], $this->config->key);
        }

        if (!empty($forms)) {
            $this->helper->jsonSuccessResponse('Başarılı', $forms);
        } else {
            $this->helper->jsonWarningResponse('Veri bulunamadı');
        }
    }

    public function getContactFormById($id)
    {
        $form = $this->formModel->getContactFormById($id);
        if ($form) {
            $form['adsoyad'] = $this->helper->decrypt($form['adsoyad'], $this->config->key);
            $form['telefon'] = $this->helper->decrypt($form['telefon'], $this->config->key);
            $form['eposta'] = $this->helper->decrypt($form['eposta'], $this->config->key);
            $this->helper->jsonSuccessResponse('Başarılı', $form);
        } else {
            $this->helper->jsonWarningResponse('Form bulunamadı');
        }
    }

    public function addFormResponse($formId, $responseData)
    {
        $email = $responseData['email'];
        $name = $responseData['name'];
        $responseData['name'] = $this->helper->encrypt($responseData['name'], $this->config->key);
        $responseData['phone'] = $this->helper->encrypt($responseData['phone'], $this->config->key);
        $responseData['email'] = $this->helper->encrypt($responseData['email'], $this->config->key);

        $result = $this->formModel->addFormResponse($formId, $responseData);
        if ($result) {

            include_once Helpers. 'EmailSender.php';
            $emailSender = new EmailSender();

            $languageID = 1; //@todo: dil seçimi yapılacak

            $siteConfig = $this->adminCasper->getSiteConfig();

            $logoInfo = $siteConfig['logoSettings'];
            $logo = isset($logoInfo['resim_url']) ? $this->config->http.$this->config->hostDomain.imgRoot.$logoInfo['resim_url'] : $this->config->http.$this->config->hostDomain.'/_y/assets/img/header.jpg';;

            $companyInfo = $siteConfig['companySettings'] ?? [];

            if(!empty($companyInfo))
            {
                $companyName = $companyInfo['ayarfirmakisaad'];
                $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
                $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
                $companyEmail = $companyInfo['ayarfirmaeposta'];
            }
            else{
                $companyName = $this->config->hostDomain;
                $companyAddress = '';
                $companyPhone = '';
                $companyEmail = '';
            }

            $emailSubject = $companyInfo['ayarfirmakisaad']. ' İletişim Formunuz Hakkında';

            $emailTemplate = file_get_contents(Helpers.'mail-template/adminSendContactForm.php');
            $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
            $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
            $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
            $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
            $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
            $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);
            $emailTemplate = str_replace("[message]", $responseData["message"], $emailTemplate);

            $sendMail = $emailSender->sendEmail($email, $name, $emailSubject, $emailTemplate);
            if($sendMail){
                $this->helper->jsonSuccessResponse('Yanıt gönderimi başarılı');
            }
            else{
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Şifreniz e-posta adresinize gönderilemedi.'
                ]);
                exit();
            }
        } else {
            $this->helper->jsonErrorResponse('Form yanıtı eklenemedi');
        }
    }

    public function updateContactForm($formId, $formData)
    {
        $formData['name'] = $this->helper->encrypt($formData['name'], $this->config->key);
        $formData['phone'] = $this->helper->encrypt($formData['phone'], $this->config->key);
        $formData['email'] = $this->helper->encrypt($formData['email'], $this->config->key);

        $result = $this->formModel->updateContactForm($formId, $formData);
        if ($result) {
            $this->helper->jsonSuccessResponse('Başarılı');
        } else {
            $this->helper->jsonWarningResponse('Form güncellenemedi veya değişiklik yapılmadı');
        }
    }

    public function deleteContactForm($formId)
    {
        $result = $this->formModel->deleteContactForm($formId);
        if ($result) {
            $this->helper->jsonSuccessResponse('Silme işlemi başarılı');
        } else {
            $this->helper->jsonWarningResponse('Form silinemedi veya zaten silinmiş');
        }
    }

    public function markAsRead($formId)
    {
        $result = $this->formModel->markAsRead($formId, ['formNotification' => 0]);
        if ($result>0) {
            $this->helper->jsonSuccessResponse('Form okundu olarak işaretlendi');
        } else {
            $this->helper->jsonWarningResponse('Form okundu olarak işaretlenemedi');
        }
    }

    public function getNewsletterForms($sort = 'id DESC', $limit = 10, $offset = 0)
    {
        $forms = $this->formModel->getNewsletterForms($sort, $limit, $offset);
        foreach ($forms as &$form) {
            $form['name'] = $this->helper->decrypt($form['name'], $this->config->key);
            $form['email'] = $this->helper->decrypt($form['email'], $this->config->key);
        }

        if (!empty($forms)) {
            $this->helper->jsonSuccessResponse('Başarılı', $forms);
        } else {
            $this->helper->jsonWarningResponse('Veri bulunamadı');
        }
    }

    public function addNewsletterForm($data)
    {
        $data['name'] = $this->helper->encrypt($data['name'], $this->config->key);
        $data['email'] = $this->helper->encrypt($data['email'], $this->config->key);

        $result = $this->formModel->addNewsletterForm($data);
        if ($result) {
            $this->helper->jsonSuccessResponse('Başarılı');
        } else {
            $this->helper->jsonErrorResponse('Newsletter eklenemedi');
        }
    }

    public function updateNewsletterForm($newsletterId, $data)
    {
        $data['name'] = $this->helper->encrypt($data['name'], $this->config->key);
        $data['email'] = $this->helper->encrypt($data['email'], $this->config->key);

        $result = $this->formModel->updateNewsletterForm($newsletterId, $data);
        if ($result) {
            $this->helper->jsonSuccessResponse('Başarılı');
        } else {
            $this->helper->jsonWarningResponse('Newsletter güncellenemedi veya değişiklik yapılmadı');
        }
    }

    public function deleteNewsletterForm($email)
    {
        $email = $this->helper->encrypt($email, $this->config->key);
        $result = $this->formModel->deleteNewsletterForm($email);
        if ($result) {
            $this->helper->jsonSuccessResponse('Başarılı');
        } else {
            $this->helper->jsonWarningResponse('Newsletter silinemedi veya zaten silinmiş');
        }
    }
}

include_once MODEL .'Admin/AdminForm.php';
$formModel = new AdminForm($db);
$formController = new AdminFormController($formModel, $helper, $config,$adminCasper);

switch ($action) {
    case 'getContactForms':
        $sort = $requestData['sort'] ?? 'tarih DESC';
        $limit = $requestData['limit'] ?? 10;
        $offset = $requestData['offset'] ?? 0;
        $formController->getContactForms($sort, $limit, $offset);
        break;
    case 'getContactFormById':
        $id = $requestData['id'] ?? null;
        if ($id) {
            $formController->getContactFormById($id);
        } else {
            $errorResponse('ID gerekli');
        }
        break;
    case 'addFormResponse':
        Log::adminWrite(json_encode($requestData));
        $formId = $requestData['formId'] ?? null;
        $responseData = $requestData['responseData'] ?? [];
        if ($formId && !empty($responseData)) {
            $formController->addFormResponse($formId, $responseData);
        } else {
            $errorResponse('Form ID ve yanıt verileri gerekli');
        }
        break;
    case 'updateContactForm':
        $formId = $requestData['formId'] ?? null;
        $formData = $requestData['formData'] ?? [];
        if ($formId && !empty($formData)) {
            $formController->updateContactForm($formId, $formData);
        } else {
            $errorResponse('Form ID ve form verileri gerekli');
        }
        break;
    case 'markAsRead':
        $formId = $requestData['formId'] ?? null;
        if ($formId) {
            $formController->markAsRead($formId);
        } else {
            $errorResponse('Form ID gerekli');
        }
        break;
    case 'deleteContactForm':
        $formId = $requestData['formId'] ?? null;
        if ($formId) {
            $formController->deleteContactForm($formId);
        } else {
            $errorResponse('Form ID gerekli');
        }
        break;
    case 'getNewsletterForms':
        $sort = $requestData['sort'] ?? 'id DESC';
        $limit = $requestData['limit'] ?? 10;
        $offset = $requestData['offset'] ?? 0;
        $formController->getNewsletterForms($sort, $limit, $offset);
        break;
    case 'addNewsletterForm':
        $data = $requestData['data'] ?? [];
        if (!empty($data)) {
            $formController->addNewsletterForm($data);
        } else {
            $errorResponse('Veri gerekli');
        }
        break;
    case 'updateNewsletterForm':
        $newsletterId = $requestData['newsletterId'] ?? null;
        $data = $requestData['data'] ?? [];
        if ($newsletterId && !empty($data)) {
            $formController->updateNewsletterForm($newsletterId, $data);
        } else {
            $errorResponse('Newsletter ID ve veri gerekli');
        }
        break;
    case 'deleteNewsletterForm':
        $email = $requestData['email'] ?? null;
        if ($email) {
            $formController->deleteNewsletterForm($email);
        } else {
            $errorResponse('E-posta gerekli');
        }
        break;
    default:
        $errorResponse('Geçersiz İşlem');
        break;
}