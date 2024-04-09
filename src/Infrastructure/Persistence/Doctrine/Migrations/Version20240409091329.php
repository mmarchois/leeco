<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409091329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (uuid UUID NOT NULL, user_uuid UUID NOT NULL, title VARCHAR(100) NOT NULL, date TIMESTAMP(0) WITH TIME ZONE NOT NULL, expiration_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7ABFE1C6F ON event (user_uuid)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES "user" (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7ABFE1C6F');
        $this->addSql('DROP TABLE event');
    }
}
