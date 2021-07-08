<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\Adapter\EventDispatcher;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\EventDispatcher
 * @TestConfig("fixtures")
 */
final class EventDispatcherTest extends TestCase
{
    /** @var EventDispatcher */
    public $adapter;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    public function testDispatchTermsSignedEvent(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $response = new Response();
        $subject = DummySubject::create('user', '42');

        $event = new TermsSignedEvent($termsVersion, $subject, true, $response);

        $this->adapter->dispatchTermsSignedEvent($event);

        $this->assertCount(1, $this->eventDispatcherStub->getEvents());
        $this->assertSame($event, $this->eventDispatcherStub->getEvents()[0]);
    }
}