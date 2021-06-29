<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;

class SignTerms
{
    /** @var HasSignTerms */
    protected $hasSignTerms;

    public function __construct(HasSignTerms $hasSignTerms)
    {
        $this->$hasSignTerms = $hasSignTerms;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): int
    {
        if (($this->hasSignTerms)($termsSlug, $subject) === HasSignTerms::HAS_SIGN_LATEST_VERSION) {
            throw new AlreadySignLastTermsVersionException();
        }

        return 0;
    }
}
