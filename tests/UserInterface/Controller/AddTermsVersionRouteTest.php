<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\AddTermsVersionRoute
 * @TestConfig("fixtures")
 */
final class AddTermsVersionRouteTest extends ControllerTestCase
{
    /** @var TermsVersionRepository */
    public $termsVersionRepository;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    public function testRouteNotLogged(): void
    {
        $terms = $this->getReference(Terms::class, '4');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRouteBadRole(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '4');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteWithoutVersion(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '2');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';
        $this->assertStringContainsString('The terms has no version.', $content);
    }

    public function testRouteSpecificVersionNotFound(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '4');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version?version=42',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';
        $this->assertStringContainsString('No terms version found with version 42', $content);
    }

    public function testRouteLastVersionIsNotActive(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '5');

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';
        $this->assertStringContainsString('Cannot add new version to the terms terms-5.', $content);
    }

    public function testRouteSuccess(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '5');
        $termsVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');

        $termsVersion->enable();
        $this->getManager()->persist($termsVersion);
        $this->getManager()->flush();

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertCount(1, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsVersionCreatedEvent::class, $event);

        $newTermsVersion = $event->getTermsVersion();

        $this->assertInstanceOf(TermsVersion::class, $event->getTermsVersion());
        $this->assertNotNull($newTermsVersion->getId());
        $this->assertSame('Title Version 2', $newTermsVersion->getTitle());
        $this->assertSame('Content Version 2', $newTermsVersion->getCOntent());
        $this->assertSame(3, $newTermsVersion->getVersion());
        $this->assertSame('terms-5', $newTermsVersion->getTerms()->getSlug());
        $this->assertNull($newTermsVersion->getPublicationDate());
        $this->assertFalse($newTermsVersion->isEnabled());
    }

    public function testRouteSuccessWithSpecificVersion(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticateUser($user);

        $terms = $this->getReference(Terms::class, '5');
        $termsVersion = $this->getReference(TermsVersion::class, 'v2-terms-5');

        $termsVersion->enable();
        $this->getManager()->persist($termsVersion);
        $this->getManager()->flush();

        $response = $this->getClient()
            ->post(
                \sprintf(
                    '/administration/terms/%d/new-version?version=1',
                    $terms->getId()
                )
            );

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertCount(1, $this->entityManagerStub->getPersistedEntities());
        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsVersionCreatedEvent::class, $event);

        $newTermsVersion = $event->getTermsVersion();

        $this->assertInstanceOf(TermsVersion::class, $event->getTermsVersion());
        $this->assertNotNull($newTermsVersion->getId());
        $this->assertSame('Title Version 1', $newTermsVersion->getTitle());
        $this->assertSame('Content Version 1', $newTermsVersion->getCOntent());
        $this->assertSame(3, $newTermsVersion->getVersion());
        $this->assertSame('terms-5', $newTermsVersion->getTerms()->getSlug());
        $this->assertNull($newTermsVersion->getPublicationDate());
        $this->assertFalse($newTermsVersion->isEnabled());
    }
}
