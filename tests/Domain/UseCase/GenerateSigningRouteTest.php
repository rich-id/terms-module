<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\GenerateSigningRoute;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\GenerateSigningRoute
 * @TestConfig("kernel")
 */
final class GenerateSigningRouteTest extends TestCase
{
    /** @var GenerateSigningRoute */
    public $useCase;

    public function testUseCase(): void
    {
        $subject = DummySubject::create('user', '42');

        $url = ($this->useCase)('terms-1', $subject);

        $this->assertSame('/terms/terms-1/sign?type=user&identifier=42', $url);
    }
}
