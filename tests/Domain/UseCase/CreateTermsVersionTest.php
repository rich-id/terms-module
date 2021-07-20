<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent;
use RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException;
use RichId\TermsModuleBundle\Domain\UseCase\CreateTermsVersion;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\CreateTermsVersion
 * @TestConfig("fixtures")
 */
final class CreateTermsVersionTest extends TestCase
{
    /** @var CreateTermsVersion */
    public $useCase;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    public function testUseCaseTermsWithoutVersion(): void
    {
        $this->expectException(CannotAddVersionToTermsException::class);
        $this->expectExceptionMessage('Cannot add new version to the terms my_terms.');

        $terms = new Terms();
        $terms->setSlug('my_terms');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(42);

        ($this->useCase)($termsVersion);
    }

    public function testUseCaseLastVersionNotPublished(): void
    {
        $this->expectException(CannotAddVersionToTermsException::class);
        $this->expectExceptionMessage('Cannot add new version to the terms my_terms.');

        $terms = new Terms();
        $terms->setSlug('my_terms');

        $termsVersion = new TermsVersion();
        $termsVersion->setVersion(42);
        $termsVersion->setTerms($terms);
        $terms->addVersion($termsVersion);

        ($this->useCase)($termsVersion);
    }

    public function testUseCase(): void
    {
        $terms = $this->getReference(Terms::class, '2');

        $termsVersion1 = new TermsVersion();
        $termsVersion1->setTitle('Title 1');
        $termsVersion1->setContent('Content 1');
        $termsVersion1->setPublicationDate(new \DateTime('today - 2 days'));
        $termsVersion1->setVersion(42);
        $termsVersion1->enable();
        $termsVersion1->setTerms($terms);
        $terms->addVersion($termsVersion1);

        $termsVersion2 = new TermsVersion();
        $termsVersion2->setTitle('Title 2');
        $termsVersion2->setContent('Content 2');
        $termsVersion2->setPublicationDate(new \DateTime('today - 1 days'));
        $termsVersion2->setVersion(43);
        $termsVersion2->enable();
        $termsVersion2->setTerms($terms);
        $terms->addVersion($termsVersion2);

        $this->getManager()->persist($termsVersion1);
        $this->getManager()->persist($termsVersion2);

        ($this->useCase)($termsVersion1);

        $this->assertCount(1, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsVersionCreatedEvent::class, $event);

        $newTermsVersion = $event->getTermsVersion();

        $this->assertInstanceOf(TermsVersion::class, $event->getTermsVersion());
        $this->assertNotNull($newTermsVersion->getId());
        $this->assertSame('Title 1', $newTermsVersion->getTitle());
        $this->assertSame('Content 1', $newTermsVersion->getCOntent());
        $this->assertSame(44, $newTermsVersion->getVersion());
        $this->assertSame($terms, $newTermsVersion->getTerms());
        $this->assertNull($newTermsVersion->getPublicationDate());
        $this->assertFalse($newTermsVersion->isEnabled());
    }
}
