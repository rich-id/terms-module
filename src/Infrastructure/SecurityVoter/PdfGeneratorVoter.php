<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\SecurityVoter;

use RichId\TermsModuleBundle\Domain\Entity\TermsUserInterface;
use RichId\TermsModuleBundle\Domain\Pdf\TermsVersionSignaturePdfGeneratorManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PdfGeneratorVoter extends Voter
{
    public const MODULE_TERMS_CAN_GENERATE_SIGNATURE_PDF = 'MODULE_TERMS_CAN_GENERATE_SIGNATURE_PDF';

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
        return $attribute === self::MODULE_TERMS_CAN_GENERATE_SIGNATURE_PDF;
    }

    /** @param string $attribute */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return $token->getUser() instanceof TermsUserInterface && $this->termsVersionSignaturePdfGeneratorManager->hasConfiguredGenerator();
    }
}
