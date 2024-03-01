<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionDeletedEvent;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Exception\FirstVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\UseCase\RemoveTermsVersion;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;

/** @covers \RichId\TermsModuleBundle\Domain\UseCase\RemoveTermsVersion */
#[TestConfig('fixtures')]
final class RemoveTermsVersionTest extends TestCase
{
    /** @var RemoveTermsVersion */
    public $useCase;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    public function testUseCaseTermsVersionEnabled(): void
    {
        $this->expectException(EnabledVersionCannotBeDeletedException::class);
        $this->expectExceptionMessage('Version 42 of terms my_terms cannot be deleted.');

        $terms = new Terms();
        $terms->setSlug('my_terms');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(42);
        $termsVersion->enable();

        ($this->useCase)($termsVersion);
    }

    public function testUseCaseFirstVersion(): void
    {
        $this->expectException(FirstVersionCannotBeDeletedException::class);
        $this->expectExceptionMessage('First version of terms my_terms cannot be deleted.');

        $terms = new Terms();
        $terms->setSlug('my_terms');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(42);

        ($this->useCase)($termsVersion);
    }

    public function testUseCase(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');

        ($this->useCase)($termsVersion);

        $this->assertCount(1, $this->entityManagerStub->getRemovedEntities());
        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsVersionDeletedEvent::class, $event);
        $this->assertSame($termsVersion, $event->getTermsVersion());
    }
}
