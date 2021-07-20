<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\ListAdminRoute
 * @TestConfig("fixtures")
 */
final class ListAdminRouteTest extends ControllerTestCase
{
    public function testRouteNotLogged(): void
    {
        $response = $this->getClient()->get('/administration/terms');
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRouteBadRole(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()->get('/administration/terms');
        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteAsAdmin(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $response = $this->getClient()->get('/administration/terms');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms management', $content);
        $this->assertStringContainsString('Terms 1', $content);
        $this->assertStringContainsString('Terms 2', $content);
        $this->assertStringContainsString('Terms 2', $content);
    }
}
