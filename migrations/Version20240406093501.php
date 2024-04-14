<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240406093501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE legal_entity CHANGE id id BINARY(16) NOT NULL, CHANGE registered_at registered_at DATETIME DEFAULT NULL, CHANGE deregistered_at deregistered_at DATETIME DEFAULT NULL, CHANGE legal_entity_type_id legal_entity_type_id BINARY(16) DEFAULT NULL, CHANGE legal_entity_status_id legal_entity_status_id BINARY(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE legal_entity_status CHANGE id id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE legal_entity_type CHANGE id id BINARY(16) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE legal_entity CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE registered_at registered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE deregistered_at deregistered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE legal_entity_type_id legal_entity_type_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', CHANGE legal_entity_status_id legal_entity_status_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE legal_entity_status CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE legal_entity_type CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
    }
}
