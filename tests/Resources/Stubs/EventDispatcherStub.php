<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Stubs;

use RichCongress\WebTestBundle\OverrideService\AbstractOverrideService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherStub extends AbstractOverrideService implements EventDispatcherInterface
{
    /** @var string|array<string> */
    public static $overridenServices = EventDispatcherInterface::class;

    /** @var array<mixed> */
    protected $events = [];

    public function dispatch($event, string $eventName = null): object
    {
        $this->events[] = $event;

        /* @phpstan-ignore-next-line */
        return $this->innerService->dispatch($event, $eventName);
    }

    /** @return array<mixed> */
    public function getEvents(): array
    {
        return $this->events;
    }
}
