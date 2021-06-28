<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Port\TermsVersionRepositoryInterface;

class TermsVersionRepository extends ServiceEntityRepository implements TermsVersionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsVersion::class);
    }

    public function findLastSignedVersionForTermsSubject(string $termsSlug, TermsSubjectInterface $subject): ?TermsVersion
    {
        $qb = $this->createQueryBuilder('tv');

        return $qb->join('tv.terms', 't')
            ->join('tv.signatures', 's')
            ->where('t.slug = :termsSlug')
            ->andWhere('s.subjectType = :subjectType')
            ->andWhere('s.subjectIdentifier = :subjectIdentifier')
            ->setParameters(

                [
                    'termsSlug'         => $termsSlug,
                    'subjectType'       => $subject->getTermsSubjectType(),
                    'subjectIdentifier' => $subject->getTermsSubjectIdentifier(),
                ]
            )
            ->orderBy('tv.version', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
