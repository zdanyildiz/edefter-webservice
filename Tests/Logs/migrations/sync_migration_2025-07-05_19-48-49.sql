-- Veritabanı Senkronizasyon Migration
-- Tarih: 2025-07-05 19:48:49
-- Hedef DB: e-defter.globalpozitif.com.tr
-- Kaynak DB: johwears.globalpozitif.com.tr
-- Oluşturan: DatabaseSynchronizer

-- BACKUP KOMUTLARI
-- ================

CREATE TABLE IF NOT EXISTS `banner_groups_backup_20250705_194849` AS SELECT * FROM `banner_groups`;

CREATE TABLE IF NOT EXISTS `banner_layouts_backup_20250705_194849` AS SELECT * FROM `banner_layouts`;

CREATE TABLE IF NOT EXISTS `banner_styles_backup_20250705_194849` AS SELECT * FROM `banner_styles`;

-- MIGRATION KOMUTLARI
-- ===================

CREATE TABLE `language_copy_jobs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_language_id` int NOT NULL,
  `target_language_id` int NOT NULL,
  `translate_with_ai` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','processing','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `source_language_id` (`source_language_id`),
  KEY `target_language_id` (`target_language_id`),
  CONSTRAINT `language_copy_jobs_ibfk_1` FOREIGN KEY (`source_language_id`) REFERENCES `dil` (`dilid`),
  CONSTRAINT `language_copy_jobs_ibfk_2` FOREIGN KEY (`target_language_id`) REFERENCES `dil` (`dilid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `site_config_versions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `language_id` int NOT NULL,
  `version` int NOT NULL DEFAULT '1',
  `last_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_language_version` (`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

ALTER TABLE `banner_groups` ADD COLUMN `group_title` varchar(100) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `group_desc` varchar(255) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `group_kind` varchar(100) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `group_view` varchar(20) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `style_class` varchar(50) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `background_color` varchar(50) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `group_title_color` varchar(50) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `group_desc_color` varchar(50) NULL;

ALTER TABLE `banner_groups` ADD COLUMN `group_full_size` tinyint NULL DEFAULT '1';

ALTER TABLE `banner_groups` ADD COLUMN `banner_duration` int NULL;

ALTER TABLE `banner_groups` ADD COLUMN `banner_full_size` tinyint NULL;

ALTER TABLE `banner_layouts` ADD COLUMN `layout_group` varchar(50) NOT NULL DEFAULT 'text_and_image';

ALTER TABLE `banner_layouts` ADD COLUMN `layout_view` varchar(20) NOT NULL DEFAULT 'single';

ALTER TABLE `banner_styles` ADD COLUMN `banner_height_size` int NOT NULL DEFAULT '0';

ALTER TABLE `banner_styles` ADD COLUMN `content_box_bg_color` varchar(25) NULL;

ALTER TABLE `banner_styles` ADD COLUMN `show_button` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `banner_styles` ADD COLUMN `button_hover_background` varchar(25) NULL;

ALTER TABLE `banner_styles` ADD COLUMN `button_hover_color` varchar(25) NULL;

ALTER TABLE `banner_styles` MODIFY COLUMN `background_color` varchar(25) NULL;

ALTER TABLE `banner_styles` MODIFY COLUMN `title_color` varchar(25) NULL;

ALTER TABLE `banner_styles` MODIFY COLUMN `content_color` varchar(25) NULL;

ALTER TABLE `banner_styles` MODIFY COLUMN `button_background` varchar(25) NULL;

ALTER TABLE `banner_styles` MODIFY COLUMN `button_color` varchar(25) NULL;

