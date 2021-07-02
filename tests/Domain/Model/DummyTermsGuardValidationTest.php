<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Model;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation;
use RichId\TermsModuleBundle\Domain\Model\TermsGuardValidationInterface;

/** @covers \RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation */
final class DummyTermsGuardValidationTest extends TestCase
{
    public function testModel(): void
    {
        $model = DummyTermsGuardValidation::create('terms_slug', 'subject_type', 'subject_identifier');

        $this->assertInstanceOf(TermsGuardValidationInterface::class, $model);
        $this->assertSame('terms_slug', $model->getTermsSlug());
        $this->assertSame('subject_type', $model->getTermsSubjectType());
        $this->assertSame('subject_identifier', $model->getTermsSubjectIdentifier());
    }
}
