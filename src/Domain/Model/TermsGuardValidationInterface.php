<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

interface TermsGuardValidationInterface extends TermsSubjectInterface
{
    public function getTermsSlug(): string;
}
