<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260822082805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_entity CHANGE id id BINARY(16) NOT NULL, CHANGE legal_entity_type_id legal_entity_type_id BINARY(16) DEFAULT NULL, CHANGE legal_entity_status_id legal_entity_status_id BINARY(16) DEFAULT NULL, CHANGE registered_at registered_at DATETIME DEFAULT NULL, CHANGE deregistered_at deregistered_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE legal_entity ADD CONSTRAINT FK_E21E9E13970FA113 FOREIGN KEY (legal_entity_type_id) REFERENCES legal_entity_type (id)');
        $this->addSql('ALTER TABLE legal_entity ADD CONSTRAINT FK_E21E9E1315AB96C0 FOREIGN KEY (legal_entity_status_id) REFERENCES legal_entity_status (id)');
        $this->addSql('ALTER TABLE legal_entity_status CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE legal_entity_type CHANGE id id BINARY(16) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_entity DROP FOREIGN KEY FK_E21E9E13970FA113');
        $this->addSql('ALTER TABLE legal_entity DROP FOREIGN KEY FK_E21E9E1315AB96C0');
        $this->addSql('ALTER TABLE legal_entity CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE registered_at registered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE deregistered_at deregistered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE legal_entity_type_id legal_entity_type_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE legal_entity_status_id legal_entity_status_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE legal_entity_status CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE legal_entity_type CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
    }
}
