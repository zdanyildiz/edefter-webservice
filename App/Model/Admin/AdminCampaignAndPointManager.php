<?php

class CampaignAndPointsManager
{
    private AdminDatabase $db;

    public function __construct(AdminDatabase $db)
    {
        $this->db = $db;
    }

    // Kampanya Yönetimi

    public function addCampaign($data)
    {
        $query = "
            INSERT INTO kampanyalar 
            SET 
                ad = :name,
                aciklama = :description,
                baslangic_tarihi = :startDate,
                bitis_tarihi = :endDate,
                tur = :type
        ";

        $params = [
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':startDate' => $data['startDate'],
            ':endDate' => $data['endDate'],
            ':type' => $data['type']
        ];

        return $this->db->insert($query, $params);
    }

    public function updateCampaign($data)
    {
        $query = "
            UPDATE kampanyalar 
            SET 
                ad = :name,
                aciklama = :description,
                baslangic_tarihi = :startDate,
                bitis_tarihi = :endDate,
                tur = :type
            WHERE 
                id = :id
        ";

        $params = [
            ':id' => $data['id'],
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':startDate' => $data['startDate'],
            ':endDate' => $data['endDate'],
            ':type' => $data['type']
        ];

        return $this->db->update($query, $params);
    }

    public function deleteCampaign($id)
    {
        $query = "
            DELETE FROM kampanyalar 
            WHERE 
                id = :id
        ";

        $params = [':id' => $id];

        return $this->db->delete($query, $params);
    }

    public function getCampaigns()
    {
        $query = "
            SELECT * FROM kampanyalar
        ";

        return $this->db->select($query);
    }

    // Puan Yönetimi

    public function addPoints($data)
    {
        $query = "
            INSERT INTO uye_puanlari 
            SET 
                uye_id = :customerId,
                puan_bakiyesi = :pointsBalance,
                son_guncelleme = :lastUpdated
        ";

        $params = [
            ':customerId' => $data['customerId'],
            ':pointsBalance' => $data['pointsBalance'],
            ':lastUpdated' => $data['lastUpdated']
        ];

        return $this->db->insert($query, $params);
    }

    public function updatePoints($data)
    {
        $query = "
            UPDATE uye_puanlari 
            SET 
                puan_bakiyesi = :pointsBalance,
                son_guncelleme = :lastUpdated
            WHERE 
                uye_id = :customerId
        ";

        $params = [
            ':customerId' => $data['customerId'],
            ':pointsBalance' => $data['pointsBalance'],
            ':lastUpdated' => $data['lastUpdated']
        ];

        return $this->db->update($query, $params);
    }

    public function getPoints($customerId)
    {
        $query = "
            SELECT * FROM uye_puanlari 
            WHERE 
                uye_id = :customerId
        ";

        $params = [':customerId' => $customerId];

        return $this->db->select($query, $params);
    }

    public function addPointTransaction($data)
    {
        $query = "
            INSERT INTO puan_islemleri 
            SET 
                uye_id = :customerId,
                islem_tipi = :transactionType,
                puan_miktari = :pointsAmount,
                islem_tarihi = :transactionDate,
                siparis_id = :orderId
        ";

        $params = [
            ':customerId' => $data['customerId'],
            ':transactionType' => $data['transactionType'],
            ':pointsAmount' => $data['pointsAmount'],
            ':transactionDate' => $data['transactionDate'],
            ':orderId' => $data['orderId']
        ];

        return $this->db->insert($query, $params);
    }

    public function getPointTransactions($customerId)
    {
        $query = "
            SELECT * FROM puan_islemleri 
            WHERE 
                uye_id = :customerId
        ";

        $params = [':customerId' => $customerId];

        return $this->db->select($query, $params);
    }
}

?>
