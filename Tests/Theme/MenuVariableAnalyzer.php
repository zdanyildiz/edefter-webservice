<?php
/**
 * Menu Theme Variables Analyzer
 * Tema menü sistemindeki CSS değişkenlerini analiz eder
 */

// Config sistemini kullanmaya gerek yok, sadece CSS analizi yapıyoruz

class MenuVariableAnalyzer {
    private $cssFiles = [
        'index.css' => 'Public/CSS/index.css',
        'index-theme.css' => 'Public/CSS/index-theme.css',
        'nav-main.css' => 'Public/CSS/Layouts/nav-main.css'
    ];
    
    public function analyze() {
        echo "🎨 MENU THEME VARIABLES ANALYZER\n";
        echo "================================\n\n";
        
        $allVariables = [];
        $menuVariables = [];
        
        foreach ($this->cssFiles as $name => $path) {
            echo "📁 Analyzing: $name\n";
            echo "Path: $path\n";
            
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $variables = $this->extractCSSVariables($content);
                $menuVars = $this->filterMenuVariables($variables);
                
                echo "   Total variables: " . count($variables) . "\n";
                echo "   Menu variables: " . count($menuVars) . "\n";
                
                $allVariables[$name] = $variables;
                $menuVariables[$name] = $menuVars;
                
                if (count($menuVars) > 0) {
                    echo "   Menu variables found:\n";
                    foreach ($menuVars as $var) {
                        echo "     - $var\n";
                    }
                }
                echo "\n";
            } else {
                echo "   ❌ File not found!\n\n";
            }
        }
        
        $this->analyzeMenuVariableConsistency($menuVariables);
        $this->generateMissingVariables($menuVariables);
    }
    
    private function extractCSSVariables($content) {
        $variables = [];
        preg_match_all('/--([a-zA-Z0-9\-_]+)\s*:\s*([^;]+);/', $content, $matches);
        
        if (!empty($matches[1])) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $variables['--' . $matches[1][$i]] = trim($matches[2][$i]);
            }
        }
        
        return $variables;
    }
    
    private function filterMenuVariables($variables) {
        $menuKeywords = [
            'menu', 'nav', 'navigation', 'submenu', 'dropdown',
            'main-menu', 'mobile-menu', 'menu-item', 'nav-item'
        ];
        
        $menuVars = [];
        foreach ($variables as $varName => $value) {
            foreach ($menuKeywords as $keyword) {
                if (stripos($varName, $keyword) !== false) {
                    $menuVars[] = $varName;
                    break;
                }
            }
        }
        
        return $menuVars;
    }
    
    private function analyzeMenuVariableConsistency($menuVariables) {
        echo "🔍 MENU VARIABLE CONSISTENCY ANALYSIS\n";
        echo "====================================\n\n";
        
        $allMenuVars = [];
        foreach ($menuVariables as $file => $vars) {
            $allMenuVars = array_merge($allMenuVars, $vars);
        }
        $allMenuVars = array_unique($allMenuVars);
        
        echo "Total unique menu variables across all files: " . count($allMenuVars) . "\n\n";
        
        foreach ($menuVariables as $file => $vars) {
            echo "📄 $file:\n";
            foreach ($allMenuVars as $var) {
                $exists = in_array($var, $vars);
                echo "   " . ($exists ? "✅" : "❌") . " $var\n";
            }
            echo "\n";
        }
    }
    
    private function generateMissingVariables($menuVariables) {
        echo "🔧 RECOMMENDED MENU VARIABLES\n";
        echo "=============================\n\n";
        
        $recommendedVariables = [
            // Desktop Menu Variables
            '--main-menu-bg-color',
            '--main-menu-text-color',
            '--main-menu-hover-bg-color',
            '--main-menu-hover-text-color',
            '--main-menu-active-bg-color',
            '--main-menu-active-text-color',
            '--main-menu-font-size',
            '--main-menu-font-weight',
            '--main-menu-padding',
            '--main-menu-border-color',
            
            // Desktop Submenu Variables
            '--main-submenu-bg-color',
            '--main-submenu-text-color',
            '--main-submenu-hover-bg-color',
            '--main-submenu-hover-text-color',
            '--main-submenu-active-bg-color',
            '--main-submenu-active-text-color',
            '--main-submenu-font-size',
            '--main-submenu-font-weight',
            '--main-submenu-padding',
            '--main-submenu-border-color',
            
            // Mobile Menu Variables
            '--mobile-menu-bg-color',
            '--mobile-menu-text-color',
            '--mobile-menu-hover-bg-color',
            '--mobile-menu-hover-text-color',
            '--mobile-menu-active-bg-color',
            '--mobile-menu-active-text-color',
            '--mobile-menu-font-size',
            '--mobile-menu-font-weight',
            '--mobile-menu-padding',
            '--mobile-menu-border-color',
            
            // Mobile Submenu Variables
            '--mobile-submenu-bg-color',
            '--mobile-submenu-text-color',
            '--mobile-submenu-hover-bg-color',
            '--mobile-submenu-hover-text-color',
            '--mobile-submenu-active-bg-color',
            '--mobile-submenu-active-text-color',
            '--mobile-submenu-font-size',
            '--mobile-submenu-font-weight',
            '--mobile-submenu-padding',
            '--mobile-submenu-border-color'
        ];
        
        echo "Total recommended variables: " . count($recommendedVariables) . "\n\n";
        
        $allExisting = [];
        foreach ($menuVariables as $vars) {
            $allExisting = array_merge($allExisting, $vars);
        }
        $allExisting = array_unique($allExisting);
        
        $missing = array_diff($recommendedVariables, $allExisting);
        
        if (count($missing) > 0) {
            echo "❌ Missing variables (" . count($missing) . "):\n";
            foreach ($missing as $var) {
                echo "   - $var\n";
            }
            echo "\n";
            
            echo "📝 Suggested CSS additions for index.css:\n";
            echo "```css\n";
            echo "    /* ========= Desktop Menu Variables ========= */\n";
            foreach ($missing as $var) {
                if (strpos($var, 'mobile') === false) {
                    $defaultValue = $this->getDefaultValue($var);
                    echo "    $var: $defaultValue;\n";
                }
            }
            echo "\n    /* ========= Mobile Menu Variables ========= */\n";
            foreach ($missing as $var) {
                if (strpos($var, 'mobile') !== false) {
                    $defaultValue = $this->getDefaultValue($var);
                    echo "    $var: $defaultValue;\n";
                }
            }
            echo "```\n\n";
        } else {
            echo "✅ All recommended variables are present!\n\n";
        }
    }
    
    private function getDefaultValue($varName) {
        if (strpos($varName, 'bg-color') !== false) {
            return 'var(--content-bg-color)';
        } elseif (strpos($varName, 'text-color') !== false || strpos($varName, 'color') !== false) {
            return 'var(--body-text-color)';
        } elseif (strpos($varName, 'hover') !== false && strpos($varName, 'color') !== false) {
            return 'var(--primary-color)';
        } elseif (strpos($varName, 'font-size') !== false) {
            return 'var(--font-size-normal)';
        } elseif (strpos($varName, 'font-weight') !== false) {
            return 'var(--font-weight-regular)';
        } elseif (strpos($varName, 'padding') !== false) {
            return 'var(--spacing-sm) var(--spacing-md)';
        } elseif (strpos($varName, 'border') !== false) {
            return 'var(--border-color)';
        }
        return '#ffffff';
    }
}

// Çalıştır
$analyzer = new MenuVariableAnalyzer();
$analyzer->analyze();
