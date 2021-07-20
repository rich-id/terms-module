<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

class NotFoundTermsException extends TermsModuleException
{
    /** @var string */
    protected $termsSlug;

    public function __construct(string $termsSlug)
    {
        $this->termsSlug = $termsSlug;
        $message = \sprintf('Not found terms %s.', $termsSlug);

        parent::__construct($message);
    }

    public function getTermsSlug(): string
    {
        return $this->termsSlug;
    }
}
