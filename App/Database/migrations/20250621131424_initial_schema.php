<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialSchema extends AbstractMigration
{
    /**
     * Initial schema migration - devre dışı bırakıldı
     * 
     * Bu migration artık hiçbir şey yapmıyor. Sonraki migration'lar
     * gerekli tabloları oluşturacak.
     */
    public function change(): void
    {
        // Bu migration artık hiçbir şey yapmıyor
        // Sonraki migration'lar gerekli tabloları oluşturacak
        return;
    }
}
