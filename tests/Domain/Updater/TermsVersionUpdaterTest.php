<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Updater;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\InvalidValueException;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\Updater\TermsVersionUpdater;

/**
 * @covers \RichId\TermsModuleBundle\Domain\Updater\TermsVersionUpdater
 * @TestConfig("kernel")
 */
final class TermsVersionUpdaterTest extends TestCase
{
    /** @var TermsVersionUpdater */
    public $updater;

    public function testUpdateWithInvalidTitle(): void
    {
        $this->expectException(InvalidValueException::class);

        $date = new \DateTime();
        $entity = new TermsVersion();
        $entity->setTerms(new Terms());

        $model = new TermsEdition($entity);

        $model->setContent('My content');
        $model->setPublicationDate($date);

        ($this->updater)($entity, $model);
    }

    public function testUpdateWithInvalidContent(): void
    {
        $this->expectException(InvalidValueException::class);

        $date = new \DateTime();
        $entity = new TermsVersion();
        $entity->setTerms(new Terms());

        $model = new TermsEdition($entity);

        $model->setTitle('My Title');
        $model->setPublicationDate($date);

        ($this->updater)($entity, $model);
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

        ($this->updater)($entity, $model);

        $this->assertNull($entity->getId());
        $this->assertNull($entity->getVersion());
        $this->assertFalse($entity->isEnabled());
        $this->assertEmpty($entity->getSignatures());

        $this->assertSame('My Title', $entity->getTitle());
        $this->assertSame('My content', $entity->getContent());
        $this->assertSame($date, $entity->getPublicationDate());
    }
}
