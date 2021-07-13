<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\InvalidTermsEditionException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\InvalidTermsEditionException
 * @TestConfig("fixtures")
 */
final class InvalidTermsEditionExceptionTest extends TestCase
{
    public function testException(): void
    {
        $violations = new ConstraintViolationList([]);
        $exception = new InvalidTermsEditionException($violations);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame($violations, $exception->getViolations());
        $this->assertSame('Invalid model TermsEdition.', $exception->getMessage());
    }
}
