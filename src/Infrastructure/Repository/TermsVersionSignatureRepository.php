<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Model\SignatureListForm;

/** @extends ServiceEntityRepository<TermsVersionSignature> */
class TermsVersionSignatureRepository extends ServiceEntityRepository
{
    /** @codeCoverageIgnore  */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TermsVersionSignature::class);
    }

    /** @return Paginator<TermsVersionSignature> */
    public function findForSearch(SignatureListForm $data): Paginator
    {
        $offset = ($data->getPage() - 1) * $data->getNumberItemsPerPage();

        $qb = $this->createQueryBuilder('s')
            ->join('s.version', 'v')
            ->setFirstResult($offset)
            ->setMaxResults($data->getNumberItemsPerPage());

        if (!empty($data->getSearch())) {
            $qb->andWhere($qb->expr()->like('LOWER(s.signedByName)', ':search'))
                ->setParameter('search', '%' . \addslashes(mb_strtolower($data->getSearch())) . '%');
        }

        if ($data->getTerms() !== null) {
            $qb->andWhere('v.terms = :terms')
                ->setParameter('terms', $data->getTerms()->getId());
        }

        switch ($data->getSort()) {
            case SignatureListForm::SORT_DATE:
                $qb->orderBy('s.date', $data->getSortDirection());
                break;
            default:
                $qb->orderBy('LOWER(s.signedByNameForSort)', $data->getSortDirection() === SignatureListForm::SORT_ASC ? SignatureListForm::SORT_DESC : SignatureListForm::SORT_ASC);
        }

        return new Paginator($qb);
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
