<?php
declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create table user
 */
final class Version20200427110700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table user';
    }

    public function up(Schema $schema): void
    {
        /* User */
        $this->addSql('CREATE TABLE `user` 
        (
            `id` char(36) NOT NULL,
            `email` varchar(45) NOT NULL,
            `password` varchar(255) NOT NULL,
            `roles` json DEFAULT NULL,
            `created_at` datetime NOT NULL,
            `modified_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_user_email` (`email`)
        ) 
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP user;');
    }
}