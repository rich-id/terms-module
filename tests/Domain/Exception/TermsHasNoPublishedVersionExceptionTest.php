<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/** @covers \RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException */
#[TestConfig('fixtures')]
final class TermsHasNoPublishedVersionExceptionTest extends TestCase
{
    public function testException(): void
    {
        $terms = $this->getReference(Terms::class, '1');
        $exception = new TermsHasNoPublishedVersionException($terms);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame($terms, $exception->getTerms());
        $this->assertSame('Terms terms-1 hasn\'t published version.', $exception->getMessage());
    }
}
