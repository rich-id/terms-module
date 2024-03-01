<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Exception\FirstVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\UseCase\RemoveTermsVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RemoveTermsVersionRoute extends AbstractController
{
    /** @var RemoveTermsVersion */
    protected $removeTermsVersion;

    public function __construct(RemoveTermsVersion $removeTermsVersion)
    {
        $this->removeTermsVersion = $removeTermsVersion;
    }

    #[IsGranted('MODULE_TERMS_ADMIN')]
    public function __invoke(TermsVersion $termsVersion): Response
    {
        try {
            ($this->removeTermsVersion)($termsVersion);

            return new JsonResponse(null, Response::HTTP_OK);
        } catch (EnabledVersionCannotBeDeletedException | FirstVersionCannotBeDeletedException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
