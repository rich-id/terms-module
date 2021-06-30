<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;


final class DummyTermsGuardValidation implements TermsGuardValidationInterface
{
    /** @var string */
    protected $slug;

    /** @var string */
    protected $type;

    /** @var string */
    protected $identifier;

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
