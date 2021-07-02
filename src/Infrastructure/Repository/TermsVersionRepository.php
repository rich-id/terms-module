<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

/** @extends ServiceEntityRepository<TermsVersion> */
class TermsVersionRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore  */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsVersion::class);
    }
}
