<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAnalyticsDailySummaryTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('analytics_daily_summary', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('client_id', 'integer', ['null' => false])
            ->addColumn('summary_date', 'date', ['null' => false])
            ->addColumn('sessions', 'integer', ['null' => true])
            ->addColumn('users', 'integer', ['null' => true])
            ->addColumn('new_users', 'integer', ['null' => true])
            ->addColumn('total_ad_cost', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
            ->addColumn('total_ad_conversions', 'integer', ['null' => true])
            ->addTimestamps()
            ->addIndex(['client_id', 'summary_date'], ['unique' => true])
            ->create();
    }
}