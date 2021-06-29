<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Port\TermsVersionRepositoryInterface;

class RemoveTermsVersion
{
    /** @var TermsVersionRepositoryInterface */
    protected $termsVersionRepository;

    public function __construct(TermsVersionRepositoryInterface $termsVersionRepository)
    {
        $this->termsVersionRepository = $termsVersionRepository;
    }

    public function __invoke(TermsVersion $termsVersion): void
    {
        if ($termsVersion->isEnabled()) {
            throw new EnabledVersionCannotBeDeletedException($termsVersion);
        }

        $this->termsVersionRepository->removeTermsVersion($termsVersion);
    }
}
