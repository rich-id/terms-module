<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\Exception\AlreadyEnabledTermsVersionException;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;

class ActivateTermsVersion
{
    /** @var EntityRecoderInterface */
    protected $entityRecoder;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(EntityRecoderInterface $entityRecoder, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityRecoder = $entityRecoder;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(TermsVersion $termsVersion): void
    {
        if ($termsVersion->isEnabled()) {
            throw new AlreadyEnabledTermsVersionException($termsVersion);
        }

        $termsVersion->enable();
        $this->entityRecoder->saveTermsVersion($termsVersion);

        $this->eventDispatcher->dispatchTermsEvent(
            new TermsVersionEnabledEvent($termsVersion)
        );
    }
}
