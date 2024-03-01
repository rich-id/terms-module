<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Infrastructure\Adapter\TermsRepository;

/** @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\TermsRepository */
#[TestConfig('fixtures')]
final class TermsRepositoryTest extends TestCase
{
    /** @var TermsRepository */
    public $adapter;

    public function testFindOneBySlugNotFound(): void
    {
        $terms = $this->adapter->findOneBySlug('terms-999');

        $this->assertNull($terms);
    }

    public function testFindOneBySlug(): void
    {
        $terms = $this->adapter->findOneBySlug('terms-1');

        $this->assertInstanceOf(Terms::class, $terms);

        $slug = $terms ? $terms->getSlug() : '';
        $this->assertSame('terms-1', $slug);
    }
}
