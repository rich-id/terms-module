<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\EventListener;

use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Port\LoggerInterface;

final class TermsSignedEventListener
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(TermsSignedEvent $event): void
    {
        $termsVersion = $event->getTermsVersion();
        $terms = $termsVersion->getTerms();

        $this->logger->logTermsSigned($terms->getSlug() ?? '', $event->getSubject(), $event->isAccepted());
    }
}
