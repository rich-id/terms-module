<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\SecurityVoter;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TermsVoter extends Voter
{
    public const MODULE_TERMS_ADMIN = 'MODULE_TERMS_ADMIN';
    public const HAS_SIGNED_TERMS_PREFIX = 'HAS_SIGNED_TERMS_';

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var array<string> */
    protected $moduleAdminRoles;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, ParameterBagInterface $parameterBag)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->moduleAdminRoles = $parameterBag->get('rich_id_terms_module.admin_roles');
    }

    protected function supports($attribute, $subject): bool
    {
        return $attribute === self::MODULE_TERMS_ADMIN || $this->isTheAttributeMatchHasSignedTerms($attribute);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($attribute === self::MODULE_TERMS_ADMIN) {
            return $this->moduleTermsAdmin($token);
        }

        if ($this->isTheAttributeMatchHasSignedTerms($attribute)) {
            return $this->hasSignedTerms();
        }

        throw new \LogicException('This code should not be reached!');
    }

    protected function moduleTermsAdmin(TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        foreach ($this->moduleAdminRoles as $adminRole) {
            if ($this->authorizationChecker->isGranted($adminRole, $user)) {
                return true;
            }
        }

        return false;
    }

    protected function hasSignedTerms(): bool
    {
        // todo
        return false;
    }

    private function isTheAttributeMatchHasSignedTerms(string $attribute): bool
    {
        return $attribute !== '' && strpos(self::HAS_SIGNED_TERMS_PREFIX, $attribute) === 0;
    }
}
