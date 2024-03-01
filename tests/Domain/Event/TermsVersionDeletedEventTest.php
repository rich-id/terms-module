<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Event;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionDeletedEvent;

/** @covers \RichId\TermsModuleBundle\Domain\Event\TermsVersionDeletedEvent */
 #[TestConfig('kernel')]
final class TermsVersionDeletedEventTest extends TestCase
{
    public function testEvent(): void
    {
        $termsVersion = new TermsVersion();
        $event = new TermsVersionDeletedEvent($termsVersion);

        $this->assertSame($termsVersion, $event->getTermsVersion());
    }
}
