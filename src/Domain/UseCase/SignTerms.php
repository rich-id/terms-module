<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;
use RichId\TermsModuleBundle\Domain\Port\ResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

class SignTerms
{
    /** @var EntityRecoderInterface */
    protected $entityRecoder;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ResponseBuilderInterface */
    protected $responseBuilder;

    /** @var GetTermsVersionToSign */
    protected $getTermsVersionToSign;

    public function __construct(
        EntityRecoderInterface $entityRecoder,
        EventDispatcherInterface $eventDispatcher,
        ResponseBuilderInterface $responseBuilder,
        GetTermsVersionToSign $getTermsVersionToSign
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityRecoder = $entityRecoder;
        $this->responseBuilder = $responseBuilder;
        $this->getTermsVersionToSign = $getTermsVersionToSign;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject, ?bool $accepted): Response
    {
        $lastVersion = ($this->getTermsVersionToSign)($termsSlug, $subject);

        if ($accepted === true) {
            $signature = TermsVersionSignature::sign($lastVersion, $subject);
            $this->entityRecoder->saveSignature($signature);
            $this->entityRecoder->flush();
        }

        $defaultResponse = $this->responseBuilder->buildDefaultTermsSignedResponse($accepted);
        $event = new TermsSignedEvent($lastVersion, $subject, $accepted, $defaultResponse);
        $this->eventDispatcher->dispatchTermsSignedEvent($event);

        return $event->getResponse();
    }
}
