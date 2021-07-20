<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Infrastructure\Adapter\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\ResponseBuilder
 * @TestConfig("fixtures")
 */
final class ResponseBuilderTest extends TestCase
{
    /** @var ResponseBuilder */
    public $adapter;

    public function testBuildDefaultTermsSignedResponsePreferAnswerLater(): void
    {
        /** @var RedirectResponse $response */
        $response = $this->adapter->buildDefaultTermsSignedResponse(null);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/ignore', $response->getTargetUrl());
    }

    public function testBuildDefaultTermsSignedResponseRefuse(): void
    {
        /** @var RedirectResponse $response */
        $response = $this->adapter->buildDefaultTermsSignedResponse(false);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/refusal', $response->getTargetUrl());
    }

    public function testBuildDefaultTermsSignedResponseAcceptation(): void
    {
        /** @var RedirectResponse $response */
        $response = $this->adapter->buildDefaultTermsSignedResponse(true);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/acceptation', $response->getTargetUrl());
    }
}
