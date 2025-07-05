<?php
class AssistantLogger
{
    private Database $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->checkTables();
    }

    // Tabloları kontrol et ve yoksa oluştur
    private function checkTables()
    {
        $threadTableSql = "CREATE TABLE IF NOT EXISTS assistant_threads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            thread_id VARCHAR(255) UNIQUE NOT NULL,
            visitor_uniq_id VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $messageTableSql = "CREATE TABLE IF NOT EXISTS assistant_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            thread_id VARCHAR(255) NOT NULL,
            message_id VARCHAR(255),
            sender ENUM('user', 'assistant') NOT NULL,
            content TEXT NOT NULL,
            tokens_used INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (thread_id) REFERENCES assistant_threads(thread_id)
        )";

        $this->db->createTable($threadTableSql);
        $this->db->createTable($messageTableSql);
    }

    // Thread kaydı oluştur
    public function createThread($threadId, $visitorUniqID)
    {
        $sql = "INSERT INTO assistant_threads (thread_id, visitor_uniq_id) VALUES (:thread_id, :visitor_uniq_id)";
        $params = [
            'thread_id' => $threadId,
            'visitor_uniq_id' => $visitorUniqID
        ];
        return $this->db->insert($sql, $params);
    }

    public function getMessageByMessageId($messageId)
    {
        $sql = "SELECT * FROM assistant_messages WHERE message_id = :message_id";
        $params = [
            'message_id' => $messageId
        ];
        return $this->db->select($sql, $params);

    }
    // Mesaj kaydı oluştur
    public function logMessage($threadId, $messageId, $sender, $content, $tokensUsed = 0)
    {
        $sql = "INSERT INTO assistant_messages (thread_id, message_id, sender, content, tokens_used) VALUES (:thread_id, :message_id, :sender, :content, :tokens_used)";
        $params = [
            'thread_id' => $threadId,
            'message_id' => $messageId,
            'sender' => $sender,
            'content' => $content,
            'tokens_used' => $tokensUsed
        ];
        return $this->db->insert($sql, $params);
    }

    // Konuşma geçmişini getirme
    public function getConversationHistory($threadId)
    {
        $sql = "SELECT * FROM assistant_messages WHERE thread_id = :thread_id ORDER BY created_at ASC";
        $params = ['thread_id' => $threadId];
        return $this->db->select($sql, $params);
    }

    // Kullanılan toplam token sayısını getirme
    public function getTotalTokensUsed($threadId)
    {
        $sql = "SELECT SUM(tokens_used) as total_tokens FROM assistant_messages WHERE thread_id = :thread_id";
        $params = ['thread_id' => $threadId];
        $result = $this->db->select($sql, $params);
        return $result[0]['total_tokens'] ?? 0;
    }

    // Ortalama maliyet hesaplama (token başına ücret ile)
    public function calculateCost($threadId, $costPerToken)
    {
        $totalTokens = $this->getTotalTokensUsed($threadId);
        return $totalTokens * $costPerToken;
    }

    public function getLastUserMessage($threadId)
    {
        $sql = "SELECT * FROM assistant_messages 
            WHERE thread_id = :thread_id AND sender = 'user' 
            ORDER BY created_at DESC LIMIT 1";
        $params = ['thread_id' => $threadId];
        $result = $this->db->select($sql, $params);
        return $result[0] ?? null;
    }

    //threadId'ye göre sender'ı user olan son satırın token_used değerini günceller
    public function updateMessageTokensUsed($messageId, $tokensUsed)
    {
        $sql = "UPDATE assistant_messages SET tokens_used = :tokens_used WHERE message_id = :message_id";
        $params = [
            'tokens_used' => $tokensUsed,
            'message_id' => $messageId
        ];
        return $this->db->update($sql, $params);
    }

    public function beginTransaction($funcName = "")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName = "")
    {
        $this->db->commit($funcName);
    }

    public function rollback($funcName = "")
    {
        $this->db->rollback($funcName);
    }
}