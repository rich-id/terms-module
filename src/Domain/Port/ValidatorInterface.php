<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Model\TermsEdition;

interface ValidatorInterface
{
    public function validateTermsEdition(TermsEdition $termsEdition): void;
}
