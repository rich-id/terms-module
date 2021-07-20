<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Stubs;

use Psr\Log\LoggerInterface;
use RichCongress\WebTestBundle\OverrideService\AbstractOverrideService;

final class LoggerStub extends AbstractOverrideService implements LoggerInterface
{
    /** @var string|array<string> */
    public static $overridenServices = LoggerInterface::class;

    /** @var array<array> */
    protected $logs = [];

    public function emergency($message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->logs[] = [$level, $message, $context];

        /* @phpstan-ignore-next-line */
        $this->innerService->log($level, $message, $context);
    }

    /** @return array<array> */
    public function getLogs(): array
    {
        return $this->logs;
    }
}
