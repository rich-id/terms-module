<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\SubjectNeedToSignTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\SubjectNeedToSignTermsException
 */
final class SubjectNeedToSignTermsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $subject = DummySubject::create('user', '42');
        $exception = new SubjectNeedToSignTermsException('terms-1', $subject);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame('terms-1', $exception->getTermsSlug());
        $this->assertSame($subject, $exception->getSubject());
    }
}
