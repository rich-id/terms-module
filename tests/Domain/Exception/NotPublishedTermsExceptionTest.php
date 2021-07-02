<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException
 */
final class NotPublishedTermsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new NotPublishedTermsException('terms-1');

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame('terms-1', $exception->getTermsSlug());
        $this->assertSame('Terms terms-1 is not published.', $exception->getMessage());
    }
}
