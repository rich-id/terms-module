<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Factory;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Factory\DefaultTermsVersionFactory;

/** @covers \RichId\TermsModuleBundle\Domain\Factory\DefaultTermsVersionFactory */
#[TestConfig('fixtures')]
final class DefaultTermsVersionFactoryTest extends TestCase
{
    /** @var DefaultTermsVersionFactory */
    public $factory;

    public function testFactory(): void
    {
        $terms = new Terms();

        $entity = ($this->factory)($terms);

        $this->assertNull($entity->getId());
        $this->assertSame($terms, $entity->getTerms());
        $this->assertSame(1, $entity->getVersion());
        $this->assertFalse($entity->isEnabled());
        $this->assertNull($entity->getTitle());
        $this->assertNull($entity->getContent());
        $this->assertNull($entity->getPublicationDate());
        $this->assertEmpty($entity->getSignatures());
    }
}
