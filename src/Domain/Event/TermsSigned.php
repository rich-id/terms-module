<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Event;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

final class TermsSigned
{
    /** @var TermsVersion */
    private $termsVersion;

    /** @var TermsSubjectInterface */
    private $subject;

    /** @var bool */
    private $accepted;

    public function __construct(TermsVersion $termsVersion, TermsSubjectInterface $subject, bool $accepted)
    {
        $this->termsVersion = $termsVersion;
        $this->subject = $subject;
        $this->accepted = $accepted;
    }

    public function getTermsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }

    public function getSubject(): TermsSubjectInterface
    {
        return $this->subject;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }
}
