<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedLastTermsVersion;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\HasSignedLastTermsVersion
 * @TestConfig("fixtures")
 */
final class HasSignedLastTermsVersionTest extends TestCase
{
    /** @var HasSignedLastTermsVersion */
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
        $hasSign = ($this->useCase)('terms-2', $subject);

        $this->assertFalse($hasSign);
    }

    public function testUseCaseSubjectNotExist(): void
    {
        $subject = DummySubject::create('user', '999');
        $hasSign = ($this->useCase)('terms-1', $subject);

        $this->assertFalse($hasSign);
    }

    public function testUseCaseHasSignOldVersion(): void
    {
        $subject = DummySubject::create('user', '42');
        $hasSign = ($this->useCase)('terms-1', $subject);

        $this->assertFalse($hasSign);
    }

    public function testUseCaseHasSignLatestVersion(): void
    {
        $subject = DummySubject::create('user', '43');
        $hasSign = ($this->useCase)('terms-1', $subject);

        $this->assertTrue($hasSign);
    }
}
