<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Exception;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

class SubjectNeedToSignTermsException extends TermsModuleException
{
    /** @var string */
    protected $termsSlug;

    /** @var TermsSubjectInterface */
    protected $subject;

    public function __construct(string $termsSlug, TermsSubjectInterface $subject)
    {
        parent::__construct();

        $this->termsSlug = $termsSlug;
        $this->subject = $subject;
    }

    public function getTermsSlug(): string
    {
        return $this->termsSlug;
    }

    public function getSubject(): TermsSubjectInterface
    {
        return $this->subject;
    }
}
