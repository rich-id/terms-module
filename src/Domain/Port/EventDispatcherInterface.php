<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Event\TermsEvent;

interface EventDispatcherInterface
{
    public function dispatchTermsEvent(TermsEvent $event): void;
}
