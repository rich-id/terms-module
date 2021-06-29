<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException
 */
final class NotFoundTermsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new NotFoundTermsException('terms-1');

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame('terms-1', $exception->getTermsSlug());
        $this->assertSame('Not found terms terms-1.', $exception->getMessage());
    }
}
