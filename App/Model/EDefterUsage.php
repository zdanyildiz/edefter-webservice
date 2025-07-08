<?php

/**
 * E-Defter kullanım sınırlaması modeli
 * Üye olmayanlar günde 5, üyeler günde 20 işlem yapabilir
 */
class EDefterUsage {
    private $db;
    
    // Günlük sınırlar
    const VISITOR_DAILY_LIMIT = 5;
    const MEMBER_DAILY_LIMIT = 20;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Kullanıcının günlük kullanım sayısını artır
     * @param string $userIdentifier Kullanıcı kimliği (member ID veya session ID)
     * @param string $userType 'member' veya 'visitor'
     * @return bool İşlem başarılı mı
     */
    public function incrementUsage($userIdentifier, $userType = 'visitor') {
        $sql = "INSERT INTO edefter_usage (user_identifier, user_type, usage_date, usage_count, last_usage_time) 
                VALUES (?, ?, CURDATE(), 1, NOW())
                ON DUPLICATE KEY UPDATE 
                usage_count = usage_count + 1, 
                last_usage_time = NOW()";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userIdentifier, $userType]);
    }
    
    /**
     * Kullanıcının günlük kullanım sayısını getir
     * @param string $userIdentifier Kullanıcı kimliği
     * @return int Günlük kullanım sayısı
     */
    public function getDailyUsage($userIdentifier) {
        $sql = "SELECT usage_count FROM edefter_usage 
                WHERE user_identifier = ? AND usage_date = CURDATE()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userIdentifier]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['usage_count'] : 0;
    }
    
    /**
     * Kullanıcının sınırını aşıp aşmadığını kontrol et
     * @param string $userIdentifier Kullanıcı kimliği
     * @param string $userType 'member' veya 'visitor'
     * @return bool Sınır aşıldı mı
     */
    public function isLimitExceeded($userIdentifier, $userType = 'visitor') {
        $currentUsage = $this->getDailyUsage($userIdentifier);
        $limit = ($userType === 'member') ? self::MEMBER_DAILY_LIMIT : self::VISITOR_DAILY_LIMIT;
        
        return $currentUsage >= $limit;
    }
    
    /**
     * Kullanıcının kalan hakkını getir
     * @param string $userIdentifier Kullanıcı kimliği
     * @param string $userType 'member' veya 'visitor'
     * @return int Kalan kullanım hakkı
     */
    public function getRemainingUsage($userIdentifier, $userType = 'visitor') {
        $currentUsage = $this->getDailyUsage($userIdentifier);
        $limit = ($userType === 'member') ? self::MEMBER_DAILY_LIMIT : self::VISITOR_DAILY_LIMIT;
        
        return max(0, $limit - $currentUsage);
    }
    
    /**
     * Kullanıcının kullanım bilgilerini getir
     * @param string $userIdentifier Kullanıcı kimliği
     * @param string $userType 'member' veya 'visitor'
     * @return array Kullanım bilgileri
     */
    public function getUsageInfo($userIdentifier, $userType = 'visitor') {
        $currentUsage = $this->getDailyUsage($userIdentifier);
        $limit = ($userType === 'member') ? self::MEMBER_DAILY_LIMIT : self::VISITOR_DAILY_LIMIT;
        $remaining = max(0, $limit - $currentUsage);
        $isExceeded = $currentUsage >= $limit;
        
        return [
            'current_usage' => $currentUsage,
            'daily_limit' => $limit,
            'remaining_usage' => $remaining,
            'is_limit_exceeded' => $isExceeded,
            'user_type' => $userType
        ];
    }
    
    /**
     * Eski kullanım kayıtlarını temizle (30 günden eski)
     * @return bool Temizleme başarılı mı
     */
    public function cleanOldRecords() {
        $sql = "DELETE FROM edefter_usage WHERE usage_date < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
    }
}
