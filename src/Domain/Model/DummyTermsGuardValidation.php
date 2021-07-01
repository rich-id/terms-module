<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

final class DummyTermsGuardValidation implements TermsGuardValidationInterface
{
    /** @var string */
    private $slug;

    /** @var string */
    private $type;

    /** @var string */
    private $identifier;

    public function __construct()
    {
        // Avoid instantiation
    }

    public function getTermsSlug(): string
    {
        return $this->slug;
    }

    public function getTermsSubjectType(): string
    {
        return $this->type;
    }

    public function getTermsSubjectIdentifier(): string
    {
        return $this->identifier;
    }

    public static function create(string $slug, string $type, string $identifier): self
    {
        $model = new self();

        $model->slug = $slug;
        $model->type = $type;
        $model->identifier = $identifier;

        return $model;
    }
}
