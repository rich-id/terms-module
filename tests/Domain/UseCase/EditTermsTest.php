<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsPublishedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsUnpublishedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;
use RichId\TermsModuleBundle\Domain\Exception\InvalidTermsEditionException;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\UseCase\EditTerms;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\EditTerms
 * @TestConfig("fixtures")
 */
final class EditTermsTest extends TestCase
{
    /** @var EditTerms */
    public $useCase;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    public function testUseCaseInvalidModel(): void
    {
        $this->expectException(InvalidTermsEditionException::class);
        $this->expectExceptionMessage('Invalid model TermsEdition.');

        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');

        $model = new TermsEdition($termsVersion);
        $model->setContent(null);

        ($this->useCase)($model);
    }

    public function testUseCaseNoChanges(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');
        $oldTermsVersion = clone $termsVersion;

        $model = new TermsEdition($termsVersion);

        ($this->useCase)($model);

        $this->assertSame($oldTermsVersion->getId(), $termsVersion->getId());
        $this->assertSame($oldTermsVersion->getTitle(), $termsVersion->getTitle());
        $this->assertSame($oldTermsVersion->getContent(), $termsVersion->getContent());
        $this->assertSame($oldTermsVersion->getPublicationDate(), $termsVersion->getPublicationDate());
        $this->assertSame($oldTermsVersion->isEnabled(), $termsVersion->isEnabled());
        $this->assertSame($oldTermsVersion->getTerms()->isPublished(), $termsVersion->getTerms()->isPublished());

        $this->assertCount(2, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsVersionUpdatedEvent::class, $event);
    }

    public function testUseCaseWithChanges(): void
    {
        $date = new \DateTime();

        $termsVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');

        $model = new TermsEdition($termsVersion);
        $model->setTitle('New title');
        $model->setContent('New content');
        $model->setPublicationDate($date);
        $model->setIsTermsPublished(false);

        ($this->useCase)($model);

        $this->assertSame('New title', $termsVersion->getTitle());
        $this->assertSame('New content', $termsVersion->getContent());
        $this->assertSame($date, $termsVersion->getPublicationDate());
        $this->assertFalse($termsVersion->isEnabled());
        $this->assertFalse($termsVersion->getTerms()->isPublished());

        $this->assertCount(2, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(2, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsVersionUpdatedEvent::class, $event);

        $event = $this->eventDispatcherStub->getEvents()[1];
        $this->assertInstanceOf(TermsUnpublishedEvent::class, $event);
    }

    public function testUseCaseWithChangesAndActivation(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');

        $model = new TermsEdition($termsVersion);
        $model->setTitle('New title');
        $model->setContent('New content');
        $model->setIsTermsPublished(false);
        $model->setNeedVersionActivation(true);

        ($this->useCase)($model);

        $this->assertSame('New title', $termsVersion->getTitle());
        $this->assertSame('New content', $termsVersion->getContent());
        $this->assertNotNull($termsVersion->getPublicationDate());
        $this->assertTrue($termsVersion->isEnabled());
        $this->assertFalse($termsVersion->getTerms()->isPublished());

        $this->assertCount(4, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(3, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsVersionEnabledEvent::class, $event);

        $event = $this->eventDispatcherStub->getEvents()[1];
        $this->assertInstanceOf(TermsVersionUpdatedEvent::class, $event);

        $event = $this->eventDispatcherStub->getEvents()[2];
        $this->assertInstanceOf(TermsUnpublishedEvent::class, $event);
    }

    public function testUseCaseFirstTermsVersionAndTermsPublished(): void
    {
        $terms = $this->getReference(Terms::class, '2');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(1);
        $terms->addVersion($termsVersion);

        $model = new TermsEdition($termsVersion);
        $model->setTitle('New title');
        $model->setContent('New content');
        $model->setIsTermsPublished(true);

        ($this->useCase)($model);

        $this->assertSame('New title', $termsVersion->getTitle());
        $this->assertSame('New content', $termsVersion->getContent());
        $this->assertNotNull($termsVersion->getPublicationDate());
        $this->assertTrue($termsVersion->isEnabled());
        $this->assertTrue($termsVersion->getTerms()->isPublished());

        $this->assertCount(4, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(3, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsVersionUpdatedEvent::class, $event);

        $event = $this->eventDispatcherStub->getEvents()[1];
        $this->assertInstanceOf(TermsVersionEnabledEvent::class, $event);

        $event = $this->eventDispatcherStub->getEvents()[2];
        $this->assertInstanceOf(TermsPublishedEvent::class, $event);
    }
}
