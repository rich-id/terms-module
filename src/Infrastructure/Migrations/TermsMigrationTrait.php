<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Migrations;

/**
 * @deprecated The migrations should be stateless, please copy the raw SQL instead
 */
trait TermsMigrationTrait
{
    /** @deprecated The migrations should be stateless, please copy the raw SQL instead */
    protected function createTerms(
        string $slug,
        string $name,
        int $startVersion = 1
    ): void {
        $this->addSQL("
            INSERT INTO module_terms_terms 
            (slug, name, is_published, is_depublication_locked) 
            VALUES ('$slug', '$name', 0, 0)
        ");

        $this->addSQL("
            INSERT INTO module_terms_terms_version 
            (version, is_enabled, title, content, publication_date, terms_id) 
            VALUES (
                $startVersion, 
                0, 
                'My title', 
                'My content', 
                CURRENT_TIME, 
                (SELECT id FROM module_terms_terms WHERE slug = '$slug')
            )
        ");
    }

    /** @deprecated The migrations should be stateless, please copy the raw SQL instead */
    protected function deleteTerms(string $slug): void
    {
        $this->addSQL("
            DELETE FROM module_terms_terms_version AS version WHERE 
            version.terms_id = (SELECT terms.id FROM module_terms_terms WHERE terms.slug = '$slug')
        ");

        $this->addSQL("DELETE FROM module_terms_terms WHERE slug = '$slug'");
    }
}
