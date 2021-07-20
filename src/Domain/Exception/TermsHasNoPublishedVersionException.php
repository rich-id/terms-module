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
        $this->terms = $terms;
        $message = \sprintf('Terms %s hasn\'t published version.', $terms->getSlug());

        parent::__construct($message);
    }

    public function getTerms(): Terms
    {
        return $this->terms;
    }
}
