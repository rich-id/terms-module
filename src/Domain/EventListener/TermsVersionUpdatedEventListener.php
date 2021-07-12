<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\EventListener;

use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;
use RichId\TermsModuleBundle\Domain\UseCase\ActivateTermsVersion;

final class TermsVersionUpdatedEventListener
{
    /** @var ActivateTermsVersion */
    private $activateTermsVersion;

    public function __construct(ActivateTermsVersion $activateTermsVersion)
    {
        $this->activateTermsVersion = $activateTermsVersion;
    }

    public function __invoke(TermsVersionUpdatedEvent $event): void
    {
        $termsVersion = $event->getTermsVersion();
        $terms = $termsVersion->getTerms();

        if ($terms->isPublished() && $terms->getVersions()->count() === 1 && !$termsVersion->isEnabled()) {
            ($this->activateTermsVersion)($termsVersion);
        }
    }
}
