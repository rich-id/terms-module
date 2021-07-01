<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;

class EntityRecorder implements EntityRecoderInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveSignature(TermsVersionSignature $signature): void
    {
        $this->entityManager->persist($signature);
        $this->entityManager->flush();
    }
}
