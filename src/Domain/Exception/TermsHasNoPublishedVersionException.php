<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use RichId\TermsModuleBundle\Domain\Entity\Terms;

class TermsHasNoPublishedVersionException extends TermsModuleException
{
    /** @var Terms */
    protected $terms;

    public function __construct(Terms $terms)
    {
        parent::__construct(\sprintf('Terms %s hasn\'t published version.', $terms->getSlug()));

        $this->terms = $terms;
    }

    public function getTerms(): Terms
    {
        return $this->terms;
    }
}
