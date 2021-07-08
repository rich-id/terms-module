<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Repository;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository
 * @TestConfig("fixtures")
 */
final class TermsVersionRepositoryTest extends TestCase
{
    /** @var TermsVersionRepository */
    public $repository;

    public function testFindLastSignedVersionForTermsSubjectForUser42(): void
    {
        $lastVersionSigned = $this->repository->findLastSignedVersionForTermsSubject(
            'terms-1',
            DummySubject::create('user', '42')
        );

        /* @phpstan-ignore-next-line */
        $this->assertSame(2, $lastVersionSigned->getVersion());
    }

    public function testFindLastSignedVersionForTermsSubjectNotExistingSubject(): void
    {
        $lastVersionSigned = $this->repository->findLastSignedVersionForTermsSubject(
            'terms-1',
            DummySubject::create('user', '999')
        );

        $this->assertNull($lastVersionSigned);
    }
}
