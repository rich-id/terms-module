<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints;

use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/** @Annotation */
class CantEnableVersionIfAlreadyEnabled extends Constraint implements ConstraintValidatorInterface
{
    public const MESSAGE = 'terms_edition.validation.cant_enable_version_if_already_enabled';

    /** @var ExecutionContextInterface */
    protected $context;

    public function initialize(ExecutionContextInterface $context): void
    {
        $this->context = $context;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof TermsEdition) {
            return;
        }

        $originalTermsVersion = $value->getEntity();

        if ($originalTermsVersion->isEnabled() && $value->getActivateVersion()) {
            $this->context->buildViolation(self::MESSAGE)
                ->atPath('activateVersion')
                ->setTranslationDomain('terms_module_validation')
                ->addViolation();
        }
    }

    public function validatedBy(): string
    {
        return self::class;
    }
}
