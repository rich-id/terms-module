<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

class DummySubject implements TermsSubjectInterface
{
    /** @var string */
    protected $type;

    /** @var string */
    protected $identifier;

    private function __construct()
    {
        // Avoid instantiation
    }

    public function getTermsSubjectType(): string
    {
        return $this->type;
    }

    public function getTermsSubjectIdentifier(): string
    {
        return $this->identifier;
    }

    public static function create(string $type, string $identifier): self
    {
        $model = new self();

        $model->type = $type;
        $model->identifier = $identifier;

        return $model;
    }
}
