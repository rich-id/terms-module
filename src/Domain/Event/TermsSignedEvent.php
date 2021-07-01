<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Event;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use Symfony\Component\HttpFoundation\Response;

final class TermsSignedEvent
{
    /** @var TermsVersion */
    private $termsVersion;

    /** @var TermsSubjectInterface */
    private $subject;

    /** @var bool|null */
    private $accepted;

    /** @var Response */
    private $response;

    public function __construct(TermsVersion $termsVersion, TermsSubjectInterface $subject, ?bool $accepted, Response $response)
    {
        $this->termsVersion = $termsVersion;
        $this->subject = $subject;
        $this->accepted = $accepted;
        $this->response = $response;
    }

    public function getTermsVersion(): TermsVersion
    {
        return $this->termsVersion;
    }

    public function getSubject(): TermsSubjectInterface
    {
        return $this->subject;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }
}
