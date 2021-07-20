<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Fetcher;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign
 * @TestConfig("fixtures")
 */
final class GetTermsVersionToSignTest extends TestCase
{
    /** @var GetTermsVersionToSign */
    public $fetcher;

    public function testFetcherTermsNotFound(): void
    {
        $this->expectException(NotFoundTermsException::class);
        $this->expectExceptionMessage('Not found terms terms-999.');

        $subject = DummySubject::create('user', '42');

        ($this->fetcher)('terms-999', $subject);
    }

    public function testFetcherTermsNotPublished(): void
    {
        $this->expectException(NotPublishedTermsException::class);
        $this->expectExceptionMessage('Terms terms-2 is not published.');

        $subject = DummySubject::create('user', '42');

        ($this->fetcher)('terms-2', $subject);
    }

    public function testFetcherTermsWithoutLastVersion(): void
    {
        $this->expectException(TermsHasNoPublishedVersionException::class);
        $this->expectExceptionMessage('Terms terms-3 hasn\'t published version.');

        $subject = DummySubject::create('user', '42');

        ($this->fetcher)('terms-3', $subject);
    }

    public function testFetcherSubjectHasALreadySignLastVersion(): void
    {
        $this->expectException(AlreadySignLastTermsVersionException::class);
        $this->expectExceptionMessage('Terms terms-1 is already sign by this subject.');

        $subject = DummySubject::create('user', '43');

        ($this->fetcher)('terms-1', $subject);
    }

    public function testFetcher(): void
    {
        $subject = DummySubject::create('user', '42');

        $termsVersion = ($this->fetcher)('terms-1', $subject);

        $this->assertInstanceOf(TermsVersion::class, $termsVersion);
        $this->assertSame(3, $termsVersion->getVersion());
        $this->assertSame('terms-1', $termsVersion->getTerms()->getSlug());
    }
}
