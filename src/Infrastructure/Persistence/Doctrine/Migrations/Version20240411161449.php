<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411161449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participant (uuid UUID NOT NULL, event_uuid UUID NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, access_code VARCHAR(50) NOT NULL, access_sent BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_D79F6B11CEB41C0D ON participant (event_uuid)');
        $this->addSql('CREATE INDEX IDX_D79F6B11E7927C74 ON participant (email)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11CEB41C0D FOREIGN KEY (event_uuid) REFERENCES event (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT FK_D79F6B11CEB41C0D');
        $this->addSql('DROP TABLE participant');
    }
}
