<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Event\TermsSignedEvent;
use RichId\TermsModuleBundle\Domain\Factory\TermsVersionSignatureFactory;
use RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign;
use RichId\TermsModuleBundle\Domain\Port\EntityRecoderInterface;
use RichId\TermsModuleBundle\Domain\Port\EventDispatcherInterface;
use RichId\TermsModuleBundle\Domain\Port\ResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

class SignTerms
{
    /** @var TermsVersionSignatureFactory */
    protected $termsVersionSignatureFactory;

    /** @var EntityRecoderInterface */
    protected $entityRecoder;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ResponseBuilderInterface */
    protected $responseBuilder;

    /** @var GetTermsVersionToSign */
    protected $getTermsVersionToSign;

    public function __construct(
        TermsVersionSignatureFactory $termsVersionSignatureFactory,
        EntityRecoderInterface $entityRecoder,
        EventDispatcherInterface $eventDispatcher,
        ResponseBuilderInterface $responseBuilder,
        GetTermsVersionToSign $getTermsVersionToSign
    ) {
        $this->termsVersionSignatureFactory = $termsVersionSignatureFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityRecoder = $entityRecoder;
        $this->responseBuilder = $responseBuilder;
        $this->getTermsVersionToSign = $getTermsVersionToSign;
    }

    public function __invoke(string $termsSlug, TermsSubjectInterface $subject, ?bool $accepted): Response
    {
        $lastVersion = ($this->getTermsVersionToSign)($termsSlug, $subject);

        if ($accepted === true) {
            $signature = ($this->termsVersionSignatureFactory)($lastVersion, $subject);
            $this->entityRecoder->saveSignature($signature);
        }

        $defaultResponse = $this->responseBuilder->buildDefaultTermsSignedResponse($accepted);
        $event = new TermsSignedEvent($lastVersion, $subject, $accepted, $defaultResponse);
        $this->eventDispatcher->dispatchTermsEvent($event);

        return $event->getResponse();
    }
}
