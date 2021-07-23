<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Event;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Event\TermsUnpublishedEvent;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Event\TermsUnpublishedEvent
 * @TestConfig("kernel")
 */
final class TermsUnpublishedEventTest extends TestCase
{
    public function testEvent(): void
    {
        $terms = new Terms();
        $event = new TermsUnpublishedEvent($terms);

        $this->assertSame($terms, $event->getTerms());
    }
}
