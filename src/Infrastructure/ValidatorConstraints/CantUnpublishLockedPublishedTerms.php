<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints;

use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CantUnpublishLockedPublishedTerms extends Constraint implements ConstraintValidatorInterface
{
    public const MESSAGE = 'terms_edition.validation.cant_unpublish_locked_published_terms';

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
        if (!$value instanceof TermsEdition || $value->isTermsPublished()) {
            return;
        }

        $originalTermsVersion = $value->getEntity();
        $originalTerms = $originalTermsVersion->getTerms();

        if ($originalTerms->isPublished() && $originalTerms->isDepublicationLocked()) {
            $this->context->buildViolation(self::MESSAGE)
                ->atPath('isTermsPublished')
                ->setTranslationDomain('terms_module_validation')
                ->addViolation();
        }
    }

    public function validatedBy(): string
    {
        return self::class;
    }
}
