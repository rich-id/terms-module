<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\ValidatorConstraints;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantTitleChangeIfTermsPublished;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\TestCase\ConstraintTestCase;

/** @covers \RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantTitleChangeIfTermsPublished */
#[TestConfig('kernel')]
final class CantTitleChangeIfTermsPublishedTest extends ConstraintTestCase
{
    /** @var CantTitleChangeIfTermsPublished */
    public $validator;

    public function testGetTargets(): void
    {
        $this->assertSame('class', $this->validator->getTargets());
    }

    public function testValidatedBy(): void
    {
        $this->assertSame(CantTitleChangeIfTermsPublished::class, $this->validator->validatedBy());
    }

    public function testValidatorWithBadClass(): void
    {
        $violations = $this->validate(new DummyUser());
        $this->assertEmpty($violations);
    }

    public function testValidatorNotEnabledTermsVersion(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('Old title');

        $model = new TermsEdition($termsVersion);
        $model->setTitle('New title');

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorEnabledTermsVersionTitleNotChange(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('Old title');
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setTitle('Old title');

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorEnabledTermsVersionTitleChange(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setTitle('Old title');
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setTitle('New title');

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('The title cannot be changed when the version is published.', $violations[0]->getMessage());
        $this->assertSame('title', $violations[0]->getPropertyPath());
    }
}
