<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

interface EntityRemoverInterface
{
    public function removeTermsVersion(TermsVersion $termsVersion): void;
}
