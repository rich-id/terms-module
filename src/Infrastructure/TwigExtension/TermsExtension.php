<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\TwigExtension;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\UseCase\GenerateSigningRoute;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TermsExtension extends AbstractExtension
{
    /** @var HasSignedTerms */
    protected $hasSignedTerms;

    /** @var GenerateSigningRoute */
    protected $generateSigningRoute;

    public function __construct(HasSignedTerms $hasSignedTerms, GenerateSigningRoute $generateSigningRoute)
    {
        $this->hasSignedTerms = $hasSignedTerms;
        $this->generateSigningRoute = $generateSigningRoute;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('generateSigningRoute', [$this, 'generateSigningRoute']),
            new TwigFunction('hasSignedTerms', [$this, 'hasSignedTerms']),
        ];
    }

    public function generateSigningRoute(Terms $terms, TermsSubjectInterface $subject): string
    {
        return ($this->generateSigningRoute)($terms->getSlug(), $subject);
    }

    public function hasSignedTerms(Terms $terms, TermsSubjectInterface $subject): int
    {
        return ($this->hasSignedTerms)($terms->getSlug(), $subject);
    }
}
