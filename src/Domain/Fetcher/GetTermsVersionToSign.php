<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Fetcher;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms;

class GetTermsVersionToSign
{
    /** @var GetLastPublishedTermsVersion */
    protected $getLastPublishedTermsVersion;

    /** @var HasSignedTerms */
    protected $hasSignedTerms;

    public function __construct(GetLastPublishedTermsVersion $getLastPublishedTermsVersion, HasSignedTerms $hasSignedTerms)
    {
        $this->getLastPublishedTermsVersion = $getLastPublishedTermsVersion;
        $this->hasSignedTerms = $hasSignedTerms;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): TermsVersion
    {
        $lastVersion = ($this->getLastPublishedTermsVersion)($termsSlug);

        if (($this->hasSignedTerms)($termsSlug, $subject) === HasSignedTerms::HAS_SIGNED_LATEST_VERSION) {
            throw new AlreadySignLastTermsVersionException($termsSlug, $subject);
        }

        return $lastVersion;
    }
}
