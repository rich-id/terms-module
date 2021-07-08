<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\Adapter\Logger;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\LoggerStub;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\Logger
 * @TestConfig("fixtures")
 */
final class LoggerTest extends TestCase
{
    /** @var Logger */
    public $adapter;

    /** @var LoggerStub */
    public $loggerStub;

    public function testLogTermsSignedPreferAnswerLater(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $subject = DummySubject::create('user', '42');

        $this->adapter->logTermsSigned('terms-1', $subject, null);

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I prefer to answer later.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $this->assertSame(
            [
                'extra' => [
                    '_event'  => 'terms_module.terms_signed',
                    '_terms'  => 'terms-1',
                    '_choice' => 'I prefer to answer later',
                    '_user'   => 'my_user_1',
                ],
            ],
            $log[2]
        );
    }

    public function testLogTermsRefuse(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $subject = DummySubject::create('user', '42');

        $this->adapter->logTermsSigned('terms-1', $subject, false);

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I refuse.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $this->assertSame(
            [
                'extra' => [
                    '_event'  => 'terms_module.terms_signed',
                    '_terms'  => 'terms-1',
                    '_choice' => 'I refuse',
                    '_user'   => 'my_user_1',
                ],
            ],
            $log[2]
        );
    }

    public function testLogTermsSignedAcceptation(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticateUser($user);

        $subject = DummySubject::create('user', '42');

        $this->adapter->logTermsSigned('terms-1', $subject, true);

        $this->assertCount(1, $this->loggerStub->getLogs());

        $log = $this->loggerStub->getLogs()[0];
        $this->assertSame('info', $log[0]);
        $this->assertStringContainsString('A decision has been made for the terms terms-1.', $log[1]);
        $this->assertStringContainsString('Decision: I agree.', $log[1]);
        $this->assertStringContainsString('Date: ', $log[1]);
        $this->assertStringContainsString('User: my_user_1.', $log[1]);

        $this->assertSame(
            [
                'extra' => [
                    '_event'  => 'terms_module.terms_signed',
                    '_terms'  => 'terms-1',
                    '_choice' => 'I agree',
                    '_user'   => 'my_user_1',
                ],
            ],
            $log[2]
        );
    }
}
