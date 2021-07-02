<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignTerms;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\HasSignTerms
 * @TestConfig("fixtures")
 */
final class HasSignTermsTest extends TestCase
{
    /** @var HasSignTerms */
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

        $this->assertSame(HasSignTerms::HAS_NOT_SIGN, $code);
    }

    public function testUseCaseSubjectNotExist(): void
    {
        $subject = DummySubject::create('user', '999');
        $code = ($this->useCase)('terms-1', $subject);

        $this->assertSame(HasSignTerms::HAS_NOT_SIGN, $code);
    }

    public function testUseCaseHasSignOldVersion(): void
    {
        $subject = DummySubject::create('user', '42');
        $code = ($this->useCase)('terms-1', $subject);

        $this->assertSame(HasSignTerms::HAS_SIGN_OLD_VERSION, $code);
    }

    public function testUseCaseHasSignLatestVersion(): void
    {
        $subject = DummySubject::create('user', '43');
        $code = ($this->useCase)('terms-1', $subject);

        $this->assertSame(HasSignTerms::HAS_SIGN_LATEST_VERSION, $code);
    }
}
