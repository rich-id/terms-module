<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Event\TermsEvent;
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

    public function dispatchTermsEvent(TermsEvent $event): void
    {
        $this->eventDispatcher->dispatch($event);
    }
}
