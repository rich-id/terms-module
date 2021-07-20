<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionEnabledEvent;
use RichId\TermsModuleBundle\Domain\Exception\AlreadyEnabledTermsVersionException;
use RichId\TermsModuleBundle\Domain\UseCase\ActivateTermsVersion;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\ActivateTermsVersion
 * @TestConfig("fixtures")
 */
final class ActivateTermsVersionTest extends TestCase
{
    /** @var ActivateTermsVersion */
    public $useCase;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    public function testUseCaseTermsVersionAlreadyEnabled(): void
    {
        $this->expectException(AlreadyEnabledTermsVersionException::class);
        $this->expectExceptionMessage('Version 42 of terms my_terms is already enabled.');

        $terms = new Terms();
        $terms->setSlug('my_terms');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(42);
        $termsVersion->enable();

        ($this->useCase)($termsVersion);
    }

    public function testUseCase(): void
    {
        $terms = $this->getReference(Terms::class, '1');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setVersion(42);
        $termsVersion->setTitle('Title');
        $termsVersion->setContent('Content');

        ($this->useCase)($termsVersion);

        $this->assertTrue($termsVersion->isEnabled());
        $this->assertNotNull($termsVersion->getPublicationDate());

        $this->assertCount(2, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsVersionEnabledEvent::class, $event);
        $this->assertSame($termsVersion, $event->getTermsVersion());
    }
}
