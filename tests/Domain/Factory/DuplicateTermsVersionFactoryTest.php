<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Factory;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\InvalidValueException;
use RichId\TermsModuleBundle\Domain\Factory\DuplicateTermsVersionFactory;

/** @covers \RichId\TermsModuleBundle\Domain\Factory\DuplicateTermsVersionFactory */
#[TestConfig('fixtures')]
final class DuplicateTermsVersionFactoryTest extends TestCase
{
    /** @var DuplicateTermsVersionFactory */
    public $factory;

    public function testFactoryWithBadTitle(): void
    {
        $this->expectException(InvalidValueException::class);

        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $termsVersion->setTitle('');

        ($this->factory)($termsVersion);
    }

    public function testFactoryWithBadContent(): void
    {
        $this->expectException(InvalidValueException::class);

        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $termsVersion->setContent('');

        ($this->factory)($termsVersion);
    }

    public function testFactory(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $this->assertSame(3, $termsVersion->getVersion());

        $entity = ($this->factory)($termsVersion);

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
