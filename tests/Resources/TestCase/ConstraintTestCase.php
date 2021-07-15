<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\TestCase;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Tests\Resources\Stubs\ValidationContextStub;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintTestCase extends TestCase
{
    /** @var Constraint|ConstraintValidatorInterface */
    protected $validator;

    /** @var ValidationContextStub */
    public $context;

    public function beforeTest(): void
    {
        $this->context->setConstraint($this->validator); /* @phpstan-ignore-line */
        $this->validator->initialize($this->context); /* @phpstan-ignore-line */
    }

    /**
     * @param mixed      $value
     * @param mixed|null $object
     *
     * @return ConstraintViolationListInterface<int, ConstraintViolationInterface>
     */
    public function validate($value, $object = null): ConstraintViolationListInterface
    {
        $this->context->setNode($value, $object, null, '');
        $violations = $this->validator->validate($value, $this->validator); /* @phpstan-ignore-line */

        return $violations ?? $this->context->getViolations();
    }
}
