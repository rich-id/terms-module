<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\EventListener;

use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\UseCase\ActivateTermsVersion;

final class TermsVersionEnabledEventListener
{
    /** @var ActivateTermsVersion */
    private $activateTermsVersion;

    /** @var EntityRecoderInterface */
    private $entityRecoder;

    public function __construct(ActivateTermsVersion $activateTermsVersion, EntityRecoderInterface $entityRecoder)
    {
        $this->activateTermsVersion = $activateTermsVersion;
        $this->entityRecoder = $entityRecoder;
    }

    public function __invoke(TermsVersionEnabledEvent $event): void
    {
        $termsVersion = $event->getTermsVersion();

        if ($termsVersion->getPublicationDate() !== null) {
            return;
        }

        $termsVersion->setPublicationDate(new \DateTime('today midnight'));
        $this->entityRecoder->saveTermsVersion($termsVersion);
    }
}
