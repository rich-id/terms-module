<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

class NotFoundTermsException extends TermsModuleException
{
    /** @var string */
    protected $termsSlug;

    public function __construct(string $termsSlug)
    {
        parent::__construct(\sprintf('Not found terms %s.', $termsSlug));

        $this->termsSlug = $termsSlug;
    }

    public function getTermsSlug(): string
    {
        return $this->termsSlug;
    }
}
