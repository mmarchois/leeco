<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514190056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE guest (uuid UUID NOT NULL, event_uuid UUID NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, device_identifier VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_ACB79A35CEB41C0D ON guest (event_uuid)');
        $this->addSql('CREATE INDEX IDX_ACB79A35F01DC0AC ON guest (device_identifier)');
        $this->addSql('ALTER TABLE guest ADD CONSTRAINT FK_ACB79A35CEB41C0D FOREIGN KEY (event_uuid) REFERENCES event (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE participant DROP CONSTRAINT fk_d79f6b11ceb41c0d');
        $this->addSql('DROP TABLE participant');
        $this->addSql('ALTER TABLE event ADD access_code VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX event_access_code ON event (access_code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE participant (uuid UUID NOT NULL, event_uuid UUID NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, access_code VARCHAR(50) NOT NULL, access_sent BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX participant_access_code ON participant (access_code)');
        $this->addSql('CREATE INDEX idx_d79f6b11e7927c74 ON participant (email)');
        $this->addSql('CREATE INDEX idx_d79f6b11ceb41c0d ON participant (event_uuid)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT fk_d79f6b11ceb41c0d FOREIGN KEY (event_uuid) REFERENCES event (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE guest DROP CONSTRAINT FK_ACB79A35CEB41C0D');
        $this->addSql('DROP TABLE guest');
        $this->addSql('DROP INDEX event_access_code');
        $this->addSql('ALTER TABLE event DROP access_code');
    }
}
