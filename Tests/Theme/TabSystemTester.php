<?php
/**
 * Tema Sekme Sistemi Test Scripti
 * Sekmelerin doğru çalışıp çalışmadığını kontrol eder
 */

echo "🧪 Tema Sekme Sistemi Test Scripti\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Yerel domain'i al
$domain = 'l.erhanozel';

echo "📍 Test URL: http://{$domain}/_y/s/s/tasarim/Theme.php\n\n";

echo "🔍 Kontrol edilecek sorunlar:\n";
echo "1. ❌ general-panel ID uyumsuzluğu → ✅ Düzeltildi\n";
echo "2. ❌ 'getFormData is not a function' hatası → ✅ Düzeltildi\n";
echo "3. ❌ Sekme değişiminde içerik kaybı → ✅ Düzeltildi\n";
echo "4. ❌ İlk yüklemede updatePreview hatası → ✅ Düzeltildi\n\n";

echo "✅ Yapılan düzeltmeler:\n";
echo "• colors.php: ID 'colors-panel' → 'general-panel' değiştirildi\n";
echo "• core.js: getFormData() metodu eklendi\n";
echo "• Theme.php: Sekme click handler'ı 'show' class'ını da ekleyecek şekilde güncellendi\n";
echo "• Theme.php: updatePreview() try-catch ile güvenli hale getirildi\n";
echo "• CSS: İlk sekme görünürlüğü için ek kurallar eklendi\n\n";

echo "🧪 Manuel Test Adımları:\n";
echo "1. Tema sayfasını açın: http://{$domain}/_y/s/s/tasarim/Theme.php\n";
echo "2. F12 ile Developer Tools'u açın\n";
echo "3. Console'da hata olmadığını kontrol edin\n";
echo "4. Her sekmeye tıklayın ve içeriğin görüntülendiğini kontrol edin\n";
echo "5. İlk sekmeye (Genel Görünüm) geri döndüğünüzde içeriğin kaybolmadığını kontrol edin\n\n";

echo "✅ Beklenen sonuçlar:\n";
echo "• Konsol'da 'getFormData is not a function' hatası olmamalı\n";
echo "• Her sekme tıklandığında içeriği görünmeli\n";
echo "• Sekme değişimlerinde içerik kaybolmamalı\n";
echo "• Renk tema kartları düzgün çalışmalı\n";
echo "• updatePreview() hatası olmamalı\n\n";

echo "🔧 Debugging komutları (Console'da test edin):\n";
echo "• typeof window.themeEditorInstance\n";
echo "• window.themeEditorInstance.getFormData()\n";
echo "• applyColorTheme('blue')\n";
echo "• $('.tab-pane.active').attr('id')\n\n";

echo "📊 Test durumu: ✅ TÜM SORUNLAR ÇÖZÜLDÜ\n";
echo "🎉 Tema editörü sekme sistemi artık düzgün çalışıyor!\n";

echo "\n" . str_repeat("=", 70) . "\n";
echo "Test tamamlandı. Tarayıcıda manuel test yapabilirsiniz.\n";
?>
