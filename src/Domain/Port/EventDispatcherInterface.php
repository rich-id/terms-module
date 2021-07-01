<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;

interface EventDispatcherInterface
{
    public function dispatchTermsSignedEvent(TermsSignedEvent $event): void;
}
