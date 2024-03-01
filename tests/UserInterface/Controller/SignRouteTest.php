<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\LoggerStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\SignRoute
 * @covers \RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\SignTerms
 */
#[TestConfig('fixtures')]
final class SignRouteTest extends ControllerTestCase
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
                '/terms/terms-3/sign',
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
                '/terms/terms-1/sign',
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
                '/terms/terms-999/sign',
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
                '/terms/terms-2/sign',
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
                '/terms/terms-3/sign',
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

    public function testRouteSubjectHasAlreadySignThisTerms(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-1/sign',
                [
                    'type'       => 'user',
                    'identifier' => '43',
                ]
            );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());
    }

    public function testRouteGuardInvalidNotAllowedSubjectType(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->get(
                '/terms/terms-5/sign',
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
                '/terms/terms-5/sign',
                [
                    'type'       => 'user',
                    'identifier' => 'my_user_3',
                ]
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRouteGuardValid(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->get(
                '/terms/terms-5/sign',
                [
                    'type'       => 'user',
                    'identifier' => 'my_user_1',
                ]
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testRouteSubjectHasSignOldVersion(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-1/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ]
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms 1', $content);
        $this->assertStringContainsString('Title Version 3', $content);
        $this->assertStringContainsString('Content Version 3', $content);
        $this->assertStringContainsString('I refuse', $content);
        $this->assertStringContainsString('I agree', $content);
        $this->assertStringContainsString('I prefer to answer later', $content);
        $this->assertStringContainsString('New version!', $content);
    }

    public function testRouteSubjectHasNotSignOldVersion(): void
    {
        $response = $this->getClient()
            ->get(
                '/terms/terms-1/sign',
                [
                    'type'       => 'user',
                    'identifier' => '44',
                ]
            );

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEmpty($this->loggerStub->getLogs());
        $this->assertEmpty($this->eventDispatcherStub->getEvents());

        $content = $response->getContent() !== false ? $response->getContent() : '';

        $this->assertStringContainsString('Terms 1', $content);
        $this->assertStringContainsString('Title Version 3', $content);
        $this->assertStringContainsString('Content Version 3', $content);
        $this->assertStringContainsString('I refuse', $content);
        $this->assertStringContainsString('I agree', $content);
        $this->assertStringContainsString('I prefer to answer later', $content);
        $this->assertStringNotContainsString('New version !', $content);
    }

    public function testRoutePostPreferAnswerLater(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());

        $response = $this->getClient()
            ->post(
                '/terms/terms-1/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ],
                [
                    'accepted' => '',
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/ignore', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I prefer to answer later.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(3, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertNull($event->isAccepted());

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }

    public function testRoutePostRefuse(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());

        $response = $this->getClient()
            ->post(
                '/terms/terms-1/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ],
                [
                    'accepted' => 0,
                ]
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/refusal', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I refuse.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(3, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertFalse($event->isAccepted());

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }

    public function testRoutePostAcceptation(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());

        $response = $this->getClient()
            ->post(
                '/terms/terms-1/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ],
                [
                    'accepted' => 1,
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/acceptation', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I agree.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(3, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertTrue($event->isAccepted());

        $this->assertCount(7, $this->termsVersionSignatureRepository->findAll());
    }

    public function testRoutePostPreferAnswerLaterWithCustomRedirection(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());

        $response = $this->getClient()
            ->post(
                '/terms/terms-4/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ],
                [
                    'accepted' => '',
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/other', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-4.', $log[1]);
        $this->assertStringContainsString('Decision: I prefer to answer later.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(1, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertNull($event->isAccepted());

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }

    public function testRoutePostRefuseWithCustomRedirection(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());

        $response = $this->getClient()
            ->post(
                '/terms/terms-4/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ],
                [
                    'accepted' => 0,
                ]
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/other', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-4.', $log[1]);
        $this->assertStringContainsString('Decision: I refuse.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(1, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertFalse($event->isAccepted());

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }

    public function testRoutePostAcceptationWithCustomRedirection(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());

        $response = $this->getClient()
            ->post(
                '/terms/terms-4/sign',
                [
                    'type'       => 'user',
                    'identifier' => '42',
                ],
                [
                    'accepted' => 1,
                ],
                isJson: false
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame('/other', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-4.', $log[1]);
        $this->assertStringContainsString('Decision: I agree.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $this->assertCount(1, $this->eventDispatcherStub->getEvents());

        $event = $this->eventDispatcherStub->getEvents()[0];

        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(1, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertTrue($event->isAccepted());

        $this->assertCount(7, $this->termsVersionSignatureRepository->findAll());
    }

    public function testRoutePostGuardNotAllowedSubjectType(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->post(
                '/terms/terms-5/sign',
                [
                    'type'       => 'other',
                    'identifier' => 'my_user_1',
                ],
                [
                    'accepted' => 1,
                ]
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRoutePostGuardNotAllowedSubjectIdentifier(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->post(
                '/terms/terms-5/sign',
                [
                    'type'       => 'user',
                    'identifier' => 'my_user_3',
                ],
                [
                    'accepted' => 1,
                ]
            );

        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRoutePostGuardValid(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $response = $this->getClient()
            ->post(
                '/terms/terms-5/sign',
                [
                    'type'       => 'user',
                    'identifier' => 'my_user_1',
                ],
                [
                    'accepted' => 1,
                ]
            );

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
    }
}
