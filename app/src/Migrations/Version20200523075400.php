<?php
declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add log table
 */
final class Version20200523075400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add log table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `log`
            (
                `id` char(36) NOT NULL,
                `user_id` char(36) NOT NULL,
                `happened_at` datetime NOT NULL,
                `action` varchar(10) NOT NULL,
                `table` varchar(30) NOT NULL,
                `record_id` char(36) NOT NULL,
                `value` json,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`user_id`) REFERENCES user(`id`) ON DELETE RESTRICT
            )
            ENGINE=InnoDB 
            DEFAULT CHARSET=utf8mb4 
            COLLATE=utf8mb4_unicode_ci;'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE log;');
    }
}