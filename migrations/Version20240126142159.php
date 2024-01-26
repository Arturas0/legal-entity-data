<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240126142159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_entity ADD CONSTRAINT FK_E21E9E13970FA113 FOREIGN KEY (legal_entity_type_id) REFERENCES legal_entity_type (id)');
        $this->addSql('ALTER TABLE legal_entity ADD CONSTRAINT FK_E21E9E1315AB96C0 FOREIGN KEY (legal_entity_status_id) REFERENCES legal_entity_status (id)');
        $this->addSql('CREATE INDEX IDX_E21E9E13970FA113 ON legal_entity (legal_entity_type_id)');
        $this->addSql('CREATE INDEX IDX_E21E9E1315AB96C0 ON legal_entity (legal_entity_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE legal_entity DROP FOREIGN KEY FK_E21E9E13970FA113');
        $this->addSql('ALTER TABLE legal_entity DROP FOREIGN KEY FK_E21E9E1315AB96C0');
        $this->addSql('DROP INDEX IDX_E21E9E13970FA113 ON legal_entity');
        $this->addSql('DROP INDEX IDX_E21E9E1315AB96C0 ON legal_entity');
    }
}
