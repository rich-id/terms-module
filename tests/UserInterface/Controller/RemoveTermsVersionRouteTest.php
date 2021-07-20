<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EntityManagerStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\RemoveTermsVersionRoute
 * @TestConfig("fixtures")
 */
final class RemoveTermsVersionRouteTest extends ControllerTestCase
{
    /** @var TermsVersionRepository */
    public $termsVersionRepository;

    /** @var EntityManagerStub */
    public $entityManagerStub;

    public function testRouteNotLogged(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');

        $response = $this->getClient()
            ->delete(
                \sprintf(
                    '/administration/terms-version/%d',
                    $termsVersion->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testRouteBadRole(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');
        $response = $this->getClient()
            ->delete(
                \sprintf(
                    '/administration/terms-version/%d',
                    $termsVersion->getId()
                )
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteWithEnabledVersion(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');

        $response = $this->getClient()
            ->delete(
                \sprintf(
                    '/administration/terms-version/%d',
                    $termsVersion->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';
        $this->assertStringContainsString('Version 3 of terms terms-1 cannot be deleted.', $content);
    }

    public function testRouteRemoveFirstVersion(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $terms = $this->getReference(Terms::class, '2');

        $termsVersion = new TermsVersion();
        $termsVersion->setVersion(1);
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('My title');
        $termsVersion->setContent('My content');

        $this->getManager()->persist($termsVersion);
        $this->getManager()->flush();

        $response = $this->getClient()
            ->delete(
                \sprintf(
                    '/administration/terms-version/%d',
                    $termsVersion->getId()
                )
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $content = $response->getContent() !== false ? $response->getContent() : '';
        $this->assertStringContainsString('First version of terms terms-2 cannot be deleted.', $content);
    }

    public function testRouteSuccess(): void
    {
        $this->assertCount(7, $this->termsVersionRepository->findAll());

        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $termsVersion = $this->getReference(TermsVersion::class, 'v4-terms-1');

        $response = $this->getClient()
            ->delete(
                \sprintf(
                    '/administration/terms-version/%d',
                    $termsVersion->getId()
                )
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $this->entityManagerStub->getRemovedEntities());

        // Skipped, waiting a correction in the test-framework
        //$this->assertCount(6, $this->termsVersionRepository->findAll());
    }
}
