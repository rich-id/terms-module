<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

interface LoggerInterface
{
    public function logTermsSigned(string $termsSlug, TermsSubjectInterface $subject, ?bool $accepted): void;
}
