<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

/** @extends ServiceEntityRepository<TermsVersionSignature> */
class TermsVersionSignatureRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore  */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsVersionSignature::class);
    }

    /** @return array<TermsVersionSignature> */
    public function findAllForSearch(): array
    {
        return $this->findBy([], ['signedByName' => 'ASC']);
    }

    public function findOneByVersionAndSubject(TermsVersion $termsVersion, TermsSubjectInterface $subject): ?TermsVersionSignature
    {
        return $this->createQueryBuilder('tvs')
            ->where('tvs.version = :version')
            ->andWhere('tvs.subjectType = :subjectType')
            ->andWhere('tvs.subjectIdentifier = :subjectIdentifier')
            ->setParameters(
                [
                    'version'           => $termsVersion->getId(),
                    'subjectType'       => $subject->getTermsSubjectType(),
                    'subjectIdentifier' => $subject->getTermsSubjectIdentifier(),
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();
    }
}
