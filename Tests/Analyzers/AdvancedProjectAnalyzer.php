<?php
/**
 * Advanced Project Analyzer - yeni.globalpozitif.com.tr
 * Veritabanı bağlantılı, MCP prompt oluşturucu analiz aracı
 */

require_once 'SimpleDatabaseConnector.php';

class AdvancedProjectAnalyzer {
    private $db;
    private $modules = [];
    private $analysisResults = [];
    private $projectRoot;
    
    public function __construct() {
        $this->projectRoot = dirname(__DIR__);
        $this->db = new SimpleDatabaseConnector();
        echo "🚀 Advanced Project Analyzer başlatıldı\n";
        echo "📍 Proje kök: " . $this->projectRoot . "\n";
    }
    
    public function discoverModules() {
        echo "🔍 Modül keşfi başlatılıyor...\n\n";
        
        // Controller dosyalarını analiz et
        $controllerDir = $this->projectRoot . '/App/Controller/';
        $controllers = glob($controllerDir . '*.php');
        
        foreach ($controllers as $controller) {
            $className = basename($controller, '.php');
            if ($className === 'RouterController') continue; // Router özel
            
            $moduleName = str_replace('Controller', '', $className);
            $this->modules[$moduleName] = [
                'controller' => $className,
                'model' => $this->findModel($moduleName),
                'views' => $this->findViews($moduleName),
                'status' => 'discovered'
            ];
        }
        
        echo "📋 Keşfedilen modüller:\n";
        foreach ($this->modules as $name => $module) {
            echo "  • {$name}: Controller({$module['controller']}) Model({$module['model']}) Views(" . count($module['views']) . ")\n";
        }
        echo "\n";
        
        return $this->modules;
    }
    
    private function findModel($moduleName) {
        $possibleModels = [
            $this->projectRoot . "/App/Model/{$moduleName}.php",
            $this->projectRoot . "/App/Model/{$moduleName}Model.php",
            $this->projectRoot . "/App/Model/" . ucfirst($moduleName) . ".php"
        ];
        
        foreach ($possibleModels as $model) {
            if (file_exists($model)) {
                return basename($model, '.php');
            }
        }
        return null;
    }
    
    private function findViews($moduleName) {
        $viewDirs = [
            $this->projectRoot . '/App/View/' . strtolower($moduleName) . '/',
            $this->projectRoot . '/_y/s/s/' . strtolower($moduleName) . '/',
            $this->projectRoot . '/_y/'
        ];
        
        $views = [];
        foreach ($viewDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '*.php');
                $files = array_merge($files, glob($dir . '*.html'));
                $views = array_merge($views, $files);
            }
        }
        
        return $views;
    }
    
    public function analyzeModule($moduleName) {
        if (!isset($this->modules[$moduleName])) {
            echo "❌ Modül bulunamadı: {$moduleName}\n";
            return false;
        }
        
        echo "🔍 Modül analizi: {$moduleName}\n";
        echo "================================\n";
        
        $module = $this->modules[$moduleName];
        $analysis = [
            'name' => $moduleName,
            'files' => $module,
            'database_tables' => $this->findDatabaseTables($moduleName),
            'methods' => $this->analyzeMethods($moduleName),
            'dependencies' => $this->findDependencies($moduleName),
            'css_files' => $this->findCSSFiles($moduleName),
            'js_files' => $this->findJSFiles($moduleName)
        ];
        
        $this->analysisResults[$moduleName] = $analysis;
        $this->generateModulePrompt($moduleName, $analysis);
        
        echo "✅ {$moduleName} modülü analizi tamamlandı\n\n";
        return $analysis;
    }
    
    private function findDatabaseTables($moduleName) {
        $tables = $this->db->listTables();
        
        // Modül adına göre ilgili tabloları bul
        $relatedTables = [];
        $searchTerms = [
            strtolower($moduleName),
            strtolower($moduleName) . 's',
            strtolower($moduleName) . '_'
        ];
        
        foreach ($tables as $table) {
            foreach ($searchTerms as $term) {
                if (strpos(strtolower($table), $term) !== false) {
                    $relatedTables[] = $table;
                    break;
                }
            }
        }
        
        return $relatedTables;
    }
    
    private function analyzeMethods($moduleName) {
        $controllerFile = $this->projectRoot . "/App/Controller/{$this->modules[$moduleName]['controller']}.php";
        if (!file_exists($controllerFile)) {
            return [];
        }
        
        $content = file_get_contents($controllerFile);
        preg_match_all('/public function (\w+)\s*\([^)]*\)/', $content, $matches);
        
        return $matches[1] ?? [];
    }
    
    private function findDependencies($moduleName) {
        $controllerFile = $this->projectRoot . "/App/Controller/{$this->modules[$moduleName]['controller']}.php";
        if (!file_exists($controllerFile)) {
            return [];
        }
        
        $content = file_get_contents($controllerFile);
        preg_match_all('/(?:require_once|include_once|new\s+)[\s\'\"]*([A-Z]\w+)/', $content, $matches);
        
        return array_unique($matches[1] ?? []);
    }
    
    private function findCSSFiles($moduleName) {
        $cssDir = $this->projectRoot . '/Public/CSS/' . ucfirst($moduleName) . 's/';
        $files = [];
        if (is_dir($cssDir)) {
            $files = glob($cssDir . '*.css');
        }
        return $files;
    }
    
    private function findJSFiles($moduleName) {
        $jsDir = $this->projectRoot . '/Public/JS/' . ucfirst($moduleName) . 's/';
        $files = [];
        if (is_dir($jsDir)) {
            $files = glob($jsDir . '*.js');
        }
        return $files;
    }
    
    private function generateModulePrompt($moduleName, $analysis) {
        $promptDir = $this->projectRoot . "/Tests/" . ucfirst($moduleName) . "s/";
        if (!is_dir($promptDir)) {
            mkdir($promptDir, 0755, true);
        }
        
        $promptFile = $promptDir . strtolower($moduleName) . "_system_prompt.md";
        
        $content = "# " . strtoupper($moduleName) . " SYSTEM PROMPT\n";
        $content .= "*MCP (Model Context Protocol) için " . ucfirst($moduleName) . " sistemi rehberi*\n\n";
        
        $content .= "## 📋 SİSTEM ÖZETİ\n";
        $content .= "- **Controller**: " . ($analysis['files']['controller'] ?? 'Yok') . "\n";
        $content .= "- **Model**: " . ($analysis['files']['model'] ?? 'Yok') . "\n";
        $content .= "- **Views**: " . count($analysis['files']['views']) . " dosya\n";
        $content .= "- **Database Tables**: " . count($analysis['database_tables']) . " tablo\n";
        $content .= "- **CSS Files**: " . count($analysis['css_files']) . " dosya\n";
        $content .= "- **JS Files**: " . count($analysis['js_files']) . " dosya\n\n";
        
        if (!empty($analysis['methods'])) {
            $content .= "## 🔧 CONTROLLER METHODS\n";
            foreach ($analysis['methods'] as $method) {
                $content .= "- `{$method}()`\n";
            }
            $content .= "\n";
        }
        
        if (!empty($analysis['dependencies'])) {
            $content .= "## 🔗 BAĞIMLILIKLAR\n";
            foreach ($analysis['dependencies'] as $dep) {
                $content .= "- {$dep}\n";
            }
            $content .= "\n";
        }
        
        $content .= "## 📊 VERİTABANI ANALIZI\n";
        foreach ($analysis['database_tables'] as $table) {
            $content .= $this->getTableStructureMarkdown($table);
        }
        
        if (!empty($analysis['css_files'])) {
            $content .= "## 🎨 CSS DOSYALARI\n";
            foreach ($analysis['css_files'] as $cssFile) {
                $content .= "- " . basename($cssFile) . "\n";
            }
            $content .= "\n";
        }
        
        if (!empty($analysis['js_files'])) {
            $content .= "## ⚡ JAVASCRIPT DOSYALARI\n";
            foreach ($analysis['js_files'] as $jsFile) {
                $content .= "- " . basename($jsFile) . "\n";
            }
            $content .= "\n";
        }
        
        $content .= "## 📁 DOSYA YERLEŞİMLERİ\n";
        $content .= "```\n";
        $content .= "App/Controller/" . ($analysis['files']['controller'] ?? 'Yok') . ".php\n";
        if ($analysis['files']['model']) {
            $content .= "App/Model/" . $analysis['files']['model'] . ".php\n";
        }
        foreach ($analysis['files']['views'] as $view) {
            $content .= str_replace($this->projectRoot, '', $view) . "\n";
        }
        $content .= "```\n\n";
        
        $content .= "---\n";
        $content .= "*Bu prompt dosyası otomatik olarak AdvancedProjectAnalyzer tarafından oluşturulmuştur.*\n";
        $content .= "*Son güncelleme: " . date('Y-m-d H:i:s') . "*\n";
        
        file_put_contents($promptFile, $content);
        echo "📝 Sistem prompt dosyası oluşturuldu: " . str_replace($this->projectRoot, '', $promptFile) . "\n";
    }
    
    private function getTableStructureMarkdown($tableName) {
        $columns = $this->db->getTableStructure($tableName);
        if (empty($columns)) {
            return "### ⚠️ Tablo: `{$tableName}` - Yapı alınamadı\n\n";
        }
        
        $structure = "### 📋 Tablo: `{$tableName}`\n";
        $structure .= "| Sütun | Tip | Null | Key | Default |\n";
        $structure .= "|-------|-----|------|-----|----------|\n";
        
        foreach ($columns as $column) {
            $structure .= "| `{$column['Field']}` | {$column['Type']} | {$column['Null']} | {$column['Key']} | " . ($column['Default'] ?? 'NULL') . " |\n";
        }
        
        $structure .= "\n";
        return $structure;
    }
    
    public function generateProjectSummary() {
        echo "📊 Proje özeti oluşturuluyor...\n";
        
        $summary = "# PROJECT ANALYSIS SUMMARY\n";
        $summary .= "*" . date('Y-m-d H:i:s') . " tarihinde oluşturuldu*\n\n";
        
        $summary .= "## 📋 MODÜL İSTATİSTİKLERİ\n";
        $summary .= "Toplam modül sayısı: " . count($this->modules) . "\n\n";
        
        foreach ($this->modules as $name => $module) {
            $summary .= "### {$name}\n";
            $summary .= "- Controller: " . ($module['controller'] ?? 'Yok') . "\n";
            $summary .= "- Model: " . ($module['model'] ?? 'Yok') . "\n";
            $summary .= "- View dosyaları: " . count($module['views']) . "\n";
            if (isset($this->analysisResults[$name])) {
                $summary .= "- Database tabloları: " . count($this->analysisResults[$name]['database_tables']) . "\n";
                $summary .= "- Methods: " . count($this->analysisResults[$name]['methods']) . "\n";
                $summary .= "- CSS dosyaları: " . count($this->analysisResults[$name]['css_files']) . "\n";
                $summary .= "- JS dosyaları: " . count($this->analysisResults[$name]['js_files']) . "\n";
            }
            $summary .= "\n";
        }
        
        $summary .= "## 📊 VERİTABANI ÖZETİ\n";
        $tables = $this->db->listTables();
        $summary .= "Toplam tablo sayısı: " . count($tables) . "\n\n";
        
        // Önemli tabloları kategorize et
        $categories = [
            'Banner' => ['banner', 'banners'],
            'Ürün' => ['urun', 'product'],
            'Üye' => ['uye', 'member'],
            'Sipariş' => ['siparis', 'order'],
            'Kategori' => ['kategori', 'category'],
            'Sayfa' => ['sayfa', 'page'],
            'Ayar' => ['ayar', 'config', 'settings']
        ];
        
        foreach ($categories as $category => $keywords) {
            $categoryTables = [];
            foreach ($tables as $table) {
                foreach ($keywords as $keyword) {
                    if (strpos(strtolower($table), $keyword) !== false) {
                        $categoryTables[] = $table;
                        break;
                    }
                }
            }
            if (!empty($categoryTables)) {
                $summary .= "### {$category} Tabloları (" . count($categoryTables) . " adet)\n";
                foreach ($categoryTables as $table) {
                    $summary .= "- {$table}\n";
                }
                $summary .= "\n";
            }
        }
        
        file_put_contents($this->projectRoot . '/Tests/PROJECT_ANALYSIS_SUMMARY.md', $summary);
        echo "✅ Proje özeti oluşturuldu: Tests/PROJECT_ANALYSIS_SUMMARY.md\n";
    }

    public function updateProjectPrompt() {
        echo "📝 PROJECT_PROMPT.md güncelleniyor...\n";
        
        $promptFile = $this->projectRoot . '/Tests/PROJECT_PROMPT.md';
        $existingContent = file_exists($promptFile) ? file_get_contents($promptFile) : '';
        
        // Analiz sonuçlarını ekle
        $newSection = "\n\n## 🔍 MODÜL ANALİZ SONUÇLARI\n";
        $newSection .= "*Son analiz: " . date('Y-m-d H:i:s') . "*\n\n";
        
        foreach ($this->analysisResults as $moduleName => $analysis) {
            $newSection .= "### {$moduleName} Modülü\n";
            $newSection .= "- **Controller**: {$analysis['files']['controller']}\n";
            $newSection .= "- **Model**: " . ($analysis['files']['model'] ?? 'Yok') . "\n";
            $newSection .= "- **Database**: " . implode(', ', $analysis['database_tables']) . "\n";
            $newSection .= "- **Methods**: " . implode(', ', array_slice($analysis['methods'], 0, 5)) . (count($analysis['methods']) > 5 ? '...' : '') . "\n";
            $newSection .= "- **Prompt Dosyası**: `Tests/" . ucfirst($moduleName) . "s/" . strtolower($moduleName) . "_system_prompt.md`\n\n";
        }
        
        // Mevcut içeriğe ekle
        if (strpos($existingContent, '## 🔍 MODÜL ANALİZ SONUÇLARI') !== false) {
            // Varolan bölümü güncelle
            $pattern = '/## 🔍 MODÜL ANALİZ SONUÇLARI.*?(?=##|\Z)/s';
            $updatedContent = preg_replace($pattern, trim($newSection), $existingContent);
        } else {
            // Yeni bölüm ekle
            $updatedContent = $existingContent . $newSection;
        }
        
        file_put_contents($promptFile, $updatedContent);
        echo "✅ PROJECT_PROMPT.md güncellendi\n";
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $analyzer = new AdvancedProjectAnalyzer();
    
    if ($argc > 1) {
        $command = $argv[1];
        switch ($command) {
            case 'discover':
                $analyzer->discoverModules();
                break;
            case 'analyze':
                if (isset($argv[2])) {
                    $analyzer->discoverModules();
                    $analyzer->analyzeModule($argv[2]);
                } else {
                    echo "❌ Modül adı belirtilmedi. Kullanım: php AdvancedProjectAnalyzer.php analyze ModuleName\n";
                }
                break;
            case 'analyze-all':
                $analyzer->discoverModules();
                foreach ($analyzer->modules as $moduleName => $module) {
                    $analyzer->analyzeModule($moduleName);
                }
                $analyzer->updateProjectPrompt();
                break;
            case 'summary':
                $analyzer->discoverModules();
                $analyzer->generateProjectSummary();
                break;
            default:
                echo "❌ Geçersiz komut. Kullanılabilir komutlar:\n";
                echo "  discover - Modülleri keşfet\n";
                echo "  analyze <ModuleName> - Belirli modülü analiz et\n";
                echo "  analyze-all - Tüm modülleri analiz et\n";
                echo "  summary - Proje özetini oluştur\n";
        }
    } else {
        echo "📋 Advanced Project Analyzer Komutları:\n";
        echo "  discover - Modülleri keşfet\n";
        echo "  analyze <ModuleName> - Belirli modülü analiz et\n";
        echo "  analyze-all - Tüm modülleri analiz et\n";
        echo "  summary - Proje özetini oluştur\n";
    }
}
