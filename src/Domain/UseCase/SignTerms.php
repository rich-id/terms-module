<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;

class SignTerms
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

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject, ?bool $accepted): void
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

        if ($accepted === null) {
            return;
        }

        if ($accepted) {
        }

        $this->notify($lastVersion);
        $this->log($lastVersion);
    }
}
