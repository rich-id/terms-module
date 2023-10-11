<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Factory;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Factory\TermsVersionSignatureFactory;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\Fixtures\DummyUserFixtures;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Factory\TermsVersionSignatureFactory
 * @TestConfig("fixtures")
 */
final class TermsVersionSignatureFactoryTest extends TestCase
{
    /** @var TermsVersionSignatureFactory */
    public $factory;

    public function testFactory(): void
    {
        $terms = new Terms();
        $terms->setSlug('terms-5');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $entity = ($this->factory)($termsVersion, DummySubject::create('user', '42'));

        $this->assertNull($entity->getId());
        $this->assertInstanceOf(\DateTime::class, $entity->getDate());
        $this->assertSame('user', $entity->getSubjectType());
        $this->assertSame('42', $entity->getSubjectIdentifier());
        $this->assertSame('Unknown', $entity->getSubjectName());
        $this->assertSame($termsVersion, $entity->getVersion());
        $this->assertNull($entity->getSignedBy());
    }

    public function testFactoryLoggedUser(): void
    {
        $terms = new Terms();
        $terms->setSlug('terms-5');

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $this->authenticate(DummyUser::class, DummyUserFixtures::USER);

        $entity = ($this->factory)($termsVersion, DummySubject::create('user', '42'));

        $this->assertNull($entity->getId());
        $this->assertInstanceOf(\DateTime::class, $entity->getDate());
        $this->assertSame('user', $entity->getSubjectType());
        $this->assertSame('42', $entity->getSubjectIdentifier());
        $this->assertSame('my_user_1', $entity->getSubjectName());
        $this->assertSame($termsVersion, $entity->getVersion());
        $this->assertSame('my_user_1', $entity->getSignedBy());
    }
}
