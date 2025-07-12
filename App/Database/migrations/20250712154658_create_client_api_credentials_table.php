<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateClientApiCredentialsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('client_api_credentials', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('client_id', 'integer', ['null' => false])
            ->addColumn('google_refresh_token', 'text', ['null' => false])
            ->addColumn('google_account_email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('last_sync_date', 'datetime', ['null' => true])
            ->addTimestamps()
            ->addIndex(['client_id'], ['unique' => true])
            ->create();
    }
}