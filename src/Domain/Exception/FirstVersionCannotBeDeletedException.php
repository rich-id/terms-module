<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

class FirstVersionCannotBeDeletedException extends TermsModuleException
{
    /** @var TermsVersion */
    protected $termsVersion;

    public function __construct(TermsVersion $termsVersion)
    {
        $terms = $termsVersion->getTerms();

        parent::__construct(
            \sprintf('First version of terms %s cannot be deleted.', $terms->getSlug())
        );

        $this->termsVersion = $termsVersion;
    }

    public function getTermsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }
}
