<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Fetcher;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignTerms;

class GetTermsVersionToSign
{
    /** @var TermsRepositoryInterface */
    protected $termsRepository;

    /** @var HasSignTerms */
    protected $hasSignTerms;

    public function __construct(TermsRepositoryInterface $termsRepository, HasSignTerms $hasSignTerms)
    {
        $this->termsRepository = $termsRepository;
        $this->hasSignTerms = $hasSignTerms;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): TermsVersion
    {
        $terms = $this->termsRepository->findOneBySlug($termsSlug);

        if ($terms === null) {
            throw new NotFoundTermsException($termsSlug);
        }

        $lastVersion = $terms->getLatestVersion();

        if ($lastVersion === null) {
            throw new TermsHasNoPublishedVersionException($terms);
        }

        if (($this->hasSignTerms)($termsSlug, $subject) === HasSignTerms::HAS_SIGN_LATEST_VERSION) {
            throw new AlreadySignLastTermsVersionException($termsSlug, $subject);
        }

        return $lastVersion;
    }
}
