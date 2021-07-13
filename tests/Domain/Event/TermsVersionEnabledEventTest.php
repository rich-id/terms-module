<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Event;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent
 * @TestConfig("kernel")
 */
final class TermsVersionEnabledEventTest extends TestCase
{
    public function testEvent(): void
    {
        $termsVersion = new TermsVersion();
        $event = new TermsVersionEnabledEvent($termsVersion);

        $this->assertSame($termsVersion, $event->getTermsVersion());
    }
}
