<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;

interface EventDispatcherInterface
{
    public function dispatchTermsSignedEvent(TermsSignedEvent $event): void;

    public function dispatchTermsVersionCreatedEvent(TermsVersionCreatedEvent $event): void;

    public function dispatchTermsVersionEnabledEvent(TermsVersionEnabledEvent $event): void;

    public function dispatchTermsVersionUpdatedEvent(TermsVersionUpdatedEvent $event): void;
}
