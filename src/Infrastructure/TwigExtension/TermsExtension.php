<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\TwigExtension;

use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignTerms;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TermsExtension extends AbstractExtension
{
    /** @var HasSignTerms */
    protected $hasSignTerms;

    public function __construct(HasSignTerms $hasSignTerms)
    {
        $this->hasSignTerms = $hasSignTerms;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasSignTerms', [$this, 'hasSignTerms']),
        ];
    }

    public function hasSignTerms(string $termsSlug, string $subjectType, string $subjectIdentifier): int
    {
        return ($this->hasSignTerms)($termsSlug, DummySubject::create($subjectType, $subjectIdentifier));
    }
}
