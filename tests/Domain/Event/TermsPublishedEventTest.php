<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Event;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Event\TermsPublishedEvent;

/** @covers \RichId\TermsModuleBundle\Domain\Event\TermsPublishedEvent */
 #[TestConfig('kernel')]
final class TermsPublishedEventTest extends TestCase
{
    public function testEvent(): void
    {
        $terms = new Terms();
        $event = new TermsPublishedEvent($terms);

        $this->assertSame($terms, $event->getTerms());
    }
}
