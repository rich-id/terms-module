<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var SymfonyEventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatchTermsSignedEvent(TermsSignedEvent $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchTermsVersionCreatedEvent(TermsVersionCreatedEvent $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchTermsVersionEnabledEvent(TermsVersionEnabledEvent $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchTermsVersionUpdatedEvent(TermsVersionUpdatedEvent $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
