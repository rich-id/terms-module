<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\SecurityVoter;

use RichId\TermsModuleBundle\Domain\Guard\TermsGuardManager;
use RichId\TermsModuleBundle\Domain\Model\TermsGuardValidationInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TermsGuardVoter extends Voter
{
    public const MODULE_TERMS_GUARD_VALID = 'MODULE_TERMS_GUARD_VALID';

    /** @var TermsGuardManager */
    protected $termsGuardManager;

    public function __construct(TermsGuardManager $termsGuardManager)
    {
        $this->termsGuardManager = $termsGuardManager;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof TermsGuardValidationInterface && $attribute === self::MODULE_TERMS_GUARD_VALID;
    }

    /** @param string $attribute */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $guard = $this->termsGuardManager->getGuardFor($subject->getTermsSlug(), $subject);

        if ($guard === null) {
            return true;
        }

        return $guard->check($subject->getTermsSlug(), $subject);
    }
}
