<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Factory;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

class TermsVersionSignatureFactory
{
    public function __invoke(TermsVersion $version, TermsSubjectInterface $subject): TermsVersionSignature
    {
        $entity = new TermsVersionSignature();

        $entity->setVersion($version);
        $entity->setSubjectType($subject->getTermsSubjectType());
        $entity->setSubjectIdentifier($subject->getTermsSubjectIdentifier());
        $entity->setDate(new \DateTime());

        return $entity;
    }
}
