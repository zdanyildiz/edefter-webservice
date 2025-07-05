<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OrnekTablo extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // Yeni bir tablo oluştur
        $ornek = $this->table('ornek_tablo');
        $ornek->addColumn('baslik', 'string', ['limit' => 255])
              ->addColumn('aciklama', 'text', ['null' => true])
              ->addColumn('durum', 'boolean', ['default' => true])
              ->addColumn('sira', 'integer', ['default' => 0])
              ->addColumn('eklenme_tarihi', 'datetime')
              ->addColumn('guncelleme_tarihi', 'datetime', ['null' => true])
              ->addIndex(['baslik'], ['unique' => true])
              ->create();
    }
}
