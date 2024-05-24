<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524061309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (uuid UUID NOT NULL, event_uuid UUID NOT NULL, path VARCHAR(100) NOT NULL, type VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_6A2CA10CCEB41C0D ON media (event_uuid)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C8CDE5729 ON media (type)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CCEB41C0D FOREIGN KEY (event_uuid) REFERENCES event (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD media_uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA712388F75 FOREIGN KEY (media_uuid) REFERENCES media (uuid) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3BAE0AA712388F75 ON event (media_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA712388F75');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10CCEB41C0D');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP INDEX IDX_3BAE0AA712388F75');
        $this->addSql('ALTER TABLE event DROP media_uuid');
    }
}
