<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidTermsEditionException extends TermsModuleException
{
    /** @var ConstraintViolationListInterface<int, ConstraintViolationInterface> */
    protected $violations;

    /** @param ConstraintViolationListInterface<int, ConstraintViolationInterface> $violations */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        parent::__construct('Invalid model TermsEdition.');

        $this->violations = $violations;
    }

    /** @return ConstraintViolationListInterface<int, ConstraintViolationInterface> */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
