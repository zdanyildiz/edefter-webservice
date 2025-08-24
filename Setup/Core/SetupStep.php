<?php

namespace Setup\Core;

abstract class SetupStep {
    protected $manager;

    public function __construct(SetupManager $manager) {
        $this->manager = $manager;
    }

    /**
     * Adımın adını döndürür.
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Adımın açıklamasını döndürür.
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Kurulum adımını çalıştırır.
     * @return array ['status' => 'success'|'error', 'message' => '...']
     */
    abstract public function run(): array;

    /**
     * Hata durumunda adımı geri alır.
     * @return array ['status' => 'success'|'error', 'message' => '...']
     */
    abstract public function rollback(): array;
}
