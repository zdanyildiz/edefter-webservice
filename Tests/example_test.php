<?php
/**
 * Örnek Test Dosyası
 * 
 * Bu dosya, test framework'ünün nasıl kullanılacağını gösterir.
 * Tüm test dosyaları bu dosyayı referans alarak oluşturulmalıdır.
 * 
 * @author GitHub Copilot
 * @date 2025-06-24
 */

// Test framework'ünü yükle
include_once __DIR__ . '/index.php';

// Test başlat
TestHelper::startTest('Örnek Test Çalıştırması');

try {
    // Database bağlantısını test et
    TestHelper::info('Database bağlantısı test ediliyor...');
    $db = TestDatabase::getInstance();
    $connection = $db->getConnection();
    TestAssert::assertTrue($connection !== null, 'Database bağlantısı başarılı olmalı');
    TestHelper::success('Database bağlantısı başarılı');
    
    // Test verileri oluştur
    TestHelper::info('Test verileri oluşturuluyor...');
    $testData = TestDataGenerator::generateUserData();
    TestAssert::assertNotEmpty($testData['name'], 'Test kullanıcı adı boş olmamalı');
    TestAssert::assertNotEmpty($testData['email'], 'Test email boş olmamalı');
    TestHelper::success('Test verileri oluşturuldu');
    
    // Email validasyonu test et
    TestHelper::info('Email validasyonu test ediliyor...');
    $validEmail = 'test@example.com';
    $invalidEmail = 'invalid-email';
    
    TestAssert::assertTrue(
        TestValidator::validateEmail($validEmail), 
        'Geçerli email formatı kabul edilmeli'
    );
    
    TestAssert::assertFalse(
        TestValidator::validateEmail($invalidEmail), 
        'Geçersiz email formatı reddedilmeli'
    );
    TestHelper::success('Email validasyonu tamamlandı');
    
    // Telefon validasyonu test et
    TestHelper::info('Telefon validasyonu test ediliyor...');
    $validPhone = '05551234567';
    $invalidPhone = '123';
    
    TestAssert::assertTrue(
        TestValidator::validatePhone($validPhone), 
        'Geçerli telefon formatı kabul edilmeli'
    );
    
    TestAssert::assertFalse(
        TestValidator::validatePhone($invalidPhone), 
        'Geçersiz telefon formatı reddedilmeli'
    );
    TestHelper::success('Telefon validasyonu tamamlandı');
    
    // Parola güvenlik seviyesi test et
    TestHelper::info('Parola güvenlik seviyesi test ediliyor...');
    $strongPassword = 'StrongP@ss123';
    $weakPassword = '123';
    
    $strongLevel = TestValidator::getPasswordStrength($strongPassword);
    $weakLevel = TestValidator::getPasswordStrength($weakPassword);
    
    TestAssert::assertTrue(
        $strongLevel >= 3, 
        'Güçlü parola en az 3 seviye güvenlik skoru almalı'
    );
    
    TestAssert::assertTrue(
        $weakLevel < 3, 
        'Zayıf parola 3 seviyeden düşük güvenlik skoru almalı'
    );
    TestHelper::success('Parola güvenlik seviyesi tamamlandı');
    
    // Test tamamlandı
    TestHelper::success('Tüm testler başarıyla tamamlandı!');
    
} catch (Exception $e) {
    TestHelper::error('Test hatası: ' . $e->getMessage());
    TestLogger::error('Örnek Test Hatası', [
        'hata' => $e->getMessage(),
        'dosya' => $e->getFile(),
        'satir' => $e->getLine()
    ]);
} finally {
    // Test sonlandır
    TestHelper::endTest();
}
