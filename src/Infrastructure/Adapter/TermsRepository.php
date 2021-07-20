<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository as InfrastructureTermsRepository;

class TermsRepository implements TermsRepositoryInterface
{
    /** @var InfrastructureTermsRepository */
    protected $termsRepository;

    public function __construct(InfrastructureTermsRepository $termsRepository)
    {
        $this->termsRepository = $termsRepository;
    }

    public function findOneBySlug(string $termsSlug): ?Terms
    {
        return $this->termsRepository->findOneBySlug($termsSlug);
    }
}
