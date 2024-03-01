<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\GenerateTermsRoute;

/** @covers \RichId\TermsModuleBundle\Domain\UseCase\GenerateTermsRoute */
#[TestConfig('kernel')]
final class GenerateTermsRouteTest extends TestCase
{
    /** @var GenerateTermsRoute */
    public $useCase;

    public function testUseCase(): void
    {
        $subject = DummySubject::create('user', '42');

        $url = ($this->useCase)('terms-1', $subject);

        $this->assertSame('/terms/terms-1?type=user&identifier=42', $url);
    }
}
