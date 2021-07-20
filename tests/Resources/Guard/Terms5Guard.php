<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Guard;

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

    public function supports(string $slug, string $subjectType, string $subjectIdentifier): bool
    {
        return $slug === 'terms-5';
    }

    public function check(string $slug, string $subjectType, string $subjectIdentifier): bool
    {
        $user = $this->security->getUser();

        if ($user === null || $subjectType !== 'user') {
            return false;
        }

        return $subjectIdentifier === (string) $user->getUsername();
    }
}
