<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\SecurityVoter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\VoterTestCase;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\UserVoter;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\SecurityVoter\UserVoter
 * @TestConfig("fixtures")
 */
final class UserVoterTest extends VoterTestCase
{
    /** @var UserVoter */
    public $voter;

    public function testVoterWithNotSupportedAttribute(): void
    {
        $result = $this->vote(
            null,
            'other'
        );

        $this->assertSame(Voter::ACCESS_ABSTAIN, $result);
    }

    public function testVoterNotLogged(): void
    {
        $result = $this->vote(
            null,
            UserVoter::MODULE_TERMS_ADMIN
        );

        $this->assertSame(Voter::ACCESS_DENIED, $result);
    }

    public function testVoterLoggedByBadRole(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER);
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $result = $this->vote(
            null,
            UserVoter::MODULE_TERMS_ADMIN,
            $user
        );

        $this->assertSame(Voter::ACCESS_DENIED, $result);
    }

    public function testVoterLoggedAndGoodRole(): void
    {
        $user = $this->getReference(DummyUser::class, DummyUserFixtures::USER_ADMIN);
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER_ADMIN);

        $result = $this->vote(
            null,
            UserVoter::MODULE_TERMS_ADMIN,
            $user
        );

        $this->assertSame(Voter::ACCESS_GRANTED, $result);
    }
}
