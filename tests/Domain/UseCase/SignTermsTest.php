<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\UseCase;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\UseCase\SignTerms;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\EventDispatcherStub;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\LoggerStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\Domain\UseCase\SignTerms
 * @TestConfig("fixtures")
 */
final class SignTermsTest extends TestCase
{
    /** @var SignTerms */
    public $useCase;

    /** @var LoggerStub */
    public $loggerStub;

    /** @var EventDispatcherStub */
    public $eventDispatcherStub;

    /** @var TermsVersionSignatureRepository */
    public $termsVersionSignatureRepository;

    public function testUseCaseTermsNotFound(): void
    {
        $this->expectException(NotFoundTermsException::class);
        $this->expectExceptionMessage('Not found terms terms-999.');

        $subject = DummySubject::create('user', '42');

        ($this->useCase)('terms-999', $subject, null);
    }

    public function testUseCaseTermsNotPublished(): void
    {
        $this->expectException(NotPublishedTermsException::class);
        $this->expectExceptionMessage('Terms terms-2 is not published.');

        $subject = DummySubject::create('user', '42');

        ($this->useCase)('terms-2', $subject, null);
    }

    public function testUseCaseTermsWithoutLastVersion(): void
    {
        $this->expectException(TermsHasNoPublishedVersionException::class);
        $this->expectExceptionMessage('Terms terms-3 hasn\'t published version.');

        $subject = DummySubject::create('user', '42');

        ($this->useCase)('terms-3', $subject, null);
    }

    public function testUseCaseSubjectHasALreadySignLastVersion(): void
    {
        $this->expectException(AlreadySignLastTermsVersionException::class);
        $this->expectExceptionMessage('Terms terms-1 is already sign by this subject.');

        $subject = DummySubject::create('user', '43');

        ($this->useCase)('terms-1', $subject, null);
    }

    public function testUseCasePreferAnswerLater(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());

        $subject = DummySubject::create('user', '42');

        $response = ($this->useCase)('terms-1', $subject, null);
        $this->getManager()->flush();

        $this->assertInstanceOf(Response::class, $response);
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

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());
    }

    public function testUseCaseRefuse(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());

        $subject = DummySubject::create('user', '42');

        $response = ($this->useCase)('terms-1', $subject, false);
        $this->getManager()->flush();

        $this->assertInstanceOf(Response::class, $response);
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

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());
    }

    public function testUseCaseAcceptation(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());

        $subject = DummySubject::create('user', '42');

        $response = ($this->useCase)('terms-1', $subject, true);
        $this->getManager()->flush();

        $this->assertInstanceOf(Response::class, $response);
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

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }

    public function testUseCasePreferAnswerLaterWithCustomRedirection(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());

        $subject = DummySubject::create('user', '42');

        $response = ($this->useCase)('terms-4', $subject, null);
        $this->getManager()->flush();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('/other', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-4.', $log[1]);
        $this->assertStringContainsString('Decision: I prefer to answer later.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(1, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertNull($event->isAccepted());

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());
    }

    public function testUseCaseRefuseWithCustomRedirection(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());

        $subject = DummySubject::create('user', '42');

        $response = ($this->useCase)('terms-4', $subject, false);
        $this->getManager()->flush();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('/other', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-4.', $log[1]);
        $this->assertStringContainsString('Decision: I refuse.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(1, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertFalse($event->isAccepted());

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());
    }

    public function testUseCaseAcceptationWithCustomRedirection(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());

        $subject = DummySubject::create('user', '42');

        $response = ($this->useCase)('terms-4', $subject, true);
        $this->getManager()->flush();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('/other', $response->headers->get('location'));

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-4.', $log[1]);
        $this->assertStringContainsString('Decision: I agree.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $event = $this->eventDispatcherStub->getEvents()[0];
        $this->assertInstanceOf(TermsSignedEvent::class, $event);
        $this->assertSame(1, $event->getTermsVersion()->getVersion());
        $this->assertSame('user', $event->getSubject()->getTermsSubjectType());
        $this->assertSame('42', $event->getSubject()->getTermsSubjectIdentifier());
        $this->assertTrue($event->isAccepted());

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }
}
