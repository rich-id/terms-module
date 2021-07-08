<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Port\EntityRemoverInterface;

class EntityRemover implements EntityRemoverInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function removeTermsVersion(TermsVersion $termsVersion): void
    {
        $this->entityManager->remove($termsVersion);
        $this->entityManager->flush();
    }
}
