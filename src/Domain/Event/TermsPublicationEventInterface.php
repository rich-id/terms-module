<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Event;

use RichId\TermsModuleBundle\Domain\Entity\Terms;

interface TermsPublicationEventInterface
{
    public function getTerms(): Terms;
}
