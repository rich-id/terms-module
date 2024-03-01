<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/** @covers \RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException */
#[TestConfig('fixtures')]
final class CannotAddVersionToTermsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $terms = $this->getReference(Terms::class, '1');
        $exception = new CannotAddVersionToTermsException($terms);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame($terms, $exception->getTerms());
        $this->assertSame('Cannot add new version to the terms terms-1.', $exception->getMessage());
    }
}
