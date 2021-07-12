<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Event;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

final class TermsVersionCreatedEvent
{
    /** @var TermsVersion */
    private $termsVersion;

    public function __construct(TermsVersion $termsVersion)
    {
        $this->termsVersion = $termsVersion;
    }

    public function getTermsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }
}
