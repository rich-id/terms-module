<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;
use RichId\TermsModuleBundle\Domain\Port\TermsVersionRepositoryInterface;

class HasSignTerms
{
    public const HAS_SIGN_LATEST_VERSION = 0;
    public const HAS_SIGN_OLD_VERSION = 1;
    public const HAS_NOT_SIGN = 2;

    /** @var TermsRepositoryInterface */
    protected $termsRepository;

    /** @var TermsVersionRepositoryInterface */
    protected $termsVersionRepository;

    public function __construct(TermsRepositoryInterface $termsRepository, TermsVersionRepositoryInterface $termsVersionRepository)
    {
        $this->termsRepository = $termsRepository;
        $this->termsVersionRepository = $termsVersionRepository;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject): int
    {
        $terms = $this->termsRepository->findOneBySlug($termsSlug);

        if ($terms === null) {
            throw new NotFoundTermsException($termsSlug);
        }

        if (!$terms->isPublished()) {
            return self::HAS_NOT_SIGN;
        }

        $lastSignedVersion = $this->termsVersionRepository->findLastSignedVersionForTermsSubject($termsSlug, $subject);

        if ($lastSignedVersion === null) {
            return self::HAS_NOT_SIGN;
        }

        $terms = $lastSignedVersion->getTerms();
        $lastVersion = $terms->getLatestVersion();

        if ($lastVersion === null || $lastVersion->getId() !== $lastSignedVersion->getId()) {
            return self::HAS_SIGN_OLD_VERSION;
        }

        return self::HAS_SIGN_LATEST_VERSION;
    }
}
