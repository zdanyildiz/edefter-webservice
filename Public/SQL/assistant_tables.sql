-- Kullanıcılar tablosu
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  company VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  subscription_plan VARCHAR(50) DEFAULT 'free',
  subscription_expires TIMESTAMP NULL,
  is_active TINYINT(1) DEFAULT 1
);

-- Web siteleri tablosu
CREATE TABLE IF NOT EXISTS assistant_websites (
  id VARCHAR(36) PRIMARY KEY,
  user_id INT NOT NULL,
  website_name VARCHAR(100) NOT NULL,
  website_url VARCHAR(255) NOT NULL,
  logo_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_active TINYINT(1) DEFAULT 1,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Asistan ayarları tablosu
CREATE TABLE IF NOT EXISTS assistant_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  website_id VARCHAR(36) NOT NULL,
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (website_id) REFERENCES assistant_websites(id) ON DELETE CASCADE,
  UNIQUE KEY unique_website_setting (website_id, setting_key)
);

-- Asistan konuşmaları tablosu
CREATE TABLE IF NOT EXISTS assistant_conversations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  website_id VARCHAR(36) NOT NULL,
  session_id VARCHAR(64) NOT NULL,
  user_message TEXT NOT NULL,
  assistant_response TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (website_id) REFERENCES assistant_websites(id) ON DELETE CASCADE,
  INDEX (session_id)
);

-- Fonksiyon çağrıları tablosu
CREATE TABLE IF NOT EXISTS assistant_function_calls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  website_id VARCHAR(36) NOT NULL,
  function_name VARCHAR(100) NOT NULL,
  parameters TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (website_id) REFERENCES assistant_websites(id) ON DELETE CASCADE
);

-- Müşteri adayları tablosu
CREATE TABLE IF NOT EXISTS assistant_leads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  website_id VARCHAR(36) NOT NULL,
  name VARCHAR(100) NOT NULL,
  surname VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  status VARCHAR(20) DEFAULT 'new',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (website_id) REFERENCES assistant_websites(id) ON DELETE CASCADE
);

-- Gemini API kullanım istatistikleri
CREATE TABLE IF NOT EXISTS assistant_api_usage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  website_id VARCHAR(36) NOT NULL,
  request_count INT DEFAULT 1,
  token_count INT DEFAULT 0,
  date DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (website_id) REFERENCES assistant_websites(id) ON DELETE CASCADE,
  UNIQUE KEY unique_website_date (website_id, date)
);

-- Geri bildirimler tablosu
CREATE TABLE IF NOT EXISTS assistant_feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  conversation_id INT NOT NULL,
  rating TINYINT(1) DEFAULT NULL,
  feedback_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversation_id) REFERENCES assistant_conversations(id) ON DELETE CASCADE
);

-- Fonksiyon tanımlamaları tablosu
CREATE TABLE IF NOT EXISTS assistant_functions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  function_name VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  parameters JSON NOT NULL,
  is_system TINYINT(1) DEFAULT 0 COMMENT 'Sistemde tanımlı mı yoksa kullanıcı tanımlı mı',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Web sitesi fonksiyon bağlantıları
CREATE TABLE IF NOT EXISTS assistant_website_functions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  website_id VARCHAR(36) NOT NULL,
  function_id INT NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  api_endpoint VARCHAR(255) DEFAULT NULL COMMENT 'Özel API endpoint URL',
  api_method ENUM('GET', 'POST', 'PUT', 'DELETE') DEFAULT 'GET',
  api_auth_type ENUM('none', 'basic', 'bearer', 'api_key', 'custom') DEFAULT 'none',
  api_auth_data JSON DEFAULT NULL COMMENT 'API kimlik doğrulama verileri',
  request_mapping JSON DEFAULT NULL COMMENT 'İstek parametrelerinin nasıl iletileceği',
  response_mapping JSON DEFAULT NULL COMMENT 'Yanıtın nasıl işleneceği',
  success_message VARCHAR(255) DEFAULT NULL,
  error_message VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (website_id) REFERENCES assistant_websites(id) ON DELETE CASCADE,
  FOREIGN KEY (function_id) REFERENCES assistant_functions(id) ON DELETE CASCADE,
  UNIQUE KEY unique_website_function (website_id, function_id)
);

-- Default fonksiyon değerleri
INSERT IGNORE INTO assistant_functions 
(function_name, description, parameters, is_system) VALUES
('getCompanyInfo', 'Şirket hakkında bilgi verir.', '{"type": "object", "properties": {}}', 1),
('getContactDetails', 'Şirketin iletişim bilgilerini döner.', '{"type": "object", "properties": {}}', 1),
('getFilters', 'Ürün araması için geçerli filtreleri döner.', '{"type": "object", "properties": {}}', 1),
('productSearch', 'Ürün araması gerçekleştirir.', '{"type": "object", "properties": {"searchKey": {"type": "string", "description": "Aranacak ürün anahtar kelimesi"}, "searchFilters": {"type": "object", "description": "Filtre kriterleri"}}, "required": ["searchKey"]}', 1),
('getProductDetails', 'Belirli bir ürünün detay bilgilerini döner.', '{"type": "object", "properties": {"productId": {"type": "string", "description": "İstenen ürünün kimliği"}}, "required": ["productId"]}', 1),
('getFAQ', 'Sıkça sorulan soruları döner.', '{"type": "object", "properties": {}}', 1),
('addUser', 'Sisteme yeni kullanıcı ekler.', '{"type": "object", "properties": {"name": {"type": "string", "description": "Kullanıcının adı"}, "surname": {"type": "string", "description": "Kullanıcının soyadı"}, "email": {"type": "string", "description": "Kullanıcının e-posta adresi"}, "phone": {"type": "string", "description": "Kullanıcının telefon numarası"}}, "required": ["name", "surname", "email", "phone"]}', 1);
