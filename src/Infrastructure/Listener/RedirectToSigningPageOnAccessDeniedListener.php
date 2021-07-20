<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Listener;

use RichId\TermsModuleBundle\Domain\Exception\SubjectNeedToSignTermsException;
use RichId\TermsModuleBundle\Domain\UseCase\GenerateSigningRoute;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class RedirectToSigningPageOnAccessDeniedListener
{
    /** @var GenerateSigningRoute */
    protected $generateSigningRoute;

    public function __construct(GenerateSigningRoute $generateSigningRoute)
    {
        $this->generateSigningRoute = $generateSigningRoute;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof SubjectNeedToSignTermsException) {
            return;
        }

        $event->setResponse(
            new RedirectResponse(
                ($this->generateSigningRoute)($exception->getTermsSlug(), $exception->getSubject())
            )
        );
    }
}
