<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\InvalidValueException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\InvalidValueException
 */
final class InvalidValueExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new InvalidValueException('title', 'invalid');

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame('title', $exception->getPropertyPath());
        $this->assertSame('invalid', $exception->getValue());
    }
}
