<?php
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
define('OPENAI_ASSISTANT_ID', getenv('OPENAI_ASSISTANT_ID_2') ?: '');
define('OPENAI_ASSISTANT_MODEL', getenv('OPENAI_ASSISTANT_MODEL_2') ?: '');
define('OPENAI_API_URL', 'https://api.openai.com/v1/');

class OpenAIAssistant {
    private $apiKey;
    private $assistantId;
    private $model;
    private $apiUrl;

    public function __construct($apiKey, $assistantId, $model = 'ft:gpt-4o-2024-08-06:personal:addfavorites:AMyUBoYj') {
        $this->apiKey = $apiKey;
        $this->assistantId = $assistantId;
        $this->model = $model;
        $this->apiUrl = 'https://api.openai.com/v1';
    }

    // Genel API isteği gönderme metodu
    private function sendRequest($url, $method, $data = null) {
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->apiKey}",
            "OpenAI-Beta: assistants=v2"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // SSL doğrulamasını devre dışı bırak (yerel geliştirme için)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return ['error' => curl_error($ch)];
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    // Yeni konu oluşturma (Thread)
    public function createThread() {
        $url = "{$this->apiUrl}/threads";
        return $this->sendRequest($url, "POST");
    }

    // Mesaj oluşturma ve konuya ekleme
    public function createMessage($threadId, $role, $content) {
        $url = "{$this->apiUrl}/threads/{$threadId}/messages";
        $data = [
            "role" => $role,
            "content" => $content
        ];
        return $this->sendRequest($url, "POST", $data);
    }

    // Run oluşturma (Asistanın yanıt vermesi için)
    public function createRun($threadId) {
        $url = "{$this->apiUrl}/threads/{$threadId}/runs";
        $data = [
            "assistant_id" => $this->assistantId
        ];
        return $this->sendRequest($url, "POST", $data);
    }

    // Run adımlarını izleme
    public function listRunSteps($threadId, $runId) {
        $url = "{$this->apiUrl}/threads/{$threadId}/runs/{$runId}/steps";
        return $this->sendRequest($url, "GET");
    }

    // Mesaj içeriğini getirme
    public function getMessage($threadId, $messageId) {
        $url = "{$this->apiUrl}/threads/{$threadId}/messages/{$messageId}";
        return $this->sendRequest($url, "GET");
    }

    // Konudaki son mesajı alma
    public function getLastMessage($threadId) {
        $url = "{$this->apiUrl}/threads/{$threadId}/messages?order=desc&limit=1";
        $response = $this->sendRequest($url, "GET");
        return $response['data'][0] ?? null;
    }

    public function getRun($threadId, $runId) {
        $url = "{$this->apiUrl}/threads/{$threadId}/runs/{$runId}";
        return $this->sendRequest($url, "GET");
    }

    // Run nesnesini güncelleme metodu
    public function updateRun($threadId, $runId, $outputData) {
        $url = "{$this->apiUrl}/threads/{$threadId}/runs/{$runId}/submit_tool_outputs";
        $data = [
            "tool_outputs" => $outputData
        ];

        Log::write("OpenAI updateRun: ".json_encode($data), "info");

        return $this->sendRequest($url, "POST", $data);
    }
}
