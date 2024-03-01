<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\EventListener;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;
use RichId\TermsModuleBundle\Domain\EventListener\AutopublishFirstTermsVersionEventListener;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;

/** @covers \RichId\TermsModuleBundle\Domain\EventListener\AutopublishFirstTermsVersionEventListener */
#[TestConfig('fixtures')]
final class AutopublishFirstTermsVersionEventListenerTest extends TestCase
{
    /** @var AutopublishFirstTermsVersionEventListener */
    public $listener;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    public function testListenerNotPublishedTerms(): void
    {
        $terms = new Terms();
        $terms->setName('Terms');
        $terms->setSlug('my_terms');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $terms->addVersion($termsVersion);

        $event = new TermsVersionUpdatedEvent($termsVersion);

        ($this->listener)($event);

        $this->assertFalse($termsVersion->isEnabled());
        $this->assertNull($termsVersion->getPublicationDate());
        $this->assertEmpty($this->entityManagerStub->getPersistedEntities());
    }

    public function testListenerWithMultipleVersion(): void
    {
        $terms = new Terms();
        $terms->setName('Terms');
        $terms->setSlug('my_terms');
        $terms->setIsPublished(true);

        $termsVersion1 = new TermsVersion();
        $termsVersion1->setTerms($terms);
        $terms->addVersion($termsVersion1);

        $termsVersion2 = new TermsVersion();
        $termsVersion2->setTerms($terms);
        $terms->addVersion($termsVersion2);

        $event = new TermsVersionUpdatedEvent($termsVersion2);

        ($this->listener)($event);

        $this->assertFalse($termsVersion2->isEnabled());
        $this->assertNull($termsVersion2->getPublicationDate());
        $this->assertEmpty($this->entityManagerStub->getPersistedEntities());
    }

    public function testListenerAlreadyEnabledVersion(): void
    {
        $terms = new Terms();
        $terms->setName('Terms');
        $terms->setSlug('my_terms');
        $terms->setIsPublished(true);

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->enable();
        $terms->addVersion($termsVersion);

        $event = new TermsVersionUpdatedEvent($termsVersion);

        ($this->listener)($event);

        $this->assertTrue($termsVersion->isEnabled());
        $this->assertNull($termsVersion->getPublicationDate());
        $this->assertEmpty($this->entityManagerStub->getPersistedEntities());
    }

    public function testListenerActivateVersion(): void
    {
        $terms = $this->getReference(Terms::class, '3');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(1);
        $termsVersion->setTitle('Title');
        $termsVersion->setContent('Content');
        $terms->addVersion($termsVersion);

        $event = new TermsVersionUpdatedEvent($termsVersion);

        ($this->listener)($event);

        $this->assertTrue($termsVersion->isEnabled());
        $this->assertInstanceOf(\DateTime::class, $termsVersion->getPublicationDate());
        $this->assertCount(2, $this->entityManagerStub->getPersistedEntities());
    }
}
