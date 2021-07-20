<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\EventListener;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\EventListener\LogSignedTermsEventListener;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\LoggerStub;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \RichId\TermsModuleBundle\Domain\EventListener\LogSignedTermsEventListener
 * @TestConfig("fixtures")
 */
final class LogSignedTermsEventListenerTest extends TestCase
{
    /** @var LogSignedTermsEventListener */
    public $listener;

    /** @var LoggerStub */
    public $loggerStub;

    public function testListener(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $response = new Response();
        $subject = DummySubject::create('user', '42');

        $event = new TermsSignedEvent($termsVersion, $subject, true, $response);

        ($this->listener)($event);

        $this->assertSame($response, $event->getResponse());
        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I agree.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);
    }
}
