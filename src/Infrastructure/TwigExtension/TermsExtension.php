<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\TwigExtension;

use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TermsExtension extends AbstractExtension
{
    /** @var HasSignedTerms */
    protected $hasSignedTerms;

    public function __construct(HasSignedTerms $hasSignedTerms)
    {
        $this->hasSignedTerms = $hasSignedTerms;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasSignedTerms', [$this, 'hasSignedTerms']),
        ];
    }

    public function hasSignedTerms(string $termsSlug, string $subjectType, string $subjectIdentifier): int
    {
        return ($this->hasSignedTerms)($termsSlug, DummySubject::create($subjectType, $subjectIdentifier));
    }
}
