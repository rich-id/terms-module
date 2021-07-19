<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Factory;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Factory\TermsVersionFactory;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Factory\TermsVersionFactory
 * @TestConfig("fixtures")
 */
final class TermsVersionFactoryTest extends TestCase
{
    /** @var TermsVersionFactory */
    public $factory;

    public function testBuildDefaultVersion(): void
    {
        $terms = new Terms();

        $entity = $this->factory->buildDefaultVersion($terms);

        $this->assertNull($entity->getId());
        $this->assertSame($terms, $entity->getTerms());
        $this->assertSame(1, $entity->getVersion());
        $this->assertFalse($entity->isEnabled());
        $this->assertNull($entity->getTitle());
        $this->assertNull($entity->getContent());
        $this->assertNull($entity->getPublicationDate());
        $this->assertEmpty($entity->getSignatures());
    }

    public function testBuildFromCopy(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $this->assertSame(3, $termsVersion->getVersion());

        $entity = $this->factory->buildFromCopy($termsVersion);

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getPublicationDate());
        $this->assertFalse($entity->isEnabled());
        $this->assertEmpty($entity->getSignatures());

        $this->assertSame($termsVersion->getTerms(), $entity->getTerms());
        $this->assertSame(5, $entity->getVersion());
        $this->assertSame('Title Version 3', $entity->getTitle());
        $this->assertSame('Content Version 3', $entity->getContent());
    }
}
