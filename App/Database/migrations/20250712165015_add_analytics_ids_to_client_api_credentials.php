<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAnalyticsIdsToClientApiCredentials extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('client_api_credentials');
        $table->addColumn('ga_property_id', 'string', ['limit' => 255, 'null' => true, 'after' => 'google_account_email'])
              ->addColumn('ads_customer_id', 'string', ['limit' => 255, 'null' => true, 'after' => 'ga_property_id'])
              ->update();
    }
}