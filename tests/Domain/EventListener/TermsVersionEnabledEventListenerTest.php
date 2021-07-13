<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\EventListener;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\EventListener\TermsVersionEnabledEventListener;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;

/**
 * @covers \RichId\TermsModuleBundle\Domain\EventListener\TermsVersionEnabledEventListener
 * @TestConfig("kernel")
 */
final class TermsVersionEnabledEventListenerTest extends TestCase
{
    /** @var TermsVersionEnabledEventListener */
    public $listener;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    public function testListenerWithExistingPublicationDate(): void
    {
        $date = new \DateTime();

        $termsVersion = new TermsVersion();
        $termsVersion->setPublicationDate($date);

        $event = new TermsVersionEnabledEvent($termsVersion);

        ($this->listener)($event);

        $this->assertSame($date, $termsVersion->getPublicationDate());
        $this->assertEmpty($this->entityManagerStub->getPersistedEntities());
    }

    public function testListenerWithoutExistingPublicationDate(): void
    {
        $termsVersion = new TermsVersion();

        $event = new TermsVersionEnabledEvent($termsVersion);

        ($this->listener)($event);

        $this->assertInstanceOf(\DateTime::class, $termsVersion->getPublicationDate());
        $this->assertCount(1, $this->entityManagerStub->getPersistedEntities());
    }
}
