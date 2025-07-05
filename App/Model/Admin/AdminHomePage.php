<?php
class AdminHomePage
{
    private AdminDatabase $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $query = "
            CREATE TABLE IF NOT EXISTS homepage_blocks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(50) NOT NULL,
                content JSON NOT NULL,
                position INT NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                language VARCHAR(10) NOT NULL DEFAULT 'en',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ";
        $this->db->createTable($query);
        $this->initializeDefaultBlocks();
        $query = "
            CREATE TABLE IF NOT EXISTS homepage_product_groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(50) NOT NULL, -- Örneğin: 'discounted_products', 'new_arrivals'
                title VARCHAR(255) NOT NULL, -- Örneğin: 'İndirimdeki Ürünler'
                product_count INT DEFAULT 10, -- Kaç ürün gösterilecek
                product_ids VARCHAR(1000) DEFAULT '', -- Sıralama
                is_active TINYINT(1) DEFAULT 1, -- Aktif/Pasif durumu
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );
        ";
        $this->db->createTable($query);
        $this->initializeDefaultProductGroups();
    }

    public function initializeDefaultBlocks($language = 'tr')
    {
        // Varsayılan blokları tanımlıyoruz
        $defaultBlocks = [
            ['type' => 'slider', 'content' => ['title' => 'Ana Sayfa Slayt','link'=>'/_y/s/s/tasarim/AddMainSlide.php'], 'position' => 1],
            ['type' => 'middle_banners', 'content' => ['title' => 'Orta Bannerlar','link'=>'/_y/s/s/tasarim/AddMainPageMiddleBanner.php'], 'position' => 2],
            ['type' => 'discounted_products', 'content' => ['title' => 'İndirimdeki Ürünler'], 'position' => 3],
            ['type' => 'best_sellers', 'content' => ['title' => 'Çok Satanlar'], 'position' => 4],
            ['type' => 'recommended_products', 'content' => ['title' => 'Sizin İçin Seçtiklerimiz'], 'position' => 5],
            ['type' => 'daily_deals', 'content' => ['title' => 'Günün Fırsatı'], 'position' => 6],
            ['type' => 'bottom_banners', 'content' => ['title' => 'Alt Bannerlar','link'=>'/_y/s/s/tasarim/AddBottomBanner.php'], 'position' => 7],
        ];

        // Veritabanında blok var mı kontrol ediyoruz
        $existingBlocks = $this->getBlocks($language);

        if (empty($existingBlocks)) {
            foreach ($defaultBlocks as $block) {
                $this->addBlock($block['type'], $block['content'], $language);
            }
            return true;
        }

        return false; // Zaten bloklar mevcut
    }

    public function initializeDefaultProductGroups()
    {
        $defaultGroups = [
            ['type' => 'recommended_products', 'title' => 'Sizin İçin Seçtiklerimiz', 'product_count' => 10, 'product_ids' => ''],
            ['type' => 'daily_deals', 'title' => 'Fırsat Ürünleri', 'product_count' => 8, 'product_ids' => ''],
            ['type' => 'discounted_products', 'title' => 'İndirimli Ürünler', 'product_count' => 12, 'product_ids' => ''],
            ['type' => 'new_arrivals', 'title' => 'Yeni Ürünler', 'product_count' => 10, 'product_ids' => ''],
            ['type' => 'campaign_products', 'title' => 'Kampanyalı Ürünler', 'product_count' => 6, 'product_ids' => ''],
        ];

        $existingGroups = $this->getProductGroups();

        if (empty($existingGroups)) {
            foreach ($defaultGroups as $group) {
                $this->addProductGroup($group['type'], $group['title'], $group['product_count'], $group['product_ids']);
            }
            return true;
        }

        return false; // Zaten gruplar mevcut
    }

    public function getBlocks($language = 'en')
    {
        $query = "SELECT * FROM homepage_blocks WHERE language = :language ORDER BY position ASC";
        $params = [':language' => $language];
        return $this->db->select($query, $params);
    }

    public function addBlock($type, $content, $language = 'tr')
    {
        $query = "
            INSERT INTO homepage_blocks (type, content, position, is_active, language)
            VALUES (:type, :content, :position, :is_active, :language)
        ";
        $params = [
            ':type' => $type,
            ':content' => json_encode($content),
            ':position' => $this->getNextPosition($language),
            ':is_active' => 1,
            ':language' => $language,
        ];
        return $this->db->insert($query, $params);
    }

    public function updateBlock($id, $content)
    {
        $query = "UPDATE homepage_blocks SET content = :content WHERE id = :id";
        $params = [
            ':content' => json_encode($content),
            ':id' => $id,
        ];
        return $this->db->update($query, $params);
    }

    public function deleteBlock($id)
    {
        $query = "DELETE FROM homepage_blocks WHERE id = :id";
        $params = [':id' => $id];
        return $this->db->delete($query, $params);
    }

    public function reorderBlocks($blockOrder)
    {
        // Başlangıç CASE ifadesi
        $query = "UPDATE homepage_blocks SET position = CASE id ";
        $params = [];

        // CASE yapılarını ve parametreleri oluştur
        foreach ($blockOrder as $position => $id) {
            $query .= "WHEN :id{$id} THEN :position{$id} ";
            $params[":id{$id}"] = $id;
            $params[":position{$id}"] = $position;
        }

        // WHERE koşulu ile geçerli id'leri sınırlıyoruz
        $query .= "END WHERE id IN (" . implode(", ", array_map(function ($id) {
                return ":id{$id}";
            }, array_keys($blockOrder))) . ")";

        // Güncellemeyi çalıştır
        return $this->db->update($query, $params);
    }

    private function getNextPosition($language)
    {
        $query = "SELECT MAX(position) as max_position FROM homepage_blocks WHERE language = :language";
        $params = [':language' => $language];
        $result = $this->db->select($query, $params)[0];
        return ($result['max_position'] ?? 0) + 1;
    }

    public function addProductGroup($type, $title, $product_count, $productIds)
    {
        $query = "
            INSERT INTO homepage_product_groups (type, title, product_count, product_ids, is_active)
            VALUES (:type, :title, :product_count, :product_ids, 1)
        ";
        $params = [
            ':type' => $type,
            ':title' => $title,
            ':product_count' => $product_count,
            ':product_ids' => $productIds
        ];
        return $this->db->insert($query, $params);
    }

    public function updateProductGroup($groupId, $title, $productIds, $productCount) {
        $query = "
        UPDATE product_groups
        SET title = :title, product_ids = :product_ids, product_count = :product_count
        WHERE id = :id
    ";
        $params = [
            ':title' => $title,
            ':product_ids' => $productIds,
            ':product_count' => $productCount,
            ':id' => $groupId,
        ];
        return $this->db->update($query, $params);
    }


    public function getProductGroup($identifier)
    {
        $query = "SELECT * FROM homepage_product_groups WHERE id = :identifier LIMIT 1";

        $params = [':identifier' => $identifier];

        $result = $this->db->select($query, $params);
        return $result ? $result[0] : null; // Grup bulunursa ilk kaydı döndür, yoksa null
    }

    public function getProductGroups()
    {
        $query = "SELECT * FROM homepage_product_groups";
        return $this->db->select($query);
    }

}
