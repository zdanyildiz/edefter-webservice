#!/usr/bin/env php
<?php
/**
 * JavaScript Parantez Denge Kontrolü
 */

$file = 'Theme.php';
$content = file_get_contents($file);

// JavaScript bloğunu bul
preg_match('/<script>\s*(.*?)\s*<\/script>/s', $content, $matches);

if (!isset($matches[1])) {
    echo "❌ JavaScript bloğu bulunamadı!\n";
    exit(1);
}

$js = $matches[1];
$lines = explode("\n", $js);

$braceBalance = 0;
$parenBalance = 0;
$bracketBalance = 0;
$errors = [];

foreach ($lines as $lineNum => $line) {
    $realLineNum = $lineNum + 1;
    
    // Yorumları ve stringleri ignore et (basit yaklaşım)
    $cleanLine = preg_replace('/\/\/.*$/', '', $line);
    $cleanLine = preg_replace('/\/\*.*?\*\//', '', $cleanLine);
    
    // Parantezleri say
    for ($i = 0; $i < strlen($cleanLine); $i++) {
        $char = $cleanLine[$i];
        
        switch ($char) {
            case '{':
                $braceBalance++;
                break;
            case '}':
                $braceBalance--;
                if ($braceBalance < 0) {
                    $errors[] = "⚠️  Satır $realLineNum: Fazladan kapatma parantezi '}'";
                }
                break;
            case '(':
                $parenBalance++;
                break;
            case ')':
                $parenBalance--;
                if ($parenBalance < 0) {
                    $errors[] = "⚠️  Satır $realLineNum: Fazladan kapatma parantezi ')'";
                }
                break;
            case '[':
                $bracketBalance++;
                break;
            case ']':
                $bracketBalance--;
                if ($bracketBalance < 0) {
                    $errors[] = "⚠️  Satır $realLineNum: Fazladan kapatma parantezi ']'";
                }
                break;
        }
    }
}

echo "📊 JavaScript Parantez Denge Raporu:\n";
echo "=====================================\n";
echo "🔗 Süslü parantez denge: $braceBalance " . ($braceBalance === 0 ? "✅" : "❌") . "\n";
echo "🔗 Yuvarlak parantez denge: $parenBalance " . ($parenBalance === 0 ? "✅" : "❌") . "\n";
echo "🔗 Köşeli parantez denge: $bracketBalance " . ($bracketBalance === 0 ? "✅" : "❌") . "\n";

if (!empty($errors)) {
    echo "\n❌ Hatalar:\n";
    foreach ($errors as $error) {
        echo "$error\n";
    }
} else {
    echo "\n✅ Parantez hatası bulunamadı.\n";
}

if ($braceBalance !== 0 || $parenBalance !== 0 || $bracketBalance !== 0) {
    echo "\n🔧 Öneriler:\n";
    if ($braceBalance > 0) {
        echo "- $braceBalance adet '{}' kapatma parantezi eksik\n";
    }
    if ($parenBalance > 0) {
        echo "- $parenBalance adet '()' kapatma parantezi eksik\n";
    }
    if ($bracketBalance > 0) {
        echo "- $bracketBalance adet '[]' kapatma parantezi eksik\n";
    }
}
?>
