<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\SecurityVoter;

use RichId\TermsModuleBundle\Domain\Guard\TermsGuardInterface;
use RichId\TermsModuleBundle\Domain\Model\TermsGuardValidationInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TermsGuardVoter extends Voter
{
    public const MODULE_TERMS_GUARD_VALID = 'MODULE_TERMS_GUARD_VALID';

    /** @var array<TermsGuardInterface> */
    protected $guards;

    /** @param array<TermsGuardInterface> $guards */
    public function setGuards(array $guards): void
    {
        $this->guards = $guards;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof TermsGuardValidationInterface && $attribute === self::MODULE_TERMS_GUARD_VALID;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $guard = $this->findGuard($subject);

        if ($guard === null) {
            return true;
        }

        return $guard->check($subject->getTermsSlug(), $subject->getTermsSubjectType(), $subject->getTermsSubjectIdentifier());
    }

    protected function findGuard(TermsGuardValidationInterface $subject): ?TermsGuardInterface
    {
        foreach ($this->guards as $guard) {
            if ($guard->supports($subject->getTermsSlug(), $subject->getTermsSubjectType(), $subject->getTermsSubjectIdentifier())) {
                return $guard;
            }
        }

        return null;
    }
}
