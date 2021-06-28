<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Entity;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature
 * @TestConfig("kernel")
 */
final class TermsVersionSignatureTest extends TestCase
{
    public function testSignatureUniqueForTermsVersionAndSubject(): void
    {
        $terms = new Terms();
        $terms->setSlug('slug');

        $this->getManager()->persist($terms);
        $this->getManager()->flush();

        $termsVersion = new TermsVersion();
        $termsVersion->setVersion(1);
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('title');
        $termsVersion->setContent('content');

        $this->getManager()->persist($termsVersion);
        $this->getManager()->flush();

        $entity1 = TermsVersionSignature::sign($termsVersion, DummySubject::create('user', '42'));
        $this->getManager()->persist($entity1);
        $this->getManager()->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $entity2 = TermsVersionSignature::sign($termsVersion, DummySubject::create('user', '42'));
        $this->getManager()->persist($entity2);
        $this->getManager()->flush();
    }

    public function testEntity(): void
    {
        $termsVersion = new TermsVersion();

        $entity = TermsVersionSignature::sign($termsVersion, DummySubject::create('user', '42'));

        $this->assertNull($entity->getId());
        $this->assertInstanceOf(\DateTime::class, $entity->getDate());
        $this->assertSame('user', $entity->getSubjectType());
        $this->assertSame('42', $entity->getSubjectIdentifier());
        $this->assertSame($termsVersion, $entity->getVersion());
    }
}
