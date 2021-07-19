<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\TwigExtension;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Infrastructure\TwigExtension\TermsExtension;
use Twig\TwigFunction;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\TwigExtension\TermsExtension
 * @TestConfig("fixtures")
 */
final class TermsExtensionTest extends TestCase
{
    /** @var TermsExtension */
    public $extension;

    public function testExtensions(): void
    {
        $this->assertEmpty($this->extension->getFilters());

        $this->assertCount(1, $this->extension->getFunctions());
        $this->assertInstanceOf(TwigFunction::class, $this->extension->getFunctions()[0]);
    }

    public function testHasSignedTermsTermsNotExist(): void
    {
        $this->expectException(NotFoundTermsException::class);
        $this->expectDeprecationMessage('Not found terms terms-999.');

        $this->extension->hasSignedTerms('terms-999', 'user', '42');
    }

    public function testHasSignedTermsSubjectNotExist(): void
    {
        $code = $this->extension->hasSignedTerms('terms-1', 'user', '999');

        $this->assertSame(2, $code);
    }

    public function testHasSignedTermsHasSignOldVersion(): void
    {
        $code = $this->extension->hasSignedTerms('terms-1', 'user', '42');

        $this->assertSame(1, $code);
    }

    public function testHasSignedTermsHasSignLatestVersion(): void
    {
        $code = $this->extension->hasSignedTerms('terms-1', 'user', '43');

        $this->assertSame(0, $code);
    }
}
