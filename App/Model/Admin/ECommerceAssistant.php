<?php

class ECommerceAssistant {
    private $apiKey;
    private $apiEndpoint;
    private $assistantId;

    public function __construct($apiKey, $apiEndpoint) {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
    }

    public function createAssistant($name, $instructions, $model = 'gpt-3.5-turbo') {
        $data = [
            'name' => $name,
            'instructions' => $instructions,
            'model' => $model
        ];

        $response = $this->makeApiRequest('POST', '/assistants', $data);

        if (isset($response['id'])) {
            $this->assistantId = $response['id'];
        }

        return $response;
    }

    public function askQuestion($question, $context = '') {
        if (!$this->assistantId) {
            throw new Exception("Asistan henüz oluþturulmadý.");
        }

        $data = [
            'assistant_id' => $this->assistantId,
            'question' => $question,
            'context' => $context
        ];

        $response = $this->makeApiRequest('POST', '/ask', $data);
        return $response;
    }

    public function trainAssistant($trainingData) {
        if (!$this->assistantId) {
            throw new Exception("Asistan henüz oluþturulmadý.");
        }

        $data = [
            'assistant_id' => $this->assistantId,
            'training_data' => $trainingData
        ];

        $response = $this->makeApiRequest('POST', '/train', $data);
        return $response;
    }

    private function makeApiRequest($method, $endpoint, $data) {
        $ch = curl_init($this->apiEndpoint . $endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}