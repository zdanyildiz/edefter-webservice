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
 */


$action = $requestData["action"] ?? null;

if (!isset($action)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action error'
    ]);
    exit();
}

include_once MODEL. 'OpenAIAssistant.php';
$openAiAssistant = new OpenAIAssistant(OPENAI_API_KEY, OPENAI_ASSISTANT_ID,OPENAI_ASSISTANT_MODEL);

switch ($action) {
    case "createThread":
        $response = $openAiAssistant->createThread();
        break;
    case "sendMessage":
        $threadId = $_GET['thread_id'] ?? null;
        $content = $_GET['content'] ?? null;
        if ($threadId && $content) {
            $message = $openAiAssistant->createMessage($threadId, "user", $content);
            if (isset($message['id'])) {
                $run = $openAiAssistant->createRun($threadId);
                if (isset($run['id'])) {
                    sleep(2); // Yanıt oluşması için bekleme süresi
                    $steps = $openAiAssistant->listRunSteps($threadId, $run['id']);
                    foreach ($steps['data'] as $step) {
                        if ($step['status'] === 'completed' && isset($step['step_details']['message_creation'])) {
                            $responseMessageId = $step['step_details']['message_creation']['message_id'];
                            $responseMessage = $openAiAssistant->getMessage($threadId, $responseMessageId);
                            $response = [
                                'message_id' => $responseMessageId,
                                'content' => $responseMessage['content'] ?? 'Yanıt alınamadı.',
                                'tokens_used' => $step['usage'] ?? null
                            ];
                        }
                    }
                }
            }
        }
        break;
    case "getMessageByThreadID":
        $threadId = $_GET['thread_id'] ?? null;
        if ($threadId) {
            $lastMessage = $openAiAssistant->getLastMessage($threadId);
            if ($lastMessage) {
                $response = [
                    'message_id' => $lastMessage['id'],
                    'content' => $lastMessage['content'],
                    'created_at' => $lastMessage['created_at']
                ];
            }
        }
        break;
    default:
        $response = ['error' => 'Geçersiz işlem'];
        break;
}

header('Content-Type: application/json');
echo json_encode($response);