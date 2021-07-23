<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Fetcher;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;

class GetLastPublishedTermsVersion
{
    /** @var TermsRepositoryInterface */
    protected $termsRepository;

    public function __construct(TermsRepositoryInterface $termsRepository)
    {
        $this->termsRepository = $termsRepository;
    }

    public function __invoke(string $termsSlug): TermsVersion
    {
        $terms = $this->getTermsBySlug($termsSlug);

        if (!$terms->isPublished()) {
            throw new NotPublishedTermsException($termsSlug);
        }

        $lastVersion = $terms->getLatestPublishedVersion();

        if ($lastVersion === null) {
            throw new TermsHasNoPublishedVersionException($terms);
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
