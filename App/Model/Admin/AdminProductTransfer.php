<?php

// App/Model/Admin/AdminProductTransfer.php

class AdminProductTransfer
{
    private AdminDatabase $db;

    public function __construct($db)
    {
        $this->db = $db;
        $this->createTable();
    }

    private function createTable(){
        $sql = "CREATE TABLE IF NOT EXISTS `product_transfer` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `language_id` INT NOT NULL,
            `product_stock_code` VARCHAR(20) NOT NULL,
            `product_label` VARCHAR(255) NOT NULL,
            `long_description` TEXT,
            `short_description` TEXT,
            `category_information` VARCHAR(512),
            `brand_name` VARCHAR(255),
            `model` VARCHAR(255),
            `sale_price` DECIMAL(10,2),
            `list_price` DECIMAL(10,2),
            `currency` VARCHAR(10),
            `tax` INT,
            `stock_quantity` INT,
            `variant_information` TEXT,
            `product_features` TEXT,
            `images` TEXT,
            `delivery_time` VARCHAR(100),
            `product_status` VARCHAR(50),
            `barcode` VARCHAR(100),
            `gtin` VARCHAR(100),
            `mpn` VARCHAR(100),
            `oem` VARCHAR(100),
            `is_completed` TINYINT(1) DEFAULT 0,
            `transfer_description` TEXT,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->db->createTable($sql);
    }

    /**
     * Yeni ürün aktarımı ekler
     *
     * @param array $data
     * @return array
     */
    public function create($data): array
    {
        // Varsayılan değerler ekleyin
        $data['is_completed'] = $data['is_completed'] ?? 0; // 0: Tamamlanmadı, 1: Tamamlandı
        $data['transfer_description'] = $data['transfer_description'] ?? '';

        // Alan adlarının doğru olduğundan emin olun
        $allowedFields = [
            'language_id','product_stock_code', 'product_label', 'long_description', 'short_description',
            'category_information', 'brand_name', 'model', 'sale_price', 'list_price',
            'currency', 'stock_quantity', 'variant_information', 'product_features',
            'images', 'delivery_time', 'product_status', 'barcode', 'gtin', 'mpn',
            'is_completed', 'transfer_description', 'created_at', 'updated_at'
        ];

        // $data array'inden sadece izin verilen alanları alın
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        // Sadece izin verilen alanlarla INSERT işlemi yap
        $sql = "INSERT INTO product_transfer (" . implode(", ", array_keys($filteredData)) . ") 
            VALUES (" . implode(", ", array_map(function($key) { return ":$key"; }, array_keys($filteredData))) . ")";

        $insertResult = $this->db->insert($sql, $filteredData);

        if ($insertResult) {
            return ['status' => 'success', 'message' => 'Product transfer record created successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Veritabanı Hatası: Ürün aktarımı eklenemedi.'];
        }
    }

    public function truncateTransferTable()
    {
        return $this->db->truncateTable("product_transfer");
    }

    /**
     * Aktarım kaydının tamamlanma durumunu ve açıklamasını günceller
     *
     * @param int $id
     * @param int $isCompleted (0 veya 1)
     * @param string $description
     * @return array
     */
    public function updateTransferStatus($id, $isCompleted, $description): array
    {
        $data = [
            'is_completed' => $isCompleted,
            'transfer_description' => $description,
            'id' => $id
        ];

        $sql = "UPDATE product_transfer 
            SET is_completed = :is_completed, transfer_description = :transfer_description 
            WHERE id = :id";

        $updateResult = $this->db->update($sql, [
            'is_completed' => $isCompleted,
            'transfer_description' => $description,
            'id' => $id
        ]);

        if ($updateResult !== -1) {
            return ['status' => 'success', 'message' => 'Transfer status updated successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Veritabanı Hatası: Transfer durumu güncellenemedi.'];
        }
    }

    public function updateTransferStatusByModel($model, $isCompleted, $description): array
    {
        $data = [
            'is_completed' => $isCompleted,
            'transfer_description' => $description,
            'model' => $model
        ];

        $sql = "UPDATE product_transfer 
            SET 
                is_completed = :is_completed,
                transfer_description = :transfer_description
            WHERE model = :model";

        $updateResult = $this->db->update($sql, $data);

        if ($updateResult !== -1) {
            return ['status' => 'success', 'message' => 'Transfer status updated successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Veritabanı Hatası: Transfer durumu güncellenemedi.'];
        }
    }

    public function getTransfersByLanguageID($languageID)
    {
        $sql = "SELECT * FROM product_transfer WHERE language_id = :languageID GROUP BY category_information,brand_name,model,product_stock_code";
        $params = [
            'languageID' => $languageID
        ];
        return $this->db->select($sql, $params);
    }

    public function getTransferListByLanguageID($languageID)
    {
        $sql = "SELECT * FROM product_transfer WHERE language_id = :languageID and is_completed = 0 GROUP BY category_information,brand_name,model,product_stock_code";
        $params = [
            'languageID' => $languageID
        ];
        return $this->db->select($sql, $params);
    }

    /**
     * Tüm aktarım kayıtlarını alır
     *
     * @return array
     */
    /**
     * Tüm aktarım kayıtlarını alır
     *
     * @return array
     */
    public function getAllTransfers()
    {
        $sql = "SELECT * FROM product_transfer";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            Log::adminWrite("ProductTransfer GetAllTransfers Error: " . $e->getMessage(), "error");
            return [];
        }
    }


    /**
     * Belirli bir aktarım kaydını siler
     *
     * @param int $id
     * @return array
     */
    public function deleteTransfer($id)
    {
        $sql = "DELETE FROM product_transfer WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['status' => 'success', 'message' => 'Transfer record deleted.'];
        } catch (PDOException $e) {
            Log::adminWrite("ProductTransfer Delete Error: " . $e->getMessage(), "error");
            return ['status' => 'error', 'message' => 'Failed to delete transfer record.'];
        }
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

