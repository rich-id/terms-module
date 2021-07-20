<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20210628151052 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE module_terms_terms (id INT UNSIGNED AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, is_published TINYINT(1) NOT NULL, is_depublication_locked TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_67FAB3BA989D9B62 (slug), UNIQUE INDEX UNIQ_67FAB3BA5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE module_terms_terms_version (id INT UNSIGNED AUTO_INCREMENT NOT NULL, terms_id INT UNSIGNED NOT NULL, version INT UNSIGNED NOT NULL, is_enabled TINYINT(1) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, publication_date DATETIME DEFAULT NULL, index IDX_2E43CCE53742F27 (terms_id), UNIQUE INDEX terms_version_terms_id_version_UNIQUE (version, terms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE module_terms_terms_version ADD CONSTRAINT fk_2e43cce53742f27 FOREIGN KEY (terms_id) REFERENCES module_terms_terms (id) ON DELETE RESTRICT');

        $this->addSql('CREATE TABLE module_terms_terms_version_signature (id INT UNSIGNED AUTO_INCREMENT NOT NULL, version_id INT UNSIGNED NOT NULL, date DATETIME NOT NULL, subject_type VARCHAR(255) NOT NULL, subject_identifier VARCHAR(255) NOT NULL, index IDX_DD62973A4BBC2705 (version_id), UNIQUE INDEX module_terms_terms_version_signature_UNIQUE (subject_type, subject_identifier, version_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE module_terms_terms_version_signature ADD CONSTRAINT fk_dd62973a4bbc2705 FOREIGN KEY (version_id) REFERENCES module_terms_terms_version (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE module_terms_terms_version DROP FOREIGN KEY FK_2E43CCE53742F27');
        $this->addSql('ALTER TABLE module_terms_terms_version_signature DROP FOREIGN KEY FK_DD62973A4BBC2705');
        $this->addSql('DROP TABLE module_terms_terms');
        $this->addSql('DROP TABLE module_terms_terms_version');
        $this->addSql('DROP TABLE module_terms_terms_version_signature');
    }
}
