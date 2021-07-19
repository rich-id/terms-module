<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms
 * @TestConfig("fixtures")
 */
final class HasSignedTermsTest extends TestCase
{
    /** @var HasSignedTerms */
    public $useCase;

    public function testUseCaseTermsNotExist(): void
    {
        $this->expectException(NotFoundTermsException::class);
        $this->expectDeprecationMessage('Not found terms terms-999.');

        $subject = DummySubject::create('user', '42');
        ($this->useCase)('terms-999', $subject);
    }

    public function testUseCaseTermsNotPublished(): void
    {
        $subject = DummySubject::create('user', '42');
        $code = ($this->useCase)('terms-2', $subject);

        $this->assertSame(HasSignedTerms::HAS_NOT_SIGNED, $code);
    }

    public function testUseCaseSubjectNotExist(): void
    {
        $subject = DummySubject::create('user', '999');
        $code = ($this->useCase)('terms-1', $subject);

        $this->assertSame(HasSignedTerms::HAS_NOT_SIGNED, $code);
    }

    public function testUseCaseHasSignOldVersion(): void
    {
        $subject = DummySubject::create('user', '42');
        $code = ($this->useCase)('terms-1', $subject);

        $this->assertSame(HasSignedTerms::HAS_SIGNED_OLD_VERSION, $code);
    }

    public function testUseCaseHasSignLatestVersion(): void
    {
        $subject = DummySubject::create('user', '43');
        $code = ($this->useCase)('terms-1', $subject);

        $this->assertSame(HasSignedTerms::HAS_SIGNED_LATEST_VERSION, $code);
    }
}
