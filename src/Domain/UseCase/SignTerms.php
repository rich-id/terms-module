<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

class SignTerms
{
    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): int
    {
        return 0;
    }
}
