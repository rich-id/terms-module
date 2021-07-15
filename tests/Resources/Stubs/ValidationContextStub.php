<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Stubs;

use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ValidationContextStub extends ExecutionContext
{
    public function __construct(TranslatorInterface $translator)
    {
        $validatorBuilder = new ValidatorBuilder();
        $validator = $validatorBuilder->getValidator();

        parent::__construct($validator, '', $translator);
    }

    public function countViolations(): int
    {
        return $this->getViolations()->count();
    }
}
