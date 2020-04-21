<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200421103450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create timescaledb table to track analytics';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        // Instal TimescaleDB extension
        $this->addSql('CREATE EXTENSION IF NOT EXISTS timescaledb CASCADE');
        $this->addSql('CREATE TABLE request (time TIMESTAMP(0) WITH TIME ZONE NOT NULL, application_id UUID NOT NULL, api_id UUID NOT NULL, status_code SMALLINT NOT NULL, response_time INT NOT NULL, PRIMARY KEY(time))');
        $this->addSql('CREATE INDEX IDX_3B978F9F3E030ACD ON request (application_id)');
        $this->addSql('CREATE INDEX IDX_3B978F9F54963938 ON request (api_id)');
        $this->addSql('COMMENT ON COLUMN request.time IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN request.application_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN request.api_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F3E030ACD FOREIGN KEY (application_id) REFERENCES application (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F54963938 FOREIGN KEY (api_id) REFERENCES api (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('SELECT create_hypertable(\'request\', \'time\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP EXTENSION IF EXISTS timescaledb');
    }
}
