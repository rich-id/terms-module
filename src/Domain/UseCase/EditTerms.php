<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Event\TermsVersionUpdatedEvent;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;
use RichId\TermsModuleBundle\Domain\Port\ValidatorInterface;

class EditTerms
{
    /** @var ActivateTermsVersion */
    protected $activateTermsVersion;

    /** @var EntityRecoderInterface */
    protected $entityRecoder;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ValidatorInterface */
    protected $validator;

    public function __construct(
        ActivateTermsVersion $activateTermsVersion,
        EntityRecoderInterface $entityRecoder,
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator
    ) {
        $this->activateTermsVersion = $activateTermsVersion;
        $this->entityRecoder = $entityRecoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
    }

    public function __invoke(TermsEdition $termsEdition): void
    {
        $this->validator->validateTermsEdition($termsEdition);

        $termsVersion = $termsEdition->getEntity();
        $terms = $termsVersion->getTerms();

        $termsVersion->update($termsEdition);
        $terms->update($termsEdition);

        $this->entityRecoder->saveTerms($terms);
        $this->entityRecoder->saveTermsVersion($termsVersion);

        if ($termsEdition->needVersionActivation() === true) {
            ($this->activateTermsVersion)($termsVersion);
        }

        $this->eventDispatcher->dispatchTermsVersionUpdatedEvent(
            new TermsVersionUpdatedEvent($termsVersion)
        );
    }
}
