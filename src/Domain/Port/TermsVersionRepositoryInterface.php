<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

interface TermsVersionRepositoryInterface
{
    public function findLastSignedVersionForTermsSubject(string $termsSlug, TermsSubjectInterface $subject): ?TermsVersion;
}
