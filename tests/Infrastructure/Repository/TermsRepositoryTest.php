<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Repository;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository
 * @TestConfig("fixtures")
 */
final class TermsRepositoryTest extends TestCase
{
    /** @var TermsRepository */
    public $repository;

    public function testFindAllOrderedByName(): void
    {
        $terms = $this->repository->findAllOrderedByName();

        $this->assertCount(5, $terms);

        $this->assertSame('Terms 1', $terms[0]->getName());
        $this->assertSame('Terms 2', $terms[1]->getName());
        $this->assertSame('Terms 3', $terms[2]->getName());
        $this->assertSame('Terms 4', $terms[3]->getName());
        $this->assertSame('Terms 5', $terms[4]->getName());
    }
}
