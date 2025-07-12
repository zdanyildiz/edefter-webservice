<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePlatformTrackingTable extends AbstractMigration
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
        $table = $this->table('platform_tracking', ['id' => 'tracking_id']);
        $table
            ->addColumn('platform', 'string', ['limit' => 50, 'comment' => 'Platform adı (google_analytics, facebook_pixel, etc.)'])
            ->addColumn('language_id', 'integer', ['default' => 1, 'comment' => 'Dil ID'])
            ->addColumn('config', 'text', ['comment' => 'Platform yapılandırması (JSON)'])
            ->addColumn('status', 'boolean', ['default' => true, 'comment' => 'Aktif/Pasif'])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['platform', 'language_id'], ['unique' => true])
            ->addIndex(['status'])
            ->create();
    }
}
