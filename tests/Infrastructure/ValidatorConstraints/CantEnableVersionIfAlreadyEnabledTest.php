<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\ValidatorConstraints;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantEnableVersionIfAlreadyEnabled;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;
use RichId\TermsModuleBundle\Tests\Resources\TestCase\ConstraintTestCase;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints\CantEnableVersionIfAlreadyEnabled
 * @TestConfig("kernel")
 */
final class CantEnableVersionIfAlreadyEnabledTest extends ConstraintTestCase
{
    /** @var CantEnableVersionIfAlreadyEnabled */
    public $validator;

    public function testGetTargets(): void
    {
        $this->assertSame('class', $this->validator->getTargets());
    }

    public function testValidatedBy(): void
    {
        $this->assertSame(CantEnableVersionIfAlreadyEnabled::class, $this->validator->validatedBy());
    }

    public function testValidatorWithBadClass(): void
    {
        $violations = $this->validate(new DummyUser());
        $this->assertEmpty($violations);
    }

    public function testValidatorWithoutActivation(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorNeedVersionActivationNotEnabled(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);

        $model = new TermsEdition($termsVersion);
        $model->setNeedVersionActivation(true);

        $violations = $this->validate($model);
        $this->assertEmpty($violations);
    }

    public function testValidatorNeedVersionActivationAlreadyEnabled(): void
    {
        $terms = new Terms();
        $termsVersion = new TermsVersion();
        $termsVersion->setTerms($terms);
        $termsVersion->enable();

        $model = new TermsEdition($termsVersion);
        $model->setNeedVersionActivation(true);

        $violations = $this->validate($model);

        $this->assertCount(1, $violations);
        $this->assertSame('The version is already activated.', $violations[0]->getMessage());
        $this->assertSame('needVersionActivation', $violations[0]->getPropertyPath());
    }
}
