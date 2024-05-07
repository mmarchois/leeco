<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507130359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event RENAME COLUMN date TO start_date');
        $this->addSql('ALTER TABLE event RENAME COLUMN expiration_date TO end_date');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE event RENAME COLUMN start_date TO date');
        $this->addSql('ALTER TABLE event RENAME COLUMN end_date TO expiration_date');
    }
}
