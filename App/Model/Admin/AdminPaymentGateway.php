<?php
class AdminPaymentGateway {

    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createProviderWithSettings($providerData, $settingsData) {
        $sql = "INSERT INTO payment_providers (name, description, status, languageCode) VALUES (:name, :description, :status, :languageCode)";
        $this->db->beginTransaction();
        try {
            // payment_providers tablosuna veri ekle
            $providerId = $this->db->insert($sql, $providerData);

            if(!$providerId){
                $this->db->rollBack();
                return [
                    "status" => "error",
                    "message" => "Ödeme sağlayıcısı eklenirken hata oluştu"
                ];
            }

            // Eğer yeni sağlayıcı aktif ise, diğer tüm sağlayıcıları pasif yap
            if ($providerData['status'] == 1) {
                $sql = "UPDATE payment_providers SET status = 0 WHERE id != :id";
                $this->db->update($sql, ['id' => $providerId]);
            }

            // payment_provider_settings tablosuna veri ekle
            foreach ($settingsData as $setting) {
                $setting['payment_provider_id'] = $providerId;
                $result = $this->createProviderSetting($setting);
                if (!$result) {
                    $this->db->rollBack();
                    return [
                        "status" => "error",
                        "message" => "Ayarlar eklenirken hata oluştu"
                    ];
                }
            }

            $this->db->commit();
            return [
                "status" => "success",
                "message" => "Ödeme sağlayıcısı ve ayarları eklendi",
                "provider_id" => $providerId
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    private function createProviderSetting($settingData) {
        $sql = "INSERT INTO payment_provider_settings (payment_provider_id, `key`, value) VALUES (:payment_provider_id, :key, :value)";
        return $this->db->insert($sql, $settingData);
    }


    public function updateProviderWithSettings($providerId, $providerData, $settingsData) {
        $providerData['id'] = $providerId;
        $sql = "UPDATE payment_providers SET name = :name, description = :description, status = :status, languageCode = :languageCode WHERE id = :id";
        $this->db->beginTransaction();
        try {
            $this->db->update($sql, $providerData);

            // Eğer güncellenen sağlayıcı aktif ise, diğer tüm sağlayıcıları pasif yap
            if ($providerData['status'] == 1) {
                $sql = "UPDATE payment_providers SET status = 0 WHERE id != :id";
                $this->db->update($sql, ['id' => $providerId]);
            }

            // Mevcut ayarları silip yeniden ekleyebiliriz veya güncelleyebiliriz
            $sql = "DELETE FROM payment_provider_settings WHERE payment_provider_id = :payment_provider_id";
            $this->db->delete($sql, ['payment_provider_id' => $providerId]);

            foreach ($settingsData as $setting) {
                $setting['payment_provider_id'] = $providerId;
                $result = $this->createProviderSetting($setting);
                if(!$result){
                    $this->db->rollBack();
                    return [
                        "status" => "error",
                        "message" => "Ayarlar güncellenirken hata oluştu"
                    ];
                }
            }

            $this->db->commit();
            return [
                "status" => "success",
                "message" => "Ödeme sağlayıcısı ve ayarları güncellendi"
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function deleteProvider($providerId) {
        $sql = "DELETE FROM payment_providers WHERE id = :id";
        $this->db->beginTransaction();
        try {
            $this->db->delete($sql, ['id' => $providerId]);

            $sql = "DELETE FROM payment_provider_settings WHERE payment_provider_id = :payment_provider_id";
            $this->db->delete($sql, ['payment_provider_id' => $providerId]);

            $this->db->commit();
            return [
                "status" => "success",
                "message" => "Ödeme sağlayıcısı ve ayarları silindi"
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function getProviders($languageCode) {
        $sql = "
            SELECT 
                pp.id as provider_id, 
                pp.name, 
                pp.description, 
                pp.status, 
                pp.languageCode, 
                pp.created_at, 
                pp.updated_at, 
                pps.id as setting_id, 
                pps.`key`, 
                pps.value 
            FROM 
                payment_providers pp 
            LEFT JOIN 
                payment_provider_settings pps 
            ON 
                pp.id = pps.payment_provider_id
            WHERE
                pp.languageCode = :languageCode
        ";
        $results = $this->db->select($sql, ['languageCode' => $languageCode]);

        $providers = [];
        foreach ($results as $row) {
            $providerId = $row['provider_id'];
            if (!isset($providers[$providerId])) {
                $providers[$providerId] = [
                    'id' => $row['provider_id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'status' => $row['status'],
                    'languageCode' => $row['languageCode'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'settings' => []
                ];
            }
            if ($row['setting_id']) {
                $providers[$providerId]['settings'][] = [
                    'id' => $row['setting_id'],
                    'key' => $row['key'],
                    'value' => $row['value']
                ];
            }
        }

        return array_values($providers);
    }

    public function getProvider($providerId) {
        $sql = "
            SELECT 
                pp.id as provider_id, 
                pp.name, 
                pp.description, 
                pp.status, 
                pp.languageCode, 
                pp.created_at, 
                pp.updated_at, 
                pps.id as setting_id, 
                pps.`key`, 
                pps.value 
            FROM 
                payment_providers pp 
            LEFT JOIN 
                payment_provider_settings pps 
            ON 
                pp.id = pps.payment_provider_id
            WHERE 
                pp.id = :provider_id
        ";
        $results = $this->db->select($sql, ['provider_id' => $providerId]);

        $provider = null;
        foreach ($results as $row) {
            $providerId = $row['provider_id'];
            if (!$provider) {
                $provider = [
                    'id' => $row['provider_id'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'status' => $row['status'],
                    'languageCode' => $row['languageCode'],
                    'created_at' => $row['created_at'],
                    'updated_at' => $row['updated_at'],
                    'settings' => []
                ];
            }
            if ($row['setting_id']) {
                $provider['settings'][] = [
                    'id' => $row['setting_id'],
                    'key' => $row['key'],
                    'value' => $row['value']
                ];
            }
        }

        return $provider;
    }
}


