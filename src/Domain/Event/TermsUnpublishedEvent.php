<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Event;

use RichId\TermsModuleBundle\Domain\Entity\Terms;

final class TermsUnpublishedEvent extends TermsEvent
{
    /** @var Terms */
    private $terms;

    public function __construct(Terms $terms)
    {
        $this->terms = $terms;
    }

    public function getTerms(): Terms
    {
        return $this->terms;
    }
}
