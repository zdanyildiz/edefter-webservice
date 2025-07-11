<?php

class MemberModel
{
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->createTable();
        $this->createLogTable();
        $this->createTrialUsersTable();
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

    public function createTrialUsersTable() {
        $query = "
            CREATE TABLE IF NOT EXISTS trial_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INT NOT NULL,
                trial_start_date DATETIME NOT NULL,
                trial_end_date DATETIME NOT NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_member_id (member_id),
                FOREIGN KEY (member_id) REFERENCES uye(uyeid)
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

    // Deneme kullanıcısı kontrolü
    public function checkTrialUser($memberID) {
        $query = "
            SELECT * FROM trial_users WHERE member_id = :memberID AND is_active = 1
        ";
        return $this->db->select($query, ['memberID' => $memberID]);
    }

    // Deneme kullanıcısı ekleme
    public function addTrialUser($memberID, $trialDays = 30) {
        $trialStartDate = date('Y-m-d H:i:s');
        $trialEndDate = date('Y-m-d H:i:s', strtotime("+{$trialDays} days"));
        
        $query = "
            INSERT INTO trial_users (member_id, trial_start_date, trial_end_date, is_active)
            VALUES (:memberID, :trialStartDate, :trialEndDate, 1)
        ";
        return $this->db->insert($query, [
            'memberID' => $memberID,
            'trialStartDate' => $trialStartDate,
            'trialEndDate' => $trialEndDate
        ]);
    }

    // Deneme süresi kontrolü
    public function isTrialExpired($memberID) {
        $query = "
            SELECT trial_end_date FROM trial_users 
            WHERE member_id = :memberID AND is_active = 1
        ";
        $result = $this->db->select($query, ['memberID' => $memberID]);
        
        if (!$result) {
            return true; // Deneme kaydı yoksa süresi dolmuş kabul et
        }
        
        $trialEndDate = $result[0]['trial_end_date'];
        return strtotime($trialEndDate) < time();
    }

    // Deneme kullanıcısını deaktif etme
    public function deactivateTrialUser($memberID) {
        $query = "
            UPDATE trial_users SET is_active = 0 WHERE member_id = :memberID
        ";
        return $this->db->update($query, ['memberID' => $memberID]);
    }
}