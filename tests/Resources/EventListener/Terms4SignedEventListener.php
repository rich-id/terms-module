<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\EventListener;

use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class Terms4SignedEventListener
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(TermsSignedEvent $event): void
    {
        $tersmVersion = $event->getTermsVersion();
        $terms = $tersmVersion->getTerms();

        if ($terms->getSlug() !== 'terms-4') {
            return;
        }

        $event->setResponse(
            new RedirectResponse(
                $this->router->generate('app_other_front')
            )
        );
    }
}
