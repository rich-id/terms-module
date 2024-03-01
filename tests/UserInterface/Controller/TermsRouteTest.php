<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\LoggerStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\TermsRoute
 * @covers \RichId\TermsModuleBundle\Domain\Fetcher\GetLastPublishedTermsVersion
 */
#[TestConfig('fixtures')]
final class TermsRouteTest extends ControllerTestCase
{
    /** @var LoggerStub */
    public $loggerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    /** @var TermsVersionSignatureRepository */
    public $termsVersionSignatureRepository;

    public function testRouteWithoutTypeParameter(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-3',
                [
                    'identifier' => '42',
                ]
            );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Query parameter type is missing.', $content);
    }

    public function testRouteWithoutIdentifierParameter(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-1',
                [
                    'type' => 'user',
                ]
            );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Query parameter identifier is missing.', $content);
    }

    public function testRouteWithTermsNotFound(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-999',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ]
            );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Not found terms terms-999.', $content);
    }

    public function testRouteWithTermsNotPublished(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-2',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ]
            );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms terms-2 is not published.', $content);
    }

    public function testRouteWithTermsWithoutPublishedVersion(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-3',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ]
            );

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms terms-3 hasn\'t published version.', $content);
    }

    public function testRouteGuardInvalidNotAllowedSubjectType(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->get(
                '/terms/terms-5',
                [
                    'type'       => 'other',
                    'identifier' => 'my_user_1',
                ]
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteGuardInvalidNotAllowedSubjectIdentifier(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->get(
                '/terms/terms-5',
                [
                    'type'       => 'user',
                    'identifier' => 'my_user_3',
                ]
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteGuardValid(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_2);

        $response = $this->getClient()
            ->get(
                '/terms/terms-5',
                [
                    'type'       => 'user',
                    'identifier' => 'my_user_2',
                ]
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRouteSubjectHasNotSignLastVersion(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-1',
                [
                    'type'       => 'user',
                    'identifier' => '300',
                ]
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());
    }

    public function testRouteSubjectHasSignLastVersion(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-1',
                [
                    'type'       => 'user',
                    'identifier' => '43',
                ]
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms 1', $content);
        $this->assertStringContainsString('Title Version 3', $content);
        $this->assertStringContainsString('Content Version 3', $content);
        $this->assertStringContainsString('Close', $content);
    }
}
