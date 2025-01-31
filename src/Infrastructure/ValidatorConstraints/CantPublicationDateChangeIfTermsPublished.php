<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints;

use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CantPublicationDateChangeIfTermsPublished extends Constraint implements ConstraintValidatorInterface
{
    public const MESSAGE = 'terms_edition.validation.cant_publication_date_change_if_terms_published';

    /** @var ExecutionContextInterface */
    protected $context;

    public function initialize(ExecutionContextInterface $context): void
    {
        $this->context = $context;
    }

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof TermsEdition || !$value->getEntity()->isEnabled()) {
            return;
        }

        $originalTermsVersion = $value->getEntity();

        if ($value->getPublicationDate() !== $originalTermsVersion->getPublicationDate()) {
            $this->context->buildViolation(self::MESSAGE)
                ->atPath('publicationDate')
                ->setTranslationDomain('terms_module_validation')
                ->addViolation();
        }
    }

    public function validatedBy(): string
    {
        return self::class;
    }
}
