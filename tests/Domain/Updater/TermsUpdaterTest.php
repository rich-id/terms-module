<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Updater;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\Updater\TermsUpdater;

/** @covers \RichId\TermsModuleBundle\Domain\Updater\TermsUpdater */
#[TestConfig('fixtures')]
final class TermsUpdaterTest extends TestCase
{
    /** @var TermsUpdater */
    public $updater;

    public function testUpdate(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');

        $entity = new Terms();
        $model = new TermsEdition($termsVersion);

        ($this->updater)($entity, $model);

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getSlug());
        $this->assertNull($entity->getName());
        $this->assertFalse($entity->isDepublicationLocked());
        $this->assertEmpty($entity->getVersions());
        $this->assertTrue($entity->isPublished());
    }
}
