<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\TwigExtension;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\TwigExtension\TermsExtension;
use Twig\TwigFunction;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\TwigExtension\TermsExtension
 */
final class TermsExtensionTest extends TestCase
{
    /** @var TermsExtension */
    public $extension;

    /** @TestConfig("fixtures") */
    public function testExtensions(): void
    {
        $this->assertEmpty($this->extension->getFilters());

        $this->assertCount(2, $this->extension->getFunctions());

        $this->assertInstanceOf(TwigFunction::class, $this->extension->getFunctions()[0]);
        $this->assertInstanceOf(TwigFunction::class, $this->extension->getFunctions()[1]);
    }

    /** @TestConfig("fixtures") */
    public function testGenerateSigningRoute(): void
    {
        $subject = DummySubject::create('user', '42');
        $terms = $this->getReference(Terms::class, '1');

        $url = $this->extension->generateSigningRoute($terms, $subject);

        $this->assertSame('/terms/terms-1/sign?type=user&identifier=42', $url);
    }

    /** @TestConfig("container") */
    public function testHasSignedTermsTermsNotExist(): void
    {
        $subject = DummySubject::create('user', '42');
        $terms = new Terms();
        $terms->setSlug('terms-999');

        $this->expectException(NotFoundTermsException::class);
        $this->expectDeprecationMessage('Not found terms terms-999.');

        $this->extension->hasSignedTerms($terms, $subject);
    }

    /** @TestConfig("fixtures") */
    public function testHasSignedTermsSubjectNotExist(): void
    {
        $subject = DummySubject::create('user', '999');
        $terms = $this->getReference(Terms::class, '1');
        $code = $this->extension->hasSignedTerms($terms, $subject);

        $this->assertSame(2, $code);
    }

    /** @TestConfig("fixtures") */
    public function testHasSignedTermsHasSignOldVersion(): void
    {
        $subject = DummySubject::create('user', '42');
        $terms = $this->getReference(Terms::class, '1');
        $code = $this->extension->hasSignedTerms($terms, $subject);

        $this->assertSame(1, $code);
    }

    /** @TestConfig("fixtures") */
    public function testHasSignedTermsHasSignLatestVersion(): void
    {
        $subject = DummySubject::create('user', '43');
        $terms = $this->getReference(Terms::class, '1');
        $code = $this->extension->hasSignedTerms($terms, $subject);

        $this->assertSame(0, $code);
    }
}
