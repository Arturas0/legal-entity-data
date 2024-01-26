<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125192903 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE legal_status (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status_code INT NOT NULL, status_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE legal_entity ADD legal_status_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE legal_entity ADD CONSTRAINT FK_E21E9E13873E3FEC FOREIGN KEY (legal_status_id) REFERENCES legal_status (id)');
        $this->addSql('CREATE INDEX IDX_E21E9E13873E3FEC ON legal_entity (legal_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_entity DROP FOREIGN KEY FK_E21E9E13873E3FEC');
        $this->addSql('DROP TABLE legal_status');
        $this->addSql('DROP INDEX IDX_E21E9E13873E3FEC ON legal_entity');
        $this->addSql('ALTER TABLE legal_entity DROP legal_status_id');
    }
}
