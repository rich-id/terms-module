<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Migrations;

trait TermsMigrationTrait
{
    protected function createTerms(
        string $slug,
        string $name,
        int $startVersion = 1
    ): void
    {
        $this->addSQL("
            INSERT INTO module_terms_terms 
            (slug, name, is_published, is_depublication_locked) 
            VALUES ('$slug', '$name', 0, 0)
        ");

        $this->addSQL("
            INSERT INTO module_terms_terms_version 
            (version, is_enabled, title, content, publicationDate, terms_id) 
            VALUES (
                $startVersion, 
                0, 
                'My title', 
                'My content', 
                CURRENT_TIME, 
                (SELECT terms.id FROM module_terms_terms WHERE terms.slug = '$slug')
            )
        ");
    }

    protected function deleteTerms(string $slug): void
    {
        $this->addSQL("
            DELETE FROM module_terms_terms_version AS version WHERE 
            version.terms_id = (SELECT terms.id FROM module_terms_terms WHERE terms.slug = '$slug')
        ");

        $this->addSQL("DELETE FROM module_terms_terms WHERE slug = '$slug'");
    }
}
