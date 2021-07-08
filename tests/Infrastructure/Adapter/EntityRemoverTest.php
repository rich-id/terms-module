<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\Adapter\EntityRemover;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\TermsVersionRepository
 * @TestConfig("fixtures")
 */
final class EntityRemoverTest extends TestCase
{
    /** @var EntityRemover */
    public $adapter;

    /** @var TermsVersionRepository */
    public $termsVersionRepository;

    public function testRemoveTermsVersion(): void
    {
        $this->assertCount(6, $this->termsVersionRepository->findAll());

        $termsVersion = $this->termsVersionRepository->find($this->getReference(TermsVersion::class, 'v4-terms-1'));

        /* @phpstan-ignore-next-line */
        $this->adapter->removeTermsVersion($termsVersion);

        $this->assertCount(5, $this->termsVersionRepository->findAll());
    }
}
