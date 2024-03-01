<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\ValidatorConstraints;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantPublicationDateChangeIfTermsPublished;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\TestCase\ConstraintTestCase;

/** @covers \RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantPublicationDateChangeIfTermsPublished */
#[TestConfig('kernel')]
final class CantPublicationDateChangeIfTermsPublishedTest extends ConstraintTestCase
{
    /** @var CantPublicationDateChangeIfTermsPublished */
    public $validator;

    public function testGetTargets(): void
    {
        $this->assertSame('class', $this->validator->getTargets());
    }

    public function testValidatedBy(): void
    {
        $this->assertSame(CantPublicationDateChangeIfTermsPublished::class, $this->validator->validatedBy());
    }

    public function testValidatorWithBadClass(): void
    {
        $violations = $this->validate(new DummyUser());
        $this->assertEmpty($violations);
    }

    public function testValidatorNotEnabledTermsVersion(): void
    {
        $date1 = new \DateTime();
        $date2 = new \DateTime('- 1 day');

        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setPublicationDate($date1);

        $model = new TermsEdition($termsVersion);
        $model->setPublicationDate($date2);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorEnabledTermsVersionPublicationDateNotChange(): void
    {
        $date = new \DateTime();

        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setPublicationDate($date);
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setPublicationDate($date);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorEnabledTermsVersionPublicationDateChange(): void
    {
        $date1 = new \DateTime();
        $date2 = new \DateTime('- 1 day');

        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setPublicationDate($date1);
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setPublicationDate($date2);

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('The publication date cannot be changed when the version is published.', $violations[0]->getMessage());
        $this->assertSame('publicationDate', $violations[0]->getPropertyPath());
    }

    public function testValidatorEnabledTermsVersionPublicationDateChangeToNull(): void
    {
        $date = new \DateTime();

        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setPublicationDate($date);
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setPublicationDate(null);

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('The publication date cannot be changed when the version is published.', $violations[0]->getMessage());
        $this->assertSame('publicationDate', $violations[0]->getPropertyPath());
    }

    public function testValidatorEnabledTermsVersionPublicationDateChangeFromNull(): void
    {
        $date = new \DateTime();

        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setPublicationDate($date);

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('The publication date cannot be changed when the version is published.', $violations[0]->getMessage());
        $this->assertSame('publicationDate', $violations[0]->getPropertyPath());
    }
}
