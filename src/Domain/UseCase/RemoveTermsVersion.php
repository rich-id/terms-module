<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionDeletedEvent;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Port\EntityRemoverInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;

class RemoveTermsVersion
{
    /** @var EntityRemoverInterface */
    protected $entityRemover;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(EntityRemoverInterface $entityRemover, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityRemover = $entityRemover;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(TermsVersion $termsVersion): void
    {
        if ($termsVersion->isEnabled()) {
            throw new EnabledVersionCannotBeDeletedException($termsVersion);
        }

        $this->entityRemover->removeTermsVersion($termsVersion);

        $this->eventDispatcher->dispatchTermsVersionDeletedEvent(
            new TermsVersionDeletedEvent($termsVersion)
        );
    }
}
