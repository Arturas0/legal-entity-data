<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128083852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_entity ADD checksum VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E21E9E1377153098 ON legal_entity (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E21E9E13DE6FDF9A ON legal_entity (checksum)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E21E9E1377153098 ON legal_entity');
        $this->addSql('DROP INDEX UNIQ_E21E9E13DE6FDF9A ON legal_entity');
        $this->addSql('ALTER TABLE legal_entity DROP checksum');
    }
}
