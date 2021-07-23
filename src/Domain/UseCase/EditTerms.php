<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Event\TermsPublishedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsUnpublishedEvent;
use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;
use RichId\TermsModuleBundle\Domain\Port\ValidatorInterface;
use RichId\TermsModuleBundle\Domain\Updater\TermsUpdater;
use RichId\TermsModuleBundle\Domain\Updater\TermsVersionUpdater;

class EditTerms
{
    /** @var ActivateTermsVersion */
    protected $activateTermsVersion;

    /** @var TermsUpdater */
    protected $termsUpdater;

    /** @var TermsVersionUpdater */
    protected $termsVersionUpdater;

    /** @var EntityRecoderInterface */
    protected $entityRecoder;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(
        ActivateTermsVersion $activateTermsVersion,
        TermsUpdater $termsUpdater,
        TermsVersionUpdater $termsVersionUpdater,
        EntityRecoderInterface $entityRecoder,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator
    ) {
        $this->activateTermsVersion = $activateTermsVersion;
        $this->termsUpdater = $termsUpdater;
        $this->termsVersionUpdater = $termsVersionUpdater;
        $this->entityRecoder = $entityRecoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
    }

    public function __invoke(TermsEdition $termsEdition): void
    {
        $this->validator->validateTermsEdition($termsEdition);

        $termsVersion = $termsEdition->getEntity();
        $terms = $termsVersion->getTerms();
        $termsPublished = $terms->isPublished();

        ($this->termsVersionUpdater)($termsVersion, $termsEdition);
        ($this->termsUpdater)($terms, $termsEdition);

        $this->entityRecoder->saveTermsVersion($termsVersion);
        $this->entityRecoder->saveTerms($terms);

        if ($termsEdition->needVersionActivation() === true) {
            ($this->activateTermsVersion)($termsVersion);
        }

        $this->eventDispatcher->dispatchTermsEvent(
            new TermsVersionUpdatedEvent($termsVersion)
        );

        if (!$termsPublished && $terms->isPublished()) {
            $this->eventDispatcher->dispatchTermsEvent(
                new TermsPublishedEvent($terms)
            );
        }

        if ($termsPublished && !$terms->isPublished()) {
            $this->eventDispatcher->dispatchTermsEvent(
                new TermsUnpublishedEvent($terms)
            );
        }
    }
}
