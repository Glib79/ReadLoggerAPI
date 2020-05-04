<?php
declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fill dictionary tables
 */
final class Version20200427145000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fill dictionary tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO format (id, name, translation_key) VALUES
            (1, "paper", "format.paper"),
            (2, "e-book", "format.eBook"),
            (3, "audiobook", "format.audiobook");');

        $this->addSql('INSERT INTO language (id, symbol, translation_key) VALUES
            (1, "en", "language.en"),
            (2, "pl", "language.pl");');

        $this->addSql('INSERT INTO status (id, name, translation_key) VALUES
            (1, "planned", "status.planned"),
            (2, "during", "status.during"),
            (3, "finished", "status.finished"),
            (4, "abandoned", "status.abandoned");');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('TRUNCATE TABLE language;');
        $this->addSql('TRUNCATE TABLE format;');
        $this->addSql('TRUNCATE TABLE status;');
    }
}