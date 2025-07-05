<?php

class MemberModel
{
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->createTable();
        $this->createLogTable();
    }

    public function createTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS user_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                computer_id VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES uye(uyeid)
            )
        ";
        return $this->db->createTable($query);
    }

    public function createLogTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS user_sessions_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                computer_id VARCHAR(255) NOT NULL,
                attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES uye(uyeid)
            )
        ";
        return $this->db->createTable($query);
    }

    //addUsers
    public function addUsers($userID, $computerID)
    {
        $query = "
            INSERT INTO user_sessions (user_id, computer_id)
            VALUES (:userID, :computerID)
        ";
        return $this->db->insert($query, ['userID' => $userID, 'computerID' => $computerID]);

    }

    //checkUserByUserId
    public function checkUserByUserId($userID)
    {
        $query = "
            SELECT * FROM user_sessions WHERE user_id = :userID
        ";
        return $this->db->select($query, ['userID' => $userID]);
    }

    //checkUsers
    public function checkUsers($userID, $computerID)
    {
        $query = "
            SELECT * FROM user_sessions WHERE user_id = :userID AND computer_id = :computerID
        ";
        return $this->db->select($query, ['userID' => $userID, 'computerID' => $computerID]);
    }

    public function updateComputerId($userID, $computerID)
    {
        $query = "
            UPDATE user_sessions SET computer_id = :computerID WHERE user_id = :userID
        ";
        return $this->db->update($query, ['userID' => $userID, 'computerID' => $computerID]);
    }

    public function deleteUsers($userID){
        $query = "
            DELETE FROM user_sessions WHERE user_id = :userID
        ";
        return $this->db->delete($query, ['userID' => $userID]);
    }

    public function logSessionAttempt($userID, $computerID) {
        $query = "
            INSERT INTO user_sessions_log (user_id, computer_id)
            VALUES (:userID, :computerID)
        ";
        return $this->db->insert($query, ['userID' => $userID, 'computerID' => $computerID]);
    }

    //checkSessionAttempt
    public function checkSessionAttempt($userID, $computerID){
        $query = "
            SELECT * FROM user_sessions_log WHERE user_id = :userID AND computer_id = :computerID";
        return $this->db->select($query, ['userID' => $userID, 'computerID' => $computerID]);
    }

    //checkSessionAttemptByUserID
    public function checkSessionAttemptByUserID($userID){
        $query = "
            SELECT * FROM user_sessions_log WHERE user_id = :userID";
        return $this->db->select($query, ['userID' => $userID]);
    }
}