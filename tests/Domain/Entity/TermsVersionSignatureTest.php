<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Entity;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

/** @covers \RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature */
#[TestConfig('kernel')]
final class TermsVersionSignatureTest extends TestCase
{
    public function testEntity(): void
    {
        $date = new \DateTime();
        $termsVersion = new TermsVersion();
        $entity = new TermsVersionSignature();

        $entity->setVersion($termsVersion);
        $entity->setSubjectType('user');
        $entity->setSubjectIdentifier('42');
        $entity->setSignedBy('user_1');
        $entity->setDate($date);

        $this->assertNull($entity->getId());
        $this->assertSame($termsVersion, $entity->getVersion());
        $this->assertSame('user', $entity->getSubjectType());
        $this->assertSame('42', $entity->getSubjectIdentifier());
        $this->assertSame('user_1', $entity->getSignedBy());
        $this->assertSame($date, $entity->getDate());
    }

    public function testSignatureUniqueForTermsVersionAndSubject(): void
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

        $entity1 = new TermsVersionSignature();
        $entity1->setVersion($termsVersion);
        $entity1->setSubjectType('user');
        $entity1->setSubjectIdentifier('42');
        $entity1->setSubjectName('Mr John SMITH');
        $entity1->setDate(new \DateTime());

        $this->getManager()->persist($entity1);
        $this->getManager()->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $entity2 = new TermsVersionSignature();
        $entity2->setVersion($termsVersion);
        $entity2->setSubjectType('user');
        $entity2->setSubjectIdentifier('42');
        $entity2->setSubjectName('Mr John SMITH');
        $entity2->setDate(new \DateTime());

        $this->getManager()->persist($entity2);
        $this->getManager()->flush();
    }
}
