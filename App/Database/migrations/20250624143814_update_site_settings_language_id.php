<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateSiteSettingsLanguageId extends AbstractMigration
{
    /**
     * site_settings tablosunda ID 13 olan kaydın language_id değerini 0'dan 1'e günceller
     * 
     * Bu migration ID 13 olan site ayarının dil ID'sini düzeltir.
     * Rollback durumunda değer tekrar 0 yapılır.
     */
    public function up(): void
    {
        // site_settings tablosunda ID 13 olan kaydın language_id değerini 1 yap
        $this->execute("UPDATE site_settings SET language_id = 1 WHERE id = 13");
    }
    
    /**
     * Rollback işlemi - language_id değerini tekrar 0 yapar
     */
    public function down(): void
    {
        // Rollback durumunda language_id değerini tekrar 0 yap
        $this->execute("UPDATE site_settings SET language_id = 0 WHERE id = 13");
    }
}
