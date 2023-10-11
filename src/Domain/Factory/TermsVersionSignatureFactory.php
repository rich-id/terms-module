<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Factory;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsUserInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Guard\TermsGuardManager;
use RichId\TermsModuleBundle\Domain\Port\SecurityInterface;
use RichId\TermsModuleBundle\Domain\Port\TranslatorInterface;

class TermsVersionSignatureFactory
{
    /** @var SecurityInterface */
    protected $security;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var TermsGuardManager */
    protected $termsGuardManager;

    public function __construct(SecurityInterface $security, TranslatorInterface $translator, TermsGuardManager $termsGuardManager)
    {
        $this->security = $security;
        $this->translator = $translator;
        $this->termsGuardManager = $termsGuardManager;
    }

    public function __invoke(TermsVersion $version, TermsSubjectInterface $subject): TermsVersionSignature
    {
        $guard = $this->termsGuardManager->getGuardFor($version->getTerms()->getSlug() ?? '', $subject);
        $subjectName = $guard !== null ? $guard->getSubjectName($subject) : null;
        $subjectName = $subjectName ?? $this->translator->trans('terms_module.pdf_signature.subject_not_found', [], 'terms_module');

        $user = $this->security->getUser();

        $entity = new TermsVersionSignature();

        $entity->setVersion($version);
        $entity->setSubjectType($subject->getTermsSubjectType());
        $entity->setSubjectIdentifier($subject->getTermsSubjectIdentifier());
        $entity->setSubjectName($subjectName);
        $entity->setDate(new \DateTime());

        if ($user !== null) {
            $entity->setSignedBy($user->getUsername());
        }

        if ($user instanceof TermsUserInterface) {
            $entity->setSignedByName($user->getTermsDisplayName());
            $entity->setSignedByNameForSort($user->getTermsDisplayNameForSort());
        }

        return $entity;
    }
}
