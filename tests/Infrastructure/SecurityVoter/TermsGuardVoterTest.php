<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\SecurityVoter;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\VoterTestCase;
use RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\TermsGuardVoter;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/** @covers \RichId\TermsModuleBundle\Infrastructure\SecurityVoter\TermsGuardVoter */
#[TestConfig('fixtures')]
final class TermsGuardVoterTest extends VoterTestCase
{
    /** @var TermsGuardVoter */
    public $voter;

    public function testVoterWithNotSupportedSubject(): void
    {
        $subject = new DummyUser();

        $result = $this->vote(
            $subject,
            TermsGuardVoter::MODULE_TERMS_GUARD_VALID
        );

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testVoterWithNotSupportedAttribute(): void
    {
        $subject = DummyTermsGuardValidation::create('', '', '');

        $result = $this->vote(
            $subject,
            'other'
        );

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testVoterWithoutFoundedGuard(): void
    {
        $subject = DummyTermsGuardValidation::create('other', 'user', '42');

        $result = $this->vote(
            $subject,
            TermsGuardVoter::MODULE_TERMS_GUARD_VALID
        );

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVoterCheckNotLoggedUser(): void
    {
        $subject = DummyTermsGuardValidation::create('terms-5', 'user', '42');

        $result = $this->vote(
            $subject,
            TermsGuardVoter::MODULE_TERMS_GUARD_VALID
        );

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoterCheckUnauthorized(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $subject = DummyTermsGuardValidation::create('terms-5', 'user', '42');

        $result = $this->vote(
            $subject,
            TermsGuardVoter::MODULE_TERMS_GUARD_VALID
        );

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVoterCheckAuthorized(): void
    {
        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $subject = DummyTermsGuardValidation::create('terms-5', 'user', 'my_user_1');

        $result = $this->vote(
            $subject,
            TermsGuardVoter::MODULE_TERMS_GUARD_VALID
        );

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
