<?php

/*TABLE kampanyalar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad VARCHAR(255) NOT NULL,
    aciklama TEXT,
    baslangic_tarihi DATE NOT NULL,
    bitis_tarihi DATE NOT NULL,
    tur ENUM('indirim', 'miktar_indirim', 'paket_indirim') NOT NULL
);
*/

/*TABLE kampanya_urunleri (
    kampanya_id INT,
    urun_id INT,
    kategori_id INT,
    marka_id INT,
    tedarikci_id INT,
    haric_urun_id INT,
);
*/
class Campaign {
    private $db;

    public function __construct($db) {
        $this->db = $db;

    }

    public function getAllCampaigns() {
        $query = "SELECT * FROM kampanyalar";
        $result = $this->db->query($query);
        return $result;
    }

    public function getCampaign($id) {
        $query = "SELECT * FROM kampanyalar WHERE id = ?";
        $result = $this->db->query($query, $id);
        return $result;
    }

    public function addCampaign($name, $description, $startDate, $endDate, $type) {
        $query = "INSERT INTO kampanyalar (ad, aciklama, baslangic_tarihi, bitis_tarihi, tur) VALUES (?, ?, ?, ?, ?)";
        $result = $this->db->query($query, $name, $description, $startDate, $endDate, $type);
        return $result;
    }

    public function updateCampaign($id, $name, $description, $startDate, $endDate, $type) {
        $query = "UPDATE kampanyalar SET ad = ?, aciklama = ?, baslangic_tarihi = ?, bitis_tarihi = ?, tur = ? WHERE id = ?";
        $result = $this->db->query($query, $name, $description, $startDate, $endDate, $type, $id);
        return $result;
    }

    public function deleteCampaign($id) {
        $query = "DELETE FROM kampanyalar WHERE id = ?";
        $result = $this->db->query($query, $id);
        return $result;
    }

    public function addProductToCampaign($campaignId, $productId = null, $categoryId = null, $brandId = null, $supplierId = null, $otherProductId = null) {
        if (empty($campaignId)) {
            return "Error: Campaign ID is required.";
        }

        $params = [$productId, $categoryId, $brandId, $supplierId];
        $params = array_filter($params, function($value) { return !is_null($value) && $value !== ''; });

        if (empty($params)) {
            return "Error: At least one of the following parameters is required: Product ID, Category ID, Brand ID, Supplier ID.";
        }

        if (!is_null($otherProductId) && $otherProductId !== '') {
            $params = array_merge([$campaignId], $params, [$otherProductId]);
        } else {
            $params = array_merge([$campaignId], $params);
        }

        $query = "INSERT INTO kampanya_urunleri (kampanya_id, urun_id, kategori_id, marka_id, tedarikci_id, haric_urun_id) VALUES (?, ?, ?, ?, ?, ?)";
        $result = $this->db->query($query, ...$params);

        return $result;
    }

    public function removeProductFromCampaign($campaignId, $productId = null, $categoryId = null, $brandId = null, $supplierId = null, $otherProductId = null) {
        // kampanya_id parametresi kontrolü
        if (empty($campaignId)) {
            throw new Exception("Kampanya ID'si belirtilmelidir.");
        }

        // En fazla bir parametre kontrolü
        $params = [$productId, $categoryId, $brandId, $supplierId, $otherProductId];
        $nonNullParams = array_filter($params, function($value) { return !is_null($value); });

        if (count($nonNullParams) > 1) {
            throw new Exception("En fazla bir parametre belirtmelisiniz.");
        }

        // Hangi parametrenin belirtildiğini bulma
        $column = null;
        if (!is_null($productId)) {
            $column = 'urun_id';
        } elseif (!is_null($categoryId)) {
            $column = 'kategori_id';
        } elseif (!is_null($brandId)) {
            $column = 'marka_id';
        } elseif (!is_null($supplierId)) {
            $column = 'tedarikci_id';
        } elseif (!is_null($otherProductId)) {
            $column = 'haric_urun_id';
        }

        // Eşleşen ürünü silme
        if ($column) {
            $query = "DELETE FROM kampanya_urunleri WHERE kampanya_id = ? AND $column = ?";
            $result = $this->db->query($query, [$campaignId, $nonNullParams[0]]);
            return $result;
        }

        return false;
    }

    public function checkCampaign($productId, $categoryId, $brandId, $supplierId) {
        // Kampanyalar tablosunda ürün id, kategori id, marka id veya tedarikçi id ile eşleşen bir kayıt ara
        $sql = "
            SELECT 
                kampanya_urunleri.*,
                kampanyalar.ad,kampanyalar.aciklama,kampanyalar.tur,
                kampanya_miktar_indirim.miktar_sinir, kampanya_miktar_indirim.indirim_orani
            FROM 
                kampanya_urunleri 
                    INNER JOIN kampanyalar ON kampanyalar.id = kampanya_urunleri.kampanya_id
                    LEFT JOIN kampanya_miktar_indirim ON kampanya_miktar_indirim.kampanya_id = kampanyalar.id
            WHERE 
                kampanyalar.bitis_tarihi > CURDATE() AND
                (urun_id = :productId OR kategori_id = :categoryId OR marka_id = :brandId OR tedarikci_id = :supplierId)
                ";
        $params = ['productId' => $productId, 'categoryId' => $categoryId, 'brandId' => $brandId, 'supplierId' => $supplierId];

        if (!empty($productId)) {
            $sql .= "AND (haric_urun_id IS NULL OR haric_urun_id != :haricUrunId)";
            $params['haricUrunId'] = $productId;
        }
        else {
            $sql .= " AND (haric_urun_id IS NULL OR haric_urun_id = 0)";
        }

        //print_r($params);
        $campaign = $this->db->select($sql, $params);

        // Eğer bir kampanya bulunduysa, kampanya bilgilerini döndür
        if ($campaign) {
            return $campaign;
        }

        // Eğer bir kampanya bulunamadıysa boş yanıt döndür
        return [];
    }
}