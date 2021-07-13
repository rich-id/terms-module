<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Entity\TermsVersion
 * @TestConfig("fixtures")
 */
final class TermsVersionTest extends TestCase
{
    public function testConstruct(): void
    {
        $entity = new TermsVersion();

        $this->assertFalse($entity->isEnabled());

        $this->assertInstanceOf(ArrayCollection::class, $entity->getSignatures());
        $this->assertEmpty($entity->getSignatures());
    }

    public function testVersionUniqueForTerms(): void
    {
        $terms = new Terms();
        $terms->setSlug('slug');
        $terms->setName('My Terms');

        $this->getManager()->persist($terms);
        $this->getManager()->flush();

        $entity1 = new TermsVersion();
        $entity1->setVersion(1);
        $entity1->setTerms($terms);
        $entity1->setTitle('title');
        $entity1->setContent('content');

        $this->getManager()->persist($entity1);
        $this->getManager()->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $entity2 = new TermsVersion();
        $entity2->setVersion(1);
        $entity2->setTerms($terms);
        $entity2->setTitle('title');
        $entity2->setContent('content');

        $this->getManager()->persist($entity2);
        $this->getManager()->flush();
    }

    public function testEntity(): void
    {
        $entity = new TermsVersion();
        $terms = new Terms();
        $signature = new TermsVersionSignature();
        $date = new \DateTime();

        $entity->setVersion(1);
        $entity->enable();
        $entity->setTitle('title');
        $entity->setContent('content');
        $entity->setPublicationDate($date);
        $entity->setTerms($terms);
        $entity->addSignature($signature);

        $this->assertNull($entity->getId());
        $this->assertSame(1, $entity->getVersion());
        $this->assertTrue($entity->isEnabled());
        $this->assertSame('title', $entity->getTitle());
        $this->assertSame('content', $entity->getContent());
        $this->assertSame($date, $entity->getPublicationDate());
        $this->assertSame($terms, $entity->getTerms());

        $this->assertCount(1, $entity->getSignatures());
        $this->assertSame($signature, $entity->getSignatures()->first());

        $entity->removeSignature($signature);
        $this->assertEmpty($entity->getSignatures());
    }

    public function testBuildDefaultVersion(): void
    {
        $terms = new Terms();
        $entity = TermsVersion::buildDefaultVersion($terms);

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getTitle());
        $this->assertNull($entity->getContent());
        $this->assertNull($entity->getPublicationDate());
        $this->assertSame(1, $entity->getVersion());
        $this->assertFalse($entity->isEnabled());
        $this->assertEmpty($entity->getSignatures());
    }

    public function testBuildFromCopy(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $entity = TermsVersion::buildFromCopy($termsVersion);

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getPublicationDate());
        $this->assertFalse($entity->isEnabled());
        $this->assertEmpty($entity->getSignatures());

        $this->assertSame($termsVersion->getTerms(), $entity->getTerms());
        $this->assertSame(4, $entity->getVersion());
        $this->assertSame('Title Version 3', $entity->getTitle());
        $this->assertSame('Content Version 3', $entity->getContent());
    }

    public function testUpdate(): void
    {
        $date = new \DateTime();
        $entity = new TermsVersion();
        $entity->setTerms(new Terms());

        $model = new TermsEdition($entity);

        $model->setTitle('My Title');
        $model->setContent('My content');
        $model->setPublicationDate($date);

        $entity->update($model);

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getVersion());
        $this->assertFalse($entity->isEnabled());
        $this->assertEmpty($entity->getSignatures());

        $this->assertSame('My Title', $entity->getTitle());
        $this->assertSame('My content', $entity->getContent());
        $this->assertSame($date, $entity->getPublicationDate());
    }
}
