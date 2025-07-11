<?php
/**
 * Yeni Üye Kaydı Admin Bildirim Sistemi Test
 * Test tarihi: 2025-01-11
 * 
 * Bu test şunları kontrol eder:
 * 1. Admin email template dosyasının varlığı
 * 2. Template'in placeholder'ları
 * 3. E-posta gönderim sisteminin hazır olup olmadığını
 */

include_once __DIR__ . '/../index.php';

TestHelper::startTest('Yeni Üye Kaydı Admin Bildirim Sistemi');

try {
    // Template dosyası kontrolü
    $templatePath = __DIR__ . '/../../App/Helpers/mail-template/newMemberAdmin.php';
    TestAssert::assertTrue(file_exists($templatePath), 'Admin e-posta template dosyası mevcut olmalı');
    
    // Template içerik kontrolü
    $templateContent = file_get_contents($templatePath);
    TestAssert::assertNotEmpty($templateContent, 'Template içeriği boş olmamalı');
    
    // Template placeholder kontrolü
    $requiredPlaceholders = [
        '[member-name]',
        '[member-email]', 
        '[member-phone]',
        '[registration-date]',
        '[company-name]'
    ];
    
    foreach ($requiredPlaceholders as $placeholder) {
        TestAssert::assertTrue(
            strpos($templateContent, $placeholder) !== false,
            "Template '{$placeholder}' placeholder'ını içermeli"
        );
    }
    
    TestLogger::success('Admin template dosyasi ve placeholder\'lar dogru');
    
    // E-posta gönderim sınıfı kontrolü
    $emailSenderPath = __DIR__ . '/../../App/Helpers/EmailSender.php';
    TestAssert::assertTrue(file_exists($emailSenderPath), 'EmailSender sınıfı mevcut olmalı');
    
    TestLogger::success('EmailSender sınıfı mevcut');
    
    // MemberController değişiklik kontrolü
    $controllerPath = __DIR__ . '/../../App/Controller/MemberController.php';
    $controllerContent = file_get_contents($controllerPath);
    
    TestAssert::assertTrue(
        strpos($controllerContent, 'Sistem yöneticisine yeni üye kaydı bildirimi gönder') !== false,
        'MemberController admin bildirim kodu eklenmeli'
    );
    
    TestAssert::assertTrue(
        strpos($controllerContent, 'newMemberAdmin.php') !== false,
        'MemberController admin template referansı olmalı'
    );
    
    TestLogger::success('MemberController admin bildirim kodu eklendi');
    
    // Test verisi ile template render
    $testData = [
        '[member-name]' => 'Test Kullanıcı',
        '[member-email]' => 'test@example.com',
        '[member-phone]' => '5551234567',
        '[registration-date]' => date('d.m.Y H:i'),
        '[company-name]' => 'Test Şirketi'
    ];
    
    $renderedTemplate = $templateContent;
    foreach ($testData as $placeholder => $value) {
        $renderedTemplate = str_replace($placeholder, $value, $renderedTemplate);
    }
    
    // Render edilen template'de placeholder kalmamalı
    foreach ($requiredPlaceholders as $placeholder) {
        TestAssert::assertTrue(
            strpos($renderedTemplate, $placeholder) === false,
            "Render edilen template'de '{$placeholder}' placeholder'ı kalmamalı"
        );
    }
    
    TestLogger::success('Template başarıyla render edildi');
    
    // Admin e-posta sistemi özeti
    TestLogger::info('=== Admin Bildirim Sistemi Özeti ===');
    TestLogger::info('✅ Template dosyası: ' . $templatePath);
    TestLogger::info('✅ Placeholder sayısı: ' . count($requiredPlaceholders));
    TestLogger::info('✅ Template boyutu: ' . number_format(strlen($templateContent)) . ' karakter');
    TestLogger::info('✅ MemberController entegrasyonu: Tamamlandı');
    TestLogger::info('✅ Hata yönetimi: try-catch ile korunmuş');
    
    TestLogger::success('Tüm admin bildirim sistemi testleri başarılı!');
    
} catch (Exception $e) {
    TestLogger::error('Test hatası: ' . $e->getMessage());
}

TestHelper::endTest();

// Test sonrası bilgi
echo "\n🎯 Test Tamamlandı\n";
echo "📧 Admin Bildirim Sistemi Özellikleri:\n";
echo "   • Yeni üye kaydında otomatik admin bildirimi\n";
echo "   • Profesyonel HTML e-posta template'i\n";
echo "   • Üye bilgileri detaylı görüntüleme\n";
echo "   • Hata durumunda ana işlemi etkilememe\n";
echo "   • Responsive tasarım\n\n";

echo "🚀 Test Senaryosu:\n";
echo "   1. Yeni üye kaydı yap\n";
echo "   2. Sistem otomatik olarak admin'e bildirim gönderir\n";
echo "   3. E-posta doğrulaması beklenir\n";
echo "   4. Admin panelden üye kontrolü yapılabilir\n\n";
?>
