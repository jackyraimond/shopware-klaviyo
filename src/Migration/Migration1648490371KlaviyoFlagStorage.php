<?php declare(strict_types=1);

namespace Klaviyo\Integration\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1648490371KlaviyoFlagStorage extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1648490371;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS `klaviyo_flag_storage` (
            `id`                BINARY(16)     NOT NULL,
            `key`               VARCHAR(255)   NOT NULL,
            `value`             VARCHAR(255)   NOT NULL,
            `sales_channel_id`  BINARY(16)     NOT NULL,
            `created_at`        DATETIME(3)    NOT NULL,
            `updated_at`        DATETIME(3)    NULL,
            PRIMARY KEY (`id`),
            KEY `fk.klaviyo_flag_storage.sales_channel_id` (`sales_channel_id`),
                CONSTRAINT `fk.klaviyo_flag_storage.sales_channel_id` 
                FOREIGN KEY (`sales_channel_id`) 
                REFERENCES `sales_channel` (`id`) 
                ON DELETE CASCADE 
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
