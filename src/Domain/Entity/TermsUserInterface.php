<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

interface TermsUserInterface
{
    public function getDisplayNameForTerms(): ?string;
}
