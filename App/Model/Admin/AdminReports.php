<?php

class AdminReports
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function saveRefreshToken($clientId, $email, $refreshToken)
    {
        $stmt = $this->db->prepare("INSERT INTO client_api_credentials (client_id, google_account_email, google_refresh_token, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE google_refresh_token = ?, google_account_email = ?, updated_at = NOW()");
        return $stmt->execute([$clientId, $email, $refreshToken, $refreshToken, $email]);
    }

    public function getRefreshToken($clientId)
    {
        $stmt = $this->db->prepare("SELECT google_refresh_token FROM client_api_credentials WHERE client_id = ?");
        $stmt->execute([$clientId]);
        return $stmt->fetchColumn();
    }

    public function saveDailySummary($summaryData)
    {
        $stmt = $this->db->prepare("INSERT INTO analytics_daily_summary (client_id, summary_date, sessions, users, new_users, total_ad_cost, total_ad_conversions, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE sessions = ?, users = ?, new_users = ?, total_ad_cost = ?, total_ad_conversions = ?, updated_at = NOW()");
        return $stmt->execute([
            $summaryData['client_id'],
            $summaryData['summary_date'],
            $summaryData['sessions'],
            $summaryData['users'],
            $summaryData['new_users'],
            $summaryData['total_ad_cost'],
            $summaryData['total_ad_conversions'],
            $summaryData['sessions'],
            $summaryData['users'],
            $summaryData['new_users'],
            $summaryData['total_ad_cost'],
            $summaryData['total_ad_conversions']
        ]);
    }

    public function getDailySummary($clientId, $startDate, $endDate)
    {
        $stmt = $this->db->prepare("SELECT * FROM analytics_daily_summary WHERE client_id = ? AND summary_date BETWEEN ? AND ? ORDER BY summary_date ASC");
        $stmt->execute([$clientId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCredentials()
    {
        $stmt = $this->db->prepare("SELECT client_id, google_refresh_token, google_account_email, ga_property_id, ads_customer_id FROM client_api_credentials");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
