<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTrialUsersTable extends AbstractMigration
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
        $table = $this->table('trial_users');
        $table->addColumn('member_id', 'integer', [
                'comment' => 'Üye ID si'
            ])
            ->addColumn('trial_start_date', 'datetime', [
                'comment' => 'Deneme başlangıç tarihi'
            ])
            ->addColumn('trial_end_date', 'datetime', [
                'comment' => 'Deneme bitiş tarihi'
            ])
            ->addColumn('is_active', 'boolean', [
                'default' => true,
                'comment' => 'Deneme aktif mi?'
            ])
            ->addColumn('created_at', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Oluşturma tarihi'
            ])
            ->addColumn('updated_at', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'comment' => 'Güncelleme tarihi'
            ])
            ->addIndex(['member_id'], ['unique' => true])
            ->create();
    }
}
