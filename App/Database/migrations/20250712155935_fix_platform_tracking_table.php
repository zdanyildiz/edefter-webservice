<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class FixPlatformTrackingTable extends AbstractMigration
{
    public function change(): void
    {
        // Önce tablo varsa kaldır
        if ($this->hasTable('platform_tracking')) {
            $this->table('platform_tracking')->drop()->save();
        }

        // Tabloyu doğru şema ile yeniden oluştur
        $table = $this->table('platform_tracking', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('platform', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('language_id', 'integer', ['null' => false])
            ->addColumn('config', 'json', ['null' => false])
            ->addColumn('status', 'boolean', ['default' => true])
            ->addTimestamps()
            ->addIndex(['platform', 'language_id'], ['unique' => true])
            ->create();
    }
}