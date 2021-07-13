<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Model;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;

/** @covers \RichId\TermsModuleBundle\Domain\Model\TermsEdition */
final class TermsEditionTest extends TestCase
{
    public function testConstruct(): void
    {
        $date = new \DateTime();

        $terms = new Terms();
        $terms->setIsPublished(true);

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('My title');
        $termsVersion->setContent('My content');
        $termsVersion->setPublicationDate($date);

        $model = new TermsEdition($termsVersion);

        $this->assertTrue($model->isTermsPublished());
        $this->assertSame('My title', $model->getTitle());
        $this->assertSame('My content', $model->getContent());
        $this->assertSame($date, $model->getPublicationDate());
        $this->assertSame($termsVersion, $model->getEntity());
    }

    public function testModel(): void
    {
        $date = new \DateTime();

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms(new Terms());

        $model = new TermsEdition($termsVersion);
        $model->setIsTermsPublished(true);
        $model->setTitle('My title');
        $model->setContent('My content');
        $model->setPublicationDate($date);

        $this->assertTrue($model->isTermsPublished());
        $this->assertSame('My title', $model->getTitle());
        $this->assertSame('My content', $model->getContent());
        $this->assertSame($date, $model->getPublicationDate());
        $this->assertSame($termsVersion, $model->getEntity());
    }
}
