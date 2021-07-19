<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Fetcher;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms;

class GetTermsVersionToSign
{
    /** @var TermsRepositoryInterface */
    protected $termsRepository;

    /** @var HasSignedTerms */
    protected $hasSignedTerms;

    public function __construct(TermsRepositoryInterface $termsRepository, HasSignedTerms $hasSignedTerms)
    {
        $this->termsRepository = $termsRepository;
        $this->hasSignedTerms = $hasSignedTerms;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): TermsVersion
    {
        $terms = $this->getTermsBySlug($termsSlug);

        if (!$terms->isPublished()) {
            throw new NotPublishedTermsException($termsSlug);
        }

        $lastVersion = $terms->getLatestPublishedVersion();

        if ($lastVersion === null) {
            throw new TermsHasNoPublishedVersionException($terms);
        }

        if (($this->hasSignedTerms)($termsSlug, $subject) === HasSignedTerms::HAS_SIGNED_LATEST_VERSION) {
            throw new AlreadySignLastTermsVersionException($termsSlug, $subject);
        }

        return $lastVersion;
    }

    protected function getTermsBySlug(string $termsSlug): Terms
    {
        $terms = $this->termsRepository->findOneBySlug($termsSlug);

        if ($terms === null) {
            throw new NotFoundTermsException($termsSlug);
        }

        return $terms;
    }
}
