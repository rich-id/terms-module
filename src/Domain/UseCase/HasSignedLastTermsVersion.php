<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

class HasSignedLastTermsVersion
{
    /** @var HasSignedTerms */
    protected $hasSignedTerms;

    public function __construct(HasSignedTerms $hasSignedTerms)
    {
        $this->hasSignedTerms = $hasSignedTerms;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): bool
    {
        return ($this->hasSignedTerms)($termsSlug, $subject) === HasSignedTerms::HAS_SIGNED_LATEST_VERSION;
    }
}
