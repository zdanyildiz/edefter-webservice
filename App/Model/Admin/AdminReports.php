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
        $sql = "INSERT INTO analytics_daily_summary (client_id, summary_date, sessions, users, new_users, page_views, engagement_rate, total_ad_cost, total_ad_conversions, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE sessions = ?, users = ?, new_users = ?, page_views = ?, engagement_rate = ?, total_ad_cost = ?, total_ad_conversions = ?, updated_at = NOW()";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt === false) {
            error_log("AdminReports saveDailySummary prepare failed: " . print_r($this->db->errorInfo(), true));
            return false;
        }

        return $stmt->execute([
            $summaryData['client_id'],
            $summaryData['summary_date'],
            $summaryData['sessions'],
            $summaryData['users'],
            $summaryData['new_users'],
            $summaryData['page_views'],
            $summaryData['engagement_rate'],
            $summaryData['total_ad_cost'],
            $summaryData['total_ad_conversions'],
            $summaryData['sessions'],
            $summaryData['users'],
            $summaryData['new_users'],
            $summaryData['page_views'],
            $summaryData['engagement_rate'],
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

    public function deleteRefreshToken($clientId)
    {
        $stmt = $this->db->prepare("DELETE FROM client_api_credentials WHERE client_id = ?");
        return $stmt->execute([$clientId]);
    }
    
    public function saveAnalyticsProperty($clientId, $propertyId, $propertyName)
    {
        // Önce sütunların var olup olmadığını kontrol et
        try {
            $checkStmt = $this->db->prepare("SHOW COLUMNS FROM client_api_credentials LIKE 'ga_property_name'");
            $checkStmt->execute();
            $columnExists = $checkStmt->rowCount() > 0;
            
            if (!$columnExists) {
                // Sütun yoksa sadece ga_property_id'yi güncelle
                $stmt = $this->db->prepare("UPDATE client_api_credentials SET ga_property_id = ?, updated_at = NOW() WHERE client_id = ?");
                return $stmt->execute([$propertyId, $clientId]);
            }
            
            // Sütun varsa her ikisini de güncelle
            $stmt = $this->db->prepare("UPDATE client_api_credentials SET ga_property_id = ?, ga_property_name = ?, updated_at = NOW() WHERE client_id = ?");
            $success = $stmt->execute([$propertyId, $propertyName, $clientId]);
            
            if ($success) {
                // Property başarıyla kaydedildiyse, eski günlük özet verilerini temizle
                $this->clearAnalyticsDailySummary($clientId);
            }
            return $success;
            
        } catch (Exception $e) {
            error_log("AdminReports saveAnalyticsProperty error: " . $e->getMessage());
            // Hata durumunda fallback - sadece property_id kaydet
            $stmt = $this->db->prepare("UPDATE client_api_credentials SET ga_property_id = ?, updated_at = NOW() WHERE client_id = ?");
            $success = $stmt->execute([$propertyId, $clientId]);
            
            if ($success) {
                // Property başarıyla kaydedildiyse, eski günlük özet verilerini temizle
                $this->clearAnalyticsDailySummary($clientId);
            }
            return $success;
        }
    }
    
    /**
     * Belirli bir client_id'ye ait tüm günlük analitik özet verilerini temizler.
     */
    public function clearAnalyticsDailySummary($clientId)
    {
        $stmt = $this->db->prepare("DELETE FROM analytics_daily_summary WHERE client_id = ?");
        return $stmt->execute([$clientId]);
    }
    
    public function getAnalyticsProperty($clientId)
    {
        // Önce sütunların var olup olmadığını kontrol et
        try {
            $checkStmt = $this->db->prepare("SHOW COLUMNS FROM client_api_credentials LIKE 'ga_property_name'");
            $checkStmt->execute();
            $columnExists = $checkStmt->rowCount() > 0;
            
            if (!$columnExists) {
                // Sütun yoksa sadece ga_property_id'yi al
                $stmt = $this->db->prepare("SELECT ga_property_id FROM client_api_credentials WHERE client_id = ?");
                if ($stmt === false) {
                    error_log("AdminReports getAnalyticsProperty prepare failed (fallback): " . print_r($this->db->errorInfo(), true));
                    return false;
                }
                
                if (!$stmt->execute([$clientId])) {
                    error_log("AdminReports getAnalyticsProperty execute failed (fallback): " . print_r($stmt->errorInfo(), true));
                    return false;
                }
                
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $result['ga_property_name'] = null; // Property name yok, null ekle
                }
                return $result;
            }
            
            // Sütun varsa normal sorguyu çalıştır
            $stmt = $this->db->prepare("SELECT ga_property_id, ga_property_name FROM client_api_credentials WHERE client_id = ?");
        } catch (Exception $e) {
            error_log("AdminReports getAnalyticsProperty column check failed: " . $e->getMessage());
            // Hata durumunda fallback query kullan
            $stmt = $this->db->prepare("SELECT ga_property_id FROM client_api_credentials WHERE client_id = ?");
        }
        
        if ($stmt === false) {
            error_log("AdminReports getAnalyticsProperty prepare failed: " . print_r($this->db->errorInfo(), true));
            return false;
        }
        
        $result = $stmt->execute([$clientId]);
        
        if ($result === false) {
            // Execute failed - log the error
            $errorInfo = $stmt->errorInfo();
            error_log("AdminReports getAnalyticsProperty execute failed: " . print_r($errorInfo, true));
            return false;
        }
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function saveSelectedAdsCustomer($clientId, $adsCustomerId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE client_api_credentials SET ads_customer_id = ?, updated_at = NOW() WHERE client_id = ?");
            $success = $stmt->execute([$adsCustomerId, $clientId]);
            
            if ($success) {
                // Ads müşteri ID'si başarıyla kaydedildiyse, eski günlük özet verilerini temizle
                $this->clearAnalyticsDailySummary($clientId);
            }
            return $success;
        } catch (Exception $e) {
            error_log("AdminReports saveSelectedAdsCustomer error: " . $e->getMessage());
            return false;
        }
    }

    public function getAdsCustomer($clientId)
    {
        $stmt = $this->db->prepare("SELECT ads_customer_id FROM client_api_credentials WHERE client_id = ?");
        $stmt->execute([$clientId]);
        return $stmt->fetchColumn();
    }

    /**
     * Analytics summary tablosunu oluştur
     */
    private function createAnalyticsSummaryTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS analytics_daily_summary (
                id INT AUTO_INCREMENT PRIMARY KEY,
                client_id INT NOT NULL,
                summary_date DATE NOT NULL,
                sessions INT DEFAULT 0,
                users INT DEFAULT 0,
                new_users INT DEFAULT 0,
                page_views INT DEFAULT 0,
                engagement_rate DECIMAL(5,2) DEFAULT 0.00,
                total_ad_cost DECIMAL(10,2) DEFAULT 0.00,
                total_ad_conversions INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_client_date (client_id, summary_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        return $this->db->exec($sql);
    }
}
