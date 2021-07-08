<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Port\TermsVersionRepositoryInterface;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository as InfrastructureTermsVersionRepository;

class TermsVersionRepository implements TermsVersionRepositoryInterface
{
    /** @var InfrastructureTermsVersionRepository */
    protected $termsVersionRepository;

    public function __construct(InfrastructureTermsVersionRepository $termsVersionRepository)
    {
        $this->termsVersionRepository = $termsVersionRepository;
    }

    public function findLastSignedVersionForTermsSubject(string $termsSlug, TermsSubjectInterface $subject): ?TermsVersion
    {
        return $this->termsVersionRepository->findLastSignedVersionForTermsSubject($termsSlug, $subject);
    }
}
