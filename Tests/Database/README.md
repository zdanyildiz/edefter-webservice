# Phinx Configuration Files

Bu klasörde Phinx migration sistemi için kullanılan çeşitli konfigürasyon dosyaları bulunmaktadır.

## Dosya Açıklamaları:

### 📁 Phinx Config Dosyaları:
- **`phinx-backup.php`** - Karmaşık Config sınıfı entegrasyonlu eski versiyon (yedek)
- **`phinx-complex.php`** - Config.php ve Helper.php entegrasyonlu versiyon (ana dizinden taşındı)
- **`phinx-config.php`** - Alternatif konfigürasyon dosyası
- **`phinx-new.php`** - GetLocalDatabaseInfo.php kullanan basit versiyon

### 🔧 Kullanım:
Herhangi bir Phinx config dosyasını kullanmak için:
```powershell
vendor\bin\phinx migrate -c Tests\Database\phinx-backup.php
vendor\bin\phinx status -c Tests\Database\phinx-complex.php
```

### ✅ Aktif Config:
Ana dizindeki `phinx.php` dosyası şu anda `phinx-simple.php` içeriğini kullanıyor.

### 📝 Notlar:
- Ana dizin temizliği için bu dosyalar buraya taşındı (2025-06-22)
- Gelecekte farklı ortamlar için bu config dosyaları kullanılabilir
- Tüm dosyalar çalışır durumda, sadece farklı yaklaşımlar kullanıyor
