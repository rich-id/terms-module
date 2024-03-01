<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Fetcher;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Fetcher\GetLastPublishedTermsVersion;

/** @covers \RichId\TermsModuleBundle\Domain\Fetcher\GetLastPublishedTermsVersion */
#[TestConfig('fixtures')]
final class GetLastPublishedTermsVersionTest extends TestCase
{
    /** @var GetLastPublishedTermsVersion */
    public $fetcher;

    public function testFetcherTermsNotFound(): void
    {
        $this->expectException(NotFoundTermsException::class);
        $this->expectExceptionMessage('Not found terms terms-999.');

        ($this->fetcher)('terms-999');
    }

    public function testFetcherTermsNotPublished(): void
    {
        $this->expectException(NotPublishedTermsException::class);
        $this->expectExceptionMessage('Terms terms-2 is not published.');

        ($this->fetcher)('terms-2');
    }

    public function testFetcherTermsWithoutLastVersion(): void
    {
        $this->expectException(TermsHasNoPublishedVersionException::class);
        $this->expectExceptionMessage('Terms terms-3 hasn\'t published version.');

        ($this->fetcher)('terms-3');
    }

    public function testFetcher(): void
    {
        $termsVersion = ($this->fetcher)('terms-1');

        $this->assertInstanceOf(TermsVersion::class, $termsVersion);
        $this->assertSame(3, $termsVersion->getVersion());
        $this->assertSame('terms-1', $termsVersion->getTerms()->getSlug());
    }
}
