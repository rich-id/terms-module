<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

class InvalidValueException extends TermsModuleException
{
    /** @var string */
    protected $propertyPath;

    /** @var mixed */
    protected $value;

    /** @param mixed $value */
    public function __construct(string $propertyPath, $value)
    {
        $this->propertyPath = $propertyPath;
        $this->value = $value;
        $message = \sprintf('Invalid value for path %s', $propertyPath);

        parent::__construct($message);
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /** @return mixed */
    public function getValue()
    {
        return $this->value;
    }
}
