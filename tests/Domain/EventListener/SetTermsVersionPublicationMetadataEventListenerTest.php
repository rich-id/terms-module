<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\EventListener;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\EventListener\SetTermsVersionPublicationMetadataEventListener;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;

/** @covers \RichId\TermsModuleBundle\Domain\EventListener\SetTermsVersionPublicationMetadataEventListener */
#[TestConfig('fixtures')]
final class SetTermsVersionPublicationMetadataEventListenerTest extends TestCase
{
    /** @var SetTermsVersionPublicationMetadataEventListener */
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
        $terms = $this->getReference(Terms::class, '1');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(42);
        $termsVersion->setTitle('Title');
        $termsVersion->setContent('Content');

        $event = new TermsVersionEnabledEvent($termsVersion);

        ($this->listener)($event);

        $this->assertInstanceOf(\DateTime::class, $termsVersion->getPublicationDate());
        $this->assertCount(1, $this->entityManagerStub->getPersistedEntities());
    }
}
