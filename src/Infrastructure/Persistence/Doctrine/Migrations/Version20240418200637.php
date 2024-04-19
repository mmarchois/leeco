<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418200637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (uuid UUID NOT NULL, event_uuid UUID NOT NULL, title VARCHAR(100) NOT NULL, start_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_389B783CEB41C0D ON tag (event_uuid)');
        $this->addSql('CREATE INDEX IDX_389B7832B36786B ON tag (title)');
        $this->addSql('CREATE INDEX IDX_389B78395275AB8 ON tag (start_date)');
        $this->addSql('CREATE INDEX IDX_389B783845CBB3E ON tag (end_date)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783CEB41C0D FOREIGN KEY (event_uuid) REFERENCES event (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag DROP CONSTRAINT FK_389B783CEB41C0D');
        $this->addSql('DROP TABLE tag');
    }
}
