<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528152038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Switch "request" table primary key, "time", from second-precision to micro-second precision';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE request ALTER "time" TYPE TIMESTAMP(6) WITH TIME ZONE');
        $this->addSql('COMMENT ON COLUMN request.time IS \'(DC2Type:iddatetime)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE request ALTER time TYPE TIMESTAMP(0) WITH TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN request."time" IS \'(DC2Type:datetimetz_immutable)\'');
    }
}
