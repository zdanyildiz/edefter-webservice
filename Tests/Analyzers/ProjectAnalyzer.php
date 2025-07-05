<?php
/**
 * Project Analyzer - yeni.globalpozitif.com.tr
 * Bu dosya projenin tüm modüllerini sistematik olarak analiz eder
 * MCP (Model Context Protocol) format için prompt dosyaları oluşturur
 */

require_once '../App/Core/Config.php';

class ProjectAnalyzer {
    private $config;
    private $pdo;
    private $modules = [];
    private $analysisResults = [];
    
    public function __construct() {
        $this->config = new Config();
        $this->connectDatabase();
        echo "🚀 Project Analyzer başlatıldı\n";
        echo "📍 Domain: " . $_SERVER['HTTP_HOST'] ?? 'CLI Mode' . "\n";
        echo "📊 Veritabanı: " . $this->config->dbName . "\n";
        echo "-----------------------------------\n\n";
    }
    
    private function connectDatabase() {
        try {
            $dsn = "mysql:host={$this->config->dbServerName};dbname={$this->config->dbName};charset=utf8";
            $this->pdo = new PDO($dsn, $this->config->dbUsername, $this->config->dbPassword);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Veritabanı bağlantısı başarılı\n";
        } catch (PDOException $e) {
            echo "❌ Veritabanı bağlantı hatası: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    public function discoverModules() {
        echo "🔍 Modül keşfi başlatılıyor...\n\n";
        
        // Controller dosyalarını analiz et
        $controllerDir = '../App/Controller/';
        $controllers = glob($controllerDir . '*.php');
        
        foreach ($controllers as $controller) {
            $className = basename($controller, '.php');
            if ($className === 'RouterController') continue; // Router özel
            
            $moduleName = str_replace('Controller', '', $className);
            $this->modules[$moduleName] = [
                'controller' => $className,
                'model' => $this->findModel($moduleName),
                'view' => $this->findViews($moduleName),
                'status' => 'discovered'
            ];
        }
        
        echo "📋 Keşfedilen modüller:\n";
        foreach ($this->modules as $name => $module) {
            echo "  • {$name}: Controller({$module['controller']}) Model({$module['model']}) Views(" . count($module['view']) . ")\n";
        }
        echo "\n";
        
        return $this->modules;
    }
    
    private function findModel($moduleName) {
        $possibleModels = [
            "../App/Model/{$moduleName}.php",
            "../App/Model/{$moduleName}Model.php",
            "../App/Model/" . ucfirst($moduleName) . ".php"
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
            '../App/View/' . strtolower($moduleName) . '/',
            '../_y/s/s/' . strtolower($moduleName) . '/',
            '../_y/'
        ];
        
        $views = [];
        foreach ($viewDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '*.php');
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
            'dependencies' => $this->findDependencies($moduleName)
        ];
        
        $this->analysisResults[$moduleName] = $analysis;
        $this->generateModulePrompt($moduleName, $analysis);
        
        echo "✅ {$moduleName} modülü analizi tamamlandı\n\n";
        return $analysis;
    }
    
    private function findDatabaseTables($moduleName) {
        try {
            $sql = "SHOW TABLES";
            $stmt = $this->pdo->query($sql);
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
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
        } catch (PDOException $e) {
            echo "⚠️ Veritabanı tabloları sorgulanamadı: " . $e->getMessage() . "\n";
            return [];
        }
    }
    
    private function analyzeMethods($moduleName) {
        $controllerFile = "../App/Controller/{$this->modules[$moduleName]['controller']}.php";
        if (!file_exists($controllerFile)) {
            return [];
        }
        
        $content = file_get_contents($controllerFile);
        preg_match_all('/public function (\w+)\s*\([^)]*\)/', $content, $matches);
        
        return $matches[1] ?? [];
    }
    
    private function findDependencies($moduleName) {
        $controllerFile = "../App/Controller/{$this->modules[$moduleName]['controller']}.php";
        if (!file_exists($controllerFile)) {
            return [];
        }
        
        $content = file_get_contents($controllerFile);
        preg_match_all('/(?:require_once|include_once|new\s+)[\s\'\"]*([A-Z]\w+)/', $content, $matches);
        
        return array_unique($matches[1] ?? []);
    }
    
    private function generateModulePrompt($moduleName, $analysis) {
        $promptDir = "Tests/" . ucfirst($moduleName) . "s/";
        if (!is_dir($promptDir)) {
            mkdir($promptDir, 0755, true);
        }
        
        $promptFile = $promptDir . strtolower($moduleName) . "_prompt.md";
        
        $content = "# " . strtoupper($moduleName) . " MODULE PROMPT\n";
        $content .= "*MCP (Model Context Protocol) için " . ucfirst($moduleName) . " modülü rehberi*\n\n";
        
        $content .= "## 📋 MODÜL ÖZET\n";
        $content .= "- **Controller**: " . ($analysis['files']['controller'] ?? 'Yok') . "\n";
        $content .= "- **Model**: " . ($analysis['files']['model'] ?? 'Yok') . "\n";
        $content .= "- **Views**: " . count($analysis['files']['view']) . " dosya\n";
        $content .= "- **Database Tables**: " . implode(', ', $analysis['database_tables']) . "\n\n";
        
        if (!empty($analysis['methods'])) {
            $content .= "## 🔧 CONTROLLER METHODS\n";
            foreach ($analysis['methods'] as $method) {
                $content .= "- `{$method}()`\n";
            }
            $content .= "\n";
        }
        
        if (!empty($analysis['dependencies'])) {
            $content .= "## 🔗 DEPENDENCIES\n";
            foreach ($analysis['dependencies'] as $dep) {
                $content .= "- {$dep}\n";
            }
            $content .= "\n";
        }
        
        $content .= "## 📊 DATABASE ANALYSIS\n";
        foreach ($analysis['database_tables'] as $table) {
            $content .= $this->getTableStructure($table);
        }
        
        $content .= "\n---\n";
        $content .= "*Bu prompt dosyası otomatik olarak ProjectAnalyzer tarafından oluşturulmuştur.*\n";
        $content .= "*Son güncelleme: " . date('Y-m-d H:i:s') . "*\n";
        
        file_put_contents($promptFile, $content);
        echo "📝 Prompt dosyası oluşturuldu: {$promptFile}\n";
    }
    
    private function getTableStructure($tableName) {
        try {
            $sql = "DESCRIBE `{$tableName}`";
            $stmt = $this->pdo->query($sql);
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $structure = "### 📋 Tablo: `{$tableName}`\n";
            $structure .= "| Sütun | Tip | Null | Key | Default |\n";
            $structure .= "|-------|-----|------|-----|----------|\n";
            
            foreach ($columns as $column) {
                $structure .= "| `{$column['Field']}` | {$column['Type']} | {$column['Null']} | {$column['Key']} | " . ($column['Default'] ?? 'NULL') . " |\n";
            }
            
            $structure .= "\n";
            return $structure;
        } catch (PDOException $e) {
            return "⚠️ Tablo yapısı alınamadı: {$tableName}\n\n";
        }
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
            $summary .= "- View dosyaları: " . count($module['view']) . "\n";
            if (isset($this->analysisResults[$name])) {
                $summary .= "- Database tabloları: " . count($this->analysisResults[$name]['database_tables']) . "\n";
                $summary .= "- Methods: " . count($this->analysisResults[$name]['methods']) . "\n";
            }
            $summary .= "\n";
        }
        
        file_put_contents('Tests/PROJECT_ANALYSIS_SUMMARY.md', $summary);
        echo "✅ Proje özeti oluşturuldu: Tests/PROJECT_ANALYSIS_SUMMARY.md\n";
    }
}

// CLI kullanımı
if (php_sapi_name() === 'cli') {
    $analyzer = new ProjectAnalyzer();
    
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
                    echo "❌ Modül adı belirtilmedi. Kullanım: php ProjectAnalyzer.php analyze ModuleName\n";
                }
                break;
            case 'summary':
                $analyzer->discoverModules();
                $analyzer->generateProjectSummary();
                break;
            default:
                echo "❌ Geçersiz komut. Kullanılabilir komutlar: discover, analyze, summary\n";
        }
    } else {
        echo "📋 Project Analyzer Komutları:\n";
        echo "  discover - Modülleri keşfet\n";
        echo "  analyze <ModuleName> - Belirli modülü analiz et\n";
        echo "  summary - Proje özetini oluştur\n";
    }
}
