<?php
// BannerTypeModel.php

class AdminBannerTypeModel {
    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllTypes() {
        $query = "SELECT * FROM banner_types";
        return $this->db->select($query);
    }

    public function getTypeById($id) {
        $query = "SELECT * FROM banner_types WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function addType($type_name, $description) {
        $query = "INSERT INTO banner_types (type_name, description) VALUES (:type_name, :description)";
        return $this->db->insert($query, ['type_name' => $type_name, 'description' => $description]);
    }

    public function updateType($id, $type_name, $description) {
        $query = "UPDATE banner_types SET type_name = :type_name, description = :description WHERE id = :id";
        return $this->db->update($query, ['id' => $id, 'type_name' => $type_name, 'description' => $description]);
    }

    public function deleteType($id) {
        $query = "DELETE FROM banner_types WHERE id = :id";
        return $this->db->delete($query, ['id' => $id]);
    }
}

// BannerLayoutModel.php

class AdminBannerLayoutModel {
    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllLayouts() {
        $query = "SELECT * FROM banner_layouts";
        return $this->db->select($query);
    }

    public function getLayoutById($id) {
        $query = "SELECT * FROM banner_layouts WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function getLayoutsByTypeId($type_id) {
        $query = "SELECT * FROM banner_layouts WHERE type_id = :type_id";
        return $this->db->select($query, ['type_id' => $type_id]);
    }

    public function addLayout($type_id, $layout_name, $description, $columns, $max_banners) {
        $query = "INSERT INTO banner_layouts (type_id, layout_name, description, columns, max_banners) VALUES (:type_id, :layout_name, :description, :columns, :max_banners)";
        return $this->db->insert($query, ['type_id' => $type_id, 'layout_name' => $layout_name, 'description' => $description, 'columns' => $columns, 'max_banners' => $max_banners]);
    }

    public function updateLayout($id, $type_id, $layout_name, $description, $columns, $max_banners) {
        $query = "UPDATE banner_layouts SET type_id = :type_id, layout_name = :layout_name, description = :description, columns = :columns, max_banners = :max_banners WHERE id = :id";
        return $this->db->update($query, ['id' => $id, 'type_id' => $type_id, 'layout_name' => $layout_name, 'description' => $description, 'columns' => $columns, 'max_banners' => $max_banners]);
    }

    public function deleteLayout($id) {
        $query = "DELETE FROM banner_layouts WHERE id = :id";
        return $this->db->delete($query, ['id' => $id]);
    }
}

// BannerGroupModel.php

class AdminBannerGroupModel {
    private AdminDatabase  $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllGroups() {
        $query = "SELECT * FROM banner_groups";
        return $this->db->select($query);
    }

    public function getGroupById($id) {
        $query = "SELECT * FROM banner_groups WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function addGroup($group_name, $group_title, $group_desc, $layout_id, $group_kind, $group_view, $columns, $content_alignment, $style_class, $background_color, $group_title_color, $group_desc_color, $full_size, $custom_css, $order_num, $visibility_start, $visibility_end, $banner_duration, $banner_full_size) {
        $query = "
            INSERT 
            INTO 
                banner_groups (group_name, group_title, group_desc, layout_id, group_kind, group_view, columns, content_alignment, style_class, background_color, group_title_color, group_desc_color, group_full_size, custom_css, order_num, visibility_start, visibility_end, banner_duration, banner_full_size) 
            VALUES 
                (:group_name, :group_title, :group_desc, :layout_id, :group_kind, :group_view, :columns, :content_alignment, :style_class, :background_color, :group_title_color, :group_desc_color, :group_full_size, :custom_css, :order_num, :visibility_start, :visibility_end, :banner_duration, :banner_full_size)
        ";
        return $this->db->insert($query, ['group_name' => $group_name, 'group_title' => $group_title, 'group_desc' => $group_desc, 'layout_id'=> $layout_id, 'group_kind' => $group_kind, 'group_view' => $group_view, 'columns' => $columns, 'content_alignment' => $content_alignment, 'style_class' => $style_class, 'background_color' => $background_color, 'group_title_color' => $group_title_color, 'group_desc_color' => $group_desc_color, 'group_full_size' => $full_size, 'custom_css' => $custom_css, 'order_num' => $order_num, 'visibility_start' => $visibility_start, 'visibility_end' => $visibility_end, 'banner_duration' => $banner_duration, 'banner_full_size' => $banner_full_size]);
    }

    public function updateGroup($id, $group_name, $group_title, $group_desc, $layout_id, $group_kind, $group_view, $columns, $content_alignment, $style_class, $background_color, $group_title_color, $group_desc_color, $full_size, $custom_css, $order_num, $visibility_start, $visibility_end, $banner_duration, $banner_full_size) {
        $query = "
            UPDATE 
                banner_groups 
            SET 
                group_name = :group_name, 
                group_title = :group_title, 
                group_desc = :group_desc, 
                layout_id = :layout_id, 
                group_kind = :group_kind, 
                group_view = :group_view, 
                columns = :columns, 
                content_alignment = :content_alignment, 
                style_class = :style_class, 
                background_color = :background_color, 
                group_title_color = :group_title_color, 
                group_desc_color = :group_desc_color, 
                group_full_size = :group_full_size, 
                custom_css = :custom_css, 
                order_num = :order_num, 
                visibility_start = :visibility_start, 
                visibility_end = :visibility_end, 
                banner_duration = :banner_duration, 
                banner_full_size = :banner_full_size
            WHERE 
                id = :id";
        return $this->db->update(
            $query, [
                'id' => $id,
                'group_name' => $group_name,
                'group_title' => $group_title,
                'group_desc' => $group_desc,
                'layout_id'=> $layout_id,
                'group_kind' => $group_kind,
                'group_view' => $group_view,
                'columns' => $columns,
                'content_alignment' => $content_alignment,
                'style_class' => $style_class,
                'background_color' => $background_color,
                'group_title_color' => $group_title_color,
                'group_desc_color' => $group_desc_color,
                'group_full_size' => $full_size,
                'custom_css' => $custom_css,
                'order_num' => $order_num,
                'visibility_start' => $visibility_start,
                'visibility_end' => $visibility_end,
                'banner_duration' => $banner_duration,
                'banner_full_size' => $banner_full_size
            ]
        );
    }

    public function deleteGroup($id) {
        $query = "DELETE FROM banner_groups WHERE id = :id";
        return $this->db->delete($query, ['id' => $id]);
    }

    public function beginTransaction($funcName=""){
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName=""){
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }
}

// BannerStyleModel.php

class AdminBannerStyleModel {
    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllStyles() {
        $query = "SELECT * FROM banner_styles";
        return $this->db->select($query);
    }

    public function getStyleById($id) {
        $query = "SELECT * FROM banner_styles WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function addStyle($banner_height_size, $background_color, $banner_content_box_color, $title_color, $title_size, $content_color, $content_size, $show_button, $button_title, $button_location, $button_background, $button_color, $button_hover_background, $button_hover_color,$button_size) {
        $query = "INSERT INTO banner_styles (
                   banner_height_size,
                   background_color, 
                   content_box_bg_color,
                   title_color, 
                   title_size, 
                   content_color, 
                   content_size, 
                   show_button,
                   button_title, 
                   button_location, 
                   button_background, 
                   button_color, 
                   button_hover_background,
                   button_hover_color,
                   button_size) VALUES (:banner_height_size, :background_color, :content_box_bg_color, :title_color, :title_size, :content_color, :content_size, :show_button, :button_title, :button_location, :button_background, :button_color, :button_hover_background, :button_hover_color, :button_size)";
        $params = [
            'banner_height_size' => $banner_height_size,
            'background_color' => $background_color,
            'content_box_bg_color' => $banner_content_box_color,
            'title_color' => $title_color,
            'title_size' => $title_size,
            'content_color' => $content_color,
            'content_size' => $content_size,
            'show_button' => $show_button,
            'button_title' => $button_title,
            'button_location' => $button_location,
            'button_background' => $button_background,
            'button_color' => $button_color,
            'button_hover_background' => $button_hover_background,
            'button_hover_color' => $button_hover_color,
            'button_size' => $button_size
        ];
        Log::adminWrite("AdminBannerStyleModel->addStyle params: " . json_encode($params), "info", "cron-copier");
        $result = $this->db->insert($query, $params);
        if ($result === false) {
            Log::adminWrite("AdminBannerStyleModel->addStyle insert failed.", "error", "cron-copier");
        }
        return $result;
    }

    public function updateStyle($id, $banner_height_size, $background_color, $banner_content_box_color, $title_color, $title_size, $content_color, $content_size, $button_title, $button_location, $button_background, $button_color, $button_hover_background, $button_hover_color,$button_size) {
        $query = "
            UPDATE banner_styles SET 
                 banner_height_size = :banner_height_size,
                 background_color = :background_color, 
                 content_box_bg_color = :content_box_bg_color,
                 title_color = :title_color, 
                 title_size = :title_size, 
                 content_color = :content_color, 
                 content_size = :content_size, 
                 button_title = :button_title,
                 button_location = :button_location,
                 button_background = :button_background, 
                 button_color = :button_color, 
                 button_hover_background = :button_hover_background,
                 button_hover_color = :button_hover_color,
                 button_size = :button_size
            WHERE id = :id";
        return $this->db->update($query, ['id' => $id,
            'banner_height_size' => $banner_height_size,
            'background_color' => $background_color,
            'content_box_bg_color' => $banner_content_box_color,
            'title_color' => $title_color,
            'title_size' => $title_size,
            'content_color' => $content_color,
            'content_size' => $content_size,
            'button_title' => $button_title,
            'button_location' => $button_location,
            'button_background' => $button_background,
            'button_color' => $button_color,
            'button_hover_background' => $button_hover_background,
            'button_hover_color' => $button_hover_color,
            'button_size' => $button_size]);
    }

    public function deleteStyle($id) {
        $query = "DELETE FROM banner_styles WHERE id = :id";
        return $this->db->delete($query, ['id' => $id]);
    }

    public function beginTransaction($funcName=""){
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName=""){
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }
}

// BannerModel.php

class AdminBannerModel {
    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBanners() {
        $query = "SELECT * FROM banners";
        return $this->db->select($query);
    }

    public function getBannerById($id) {
        $query = "SELECT * FROM banners WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function addBanner($group_id, $style_id, $title, $content, $image, $link, $active) {
        $query = "INSERT INTO banners (group_id, style_id, title, content, image, link, active) VALUES (:group_id, :style_id, :title, :content, :image, :link, :active)";
        return $this->db->insert($query, ['group_id' => $group_id, 'style_id' => $style_id, 'title' => $title, 'content' => $content, 'image' => $image, 'link' => $link, 'active' => $active]);
    }

    public function updateBanner($id, $group_id, $title, $content, $image, $link, $style_id, $active) {
        $query = "UPDATE banners SET group_id = :group_id, title = :title, content = :content, image = :image, link = :link, style_id = :style_id, active = :active WHERE id = :id";
        return $this->db->update($query, ['id' => $id, 'group_id' => $group_id, 'title' => $title, 'content' => $content, 'image' => $image, 'link' => $link, 'style_id' => $style_id, 'active' => $active]);
    }

    public function deleteBanner($id) {
        $query = "DELETE FROM banners WHERE id = :id";
        return $this->db->delete($query, ['id' => $id]);
    }

    public function deleteBannersByGroupID($groupID)
    {
        $query = "DELETE FROM banners WHERE group_id = :group_id";
        return $this->db->delete($query, ['group_id' => $groupID]);

    }

    public function getBannersByGroupID($bannerGroupID)
    {
        $query = "SELECT * FROM banners WHERE group_id = :group_id";
        return $this->db->select($query, ['group_id' => $bannerGroupID]);
    }

    public function beginTransaction($funcName=""){
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName=""){
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }
}

/**
 * banner_display_rules
 * id
 * group_id
 * page_id
 * category_id
 * language_code
 * created_at
 * updated_at
 */
class AdminBannerDisplayRulesModel {
    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllDisplayRules() {
        $query = "SELECT * FROM banner_display_rules";
        return $this->db->select($query);
    }

    public function getDisplayRuleById($id) {
        $query = "SELECT * FROM banner_display_rules WHERE id = :id";
        return $this->db->select($query, ['id' => $id]);
    }

    public function getDisplayRuleByLanguageId($languageCode,$bannerTypeID) {
        $query = "SELECT * FROM banner_display_rules WHERE language_code = :languageCode and type_id = :bannerTypeID Group by group_id";
        return $this->db->select($query, ['languageCode' => $languageCode, 'bannerTypeID' => $bannerTypeID]);
    }

    public function getDisplayRuleByLanguageCode($languageCode) {
        $query = "SELECT * FROM banner_display_rules WHERE language_code = :languageCode";
        return $this->db->select($query, ['languageCode' => $languageCode]);
    }

    public function getDisplayRuleByGroupId($group_id) {
        $query = "SELECT * FROM banner_display_rules WHERE group_id = :group_id";
        return $this->db->select($query, ['group_id' => $group_id]);
    }

    public function addDisplayRule($group_id, $bannerTypeID, $page_id, $category_id, $language_code) {
        $query = "INSERT INTO banner_display_rules (group_id, type_id, page_id, category_id, language_code) VALUES (:group_id, :type_id, :page_id, :category_id, :language_code)";
        return $this->db->insert($query, ['group_id' => $group_id, "type_id" => $bannerTypeID, 'page_id' => $page_id, 'category_id' => $category_id, 'language_code' => $language_code]);
    }

    public function updateDisplayRule($id, $group_id, $page_id, $category_id, $language_code) {
        $query = "UPDATE banner_display_rules SET group_id = :group_id, page_id = :page_id, category_id = :category_id, language_code = :language_code WHERE id = :id";
        return $this->db->update($query, ['id' => $id, 'group_id' => $group_id, 'page_id' => $page_id, 'category_id' => $category_id, 'language_code' => $language_code]);
    }

    public function deleteDisplayRule($id) {
        $query = "DELETE FROM banner_display_rules WHERE id = :id";
        return $this->db->delete($query, ['id' => $id]);
    }

    public function deleteDisplayRuleByGroupID($groupID){
        $query = "DELETE FROM banner_display_rules WHERE group_id = :group_id";
        return $this->db->delete($query, ['group_id' => $groupID]);
    }

    public function beginTransaction($funcName=""){
        $this->db->beginTransaction($funcName);
    }

    public function commit($funcName=""){
        $this->db->commit($funcName);
    }

    public function rollback($funcName="")
    {
        $this->db->rollback($funcName);
    }
}

class AdminBannerCreateModel {
    private AdminDatabase $db;

    public function __construct($db) {
        $this->db = $db;
        $this->createTablesIfNotExist();
    }

    private function createTablesIfNotExist() {
        $queries = [
            "CREATE TABLE IF NOT EXISTS banner_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type_name VARCHAR(50) NOT NULL,
                description TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS banner_layouts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                layout_group VARCHAR(20) NOT NULL DEFAULT 'text_and_image',
                layout_view VARCHAR(20) NOT NULL DEFAULT 'single',
                type_id INT NOT NULL,
                layout_name VARCHAR(100) NOT NULL,
                description TEXT DEFAULT NULL,
                columns INT DEFAULT 1,
                max_banners INT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_banner_layouts_type
                    FOREIGN KEY (type_id) REFERENCES banner_types(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS banner_groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_name VARCHAR(100) NOT NULL,
                layout_id INT DEFAULT NULL,
                columns INT NOT NULL,
                content_alignment ENUM('horizontal', 'vertical') DEFAULT 'horizontal',
                custom_css TEXT DEFAULT NULL,
                order_num INT DEFAULT NULL,
                visibility_start DATETIME DEFAULT NULL,
                visibility_end DATETIME DEFAULT NULL,
                bannerDuration INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_banner_groups_layout FOREIGN KEY (layout_id)
                    REFERENCES banner_layouts(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE                        
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS banner_styles (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                  `banner_height_size` int(11) NOT NULL DEFAULT 0,
                  `background_color` varchar(20) DEFAULT NULL,
                  `content_box_bg_color` varchar(20) DEFAULT NULL,
                  `title_color` varchar(20) DEFAULT NULL,
                  `title_size` int(11) DEFAULT NULL,
                  `content_color` varchar(20) DEFAULT NULL,
                  `content_size` int(11) DEFAULT NULL,
                  `show_button` TINYINT(1) NOT NULL DEFAULT '1',
                  `button_title` varchar(50) DEFAULT NULL,
                  `button_location` int(11) DEFAULT NULL,
                  `button_background` varchar(20) DEFAULT NULL,
                  `button_color` varchar(20) DEFAULT NULL,
                  `button_hover_background` varchar(20) DEFAULT NULL,
                  `button_hover_color` varchar(20) DEFAULT NULL,
                  `button_size` int(11) DEFAULT NULL,
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS banners (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                style_id INT DEFAULT NULL,
                title VARCHAR(255) DEFAULT NULL,
                content TEXT DEFAULT NULL,
                image VARCHAR(255) DEFAULT NULL,
                link VARCHAR(255) DEFAULT NULL,
                active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_banners_group FOREIGN KEY (group_id)
                    REFERENCES banner_groups(id)
                    ON DELETE CASCADE,
                CONSTRAINT fk_banners_style FOREIGN KEY (style_id)
                    REFERENCES banner_styles(id)
                    ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",

            "CREATE TABLE IF NOT EXISTS banner_display_rules (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_id INT NOT NULL,
                type_id INT NOT NULL,
                page_id INT DEFAULT NULL,
                category_id INT DEFAULT NULL,
                language_code VARCHAR(10) DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_banner_display_rules_group FOREIGN KEY (group_id)
                    REFERENCES banner_groups(id)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        ];

        foreach ($queries as $query) {
            $this->db->createTable($query);
        }

        // Veri ekleme işlemleri
        $this->insertInitialData();
    }

    private function insertInitialData() {
        // banner_types tablosuna veri eklemeden önce kontrol et
        $checkTypes = $this->db->select("SELECT COUNT(*) as count FROM banner_types");
        if ($checkTypes[0]['count'] == 0) {
            $this->db->insert("INSERT INTO banner_types (type_name, description) VALUES
            ('Slider', 'Sayfa üstünde dönen görseller içeren alan'),
            ('Tepe Banner', 'Sayfanın en üst alanında gösterilen banner'),
            ('Orta Banner', 'Sayfanın orta kısmında yer alan banner'),
            ('Alt Banner', 'Sayfanın alt kısmında gösterilen banner'),
            ('Karşılama Banner (Popup)', 'Popup olarak çıkan karşılama banner'),
            ('Carousel Slider', 'Dönerek değişen birden fazla görsel içerir'),
            ('Başlık Banner', 'Sayfa veya kategori başlığı altındaki banner')",[]);
        }

        // banner_layouts tablosuna veri eklemeden önce kontrol et
        $checkLayouts = $this->db->select("SELECT COUNT(*) as count FROM banner_layouts");
        if ($checkLayouts[0]['count'] == 0) {
            $this->db->insert("INSERT INTO banner_layouts (`id`, `layout_group`, `layout_view`, `type_id`, `layout_name`, `description`, `columns`, `max_banners`, `created_at`, `updated_at`)
                VALUES
                    (1, 'text','single',2, 'Sadece Yazılar', 'Sadece metin içeren tepe banner', 1, 1, '2025-04-06 08:15:26', '2025-04-12 13:13:08'),
                    (2, 'image','single',2, 'Sadece Görsel', 'Sadece görsel içeren tepe banner', 1, 1, '2025-04-06 08:15:26', '2025-04-12 13:13:17'),
                    (3, 'text_and_image','single',2, 'Yazılar ve Görsel', 'Metin ve görsel bir arada, tekli tepe banner', 1, 1, '2025-04-06 08:15:26', '2025-04-12 13:13:27'),
                    (4, 'text_and_image','single',3, 'Yazılar ve Görsel Tekli', 'Tek satırda metin + görsel', 1, 10, '2025-04-06 08:15:26', '2025-04-12 13:16:17'),
                    (5, 'text_and_image','double',3, 'Yazılar ve Görsel İkili', 'Tek satırda 2 li, metin + görsel', 2, 10, '2025-04-06 08:15:26', '2025-04-12 13:25:38'),
                    (6, 'text_and_image','triple',3, 'Yazılar ve Görsel Üçlü', 'Tek satırda 3 lü, metin + görsel', 3, 10, '2025-04-06 08:15:26', '2025-04-12 13:25:41'),
                    (7, 'text_and_image','quad',3, 'Yazılar ve Görsel Dörtlü', 'Tek satırda 4 lü, metin + görsel', 4, 10, '2025-04-06 08:15:26', '2025-04-12 13:25:43'),
                    (8, 'image','single',3, 'Sadece Görsel Tekli', 'Tek Satırda 1 resim', 1, 10, '2025-04-06 08:15:26', '2025-04-12 13:25:16'),
                    (9, 'image','double',3, 'Sadece Görsel İkili', 'Tek satırda 2 resim', 2, 10, '2025-04-06 08:15:26', '2025-04-12 13:25:11'),
                    (10, 'image','triple',3, 'Sadece Görsel Üçlü', 'Tek Satırda 3 resim', 3, 10, '2025-04-06 08:15:26', '2025-04-12 13:25:07'),
                    (11, 'image','quad',3, 'Sadece Görsel Dörtlü', 'tek satırda 4 resim', 4, 10, '2025-04-12 13:24:03', '2025-04-12 13:24:37'),
                    (12, 'image','quinary',3, 'Sadece Görsel Beşli', 'Tek satırda 5 resim', 5, 10, '2025-04-12 13:24:03', '2025-04-12 13:25:04')",[]);
        }
    }
}
