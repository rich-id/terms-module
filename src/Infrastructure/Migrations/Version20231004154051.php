<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;

class Version20231004154051 extends AbstractMigration
{
    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE module_terms_terms_version_signature ADD subject_name VARCHAR(255) NOT NULL, ADD signed_by_name VARCHAR(255) DEFAULT NULL, ADD signed_by_name_for_sort VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE module_terms_terms_version_signature DROP subject_name, signed_by_name, signed_by_name_for_sort');
    }
}
