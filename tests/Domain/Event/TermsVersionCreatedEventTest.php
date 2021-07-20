<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Event;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent
 * @TestConfig("kernel")
 */
final class TermsVersionCreatedEventTest extends TestCase
{
    public function testEvent(): void
    {
        $termsVersion = new TermsVersion();
        $event = new TermsVersionCreatedEvent($termsVersion);

        $this->assertSame($termsVersion, $event->getTermsVersion());
    }
}
