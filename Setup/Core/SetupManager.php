<?php

namespace Setup\Core;

class SetupManager {
    protected $steps = [];
    protected $config = [];
    protected $logs = []; // log yerine logs kullan

    public function __construct(array $config) {
        $this->config = $config;
        $this->log('SetupManager başlatıldı.');
    }

    public function addStep(SetupStep $step) {
        $this->steps[] = $step;
    }

    public function runAll() {
        $results = [];
        foreach ($this->steps as $step) {
            $this->log("Çalıştırılıyor: " . $step->getName());
            $result = $step->run();
            $results[] = $result;

            if ($result['status'] === 'error') {
                $this->log("HATA: " . $step->getName() . " - " . $result['message']);
                $this->rollbackAll($step);
                return ['status' => 'error', 'message' => 'Kurulum başarısız oldu.', 'log' => $this->getLog()];
            }
            $this->log("Tamamlandı: " . $step->getName());
        }
        return ['status' => 'success', 'message' => 'Kurulum tamamlandı.', 'log' => $this->getLog()];
    }

    protected function rollbackAll(SetupStep $failedStep) {
        $this->log("Rollback başlatıldı...");
        $reversedSteps = array_reverse($this->steps);
        foreach ($reversedSteps as $step) {
            if ($step === $failedStep) {
                $this->log("Geri alınıyor: " . $step->getName());
                $step->rollback();
                break; // Sadece başarısız olan ve öncesindekiler geri alınır.
            }
            $this->log("Geri alınıyor: " . $step->getName());
            $step->rollback();
        }
    }

    public function getConfig($key, $default = null) {
        return $this->config[$key] ?? $default;
    }

    public function getStepData($key = null, $default = null) {
        // Eğer key belirtilmemişse tüm form_data'yı döndür
        if ($key === null) {
            return $this->config['form_data'] ?? [];
        }
        
        // Form data veya JSON config'den veri al
        $formData = $this->config['form_data'] ?? [];
        $jsonConfig = $this->config['json_config'] ?? [];
        
        // Önce form data'da ara
        if (isset($formData[$key])) {
            return $formData[$key];
        }
        
        // Sonra JSON config'de ara
        if (isset($jsonConfig[$key])) {
            return $jsonConfig[$key];
        }
        
        return $default;
    }

    public function log(string $message, string $level = 'INFO') {
        $this->logs[] = date('[Y-m-d H:i:s] ') . "[$level] " . $message;
        error_log("[$level] " . $message); // Error log'a da yaz
    }

    public function getLog(): array {
        return $this->logs;
    }

    public function getSteps(): array {
        return $this->steps;
    }
}
