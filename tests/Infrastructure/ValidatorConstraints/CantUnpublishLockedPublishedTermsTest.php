<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\ValidatorConstraints;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantUnpublishLockedPublishedTerms;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\TestCase\ConstraintTestCase;

/** @covers \RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantUnpublishLockedPublishedTerms */
#[TestConfig('kernel')]
final class CantUnpublishLockedPublishedTermsTest extends ConstraintTestCase
{
    /** @var CantUnpublishLockedPublishedTerms */
    public $validator;

    public function testGetTargets(): void
    {
        $this->assertSame('class', $this->validator->getTargets());
    }

    public function testValidatedBy(): void
    {
        $this->assertSame(CantUnpublishLockedPublishedTerms::class, $this->validator->validatedBy());
    }

    public function testValidatorWithBadClass(): void
    {
        $violations = $this->validate(new DummyUser());
        $this->assertEmpty($violations);
    }

    public function testValidatorTermsAlwaysPublished(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $model->setIsTermsPublished(true);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorUnpublishedUnpublishedVersion(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $model->setIsTermsPublished(false);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorUnpublishedUnpublishedVersionLocked(): void
    {
        $terms = new Terms();
        $terms->setIsDepublicationLocked(true);

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $model->setIsTermsPublished(false);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorUnpublishedPublishedVersion(): void
    {
        $terms = new Terms();
        $terms->setIsPublished(true);

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $model->setIsTermsPublished(false);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorUnpublishedPublishedVersionLocked(): void
    {
        $terms = new Terms();
        $terms->setIsPublished(true);
        $terms->setIsDepublicationLocked(true);

        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $model->setIsTermsPublished(false);

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('It is not possible to unpublish a locked terms.', $violations[0]->getMessage());
        $this->assertSame('isTermsPublished', $violations[0]->getPropertyPath());
    }
}
