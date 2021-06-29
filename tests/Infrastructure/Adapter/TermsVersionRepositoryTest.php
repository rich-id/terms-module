<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\Adapter\TermsVersionRepository;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\TermsVersionRepository
 * @TestConfig("fixtures")
 */
final class TermsVersionRepositoryTest extends TestCase
{
    /** @var TermsVersionRepository */
    public $adapter;

    public function testFindLastSignedVersionForTermsSubjectForUser42(): void
    {
        $lastVersionSigned = $this->adapter->findLastSignedVersionForTermsSubject(
            'terms-1',
            DummySubject::create('user', '42')
        );

        /* @phpstan-ignore-next-line */
        $this->assertSame(2, $lastVersionSigned->getVersion());
    }

    public function testFindLastSignedVersionForTermsSubjectNotExistingSubject(): void
    {
        $lastVersionSigned = $this->adapter->findLastSignedVersionForTermsSubject(
            'terms-1',
            DummySubject::create('user', '999')
        );

        $this->assertNull($lastVersionSigned);
    }

    public function testFindLastSignedVersionForTermsSubjectNotExistingTerms(): void
    {
        $lastVersionSigned = $this->adapter->findLastSignedVersionForTermsSubject(
            'terms-99999',
            DummySubject::create('user', '42')
        );

        $this->assertNull($lastVersionSigned);
    }

    public function testRemoveTermsVersion(): void
    {
        $this->assertCount(4, $this->adapter->findAll());

        $termsVersion = $this->adapter->find($this->getReference(TermsVersion::class, 'v4-terms-1'));

        /* @phpstan-ignore-next-line */
        $this->adapter->removeTermsVersion($termsVersion);

        $this->assertCount(3, $this->adapter->findAll());
    }
}
