<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

interface EntityRecoderInterface
{
    public function saveSignature(TermsVersionSignature $signature): void;
}
