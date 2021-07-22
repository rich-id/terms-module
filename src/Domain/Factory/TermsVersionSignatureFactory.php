<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Factory;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Port\SecurityInterface;

class TermsVersionSignatureFactory
{
    /** @var SecurityInterface */
    protected $security;

    public function __construct(SecurityInterface $security)
    {
        $this->security = $security;
    }

    public function __invoke(TermsVersion $version, TermsSubjectInterface $subject): TermsVersionSignature
    {
        $user = $this->security->getUser();

        $entity = new TermsVersionSignature();

        $entity->setVersion($version);
        $entity->setSubjectType($subject->getTermsSubjectType());
        $entity->setSubjectIdentifier($subject->getTermsSubjectIdentifier());
        $entity->setDate(new \DateTime());

        if ($user !== null) {
            $entity->setSignedBy($user->getUsername());
        }

        return $entity;
    }
}
