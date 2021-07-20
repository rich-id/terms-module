<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\TwigExtension;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
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

    public function hasSignedTerms(Terms $terms, TermsSubjectInterface $subject): int
    {
        return ($this->hasSignedTerms)($terms->getSlug(), $subject);
    }
}
