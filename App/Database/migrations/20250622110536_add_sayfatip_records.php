<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSayfatipRecords extends AbstractMigration
{
    /**
     * Sayfatip tablosuna "Referanslar" ve "Online Randevu" kayıtlarını ekler
     * 
     * yetki = 1, gorunum = 1, sayfatipsil = 0 değerleri ile
     */
    public function change(): void
    {
        // Sayfatip tablosuna erişim
        $table = $this->table('sayfatip');
        
        // Eklenecek kayıtlar
        $data = [
            [
                'sayfatipad' => 'Referanslar',
                'yetki' => 1,
                'gorunum' => 1,
                'sayfatipsil' => 0
            ],
            [
                'sayfatipad' => 'Online Randevu',
                'yetki' => 1,
                'gorunum' => 1,
                'sayfatipsil' => 0
            ]
        ];
        
        // Kayıtları ekle
        $table->insert($data)->saveData();
    }
}
