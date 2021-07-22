<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Infrastructure\Adapter\Security;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\Security
 * @TestConfig("fixtures")
 */
final class SecurityTest extends TestCase
{
    /** @var Security */
    public $adapter;

    public function testGetUserNotLogged(): void
    {
        $user = $this->adapter->getUser();
        $this->assertNull($user);
    }

    public function testGetUserLogged(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $user = $this->adapter->getUser();
        $this->assertInstanceOf(DummyUser::class, $user);
    }
}
