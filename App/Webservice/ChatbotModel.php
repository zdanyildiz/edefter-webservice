<?php

class ChatbotModel
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->createConsentTable();
    }

    public function createConsentTable()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS user_consent (
                user_id INT NOT NULL PRIMARY KEY,
                consent_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                consent_status VARCHAR(20) NOT NULL,
                FOREIGN KEY (user_id) REFERENCES uye(uyeid)
            )
        ";
        return $this->db->createTable($query);
    }

    public function getPackageByName($packageName)
    {
        $sql = "SELECT * FROM chatbot_packages WHERE package_name = :packageName";
        return $this->db->select($sql, ['packageName' => $packageName]);
    }

    public function getUserChatbotUsage($userId)
    {
        $sql = "SELECT ucu.*, cp.package_name, cp.daily_message_limit, cp.daily_token_limit FROM user_chatbot_usage ucu JOIN chatbot_packages cp ON ucu.package_id = cp.id WHERE ucu.user_id = :userId";
        return $this->db->select($sql, ['userId' => $userId]);
    }

    public function createUserChatbotUsage($userId, $packageId, $remainingMessages, $remainingTokens, $lastMessageDate)
    {
        $sql = "INSERT INTO user_chatbot_usage (user_id, package_id, remaining_messages, remaining_tokens, last_message_date) VALUES (:userId, :packageId, :remainingMessages, :remainingTokens, :lastMessageDate)";
        return $this->db->insert($sql, [
            'userId' => $userId,
            'packageId' => $packageId,
            'remainingMessages' => $remainingMessages,
            'remainingTokens' => $remainingTokens,
            'lastMessageDate' => $lastMessageDate
        ]);
    }

    public function updateUserChatbotUsage($userId, $remainingMessages, $remainingTokens, $lastMessageDate, $totalTokensUsed)
    {
        $sql = "UPDATE user_chatbot_usage SET remaining_messages = :remainingMessages, remaining_tokens = :remainingTokens, last_message_date = :lastMessageDate, total_tokens_used = :totalTokensUsed WHERE user_id = :userId";
        return $this->db->update($sql, [
            'userId' => $userId,
            'remainingMessages' => $remainingMessages,
            'remainingTokens' => $remainingTokens,
            'lastMessageDate' => $lastMessageDate,
            'totalTokensUsed' => $totalTokensUsed
        ]);
    }

    public function logChatbotRequest($userId, $promptTokens, $completionTokens, $totalTokens, $userMessage, $chatbotResponse, $rawApiResponse)
    {
        $sql = "INSERT INTO chatbot_requests_log (user_id, prompt_tokens, completion_tokens, total_tokens, user_message, chatbot_response, raw_api_response) VALUES (:userId, :promptTokens, :completionTokens, :totalTokens, :userMessage, :chatbotResponse, :rawApiResponse)";
        return $this->db->insert($sql, [
            'userId' => $userId,
            'promptTokens' => $promptTokens,
            'completionTokens' => $completionTokens,
            'totalTokens' => $totalTokens,
            'userMessage' => $userMessage,
            'chatbotResponse' => $chatbotResponse,
            'rawApiResponse' => $rawApiResponse
        ]);
    }

    public function logUserConsent($userId, $consentStatus)
    {
        $sql = "INSERT INTO user_consent (user_id, consent_status) VALUES (:userId, :consentStatus) ON DUPLICATE KEY UPDATE consent_date = CURRENT_TIMESTAMP, consent_status = :consentStatusUpdate";
        return $this->db->insert($sql, ['userId' => $userId, 'consentStatus' => $consentStatus, 'consentStatusUpdate' => $consentStatus]);
    }

    public function getChatHistory($userId, $date, $limit = 10)
    {
        $sql = "SELECT user_message, chatbot_response FROM chatbot_requests_log WHERE user_id = :userId AND DATE(request_time) = :date ORDER BY request_time DESC LIMIT :limit";
        $result = $this->db->select($sql, ['userId' => $userId, 'date' => $date, 'limit' => $limit]);
        return $result ?: []; // Eğer sonuç yoksa boş dizi döndür
    }
}
