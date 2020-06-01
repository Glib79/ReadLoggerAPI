<?php
declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create database structure
 */
final class Version20200427112900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create database structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `author` 
        (
            `id` char(36) NOT NULL,
            `first_name` varchar(100) NOT NULL,
            `last_name` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `book` 
        (
            `id` char(36) NOT NULL,
            `title` varchar(100) NOT NULL,
            `sub_title` varchar(100) DEFAULT NULL,
            `size` int DEFAULT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');

        $this->addSql('CREATE TABLE `author_book` 
        (
            `author_id` char(36) NOT NULL,
            `book_id` char(36) NOT NULL,
            PRIMARY KEY (`author_id`, `book_id`),
            FOREIGN KEY (`author_id`) REFERENCES author(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`book_id`) REFERENCES book(`id`) ON DELETE RESTRICT
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `language` 
        (
            `id` int(3) NOT NULL,
            `symbol` varchar(5) NOT NULL,
            `translation_key` varchar(100) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_language_symbol` (`symbol`)
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `format` 
        (
            `id` int(1) NOT NULL,
            `name` varchar(20) NOT NULL,
            `translation_key` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');

        $this->addSql('CREATE TABLE `status` 
        (
            `id` int(1) NOT NULL,
            `name` varchar(20) NOT NULL,
            `translation_key` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');
        
        $this->addSql('CREATE TABLE `user_book` 
        (
            `id` char(36) NOT NULL,
            `user_id` char(36) NOT NULL,
            `book_id` char(36) NOT NULL,
            `status_id` int(1) NOT NULL,
            `start_date` datetime DEFAULT NULL,
            `end_date` datetime DEFAULT NULL,
            `format_id` int(1) NOT NULL,
            `rating` int(1) DEFAULT NULL,
            `language_id` int(3) NOT NULL,
            `notes` text DEFAULT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`user_id`) REFERENCES user(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`book_id`) REFERENCES book(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`status_id`) REFERENCES status(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`language_id`) REFERENCES language(`id`) ON DELETE RESTRICT,
            FOREIGN KEY (`format_id`) REFERENCES format(`id`) ON DELETE RESTRICT
        )
        ENGINE=InnoDB 
        DEFAULT CHARSET=utf8mb4 
        COLLATE=utf8mb4_unicode_ci;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP author_book;');
        $this->addSql('DROP user_book;');
        $this->addSql('DROP author;');
        $this->addSql('DROP book;');
        $this->addSql('DROP language;');
        $this->addSql('DROP format;');
        $this->addSql('DROP status;');
    }
}