<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use RichId\TermsModuleBundle\Domain\Entity\Terms;

class AlreadySignLastTermsVersionException extends TermsModuleException
{
    /** @var Terms */
    protected $terms;

    public function __construct(Terms $terms)
    {
        parent::__construct(sprintf('Terms %s is already sign.', $terms->getSlug()));

        $this->terms = $terms;
    }

    public function getTerms(): Terms
    {
        return $this->terms;
    }
}
