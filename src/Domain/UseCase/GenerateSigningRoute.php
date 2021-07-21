<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use Symfony\Component\Routing\RouterInterface;

class GenerateSigningRoute
{
    /** @var RouterInterface */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): string
    {
        return $this->router->generate(
            'module_terms_sign',
            [
                'termsSlug'  => $termsSlug,
                'type'       => $subject->getTermsSubjectType(),
                'identifier' => $subject->getTermsSubjectIdentifier(),
            ]
        );
    }
}
