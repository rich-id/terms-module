<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

class EnabledVersionCannotBeDeletedException extends TermsModuleException
{
    /** @var TermsVersion */
    protected $termsVersion;

    public function __construct(TermsVersion $termsVersion)
    {
        $terms = $termsVersion->getTerms();
        $this->termsVersion = $termsVersion;
        $message = \sprintf('Version %d of terms %s cannot be deleted.', $termsVersion->getVersion(), $terms->getSlug());

        parent::__construct($message);
    }

    public function getTermsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }
}
