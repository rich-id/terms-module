<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\FirstVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/** @covers \RichId\TermsModuleBundle\Domain\Exception\FirstVersionCannotBeDeletedException */
#[TestConfig('fixtures')]
final class FirstVersionCannotBeDeletedExceptionTest extends TestCase
{
    public function testException(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v1-terms-1');
        $exception = new FirstVersionCannotBeDeletedException($termsVersion);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame($termsVersion, $exception->getTermsVersion());
        $this->assertSame('First version of terms terms-1 cannot be deleted.', $exception->getMessage());
    }
}
