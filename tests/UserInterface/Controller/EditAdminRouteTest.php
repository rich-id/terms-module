<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\FormType\TermsVersionFormType;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use Symfony\Component\HttpFoundation\Response;

/** @covers \RichId\TermsModuleBundle\UserInterface\Controller\EditAdminRoute */
#[TestConfig('fixtures')]
final class EditAdminRouteTest extends ControllerTestCase
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
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

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
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

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
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

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
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

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
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

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
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

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

    public function testRoutePostTermsWithoutVersion(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '2');
        $this->assertEmpty($terms->getVersions());
        $this->assertFalse($terms->isPublished());

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => 'New title',
                        'content'               => 'New content',
                        'isTermsPublished'      => '0',
                        'publicationDate'       => '',
                        'needVersionActivation' => '',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/administration/terms/2', $response->headers->get('location'));
        $this->assertFalse($terms->isPublished());
        $this->assertCount(1, $terms->getVersions());

        $termsVersion = $terms->getVersions()->first();

        $this->assertSame('New title', $termsVersion->getTitle());
        $this->assertSame('New content', $termsVersion->getContent());
        $this->assertSame(1, $termsVersion->getVersion());
        $this->assertNull($termsVersion->getPublicationDate());
        $this->assertFalse($termsVersion->isEnabled());
    }

    public function testRoutePostTermsWithoutVersionPublishTerms(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '2');
        $this->assertEmpty($terms->getVersions());
        $this->assertFalse($terms->isPublished());

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => 'New title',
                        'content'               => 'New content',
                        'isTermsPublished'      => '1',
                        'publicationDate'       => '',
                        'needVersionActivation' => '',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/administration/terms/2', $response->headers->get('location'));
        $this->assertTrue($terms->isPublished());
        $this->assertCount(1, $terms->getVersions());

        $termsVersion = $terms->getVersions()->first();

        $this->assertSame('New title', $termsVersion->getTitle());
        $this->assertSame('New content', $termsVersion->getContent());
        $this->assertSame(1, $termsVersion->getVersion());
        $this->assertNotNull($termsVersion->getPublicationDate());
        $this->assertTrue($termsVersion->isEnabled());
    }

    public function testRoutePostWithExit(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '2');
        $this->assertEmpty($terms->getVersions());
        $this->assertFalse($terms->isPublished());

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d?exit=1',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => 'New title',
                        'content'               => 'New content',
                        'isTermsPublished'      => '0',
                        'publicationDate'       => '',
                        'needVersionActivation' => '',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/administration/terms', $response->headers->get('location'));
    }

    public function testRoutePostChangeTitleContentPublicationDateOfEnabledVersion(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '5');
        $termVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');
        $termVersion->enable();

        $this->getManager()->persist($termVersion);
        $this->getManager()->flush();

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => 'New title',
                        'content'               => 'New content',
                        'isTermsPublished'      => '1',
                        'publicationDate'       => '2020-01-01',
                        'needVersionActivation' => '',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('The title cannot be changed when the version is published.', $content);
        $this->assertStringContainsString('The publication date cannot be changed when the version is published.', $content);
    }

    public function testRoutePostUnpublishTermsLocked(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '1');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => 'New title',
                        'content'               => 'New content',
                        'isTermsPublished'      => '0',
                        'publicationDate'       => '',
                        'needVersionActivation' => '',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('It is not possible to unpublish a locked terms.', $content);
    }

    public function testRoutePostWithSpecificVersion(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '1');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d?version=1',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => 'Title Version 1',
                        'content'               => 'Content Version 1',
                        'isTermsPublished'      => '1',
                        'publicationDate'       => '',
                        'needVersionActivation' => '',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/administration/terms/1?version=1', $response->headers->get('location'));
    }

    public function testRoutePostActivateVersionAlreadyActivated(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '5');
        $termVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');
        $termVersion->enable();

        $this->getManager()->persist($termVersion);
        $this->getManager()->flush();

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => $termVersion->getTitle(),
                        'content'               => $termVersion->getContent(),
                        'isTermsPublished'      => '1',
                        'publicationDate'       => '',
                        'needVersionActivation' => '1',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ]
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRoutePostActivateVersion(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '5');
        $termVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d',
                    $terms->getId()
                ),
                [],
                [
                    'terms_version_form' => [
                        'title'                 => $termVersion->getTitle(),
                        'content'               => $termVersion->getContent(),
                        'isTermsPublished'      => '1',
                        'publicationDate'       => '',
                        'needVersionActivation' => '1',
                        '_token'                => $this->getCsrfToken(TermsVersionFormType::class),
                    ],
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
