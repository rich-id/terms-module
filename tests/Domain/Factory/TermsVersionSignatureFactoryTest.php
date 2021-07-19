<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Factory;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Factory\TermsVersionSignatureFactory;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Factory\TermsVersionSignatureFactory
 * @TestConfig("kernel")
 */
final class TermsVersionSignatureFactoryTest extends TestCase
{
    /** @var TermsVersionSignatureFactory */
    public $factory;

    public function testSign(): void
    {
        $termsVersion = new TermsVersion();

        $entity = $this->factory->sign($termsVersion, DummySubject::create('user', '42'));

        $this->assertNull($entity->getId());
        $this->assertInstanceOf(\DateTime::class, $entity->getDate());
        $this->assertSame('user', $entity->getSubjectType());
        $this->assertSame('42', $entity->getSubjectIdentifier());
        $this->assertSame($termsVersion, $entity->getVersion());
    }

    public function testSignUniqueForTermsVersionAndSubject(): void
    {
        $terms = new Terms();
        $terms->setSlug('slug');
        $terms->setName('My Terms');

        $this->getManager()->persist($terms);
        $this->getManager()->flush();

        $termsVersion = new TermsVersion();
        $termsVersion->setVersion(1);
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('title');
        $termsVersion->setContent('content');

        $this->getManager()->persist($termsVersion);
        $this->getManager()->flush();

        $entity1 = $this->factory->sign($termsVersion, DummySubject::create('user', '42'));
        $this->getManager()->persist($entity1);
        $this->getManager()->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $entity2 = $this->factory->sign($termsVersion, DummySubject::create('user', '42'));
        $this->getManager()->persist($entity2);
        $this->getManager()->flush();
    }
}
