<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Exception\InvalidTermsEditionException;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\Port\ValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class Validator implements ValidatorInterface
{
    /** @var SymfonyValidatorInterface */
    protected $validator;

    public function __construct(SymfonyValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateTermsEdition(TermsEdition $termsEdition): void
    {
        $violations = $this->validator->validate($termsEdition);

        if (\count($violations) > 0) {
            throw new InvalidTermsEditionException($violations);
        }
    }
}
