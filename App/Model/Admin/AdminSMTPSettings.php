<?php


/**
 * Table: smtp_settings
 * Columns:
 * id int AI PK
 * language_id int
 * email varchar(255)
 * password text
 * host varchar(255)
 * port int
 * encryption varchar(10)
 */
class AdminSMTPSettings
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
        $this->createSMTPSettingsTable();
    }

    // Table creation method
    public function createSMTPSettingsTable()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS smtp_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                language_id INT NOT NULL,
                email VARCHAR(255) NOT NULL,
                password TEXT NOT NULL,
                host VARCHAR(255) NOT NULL,
                port INT NOT NULL,
                encryption VARCHAR(10) NOT NULL,
                sender_name VARCHAR(100) NOT NULL
            )
        ";
        return $this->db->createTable($query);
    }

    public function getSMTPSettings(int $languageID)
    {
        $query = "
            SELECT 
                id,
                email,
                password,
                host,
                port,
                encryption,
                sender_name    
            FROM 
                smtp_settings 
            WHERE 
                language_id = :languageID";
        $params = [
            ':languageID' => $languageID
        ];
        return $this->db->select($query, $params);
    }

    public function addSMTPSettings(array $data)
    {
        $query = "
            INSERT INTO smtp_settings 
            SET 
                language_id = :languageID,
                email = :email,
                password = :password,
                host = :host,
                port = :port,
                encryption = :encryption,
                sender_name = :sender_name
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':host' => $data['host'],
            ':port' => $data['port'],
            ':encryption' => $data['encryption'],
            ':sender_name' => $data['senderName']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateSMTPSettings(array $data)
    {
        $query = "
            UPDATE smtp_settings 
            SET 
                email = :email,
                password = :password,
                host = :host,
                port = :port,
                encryption = :encryption,
                sender_name = :sender_name
            WHERE 
                language_id = :languageID
        ";

        $params = [
            ':languageID' => $data['languageID'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':host' => $data['host'],
            ':port' => $data['port'],
            ':encryption' => $data['encryption'],
            ':sender_name' => $data['senderName']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteSMTPSettings(int $languageID)
    {
        $query = "
            DELETE FROM smtp_settings 
            WHERE 
                language_id = :languageID
        ";

        $params = [
            ':languageID' => $languageID
        ];

        return $this->db->delete($query, $params);
    }

    public function beginTransaction($funcName="")
    {
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName="")
    {
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }
}
