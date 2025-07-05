<?php
/**
 * PowerShell PHP Inline Komut Sorunlarını Gösterir
 * Bu script, PowerShell'de neden ayrı dosya kullanmamız gerektiğini açıklar
 */

echo "=== PowerShell PHP Inline Test ===\n";
echo "Bu script PowerShell'de çalışacak şekilde yazılmıştır.\n\n";

// Problematik karakterler ve durumlar
$problematicChars = [
    '$variable' => 'PowerShell\'de $ özel karakter',
    '"string"' => 'Çift tırnak içinde tırnak problemi',
    'array["key"]' => 'Köşeli parantez syntax çakışması',
    'command; other' => 'Noktalı virgül komut ayırıcı çakışması'
];

echo "PowerShell'de problematik olan PHP karakterleri:\n";
foreach($problematicChars as $char => $problem) {
    echo "- {$char}: {$problem}\n";
}

echo "\n=== Çözüm ===\n";
echo "Bu script gibi ayrı .php dosyası oluşturup çalıştırmak:\n";
echo "php Tests\\System\\PowerShellTestExample.php\n\n";

// Örnek veritabanı sorgusu (güvenli)
echo "Örnek güvenli sorgu:\n";
echo "include_once 'Tests/System/GetLocalDatabaseInfo.php';\n";
echo "\$dbInfo = getLocalDatabaseInfo();\n";
echo "echo 'DB: ' . \$dbInfo['database'];\n";

echo "\n=== Test Tamamlandı ===\n";
?>
