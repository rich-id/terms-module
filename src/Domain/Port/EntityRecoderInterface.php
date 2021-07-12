<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

interface EntityRecoderInterface
{
    public function saveSignature(TermsVersionSignature $signature): void;

    public function saveTerms(Terms $terms): void;

    public function saveTermsVersion(TermsVersion $termsVersion): void;

    public function flush(): void;
}
