<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException
 * @TestConfig("fixtures")
 */
final class AlreadySignLastTermsVersionExceptionTest extends TestCase
{
    public function testException(): void
    {
        $terms = $this->getReference(Terms::class, '1');
        $exception = new AlreadySignLastTermsVersionException($terms);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame($terms, $exception->getTerms());
        $this->assertSame('Terms terms-1 is already sign.', $exception->getMessage());
    }
}
