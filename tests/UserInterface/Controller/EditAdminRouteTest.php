<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\EditAdminRoute
 * @TestConfig("fixtures")
 */
final class EditAdminRouteTest extends TestCase
{
    public function testRouteNotLogged(): void
    {
        $terms = $this->getReference(Terms::class, '1');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRouteBadRole(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '1');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteSpecificVersionNotFound(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '1');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d?version=999',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';
        $this->assertStringContainsString('No terms version found with version 999', $content);
    }

    public function testRouteTermsWithoutVersion(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '2');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms management', $content);
        $this->assertStringContainsString('Terms 2', $content);
        $this->assertStringContainsString('V. 1', $content);

        $this->assertStringNotContainsString('V. 2', $content);
        $this->assertStringNotContainsString('Duplicate', $content);
        $this->assertStringNotContainsString('New version', $content);
        $this->assertStringNotContainsString('icon-trash', $content);
        $this->assertStringNotContainsString('Active', $content);
    }

    public function testRouteTermsWithMultipleVersionLastVersionNotActive(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '1');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms management', $content);
        $this->assertStringContainsString('Terms 1', $content);
        $this->assertStringContainsString('V. 1', $content);
        $this->assertStringContainsString('V. 2', $content);
        $this->assertStringContainsString('V. 3', $content);
        $this->assertStringContainsString('V. 4', $content);
        $this->assertStringContainsString('icon-trash', $content);
        $this->assertStringContainsString('Active', $content);

        $this->assertStringContainsString('Title Version 4', $content);
        $this->assertStringContainsString('Content Version 4', $content);

        $this->assertStringNotContainsString('V. 5', $content);
        $this->assertStringNotContainsString('Duplicate', $content);
        $this->assertStringNotContainsString('New version', $content);
    }

    public function testRouteTermsLastVersionActive(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $termVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');
        $termVersion->enable();

        $this->getManager()->persist($termVersion);
        $this->getManager()->flush();

        $terms = $this->getReference(Terms::class, '5');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms management', $content);
        $this->assertStringContainsString('Terms 5', $content);
        $this->assertStringContainsString('V. 1', $content);
        $this->assertStringContainsString('V. 2', $content);
        $this->assertStringContainsString('New version', $content);

        $this->assertStringContainsString('Title Version 2', $content);
        $this->assertStringContainsString('Content Version 2', $content);

        $this->assertStringNotContainsString('V. 3', $content);
        $this->assertStringNotContainsString('Duplicate', $content);
        $this->assertStringNotContainsString('icon-trash', $content);
        $this->assertStringNotContainsString('Active', $content);
    }

    public function testRouteTermsLastVersionActiveAndSpecificVersion(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $termVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');
        $termVersion->enable();

        $this->getManager()->persist($termVersion);
        $this->getManager()->flush();

        $terms = $this->getReference(Terms::class, '5');

        $response = $this->getClient()
            ->get(
                \sprintf(
                    '/administration/terms/%d?version=1',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms management', $content);
        $this->assertStringContainsString('Terms 5', $content);
        $this->assertStringContainsString('V. 1', $content);
        $this->assertStringContainsString('V. 2', $content);
        $this->assertStringContainsString('Duplicate', $content);

        $this->assertStringContainsString('Title Version 1', $content);
        $this->assertStringContainsString('Content Version 1', $content);

        $this->assertStringNotContainsString('V. 3', $content);
        $this->assertStringNotContainsString('New version', $content);
        $this->assertStringNotContainsString('icon-trash', $content);
        $this->assertStringNotContainsString('Active', $content);
    }
}
