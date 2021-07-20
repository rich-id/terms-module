<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Exception;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Exception\TermsModuleException;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException
 * @TestConfig("fixtures")
 */
final class EnabledVersionCannotBeDeletedExceptionTest extends TestCase
{
    public function testException(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v1-terms-1');
        $exception = new EnabledVersionCannotBeDeletedException($termsVersion);

        $this->assertInstanceOf(TermsModuleException::class, $exception);
        $this->assertSame($termsVersion, $exception->getTermsVersion());
        $this->assertSame('Version 1 of terms terms-1 cannot be deleted.', $exception->getMessage());
    }
}
