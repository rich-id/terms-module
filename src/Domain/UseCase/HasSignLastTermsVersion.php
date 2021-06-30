<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

class HasSignLastTermsVersion
{
    /** @var HasSignTerms */
    protected $hasSignTerms;

    public function __construct(HasSignTerms $hasSignTerms)
    {
        $this->hasSignTerms = $hasSignTerms;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): bool
    {
        return ($this->hasSignTerms)($termsSlug, $subject) === HasSignTerms::HAS_SIGN_LATEST_VERSION;
    }
}
