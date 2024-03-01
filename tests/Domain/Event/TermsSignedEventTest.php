<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Event;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use Symfony\Component\HttpFoundation\Response;

/** @covers \RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent */
 #[TestConfig('kernel')]
final class TermsSignedEventTest extends TestCase
{
    public function testEvent(): void
    {
        $termsVersion = new TermsVersion();
        $response = new Response();
        $subject = DummySubject::create('user', '42');

        $event = new TermsSignedEvent($termsVersion, $subject, true, $response);

        $this->assertSame($termsVersion, $event->getTermsVersion());
        $this->assertSame($subject, $event->getSubject());
        $this->assertTrue($event->isAccepted());
        $this->assertSame($response, $event->getResponse());

        $otherResponse = new Response();
        $event->setResponse($otherResponse);
        $this->assertSame($otherResponse, $event->getResponse());
    }
}
