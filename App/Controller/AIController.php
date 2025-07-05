<?php
/**
 * @var Config $config
 * @var Database $db
 * @var Session $session
 * @var Casper $casper
 * @var Helper $helper
 * @var Json $json
 * @var array $requestData
 */

$userRequestAction = $requestData["action"] ?? null;
if(empty($userRequestAction)){
    echo json_encode([
        "status"=>"error",
        "message"=>"action error"
    ]);
    exit;
}

$languageCode = $requestData['languageCode'] ?? "";
if(empty($languageCode)){
    echo json_encode([
        "status"=>"error",
        "message"=>"languageCode error"
    ]);
    exit;
}

include_once MODEL . 'AssistantLogger.php';
include_once MODEL . 'Language.php';
include_once MODEL . 'Member.php';
include_once MODEL . 'OpenAIAssistant.php';
include_once MODEL . 'ProductSearch.php';
include_once MODEL . 'ProductVariant.php';
include_once MODEL . 'Page.php';
include_once Helpers . 'EmailSender.php';
class ServiceContainer {
    private $services = [];

    public function __construct($config,$db,$session,$helper,$json) {
        $this->services['Config'] = $config;
        $this->services['Database'] = $db;
        $this->services['Session'] = $session;
        $casper = $session->getCasper();
        $this->services['Casper'] = $casper;
        $this->services['Helper'] = $helper;
        $this->services['Json'] = $json;
    }

    public function get($name) {
        // Eğer servis önceden oluşturulmuşsa, doğrudan onu döndür.
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        // Servis henüz oluşturulmamışsa, createService ile oluştur ve döndür.
        $service = $this->createService($name);
        $this->services[$name] = $service;

        return $service;
    }

    private function createService($name) {
        switch ($name) {
            case 'OpenAIAssistant':
                return new OpenAIAssistant(OPENAI_API_KEY, OPENAI_ASSISTANT_ID, OPENAI_ASSISTANT_MODEL);
            case 'AssistantLogger':
                return new AssistantLogger($this->get('Database'));
            case 'ProductVariant':
                return new ProductVariant($this->get('Database'));
            case 'ProductSearch':
                return new ProductSearch($this->get('Database'),$this->get('Json'));
            case 'Member':
                return new Member($this->get('Database'));
            case 'Page':
                return new Page($this->get('Database'),$this->get('Session'));
            case 'EmailSender':
                return new EmailSender();
            default:
                throw new Exception("Service $name could not be found.");
        }
    }
}

class AIController {

    private ServiceContainer $container;
    private OpenAIAssistant $openAiAssistant;
    private AssistantLogger $assistantLogger;
    private ProductVariant $productVariantModel;
    private ProductSearch $productSearchModel;
    private Language $languageModel;
    private Member $memberModel;
    private Page $pageModel;
    private Helper $helper;
    private string $visitorUniqID;
    private string $languageCode = "tr";
    private int $languageID = 1;
    private string $threadId;
    private string $runId;
    private string $toolCallId;
    private string $messageId;
    private int $promptToken;
    private int $completedToken;
    private int $totalTokens;

    public function __construct(ServiceContainer $serviceContainer,$languageCode) {
        $this->container = $serviceContainer;
        $this->openAiAssistant = $serviceContainer->get('OpenAIAssistant');
        $this->assistantLogger = $serviceContainer->get('AssistantLogger');
        $this->productVariantModel = $serviceContainer->get('ProductVariant');
        $this->productSearchModel = $serviceContainer->get('ProductSearch');
        $this->memberModel = $serviceContainer->get('Member');
        $this->helper = $serviceContainer->get('Helper');
        $this->pageModel = $serviceContainer->get('Page');

        $this->languageCode = $languageCode;
        $this->languageModel = new Language($serviceContainer->get('Database'),$languageCode,"");
        $this->languageModel->getTranslations($languageCode);
        $this->languageID = $this->languageModel->getLanguageID($languageCode);

        $this->helper = $serviceContainer->get('Helper');
    }

    /**
     * Gelen isteği işler.
     * @param string $action - Yapılacak işlem.
     * @param array $requestData - İstek verileri.
     */
    public function handleRequest(string $userRequestAction, array $requestData): void {

        $this->visitorUniqID = $requestData['visitorUniqID'] ?? null;
        if (!$this->isValidVisitorID($this->visitorUniqID)) {
            Log::write("AssistantController: visitorUniqID geçersiz veya eksik.", "error");
            $this->exitWithMessage([
                "status" => "error",
                "message" => "Visitor ID geçersiz veya eksik."
            ]);
            exit;
        }

        switch ($userRequestAction) {
            case "createThread":
                $this->handleCreateThread($requestData);
                break;
            case "sendMessage":
                $role = $requestData['role'] ?? "user";
                Log::write("AssistantController: sendMessage - role: $role", "info");
                $this->handleSendMessage($requestData, $role);
                break;
            case "checkRunStatus":
                $this->handleCheckRunStatus($requestData);
                break;
            case "getMessageById":
                $this->handleGetMessageById($requestData);
                break;
            case "getMessageByThreadID":
                $this->handleGetMessageByThreadID($requestData);
                break;
            default:
                Log::write("AssistantController: Geçersiz işlem: $userRequestAction", "error");
                $this->exitWithErrorMessage('Geçersiz işlem');
                break;
        }
    }

    /**
     * Yeni bir thread oluşturur.
     * @param array $requestData
     */
    private function handleCreateThread(array $requestData): void {
        $visitorUniqID = $this->visitorUniqID;
        $threadResponse = $this->openAiAssistant->createThread();
        $threadId = $threadResponse['id'] ?? null;

        if ($threadId) {
            $this->threadId = $threadId;

            if ($this->logThread($threadId, $visitorUniqID)) {

                $this->exitWithMessage([
                    "status" => "success",
                    "message" => "Thread oluşturuldu.",
                    "thread_id" => $threadId
                ]);
            }
            else {

                $this->handleThreadError("Thread kaydedilemedi: " . json_encode($threadResponse), "Thread kaydedilemedi.");
                $this->exitWithMessage([
                    "status" => "error",
                    "message" => "Thread kaydedilemedi: " . json_encode($threadResponse)
                ]);
            }
        }
        else
        {
            $this->handleThreadError("Thread oluşturulamadı: " . json_encode($threadResponse), "Thread oluşturulamadı.");
            $this->exitWithMessage([
                "status" => "error",
                "message" => "Thread oluşturulamadı: " . json_encode($threadResponse)
            ]);
        }
    }

    /**
     * Mesaj gönderme işlemini yönetir.
     * @param array $requestData
     * @param string $role
     */
    private function handleSendMessage(array $requestData, string $role = "user"): void {

        $threadId = $requestData['threadId'] ?? null;
        $content = $requestData['content'] ?? null;

        if (!$threadId || !$content) {
            $this->handleSendMessageError("Ziyaretçi ID, Mesaj veya thread ID boş olamaz.", "Ziyaretçi ID, Mesaj veya thread ID boş olamaz.");

            $this->exitWithErrorMessage("Ziyaretçi ID, Mesaj veya thread ID boş olamaz.");
        }

        $this->threadId = $threadId;

        $message = $this->openAiAssistant->createMessage($threadId, $role, $content);

        if (!isset($message['id'])) {
            $this->handleSendMessageError("Gönderici: $role - Yanıt oluşturulamadı: " . json_encode($message), "Yanıt oluşturulamadı.");
        }

        $this->messageId = $message['id'];

        if($role = "user") {
            $this->logUserMessage($threadId, $content, $message['id'], 0);
        }
        else{
            $completedToken = 0;
            $this->logAssistantMessage($threadId, $message['id'], $content, $completedToken);
        }

        $run = $this->openAiAssistant->createRun($threadId);

        if (!isset($run['id'])) {
            $this->handleSendMessageError("Run oluşturulamadı: " . json_encode($run), "Run oluşturulamadı.");
        }

        $this->runId = $run['id'];

        $this->exitWithMessage([
            'status' => 'pending',
            'message' => 'Yanıt oluşturuluyor, lütfen bekleyin...',
            'run_id' => $run['id'],
            'message_id' => $message['id']
        ]);
    }

    /**
     * Run durumunu kontrol eder ve tamamlandığında mesajı alır.
     * @param array $requestData
     */
    private function handleCheckRunStatus(array $requestData): void {

        $visitorUniqID = $this->visitorUniqID;
        $languageCode = $this->languageCode;

        $threadId = $requestData['threadId'] ?? null;
        $runId = $requestData['runId'] ?? null;
        $messageId = $requestData['messageId'] ?? null;

        if ($threadId && $runId && $messageId) {

            $this->threadId = $threadId;
            $this->runId = $runId;
            $this->messageId = $messageId;

            $runData = $this->openAiAssistant->getRun($threadId, $runId);

            $runStatus = $runData['status'] ?? null;

            Log::write("AssistantController: handleCheckRunStatus: runStatus: $runStatus", "info");

            if ($runStatus === 'completed') {

                $this->promptToken = intval($runData['usage']['prompt_tokens'] ?? 0);
                $this->completedToken = intval($runData['usage']['completion_tokens'] ?? 0);
                $this->totalTokens = intval($runData['usage']['total_tokens'] ?? 0);

                $this->updateMessageTokensUsed($messageId, $this->promptToken);

                $requestData = [
                    'threadId' => $threadId,
                    'runId' => $runId,
                    'promptToken' => $this->promptToken,
                    'completedToken' => $this->completedToken,
                    'totalTokens' => $this->totalTokens
                ];

                $this->handleGetMessageByThreadID($requestData);
            }
            elseif ($runStatus === 'in_progress') {
                Log::write("AssistantController: Run tamamlanmadı (in_progress)", "info");
                $this->exitWithMessage(['status' => 'in_progress']);
            }
            elseif ($runStatus === 'queued') {
                Log::write("AssistantController: Run tamamlanmadı veya bekleniyor", "info");
                $this->exitWithMessage(['status' => 'queued']);
            }
            elseif ($runStatus === 'requires_action') {
                Log::write("AssistantController: Run tamamlanmadı Action bekleniyor", "info");
                $this->handleRunFunctionCall($runData);
            }
            else {

                Log::write("AssistantController: Run tamamlanmadı veya geçersiz durum: " . $runStatus, "error");
                $this->exitWithErrorMessage('Run tamamlanmadı veya geçersiz durum: ' . $runStatus);
            }
        }
        else {
            Log::write("AssistantController: Thread ID veya Run ID eksik.", "error");
            $this->exitWithErrorMessage('Thread ID veya Run ID eksik.');
        }
    }

    /**
     * Mesajı ID'ye göre alır.
     * @param array $requestData
     */
    private function handleGetMessageById(array $requestData): void {
        $messageId = $requestData['messageId'] ?? null;
        $threadId = $requestData['threadId'] ?? null;
        $runId = $requestData['runId'] ?? null;
        $completedToken = $requestData['completedToken'] ?? 0;

        if ($threadId && $runId && $messageId) {

            $message = $this->openAiAssistant->getMessage($threadId, $messageId);

            if ($message) {
                Log::write("Mesaj alındı işlenmeye gönderiliyor","info");
                $this->progressAssistantMessage($message, $completedToken);
            }
            else {
                $this->exitWithErrorMessage('Mesaj bulunamadı');
            }
        }
        else {
            Log::write("AssistantController: Thread ID veya Message ID boş olamaz.", "error");
            $this->exitWithErrorMessage("Message ID boş olamaz.");
        }
    }

    /**
     * Mesajları Thread ID'ye göre alır.
     * @param array $requestData
     */
    private function handleGetMessageByThreadID(array $requestData): void {

        $threadId = $requestData['threadId'] ?? null;
        $runId = $requestData['runId'] ?? null;

        $promptToken = intval($requestData['promptToken'] ?? 0);
        $completedToken = intval($requestData['completedToken'] ?? 0);
        $totalTokens = intval($requestData['totalTokens'] ?? 0);
        $lastMessageId = $requestData['lastMessageId'] ?? 0;

        try {
            if ($threadId && $runId) {

                $lastMessage = $this->openAiAssistant->getLastMessage($threadId);

                Log::write("Son Mesaj " . json_encode($lastMessage), "info");

                if ($lastMessage && $lastMessageId!=$lastMessage['id']) {

                    $this->progressAssistantMessage($lastMessage, $completedToken);
                }
                else if ($lastMessageId==$lastMessage['id']) {

                    $this->exitWithMessage([
                        'status' => 'waitingForNewMessages',
                        'message_id' => $lastMessage['id'],
                        'content' => $lastMessage['content'],
                        'created_at' => $lastMessage['created_at']
                    ]);
                }
                else {
                    $this->exitWithErrorMessage('Mesaj bulunamadı');
                }

            }
            else {

                Log::write("AssistantController: Thread ID boş.", "error");
                $this->exitWithErrorMessage("Thread ID boş");
            }
        }
        catch (Exception $e) {
            Log::write("AssistantController: Error in getMessageByThreadID: " . $e->getMessage(), "error");
            $this->exitWithErrorMessage('Bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Run Fonksiyon çağrılarını yönetir.
     * @param array $runData
     */
    private function handleRunFunctionCall(array $runData): void {
        $requiredAction = $runData['required_action'] ?? null;

        if ($requiredAction && $requiredAction['type'] === 'submit_tool_outputs') {
            $toolCalls = $requiredAction['submit_tool_outputs']['tool_calls'] ?? [];
            $toolOutputs = []; // Tüm çıktıları burada biriktiriyoruz

            foreach ($toolCalls as $toolCall) {
                $functionName = $toolCall['function']['name'] ?? null;
                $arguments = json_decode($toolCall['function']['arguments'] ?? '{}', true);
                $toolCallId = $toolCall['id'] ?? null;
                $this->toolCallId = $toolCallId;

                $threadId = $runData['thread_id'] ?? '';
                $this->threadId = $threadId;
                $runId = $runData['id'] ?? '';
                $this->runId = $runId;

                Log::write("AssistantController: handleRunFunctionCall: $functionName", "info");
                //Log::write("AssistantController: handleRunFunctionCall: $toolCallId", "info");

                if ($functionName && json_last_error() === JSON_ERROR_NONE && $toolCallId) {
                    switch ($functionName) {
                        case 'getFilters':
                            // Doğru parametre sırasını kullanın: $arguments, $threadId, $toolCallId
                            $productVariantModel = $this->productVariantModel;
                            $filters = $productVariantModel->getVariantGroups($this->languageCode);
                            $searchFilters = [];
                            foreach ($filters as $variantGroup) {
                                $searchFilters[] = $variantGroup['variantGroupName'];
                            }
                            //Log::write("AssistantController: handleRunFunctionCall - searchFilters: " . json_encode($searchFilters), "info");
                            //exit;
                            //$filters = $this->handleGetFilters(["searchFilters"=>$searchFilters]);

                            // 2. Filtreleri JSON formatına çevir
                            /*$output = json_encode([
                                "searchFilters" => $searchFilters
                            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);*/

                            $toolOutputs[] = [
                                "tool_call_id" => $toolCallId,
                                "output" => json_encode(["searchFilters" => $searchFilters] , JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                            ];
                            break;

                        default:
                            Log::write("AssistantController: Bilinmeyen fonksiyon: $functionName", "error");
                            $this->exitWithErrorMessage("Bilinmeyen fonksiyon: $functionName");
                            break;
                    }
                } else {
                    Log::write("AssistantController: Fonksiyon adı eksik veya geçersiz argümanlar.", "error");
                    $this->exitWithErrorMessage("Fonksiyon adı eksik veya geçersiz argümanlar.");
                }
            }
            Log::write("AssistantController: handleAssistantResponseWithAction: updateRun: toolOutputs: " . json_encode($toolOutputs), "info");
            // Tüm `toolOutputs` biriktirildikten sonra `updateRun` çağrısı yapılır
            $response = $this->openAiAssistant->updateRun($threadId, $runId, $toolOutputs);
            Log::write("AssistantController: updateRun response: " . json_encode($response), "info");
            if(isset($response["error"])){
                Log::write("AssistantController: updateRun response error: " . json_encode($response["error"]), "error");
                $this->exitWithErrorMessage("Yanıt hatası: " . $response["error"]);
            }

            $this->handleCheckRunStatus([
                'threadId' => $this->threadId,
                'runId' => $this->runId,
                'messageId' => $this->messageId
            ]);

        } else {
            Log::write("AssistantController: Gerekli aksiyon eksik veya geçersiz.", "error");
            $this->exitWithErrorMessage("Gerekli aksiyon eksik veya geçersiz.");
        }

    }

    /**
     * Asistan mesajını işleyerek yanıt verir.
     * @param array $message
     * @param int $completedToken
     */
    private function progressAssistantMessage(array $message, int $completedToken): void {

        $threadId = $message['thread_id'] ?? null;
        $messageId = $message['id'] ?? null;
        $content = $message['content'] ?? null;

        if (!$threadId || !$messageId || !$content) {
            $this->exitWithErrorMessage('Asistandan dönen Mesaj içeriği boş veya geçersiz.');
        }

        Log::write("AssistantController: handleAssistantResponse Asistan mesajı işleniyor", "info");

        if (!is_array($content) || !isset($content[0]['text']['value'])) {
            $this->exitWithErrorMessage('Mesaj içeriği boş veya geçersiz');
        }

        $contentString = $content[0]['text']['value'];

        Log::write("AssistantController: Asistan mesajı:" . $contentString, "info");
        // Content stringini JSON olarak ayrıştırın
        $contentJson = json_decode($contentString, true);

        // Eğer ayrıştırma başarılıysa, işleme devam edin
        if (json_last_error() === JSON_ERROR_NONE) {

            /**
             * action parametresi varsa ilgili işlemler yoksa cevap yazdırma işlemi yapılacak
             */

            if (isset($contentJson['action']) && !empty($contentJson['action']) && $this->validateAction($contentJson['action'])) {

                Log::write("AssistantController: Action var, ilgili işlemler yapılacak.", "info");

                $this->handleAssistantResponseWithAction($contentJson, $threadId);

            }
            elseif (isset($contentJson['action']) && !empty($contentJson['action']) && !$this->validateAction($contentJson['action'])) {
                Log::write("AssistantController: Action var, işlem  yapılmayacak.", "info");
                $this->logAssistantMessage($threadId, $messageId, json_encode($contentString), $completedToken);
                $this->exitWithMessage([
                    'status' => 'success',
                    'message_id' => $messageId,
                    'content' => $contentJson['answer'],
                    'action' => $contentJson['action'],
                    'url' => $contentJson['url'] ?? '',
                    'created_at' => $message['created_at']
                ]);
            }
            elseif (isset($contentJson['answer'])) {

                $answer = $contentJson['answer'];
                Log::write("AssistantController: Asistan yanıtı 1: " . $answer, "info");

                $this->logAssistantMessage($threadId, $messageId, $answer, $completedToken);


                $this->exitWithMessage([
                    'status' => 'success',
                    'message_id' => $messageId,
                    'content' => $answer,
                    'created_at' => $message['created_at']
                ]);
            }
            else {

                Log::write("AssistantController: Mesaj kaydedilemedi.", "error");
                $this->exitWithErrorMessage('Mesaj kaydedilemedi.');
            }
        }
        else {
            Log::write("AssistantController: JSON parse hatası: " . json_last_error_msg(), "error");
            $this->exitWithErrorMessage('Yanıt ayrıştırılamadı.');
        }
    }

    /**
     * Asistan fonksiyon çağrılarını yönetir.
     * @param array $contentJson
     * @param string $threadId
     */
    private function handleAssistantResponseWithAction(array $contentJson, string $threadId): void {

        $action = $contentJson['action'] ?? '';

        switch ($action) {
            case 'productSearch':

                $searchKey = $contentJson['searchKey'] ?? null;
                $searchFilters = $contentJson['searchFilters'] ?? "";

                if (is_string($searchFilters)) {
                    $searchFiltersArray = explode(",", $searchFilters);
                    $parsedFilters = [];

                    foreach ($searchFiltersArray as $filter) {
                        // "Renk:sarı" gibi bir stringi ayrıştırın
                        $parts = explode(':', $filter, 2);
                        if (count($parts) == 2) {
                            $key = trim($parts[0]);
                            $value = trim($parts[1]);

                            if (!empty($key)) {
                                $parsedFilters[$key] = $value;
                            }
                        }
                    }

                    $searchFilters = $parsedFilters;
                }
                if (is_array($searchFilters)) {
                    $url = '/?languageID=' . $this->languageID . '&q=' . urlencode($searchKey);
                    if (!empty($searchFilters)) {
                        $url .= '&' . http_build_query($searchFilters);
                    }
                }

                $search = $this->handleProductSearch($searchKey, $searchFilters);
                $totalResults = $search['searchTotalResults'] ?? 0;

                if($totalResults == 0) {
                    $sendMessageData = [
                        'threadId' => $threadId,
                        'content' => json_encode($search),
                        'systemAction' => 'searchResult',
                    ];

                    $this->exitWithMessage($sendMessageData, "assistant" );
                    exit;
                }

                $filterQuery = http_build_query($parsedFilters);

                $url = '/?languageID=' . $this->languageID . '&q=' . urlencode($searchKey);
                if (!empty($filterQuery)) {
                    $url .= '&' . $filterQuery;
                }

                $sendMessageData = [
                    'status' => 'success',
                    'threadId' => $threadId,
                    'content' => json_encode($search),
                    'systemAction' => 'searchResult',
                    'referenceUrl' => $url
                ];
                $this->exitWithMessage($sendMessageData);
                break;
            case 'addSearchResultToFavorites':
                $casper = $this->container->get('Casper');
                $siteConfig = $casper->getSiteConfig();
                $pageLinks = $siteConfig['specificPageLinks'];
                $favoriteLink = array_values(array_filter($pageLinks, fn($pageLink) => $pageLink['sayfatip'] === 19))[0]['link'] ?? null;

                $searchResultProductIDs = $contentJson['searchResultProductIDs'] ?? [];
                $addFavoriteResult = $this->addSearchResultToFavorites($searchResultProductIDs);

                if($addFavoriteResult>0){
                    $message = "$addFavoriteResult "._uye_favori_ekle_basarili_yanit;
                    $action = "goUrl";
                    $url = $favoriteLink;
                }
                else{
                    $message = _uye_favori_ekle_basarisiz_yanit;
                    $action = "";
                    $url = "";
                }
                $sendMessageData = [
                    'status' => 'success',
                    'threadId' => $threadId,
                    'content' => $message,
                    'systemAction' => $action,
                    'referenceUrl' => $url
                ];
                //$this->handleSendMessage($sendMessageData, "assistant" );
                $this->exitWithMessage($sendMessageData);
                break;
            case 'addMember':
                $this->handleAddMember($contentJson);
                break;
            case 'remindPassword':
                $this->handleRemindPassword($contentJson);
                break;
            default:

                Log::write("AssistantController: Bilinmeyen action: $action", "error");

                $this->exitWithErrorMessage("Geçersiz action: $action");

                break;
        }
    }

    /**
     * Ürün araması yapar.
     * @param string|null $searchKey
     * @param array $searchFilters
     * @return array
     */
    private function handleProductSearch(?string $searchKey, array $searchFilters): array {
        try {
            Log::write("AssistantController: handleProductSearch: Başladı.", "debug");

            // Arama verilerini başlat
            $searchData = [
                'q' => $searchKey,
                'languageID' => $this->languageID
            ];
            Log::write("AssistantController: handleProductSearch: searchData initialized.", "debug");

            // Filtrelerin türünü kontrol et ve gerekiyorsa dönüştür
            if (is_string($searchFilters)) {
                Log::write("AssistantController: handleProductSearch: searchFilters is string, parsing.", "debug");
                $filters = explode(',', $searchFilters);
                $parsedFilters = [];
                foreach ($filters as $filter) {
                    $parts = explode(':', $filter, 2);
                    if (count($parts) == 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        // Anahtarın doğru bir şekilde alındığından emin olun
                        if (!empty($key)) {
                            $parsedFilters[$key] = $value;
                        } else {
                            Log::write("AssistantController: handleProductSearch: Invalid key for filter: $filter", "error");
                        }
                    } else {
                        Log::write("AssistantController: handleProductSearch: Invalid filter format: $filter", "error");
                    }
                }
                $searchFilters = $parsedFilters;
            }


            Log::write("AssistantController: handleProductSearch: searchFilters type: " . gettype($searchFilters), "debug");
            Log::write("AssistantController: handleProductSearch: searchFilters content: " . json_encode($searchFilters), "debug");

            // Filtreleri dinamik olarak arama verisine ekle
            Log::write("AssistantController: handleProductSearch: Starting foreach loop.", "debug");
            foreach ($searchFilters as $key => $value) {
                if (!empty($key)) {
                    Log::write("AssistantController: handleProductSearch: Adding filter - $key: $value", "debug");
                    $searchData[$key] = $value;
                } else {
                    Log::write("AssistantController: handleProductSearch: Skipping filter with empty key", "warning");
                }
            }

            Log::write("AssistantController: handleProductSearch: Finished foreach loop.", "debug");
            Log::write("AssistantController: handleProductSearch: searchData after filters: " . json_encode($searchData), "debug");

            // Ürün arama fonksiyonunu çağır
            Log::write("AssistantController: handleProductSearch: Calling productSearchModel->productSearch", "debug");

            $result = $this->productSearchModel->productSearch($searchData);

            Log::write("AssistantController: handleProductSearch: productSearch sonucu: " . json_encode($result), "info");

            return $result;
        } catch (\Exception $e) {

            Log::write("AssistantController: handleProductSearch Hata: " . $e->getMessage(), "error");
            // Hata durumunda uygun bir yanıt dönebilirsiniz
            return ['status' => 'error', 'message' => 'Ürün araması sırasında bir hata oluştu.'];
        }
    }

    private function addSearchResultToFavorites($searchResultProductIDs){
        $memberModel = $this->memberModel;
        $pageModel = $this->pageModel;
        $addProductCount = 0;
        //Log::write("AssistantController: addSearchResultToFavorites - Ziyaretçi ID: " . $this->visitorUniqID . " sayfalar: ". json_encode($searchResultProductIDs), "info");
        foreach ($searchResultProductIDs as $productID) {
            $productResult = $pageModel->getPageUniqIDByID($productID);
            if(!$productResult){
                continue;
            }
            $productUniqID = $productResult[0]['pageUniqID'];
            $checkFavoriteResult = $memberModel->getVisitorPages($this->visitorUniqID, $productUniqID);
            if(!$checkFavoriteResult){
                //Log::write("AssistantController: addSearchResultToFavorites - checkFavoriteResult Sayfa ($productID) ziyaretçi sayfa listesinde yok", "error");
                $memberModel->beginTransaction();
                $favoriteResult = $memberModel->addFavorite($this->visitorUniqID, $productUniqID);
                if(!$favoriteResult){
                    //Log::write("AssistantController: addSearchResultToFavorites - favoriteResult Sayfa ($productID) ziyaretçi sayfa listesine eklenemedi", "error");
                    $memberModel->rollback();
                    continue;
                }
                //Log::write("AssistantController: addSearchResultToFavorites - favoriteResult Sayfa ($productID) ziyaretçi sayfa listesine eklendi", "info");
                $memberModel->commit();
                $addProductCount++;
            }
            else{
                //Log::write("AssistantController: addSearchResultToFavorites - checkFavoriteResult Sayfa ($productID) ziyaretci sayfa listesinde var", "info");
                $favoritePage = $checkFavoriteResult[0]['pageFavorite'];
                if($favoritePage == 0){
                    //Log::write("AssistantController: addSearchResultToFavorites - checkFavoriteResult Sayfa ($productID) favori değil", "info");
                    $memberModel->beginTransaction();
                    $favoriteResult = $memberModel->updateFavoriteByProductUniqID($this->visitorUniqID, $productUniqID);
                    if($favoriteResult<0){
                        Log::write("AssistantController: addSearchResultToFavorites - favoriteResult Sayfa ($productID) favori olarak güncellenemedi", "error");
                        $memberModel->rollback();
                        continue;
                    }
                    //Log::write("AssistantController: addSearchResultToFavorites - favoriteResult Sayfa ($productID) favori olarak güncellendi", "info");
                    $memberModel->commit();
                    $addProductCount++;
                }
                else{
                    $addProductCount++;
                }
            }
        }

        //Log::write("AssistantController: addSearchResultToFavorites - addProductCount: $addProductCount", "info");

        return $addProductCount;

    }

    /**
     * Üye ekleme işlemini gerçekleştirir.
     * @param array $assistantResponse
     */
    private function handleAddMember(array $assistantResponse): void {

        $config = $this->container->get('Config');
        $helper = $this->helper;
        $db = $this->container->get('Database');

        $name = $assistantResponse['name'] ?? '';
        $surname = $assistantResponse['surname'] ?? '';
        $email = $assistantResponse['email'] ?? '';
        $phone = $assistantResponse['phone'] ?? '';
        $password = $helper->createPassword(8,2);

        if (empty($name) || empty($surname) || empty($email) || empty($phone)) {
            Log::write("AssistantController: Üye bilgileri eksik.", "error");
            $this->exitWithErrorMessage("Üye bilgileri eksik.");
        }
        $memberTitle = $helper->encrypt($name . " " . $surname, $config->key);
        $name = $helper->encrypt($name, $config->key);
        $surname = $helper->encrypt($surname, $config->key);
        $email = $helper->encrypt($email, $config->key);
        $phone = $helper->encrypt($phone, $config->key);
        $password= $helper->encrypt($password, $config->key);

        $memberPostData = [
            'benzersizid' => $this->visitorUniqID,
            'uyeolusturmatarih' => date("Y-m-d H:i:s"),
            'uyeguncellemetarih' => date("Y-m-d H:i:s"),
            'uyetip' => 1,
            'uyetcno' => '',
            'memberTitle' => $memberTitle,
            'uyead' => $name,
            'uyesoyad' => $surname,
            'uyetelefon' => $phone,
            'uyeeposta' => $email,
            'uyesifre' => $password,
            'uyeaktif' =>0,
            'uyesil' => 0
        ];

        $memberModel = $this->memberModel;
        $result = $memberModel->register($memberPostData);
        if(!$result){
            Log::write("AssistantController: Üye eklenirken hata oluştu.", "error");
            $sendMessageData = [
                'status' => 'success',
                'visitorUniqID' => $this->visitorUniqID,
                'languageCode' => $this->languageCode,
                'threadId' => $this->threadId,
                'content' => _uye_ol_form_basarisiz_yanit,
                'action' => 'addMemberResult'
            ];
        }
        else{
            Log::write("AssistantController: Üye eklendi: $name $surname, $email, $phone", "info");

            $siteConfig = $this->container->get('Casper')->getSiteConfig();
            $visitorUniqID = $this->visitorUniqID;
            $emailSender = $this->container->get('EmailSender');
            $emailSubject = _uye_ol_eposta_konu;

            $companyInfo = $siteConfig['companySettings'];
            $companyName = $companyInfo['ayarfirmakisaad'];
            $companyAddress = $companyInfo['ayarfirmamahalle']." ".$companyInfo['ayarfirmaadres']." ".$companyInfo['ayarfirmasemt']." ".$companyInfo['ayarfirmailce']." ".$companyInfo['ayarfirmasehir']." ".$companyInfo['ayarfirmaulke'];
            $companyPhone = "+".$companyInfo['ayarfirmaulkekod'].$companyInfo['ayarfirmatelefon'];
            $companyEmail = $companyInfo['ayarfirmaeposta'];

            $logoInfo = $siteConfig['logoSettings'];
            $logo = $config->http.$config->hostDomain.imgRoot.$logoInfo['resim_url'];

            $verificationLink = $config->http.$config->hostDomain."/?/control/member/get/verificationCode&userId=$visitorUniqID&email=$email";

            $emailTemplate = file_get_contents(Helpers.'mail-template/newMember.php');
            $emailTemplate = str_replace("[company-name]", $companyName, $emailTemplate);
            $emailTemplate = str_replace("[subject]", $emailSubject, $emailTemplate);
            $emailTemplate = str_replace("[company-logo]", $logo, $emailTemplate);
            $emailTemplate = str_replace("[email-title]", _uye_ol_eposta_title, $emailTemplate);
            $emailTemplate = str_replace("[email-description]", _uye_ol_eposta_aciklama, $emailTemplate);
            $emailTemplate = str_replace("[verificationLink]", $verificationLink, $emailTemplate);
            $emailTemplate = str_replace("[email-verification-button]", _uye_ol_eposta_dogrula_buton, $emailTemplate);
            $emailTemplate = str_replace("[email-verification-button-description]", _uye_ol_eposta_dogrula_buton_aciklama, $emailTemplate);
            $emailTemplate = str_replace("[email-end-description]", _uye_ol_eposta_bitis_yazi, $emailTemplate);
            $emailTemplate = str_replace("[name-surname]", $assistantResponse['name']." ".$assistantResponse['surname'], $emailTemplate);
            $emailTemplate = str_replace("[company-address]", $companyAddress, $emailTemplate);
            $emailTemplate = str_replace("[company-phone]", $companyPhone, $emailTemplate);
            $emailTemplate = str_replace("[company-email]", $companyEmail, $emailTemplate);

            $mailResult = $emailSender->sendEmail($assistantResponse['email'],$assistantResponse['name']." ".$assistantResponse['surname'], $emailSubject, $emailTemplate);
            if(!$mailResult){
                $responseMessage = _uye_ol_eposta_gonderim_basarisiz;
            }
            else{
                $responseMessage = _uye_ol_form_basarili_yanit;
            }

            $sendMessageData = [
                'status' => 'success',
                'visitorUniqID' => $this->visitorUniqID,
                'languageCode' => $this->languageCode,
                'threadId' => $this->threadId,
                'content' => $responseMessage,
                'action' => 'addMemberResult'
            ];
        }
        $this->exitWithMessage($sendMessageData);
    }

    /**
     * Şifre sıfırlama işlemini gerçekleştirir.
     * @param array $assistantResponse
     */
    private function handleRemindPassword(array $assistantResponse): void {
        $email = $assistantResponse['email'] ?? '';

        if (empty($email)) {
            Log::write("AssistantController: E-posta bilgisi eksik.", "error");
            $this->exitWithErrorMessage("E-posta bilgisi eksik.");
        }

        // Şifre sıfırlama işlemi yapılacak
        // Gerekli yöntemler çağırılarak şifre sıfırlama süreci başlatılır
        // Örneğin:
        // $userModel = new UserModel($db);
        // $userModel->sendResetPassword($email);

        Log::write("AssistantController: Şifre sıfırlama isteği gönderildi: $email", "info");

        $this->exitWithMessage([
            'status' => 'success',
            'message' => 'Şifre sıfırlama e-postası gönderildi.'
        ]);
    }

    public function updateMessageTokensUsed(string $messageId, int $tokensUsed): void {
        Log::write("AssistantController: updateMessageTokensUsed", "info");

        $this->assistantLogger->beginTransaction("updateMessageTokensUsed");

        $updateMessageTokensUsedResult = $this->assistantLogger->updateMessageTokensUsed($messageId, $tokensUsed);

        if ($updateMessageTokensUsedResult > 0) {

            $this->assistantLogger->commit("updateMessageTokensUsed");
            Log::write("AssistantController: Message tokens used updated: $messageId", "info");

        } else {

            $this->assistantLogger->rollback("updateMessageTokensUsed");
            Log::write("AssistantController: Message tokens used update failed: $messageId", "error");
        }
    }

    /**
     * Thread kontrolü için hata durumunda işlem yapar ve yanıt döner.
     * @param string $logMessage - Log mesajı.
     * @param string $errorMessage - Kullanıcıya döndürülecek hata mesajı.
     */
    private function handleThreadError(string $logMessage, string $errorMessage): void {
        Log::write("AssistantController: $logMessage", "error");
        $this->assistantLogger->rollback("createThread");
        $this->exitWithErrorMessage($errorMessage);
    }

    /**
     * Asistana mesaj gönderiminde hata durumunda işlem yapar ve yanıt döner.
     * @param string $logMessage - Log mesajı.
     * @param string $errorMessage - Kullanıcıya döndürülecek hata mesajı.
     */
    private function handleSendMessageError(string $logMessage, string $errorMessage): void {
        Log::write("AssistantController: $logMessage", "error");
        $this->assistantLogger->rollback("sendMessage");
        $this->exitWithErrorMessage($errorMessage);
    }

    /**
     * Thread'i veri tabanına kaydeder.
     * @param string $threadId
     * @param string $visitorUniqID
     * @return bool
     */
    private function logThread(string $threadId, string $visitorUniqID): bool {

        $this->assistantLogger->beginTransaction("createThread");

        $assistantLogResult = $this->assistantLogger->createThread($threadId, $visitorUniqID);

        if ($assistantLogResult > 0) {

            $this->assistantLogger->commit("createThread");
            Log::write("AssistantController: Thread başarıyla kaydedildi: $threadId", "info");
            return true;
        }
        else {

            $this->handleThreadError("Thread veritabanına kayıt yapılamadı.", "Veritabanına kayıt yapılamadı.");
        }
        return false;
    }

    /**
     * Asistan mesajını veri tabanına kaydeder.
     * @param string $threadId
     * @param string $messageId
     * @param string $content
     * @param int $completedToken
     */
    private function logAssistantMessage(string $threadId, string $messageId, string $content, int $completedToken): void {

        $getMessageByMessageId = $this->assistantLogger->getMessageByMessageId($messageId);

        if (!$getMessageByMessageId) {

            $this->assistantLogger->beginTransaction("logAssistantMessage");

            $logResult = $this->assistantLogger->logMessage(
                $threadId,
                $messageId,
                'assistant',
                $content,
                $completedToken
            );

            if ($logResult > 0) {
                $this->assistantLogger->commit("logAssistantMessage");
                Log::write("AssistantController: Asistan mesajı başarıyla kaydedildi: $messageId", "info");
            }
            else {
                $this->assistantLogger->rollback("logAssistantMessage");
                Log::write("AssistantController: Asistan mesajı veri tabanına kaydedilemedi: $messageId", "error");
                $this->exitWithErrorMessage("Asistan mesajı veri tabanına kaydedilemedi.");
            }
        }

    }

    /**
     * Kullanıcı mesajını veri tabanına kaydeder.
     * @param string $threadId
     * @param string $userMessage
     * @param string $messageId
     * @param int $completedToken
     * @return int
     */
    private function logUserMessage(string $threadId, string $userMessage, string $messageId, int $completedToken) {

        $this->assistantLogger->beginTransaction("logUserMessage");

        $logResult = $this->assistantLogger->logMessage(
            $threadId,
            $messageId,
            'user',
            $userMessage,
            $completedToken
        );

        if ($logResult > 0) {
            Log::write("AssistantController: Kullanıcı mesajı başarıyla kaydedildi: $messageId", "info");
            $this->assistantLogger->commit("logUserMessage");

        } else {
            Log::write("AssistantController: Kullanıcı mesajı kaydedilemedi: $messageId", "error");
            $this->assistantLogger->rollback("logUserMessage");
            $this->exitWithErrorMessage("Kullanıcı mesajı veri tabanına kaydedilemedi.");
        }

    }

    /**
     * Visitor ID'nin geçerli olup olmadığını kontrol eder.
     * @param mixed $visitorUniqID
     * @return bool
     */
    private function isValidVisitorID($visitorUniqID): bool {
        return !empty($visitorUniqID) && is_string($visitorUniqID);
    }

    /**
     * Hata durumunda yanıt döner.
     * @param string $message
     */
    private function exitWithErrorMessage(string $message): void {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    /**
     * Başarılı yanıt döner.
     * @param array $message
     */
    private function exitWithMessage(array $message): void {
        header('Content-Type: application/json');
        echo json_encode($message);
        exit;
    }

    /**
     * Sohbet verilerini temizler.
     */
    private function clearChatData(): void {
        $this->assistantLogger->clearChatData(); // Veri tabanında ilgili temizleme işlemi
        Log::write("AssistantController: Sohbet verileri temizlendi.", "info");
    }

    public function validateAction($action) {
        // İstenmeyen fonksiyon adlarının listesi
        $disallowedActions = ['goUrl', 'answer'];

        // Eğer action parametresi listede varsa false döner
        return !in_array($action, $disallowedActions, true);
    }
}

$container = new ServiceContainer($config,$db,$session,$helper,$json);
$controller = new AIController($container,$languageCode);
$controller->handleRequest($userRequestAction, $requestData);
exit;
