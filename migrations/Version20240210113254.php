<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240210113254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE legal_entity ADD deregistered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\' AFTER registered_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE legal_entity DROP deregistered_at');
    }
}
