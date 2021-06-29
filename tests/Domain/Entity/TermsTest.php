<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Entity\Terms
 * @TestConfig("kernel")
 */
final class TermsTest extends TestCase
{
    public function testConstruct(): void
    {
        $entity = new Terms();

        $this->assertFalse($entity->isPublished());
        $this->assertFalse($entity->isDepublicationLocked());

        $this->assertInstanceOf(ArrayCollection::class, $entity->getVersions());
        $this->assertEmpty($entity->getVersions());
    }

    public function testSlugUnique(): void
    {
        $entity1 = new Terms();
        $entity1->setSlug('slug');
        $entity1->setName('My Terms');

        $this->getManager()->persist($entity1);
        $this->getManager()->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $entity2 = new Terms();
        $entity2->setSlug('slug');
        $entity2->setName('My other Terms');

        $this->getManager()->persist($entity2);
        $this->getManager()->flush();
    }

    public function testNameUnique(): void
    {
        $entity1 = new Terms();
        $entity1->setSlug('slug1');
        $entity1->setName('My Terms');

        $this->getManager()->persist($entity1);
        $this->getManager()->flush();

        $this->expectException(UniqueConstraintViolationException::class);

        $entity2 = new Terms();
        $entity2->setSlug('slug2');
        $entity2->setName('My Terms');

        $this->getManager()->persist($entity2);
        $this->getManager()->flush();
    }

    public function testEntity(): void
    {
        $entity = new Terms();
        $termVersion = new TermsVersion();

        $entity->setSlug('terms-slug');
        $entity->setName('My Terms');
        $entity->setIsPublished(true);
        $entity->setIsDepublicationLocked(true);
        $entity->addVersion($termVersion);

        $this->assertNull($entity->getId());
        $this->assertSame('terms-slug', $entity->getSlug());
        $this->assertSame('My Terms', $entity->getName());
        $this->assertTrue($entity->isPublished());
        $this->assertTrue($entity->isDepublicationLocked());

        $this->assertCount(1, $entity->getVersions());
        $this->assertSame($termVersion, $entity->getVersions()->first());

        $entity->removeVersion($termVersion);
        $this->assertEmpty($entity->getVersions());
    }

    public function testGetLatestVersionWithoutVersion(): void
    {
        $entity = new Terms();
        $this->assertNull($entity->getLatestVersion());
    }

    public function testGetLatestVersionAllVersionDisabled(): void
    {
        $entity = new Terms();
        $termVersion1 = new TermsVersion();
        $termVersion2 = new TermsVersion();

        $entity->addVersion($termVersion2);
        $entity->addVersion($termVersion1);

        $termVersion1->setVersion(1);
        $termVersion2->setVersion(2);

        $this->assertNull($entity->getLatestVersion());
    }

    public function testGetLatestVersionOneVersionEnabled(): void
    {
        $entity = new Terms();
        $termVersion1 = new TermsVersion();
        $termVersion2 = new TermsVersion();

        $entity->addVersion($termVersion2);
        $entity->addVersion($termVersion1);

        $termVersion1->setVersion(1);
        $termVersion2->setVersion(2);

        $termVersion1->enable();

        $this->assertSame($termVersion1, $entity->getLatestVersion());
    }

    public function testGetLatestVersionOneVersionEnabledButNotPublished(): void
    {
        $entity = new Terms();
        $termVersion1 = new TermsVersion();
        $termVersion2 = new TermsVersion();

        $entity->addVersion($termVersion2);
        $entity->addVersion($termVersion1);

        $termVersion1->setVersion(1);
        $termVersion2->setVersion(2);

        $termVersion1->enable();
        $termVersion1->setPublicationDate(new \DateTime('today midnight + 1 day'));

        $this->assertNull($entity->getLatestVersion());
    }

    public function testGetLatestVersionOneVersionEnabledAndPublished(): void
    {
        $entity = new Terms();
        $termVersion1 = new TermsVersion();
        $termVersion2 = new TermsVersion();

        $entity->addVersion($termVersion2);
        $entity->addVersion($termVersion1);

        $termVersion1->setVersion(1);
        $termVersion2->setVersion(2);

        $termVersion1->enable();
        $termVersion1->setPublicationDate(new \DateTime('today midnight'));

        $this->assertSame($termVersion1, $entity->getLatestVersion());
    }

    public function testGetLatestVersionOrderedByVersion(): void
    {
        $entity = new Terms();
        $termVersion1 = new TermsVersion();
        $termVersion2 = new TermsVersion();

        $entity->addVersion($termVersion2);
        $entity->addVersion($termVersion1);

        $termVersion1->setVersion(1);
        $termVersion1->enable();

        $termVersion2->setVersion(2);
        $termVersion2->enable();

        $this->assertSame($termVersion2, $entity->getLatestVersion());
    }
}
