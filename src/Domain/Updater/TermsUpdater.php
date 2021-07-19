<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Updater;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;

class TermsUpdater
{
    public function update(Terms $terms, TermsEdition $termsEdition): Terms
    {
        $terms->setIsPublished($termsEdition->isTermsPublished() ?? false);

        return $terms;
    }
}
