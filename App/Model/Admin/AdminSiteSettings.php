<?php

// AdminSiteSettings.php

class AdminSiteSettings
{
    private AdminDatabase $db;
    private int $language_id;

    /**
     * Constructor
     *
     * @param AdminDatabase $db Veritabanı bağlantısı sağlayan AdminDatabase nesnesi
     * @param int $language_id Dil kimliği (varsayılan: 1)
     */
    public function __construct(AdminDatabase $db, int $language_id = 1)
    {
        $this->db = $db;
        $this->language_id = $language_id;
    }

    /**
     * Tüm site ayarlarını çeker
     *
     * @return array|false Ayarlar array olarak döner, hata durumunda false
     */
    public function getAllSettings()
    {
        $sql = "SELECT * FROM site_settings WHERE language_id = :language_id ORDER BY section, element";
        $params = ['language_id' => $this->language_id];
        return $this->db->select($sql, $params);
    }

    public function getSiteSettingsByLanguageID($languageID)
    {
        $sql = "SELECT * FROM site_settings WHERE language_id = :language_id ORDER BY section, element";
        $params = ['language_id' => $languageID];
        return $this->db->select($sql, $params);

    }

    /**
     * Ayarları bölüme göre gruplar
     *
     * @return array Gruplandırılmış ayarlar
     */
    public function getSettingsGrouped(): array
    {
        $settings = $this->getAllSettings();
        $grouped = [];

        if ($settings === false) {
            return $grouped;
        }

        foreach ($settings as $setting) {
            $grouped[$setting['section']][$setting['id']] = [
                'element' => $setting['element'],
                'is_visible' => $setting['is_visible']
            ];
        }

        return $grouped;
    }

    /**
     * Var olan ayarların görünürlüğünü günceller
     *
     * @param array $settings Ayar ID => Görünürlük (1 veya 0) şeklinde
     * @return bool Güncellemenin başarılı olup olmadığı
     */
    public function updateSettings($is_visible, $id): bool
    {

        $sql = "UPDATE site_settings SET is_visible = :is_visible, updated_at = NOW() WHERE id = :id AND language_id = :language_id";
        return $this->db->update($sql, [
            'is_visible' => $is_visible,
            'id' => $id,
            'language_id' => $this->language_id
        ]);
    }

    /**
     * Yeni bir ayar ekler
     *
     * @param string $section Bölüm adı (örn. 'header', 'footer')
     * @param string $element Öğe adı (örn. 'phone', 'whatsapp')
     * @param int $is_visible Görünürlük durumu (1 = Görünür, 0 = Gizli)
     * @return bool Ayarın eklenip eklenmediği
     */
    public function addSetting(string $section, string $element, int $is_visible = 1): bool
    {
        $sql = "INSERT INTO site_settings (section, element, is_visible, language_id, created_at, updated_at) VALUES (:section, :element, :is_visible, :language_id, NOW(), NOW())";
        $params = [
            'section' => $section,
            'element' => $element,
            'is_visible' => $is_visible,
            'language_id' => $this->language_id
        ];
        return $this->db->insert($sql, $params);
    }

    public function addSettingWithLanguageId(string $section, string $element, $languageId, int $is_visible = 1): bool
    {
        $sql = "INSERT INTO site_settings (section, element, is_visible, language_id, created_at, updated_at) VALUES (:section, :element, :is_visible, :language_id, NOW(), NOW())";
        $params = [
            'section' => $section,
            'element' => $element,
            'is_visible' => $is_visible,
            'language_id' => $languageId
        ];
        return $this->db->insert($sql, $params);
    }

    /**
     * site_config_versions
     * id
     * language_id
     * version
     * last_updated_at
     */

    public function getSiteConfigVersions($languageId){
        $sql = "SELECT * FROM site_config_versions WHERE language_id = :language_id";
        $params = ['language_id' => $languageId];
        return $this->db->select($sql, $params);
    }

    public function addSiteConfigVersion($languageId, $version=1)
    {
        $sql = "INSERT INTO site_config_versions (language_id, version)  value (:language_id, :version)";
        $params = [
            'language_id' => $languageId,
            'version' => $version
        ];
        return $this->db->insert($sql, $params);
    }

    //versiyonu 1 arttıralım
    public function updateSiteConfigVersion($languageId)
    {
        $sql = "UPDATE site_config_versions SET version = version + 1 WHERE language_id = :language_id";
        $params = [
            'language_id' => $languageId
        ];
        return $this->db->update($sql, $params);
    }

    public function getGeneralSettings($languageID)
    {
        $sql = "SELECT * FROM ayargenel WHERE dilid = :languageId";
        $data = $this->db->select($sql, ['languageId' => $languageID]);
        if (empty($data)) {
            return [];
        }
        return $data[0];
    }
    public function addGeneralSettings($generalSettings){
        $sql = "INSERT INTO ayargenel 
        (domain, ssldurum, sitetip, cokludil, uyelik, dilid) 
        VALUES 
        (:domain, :ssldurum, :sitetip, :cokludil, :uyelik, :dilid)";

        return $this->db->insert($sql, $generalSettings);
    }

    //transaction
    public function beginTransaction($funcName=""){
        $this->db->beginTransaction($funcName);
    }
    public function commit($funcName=""){
        $this->db->commit($funcName);
    }
    public function rollback($funcName=""){
        $this->db->rollback($funcName);
    }
}
