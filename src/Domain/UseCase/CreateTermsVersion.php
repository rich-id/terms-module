<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionCreatedEvent;
use RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException;
use RichId\TermsModuleBundle\Domain\Factory\DuplicateTermsVersionFactory;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;

class CreateTermsVersion
{
    /** @var DuplicateTermsVersionFactory */
    protected $duplicateTermsVersionFactory;

    /** @var EntityRecoderInterface */
    protected $entityRecoder;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(DuplicateTermsVersionFactory $duplicateTermsVersionFactory, EntityRecoderInterface $entityRecoder, EventDispatcherInterface $eventDispatcher)
    {
        $this->duplicateTermsVersionFactory = $duplicateTermsVersionFactory;
        $this->entityRecoder = $entityRecoder;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(TermsVersion $basedTermsVersion): void
    {
        $terms = $basedTermsVersion->getTerms();
        $lastVersion = $terms->getLatestVersion();

        if ($lastVersion === null || !$lastVersion->isEnabled()) {
            throw new CannotAddVersionToTermsException($terms);
        }

        $newTermVersion = ($this->duplicateTermsVersionFactory)($basedTermsVersion);

        $this->entityRecoder->saveTermsVersion($newTermVersion);

        $this->eventDispatcher->dispatchTermsEvent(
            new TermsVersionCreatedEvent($newTermVersion)
        );
    }
}
