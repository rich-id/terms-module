<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Listener;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use Symfony\Component\HttpFoundation\Response;

/** @covers \RichId\TermsModuleBundle\Infrastructure\Listener\RedirectToSigningPageOnAccessDeniedListener */
#[TestConfig('kernel')]
final class RedirectToSigningPageOnAccessDeniedListenerTest extends TestCase
{
    public function testControllerThrowSubjectNeedToSignTermsException(): void
    {
        $response = $this->getClient()->get('/with-access');

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/terms/terms-1/sign?type=user&identifier=42', $response->headers->get('location'));
    }

    public function testControllerThrowOtherException(): void
    {
        $response = $this->getClient()->get('/with-exception');

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
