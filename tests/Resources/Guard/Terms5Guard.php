<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Guard;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Guard\TermsGuardInterface;
use Symfony\Component\Security\Core\Security;

final class Terms5Guard implements TermsGuardInterface
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function supports(string $slug, TermsSubjectInterface $subject): bool
    {
        return $slug === 'terms-5';
    }

    public function check(string $slug, TermsSubjectInterface $subject): bool
    {
        $user = $this->security->getUser();

        if ($user === null || $subject->getTermsSubjectType() !== 'user') {
            return false;
        }

        return $subject->getTermsSubjectIdentifier() === (string) $user->getUsername();
    }
}
