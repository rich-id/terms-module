<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Listener;

use RichId\TermsModuleBundle\Domain\Exception\SubjectNeedToSignTermsException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

class RedirectToSigningPageOnAccessDenied
{
    /** @var RouterInterface */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof SubjectNeedToSignTermsException) {
            return;
        }

        $event->setResponse(
            new RedirectResponse(
                $this->router->generate(
                    'module_terms_sign',
                    [
                        'termsSlug'  => $exception->getTermsSlug(),
                        'type'       => $exception->getSubject()->getTermsSubjectType(),
                        'identifier' => $exception->getSubject()->getTermsSubjectIdentifier(),
                    ]
                )
            )
        );
    }
}
