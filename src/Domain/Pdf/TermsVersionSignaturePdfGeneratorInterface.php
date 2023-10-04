<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Pdf;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

interface TermsVersionSignaturePdfGeneratorInterface
{
    public function __invoke(TermsVersionSignature $termsVersionSignature): string;
}
