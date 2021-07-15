<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

class AlreadyEnabledTermsVersionException extends TermsModuleException
{
    /** @var TermsVersion */
    protected $termsVersion;

    public function __construct(TermsVersion $termsVersion)
    {
        $terms = $termsVersion->getTerms();

        parent::__construct(
            \sprintf('The terms version %d of terms %s is already enabled.', $termsVersion->getVersion(), $terms->getSlug())
        );

        $this->termsVersion = $termsVersion;
    }

    public function getTermsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }
}
