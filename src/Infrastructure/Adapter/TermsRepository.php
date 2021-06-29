<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Port\TermsRepositoryInterface;

/** @extends ServiceEntityRepository<Terms> */
class TermsRepository extends ServiceEntityRepository implements TermsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terms::class);
    }

    public function findOneBySlug(string $termsSlug): ?Terms
    {
        return $this->findOneBy(['slug' => $termsSlug]);
    }
}
