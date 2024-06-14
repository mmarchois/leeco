<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240614072426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_6a2ca10c8cde5729');
        $this->addSql('ALTER TABLE media RENAME COLUMN type TO origin');
        $this->addSql('CREATE INDEX IDX_6A2CA10CDEF1561E ON media (origin)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_6A2CA10CDEF1561E');
        $this->addSql('ALTER TABLE media RENAME COLUMN origin TO type');
        $this->addSql('CREATE INDEX idx_6a2ca10c8cde5729 ON media (type)');
    }
}
