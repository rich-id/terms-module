<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Attribute\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\InvalidTermsEditionException;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Infrastructure\Adapter\Validator;

/** @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\Validator */
#[TestConfig('fixtures')]
final class ValidatorTest extends TestCase
{
    /** @var Validator */
    public $adapter;

    public function testValidateTermsEditionValid(): void
    {
        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $model = new TermsEdition($termsVersion);

        $this->adapter->validateTermsEdition($model);
        $this->assertTrue(true);
    }

    public function testValidateTermsEditionWithError(): void
    {
        $this->expectException(InvalidTermsEditionException::class);
        $this->expectExceptionMessage('Invalid model TermsEdition.');

        $termsVersion = $this->getReference(TermsVersion::class, 'v3-terms-1');

        $model = new TermsEdition($termsVersion);
        $model->setTitle('');
        $model->setContent('');

        $this->adapter->validateTermsEdition($model);
    }
}
