<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\ValidatorConstraints;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantContentChangeIfTermsPublished;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\TestCase\ConstraintTestCase;

/** @covers \RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantContentChangeIfTermsPublished */
#[TestConfig('kernel')]
final class CantContentChangeIfTermsPublishedTest extends ConstraintTestCase
{
    /** @var CantContentChangeIfTermsPublished */
    public $validator;

    public function testGetTargets(): void
    {
        $this->assertSame('class', $this->validator->getTargets());
    }

    public function testValidatedBy(): void
    {
        $this->assertSame(CantContentChangeIfTermsPublished::class, $this->validator->validatedBy());
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
        $termsVersion->setContent('Old content');

        $model = new TermsEdition($termsVersion);
        $model->setContent('New Content');

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorEnabledTermsVersionContentNotChange(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setContent('Old content');
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setContent('Old content');

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorEnabledTermsVersionContentChange(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->setContent('Old content');
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setContent('New content');

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('The content cannot be changed when the version is published.', $violations[0]->getMessage());
        $this->assertSame('content', $violations[0]->getPropertyPath());
    }
}
