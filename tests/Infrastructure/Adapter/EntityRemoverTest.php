<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\Adapter\EntityRemover;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;

/** @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\EntityRemover */
#[TestConfig('fixtures')]
final class EntityRemoverTest extends TestCase
{
    /** @var EntityRemover */
    public $adapter;

    /** @var TermsVersionRepository */
    public $termsVersionRepository;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    public function testRemoveTermsVersion(): void
    {
        $this->assertCount(7, $this->termsVersionRepository->findAll());

        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');

        $this->adapter->removeTermsVersion($termsVersion);

        $this->assertCount(1, $this->entityManagerStub->getRemovedEntities());

        // Skipped, waiting a correction in the test-framework
        //$this->assertCount(6, $this->termsVersionRepository->findAll());
    }
}
