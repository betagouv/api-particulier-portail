<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200416162027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE subscription (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , application_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , api_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , active BOOLEAN NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3C664D33E030ACD ON subscription (application_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D354963938 ON subscription (api_id)');
        $this->addSql('CREATE TABLE application (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , organization_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, signup_id INTEGER NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A45BDDC132C8A3DE ON application (organization_id)');
        $this->addSql('CREATE TABLE application_scope (application_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , scope_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , PRIMARY KEY(application_id, scope_id))');
        $this->addSql('CREATE INDEX IDX_E33BB2A93E030ACD ON application_scope (application_id)');
        $this->addSql('CREATE INDEX IDX_E33BB2A9682B5931 ON application_scope (scope_id)');
        $this->addSql('CREATE TABLE api_key (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , application_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , expires_at DATETIME NOT NULL, active BOOLEAN NOT NULL, hash VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C912ED9D3E030ACD ON api_key (application_id)');
        $this->addSql('CREATE TABLE scope (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE TABLE user_position (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , user_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , application_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , role VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A6A100F5A76ED395 ON user_position (user_id)');
        $this->addSql('CREATE INDEX IDX_A6A100F53E030ACD ON user_position (application_id)');
        $this->addSql('CREATE TABLE api (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, backend VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE organization (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , siret VARCHAR(14) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE oauth2_authorization_code (identifier CHAR(80) NOT NULL, client VARCHAR(32) NOT NULL, expiry DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , user_identifier VARCHAR(128) DEFAULT NULL, scopes CLOB DEFAULT NULL --(DC2Type:oauth2_scope)
        , revoked BOOLEAN NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX IDX_509FEF5FC7440455 ON oauth2_authorization_code (client)');
        $this->addSql('CREATE TABLE oauth2_access_token (identifier CHAR(80) NOT NULL, client VARCHAR(32) NOT NULL, expiry DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , user_identifier VARCHAR(128) DEFAULT NULL, scopes CLOB DEFAULT NULL --(DC2Type:oauth2_scope)
        , revoked BOOLEAN NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX IDX_454D9673C7440455 ON oauth2_access_token (client)');
        $this->addSql('CREATE TABLE oauth2_client (identifier VARCHAR(32) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirect_uris CLOB DEFAULT NULL --(DC2Type:oauth2_redirect_uri)
        , grants CLOB DEFAULT NULL --(DC2Type:oauth2_grant)
        , scopes CLOB DEFAULT NULL --(DC2Type:oauth2_scope)
        , active BOOLEAN NOT NULL, allow_plain_text_pkce BOOLEAN DEFAULT \'0\' NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE TABLE oauth2_refresh_token (identifier CHAR(80) NOT NULL, access_token CHAR(80) DEFAULT NULL, expiry DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , revoked BOOLEAN NOT NULL, PRIMARY KEY(identifier))');
        $this->addSql('CREATE INDEX IDX_4DD90732B6A2DD68 ON oauth2_refresh_token (access_token)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE application_scope');
        $this->addSql('DROP TABLE api_key');
        $this->addSql('DROP TABLE scope');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_position');
        $this->addSql('DROP TABLE api');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE oauth2_authorization_code');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('DROP TABLE oauth2_refresh_token');
    }
}
