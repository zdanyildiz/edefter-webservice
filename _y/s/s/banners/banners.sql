--
-- Tablo için tablo yapısı `banner`
--
CREATE TABLE IF NOT EXISTS banner_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL,
    description TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

INSERT INTO banner_types (type_name, description) VALUES
  ('Slider', 'Sayfa üstünde dönen görseller içeren alan'),
  ('Tepe Banner', 'Sayfanın en üst alanında gösterilen banner'),
  ('Orta Banner', 'Sayfanın orta kısmında yer alan banner'),
  ('Alt Banner', 'Sayfanın alt kısmında gösterilen banner'),
  ('Karşılama Banner (Popup)', 'Popup olarak çıkan karşılama banner'),
  ('Carousel Slider', 'Dönerek değişen birden fazla görsel içerir'),
  ('Başlık Banner', 'Sayfa veya kategori başlığı altındaki banner');

CREATE TABLE IF NOT EXISTS banner_layouts (
      id INT AUTO_INCREMENT PRIMARY KEY,
      layout_group VARCHAR(50) NOT NULL DEFAULT 'text_and_image',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `banner_layouts` VALUES
 (1,'fullwidth','multi',1,'Klasik Slayt','Klasik Slayt',1,10,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (2,'carousel','multi',1,'Carousel Slayt','Kutu Slayt',1,20,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (3,'top-banner','single',2,'Arkaplan Resim ve Yazı Ortalı','Tek satırda ortalanmış içerik ',1,1,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (4,'Carousel','multi',3,'Carousel Slayt','Kutu Slayt',1,10,'2025-04-06 08:15:26','2025-06-08 10:55:35'),
 (5,'ImageRightBanner','multi',3,'Resim Sağda, Yazı Solda','Resim Sağda, Yazı Solda görünür',2,10,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (6,'ImageLeftBanner','multi',3,'Resim Solda, Yazı Sağda','Resim Solda, Yazı Sağda görünür',3,10,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (7,'HoverCardBanner','multi',3,'Kart Üzerine Gelince İçerik','Sadece Resim görünür. Yazılar resmin üzerindeyken görünür olur.',4,10,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (8,'ProfileCard','multi',3,'Profil Kartı Görünümü','Resimler yuvarlanmış görünür, altında metinler görünür',1,10,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (9,'IconFeatureCard','multi',3,'Icon Özellik Kartı','Resim ikon boyutlarında görünür, genelde bilgilendirme için kullanılır.',3,5,'2025-04-06 08:15:26','2025-06-10 12:19:35'),
 (10,'FadeFeatureCard','multi',3,'Fade Özellik Kartı','Resim ve Başlık Görünür. Kartın üzerine gelince resim kaybolur.',3,10,'2025-04-06 08:15:26','2025-06-03 12:56:52'),
 (11,'BgImageCenterText','multi',3,'Arkaplan Resimli Ortalanmış İçerik','Resmin üzerine metinler ortalı gelir.',4,10,'2025-04-12 13:24:03','2025-06-03 12:56:52'),
 (12,'ImageTextOverlayBottom','multi',3,'Resim Üzeri Alt Bant Metin','Metinler resmin üzerinde alt bölümünde görünür.',5,10,'2025-04-12 13:24:03','2025-06-05 06:56:06'),
 (13,'bottom-banner','single',4,'Arkaplan Resim ve Yazı Ortalı','Tek satırda ortalanmış içerik ',2,1,'2025-04-29 10:41:02','2025-06-03 12:56:52'),
 (14,'carousel','multi',4,'Carousel Slayt','Kutu Slayt',3,20,'2025-04-29 10:41:02','2025-06-03 12:56:52'),
 (15,'ImageRightBanner','single',4,'Resim Sağda, Yazı Solda','Resim Sağda, Yazı Solda görünür',1,1,'2025-04-29 10:41:02','2025-06-03 12:56:52'),
 (16,'ImageLeftBanner','single',4,'Resim Solda, Yazı Sağda','Resim Solda, Yazı Sağda görünür',1,1,'2025-04-29 13:41:02','2025-06-03 12:56:52'),
 (17,'fullwidth','multi',4,'Klasik Slayt','Klasik Slayt',1,10,'2025-04-29 13:41:02','2025-06-03 12:56:52'),
 (18,'popup-banner','single',5,'Üstte resim altta metinler','Sayfanın ortasında her şeyin önünde sayfa ilk açıldığında görünür.',1,1,'2025-04-29 13:41:02','2025-06-03 12:56:52'),
 (19,'ImageRightBanner','single',5,'Resim Sağda, Yazı Solda','Resim Sağda, Yazı Solda görünür',1,1,'2025-06-01 14:59:54','2025-06-03 12:56:52'),
 (20,'ImageLeftBanner','single',5,'Resim Solda, Yazı Sağda','Resim Solda, Yazı Sağda görünür',1,1,'2025-06-01 15:00:15','2025-06-03 12:56:52'),
 (21,'header-banner','single',6,'Başlık arkası banner','Sayfa ve kategori sayfa başlıkları arkasına resim',1,1,'2025-06-01 15:02:14','2025-06-03 14:38:41');

CREATE TABLE IF NOT EXISTS banner_groups (
     id INT AUTO_INCREMENT PRIMARY KEY,
     group_name VARCHAR(100) NOT NULL,
     group_title VARCHAR(100) DEFAULT NULL,
     group_desc VARCHAR(255) DEFAULT NULL,
     layout_id INT DEFAULT NULL,
     group_kind VARCHAR(100) DEFAULT NULL,
     group_view VARCHAR(20) DEFAULT NULL,
     columns INT NOT NULL,
     content_alignment ENUM('horizontal', 'vertical') DEFAULT 'horizontal',
     style_class VARCHAR(50) DEFAULT NULL,
     background_color VARCHAR(50) DEFAULT NULL,
     group_title_color VARCHAR(50) DEFAULT NULL,
     group_desc_color VARCHAR(50) DEFAULT NULL,
     group_full_size TINYINT DEFAULT 1,
     custom_css TEXT DEFAULT NULL,
     order_num INT DEFAULT NULL,
     visibility_start DATETIME DEFAULT NULL,
     visibility_end DATETIME DEFAULT NULL,
     banner_duration INT DEFAULT NULL,
     banner_full_size TINYINT DEFAULT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     CONSTRAINT fk_banner_groups_layout FOREIGN KEY (layout_id)
         REFERENCES banner_layouts(id)
         ON DELETE CASCADE
         ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;;

CREATE TABLE IF NOT EXISTS banner_styles (
     `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
     `banner_height_size` int(11) NOT NULL DEFAULT 0,
     `background_color` varchar(25) DEFAULT NULL,
     `content_box_bg_color` varchar(25) DEFAULT NULL,
     `title_color` varchar(25) DEFAULT NULL,
     `title_size` int(11) DEFAULT NULL,
     `content_color` varchar(25) DEFAULT NULL,
     `content_size` int(11) DEFAULT NULL,
     `show_button` TINYINT(1) NOT NULL DEFAULT '1',
     `button_title` varchar(50) DEFAULT NULL,
     `button_location` int(11) DEFAULT NULL,
     `button_background` varchar(25) DEFAULT NULL,
     `button_color` varchar(25) DEFAULT NULL,
     `button_hover_background` varchar(25) DEFAULT NULL,
     `button_hover_color` varchar(25) DEFAULT NULL,
     `button_size` int(11) DEFAULT NULL,
     `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
     `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS banners (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS banner_display_rules (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;