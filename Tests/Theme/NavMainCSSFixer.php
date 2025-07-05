<?php
/**
 * Nav-main.css CSS Variable Usage Fixer
 * nav-main.css dosyasında CSS değişkenlerini kullanan satırları ve kullanmayanları tespit eder
 */

class NavMainCSSFixer {
    private $navMainPath = 'Public/CSS/Layouts/nav-main.css';
    
    public function analyze() {
        echo "🔧 NAV-MAIN.CSS CSS VARIABLE USAGE FIXER\n";
        echo "========================================\n\n";
        
        if (!file_exists($this->navMainPath)) {
            echo "❌ nav-main.css file not found!\n";
            return;
        }
        
        $content = file_get_contents($this->navMainPath);
        $lines = explode("\n", $content);
        
        echo "📄 File: nav-main.css\n";
        echo "Total lines: " . count($lines) . "\n\n";
        
        // CSS değişkeni kullanan satırları say
        $variableUsageCount = 0;
        $hardcodedValues = [];
        
        foreach ($lines as $lineNum => $line) {
            if (strpos($line, 'var(--') !== false) {
                $variableUsageCount++;
            }
            
            // Sabit kodlanmış renk değerlerini bul
            if (preg_match('/(background-color|color|border-color)\s*:\s*(#[a-fA-F0-9]{3,6}|rgb\([^)]+\)|rgba\([^)]+\))/', $line, $matches)) {
                $hardcodedValues[] = [
                    'line' => $lineNum + 1,
                    'property' => $matches[1],
                    'value' => $matches[2],
                    'full_line' => trim($line)
                ];
            }
        }
        
        echo "✅ Lines using CSS variables: $variableUsageCount\n";
        echo "❌ Hardcoded color values found: " . count($hardcodedValues) . "\n\n";
        
        if (count($hardcodedValues) > 0) {
            echo "🎨 HARDCODED VALUES TO FIX:\n";
            echo "===========================\n";
            foreach ($hardcodedValues as $item) {
                echo "Line {$item['line']}: {$item['property']}: {$item['value']}\n";
                echo "   {$item['full_line']}\n";
                echo "   Suggested: " . $this->getSuggestedVariable($item['property'], $item['value']) . "\n\n";
            }
        }
        
        // Eksik değişken kullanımlarını öner
        $this->suggestImprovements($content);
    }
    
    private function getSuggestedVariable($property, $value) {
        switch (strtolower($property)) {
            case 'background-color':
                if ($value === '#fff' || $value === '#ffffff') {
                    return 'var(--content-bg-color)';
                } elseif (strpos($value, 'rgba') !== false) {
                    return 'var(--mobile-overlay-color)';
                }
                return 'var(--menu-background-color)';
                
            case 'color':
                if ($value === '#333' || $value === '#333333') {
                    return 'var(--menu-text-color)';
                }
                return 'var(--menu-text-color)';
                
            case 'border-color':
                return 'var(--border-color)';
                
            default:
                return 'var(--' . str_replace('-', '-', $property) . ')';
        }
    }
    
    private function suggestImprovements($content) {
        echo "🚀 IMPROVEMENT SUGGESTIONS:\n";
        echo "===========================\n\n";
        
        $improvements = [
            // Sabit kodlanmış değerleri CSS değişkenleriyle değiştir
            ['from' => '#fff', 'to' => 'var(--content-bg-color)', 'description' => 'White background'],
            ['from' => '#ffffff', 'to' => 'var(--content-bg-color)', 'description' => 'White background'],
            ['from' => 'rgba(0,0,0,0.2)', 'to' => 'var(--box-shadow-sm)', 'description' => 'Box shadow'],
            ['from' => 'rgba(0,0,0,0.1)', 'to' => 'var(--box-shadow-sm)', 'description' => 'Box shadow'],
            ['from' => '0.3s ease', 'to' => 'var(--transition-speed) var(--transition-timing)', 'description' => 'Transition'],
            ['from' => '0.3s ease-in-out', 'to' => 'var(--transition-speed) var(--transition-timing)', 'description' => 'Transition'],
        ];
        
        foreach ($improvements as $improvement) {
            $count = substr_count($content, $improvement['from']);
            if ($count > 0) {
                echo "🔄 Replace '{$improvement['from']}' with '{$improvement['to']}' ({$improvement['description']}) - Found $count times\n";
            }
        }
        
        echo "\n📝 Missing variable implementations:\n";
        echo "- Font sizes should use var(--menu-font-size), var(--submenu-font-size)\n";
        echo "- Padding values should use var(--menu-padding), var(--mobile-menu-padding)\n";
        echo "- Heights should use var(--menu-height)\n";
        echo "- Colors should use the full set of menu variables\n";
    }
}

// Çalıştır
$fixer = new NavMainCSSFixer();
$fixer->analyze();
