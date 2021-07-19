<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Factory;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

class DefaultTermsVersionFactory
{
    public function __invoke(Terms $terms): TermsVersion
    {
        $entity = new TermsVersion();

        $entity->setTerms($terms);
        $entity->setVersion(1);
        $terms->addVersion($entity);

        return $entity;
    }
}
