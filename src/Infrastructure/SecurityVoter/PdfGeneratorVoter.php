<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\SecurityVoter;

use RichId\TermsModuleBundle\Domain\Pdf\TermsVersionSignaturePdfGeneratorManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PdfGeneratorVoter extends Voter
{
    public const MODULE_TERMS_HAS_CONFIGURED_GENERATOR = 'MODULE_TERMS_HAS_CONFIGURED_GENERATOR';

    /** @var TermsVersionSignaturePdfGeneratorManager */
    protected $termsVersionSignaturePdfGeneratorManager;

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(TermsVersionSignaturePdfGeneratorManager $termsVersionSignaturePdfGeneratorManager)
    {
        $this->termsVersionSignaturePdfGeneratorManager = $termsVersionSignaturePdfGeneratorManager;
    }

    protected function supports($attribute, $subject): bool
    {
        return $attribute === self::MODULE_TERMS_HAS_CONFIGURED_GENERATOR;
    }

    /** @param string $attribute */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }
        
        return $this->termsVersionSignaturePdfGeneratorManager->hasConfiguredGenerator();
    }
}
